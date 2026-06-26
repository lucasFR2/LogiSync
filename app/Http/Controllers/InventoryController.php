<?php
namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\Product;
use App\Services\InventoryService;
use Illuminate\Http\Request;
use App\Helpers\Logger;

class InventoryController extends Controller
{
    protected InventoryService $inventoryService;

    public function __construct(InventoryService $inventoryService)
    {
        $this->inventoryService = $inventoryService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $inventories = Inventory::select('id', 'product_id', 'supplier_id', 'quantity', 'notes', 'entry_date', 'created_at')
            ->with([
                'product:id,name,barcode,supplier_id',
                'product.supplier:id,name',
                'supplier:id,name',
            ])
            ->latest()
            ->paginate(10);

        return view('inventory.index', compact('inventories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $products  = Product::select('id', 'name', 'barcode', 'quantity', 'width', 'height', 'depth', 'warehouse_location_id')
            ->with('location:id,full_code,width,height,depth')
            ->orderBy('name')
            ->get();
        $suppliers = \App\Models\Supplier::select('id', 'name')->orderBy('name')->get();

        return view('inventory.create', compact('products', 'suppliers'));
    }

    /**
     * Store a newly created resource in storage.
     * Valida espaço físico via InventoryService antes de criar o registro.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id'       => 'required|exists:products,id',
            'quantity'         => 'required|integer|min:1|max:9999999',
            'checked_quantity' => 'required|integer|min:0|max:9999999',
            'supplier_id'      => 'nullable|exists:suppliers,id',
            'lot_number'       => 'nullable|string|max:100',
            'expiry_date'      => 'nullable|date',
            'notes'            => 'nullable|string|max:500',
            'conference_notes' => 'nullable|string|max:1000',
            'entry_date'       => 'nullable|date',
        ]);

        $product = Product::with('location')->findOrFail($validated['product_id']);

        try {
            $inventory = $this->inventoryService->registerEntry($product, $validated['quantity'], $validated);

            $logMsg = "O usuário registrou uma entrada de {$validated['quantity']} unidades "
                . "(conferidas: {$validated['checked_quantity']}) para o produto: {$product->name}";

            if (($inventory->conference_status ?? '') === 'divergente') {
                $logMsg .= ' [DIVERGENTE]';
            }

            Logger::log('inventory_create', $logMsg);

            return redirect()->route('inventory.index')
                ->with('success', 'Entrada registrada com sucesso.');

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Inventory $inventory)
    {
        $products = Product::select('id', 'name', 'barcode', 'quantity', 'width', 'height', 'depth', 'warehouse_location_id')
            ->orderBy('name')
            ->get();

        return view('inventory.edit', compact('inventory', 'products'));
    }

    /**
     * Update the specified resource in storage.
     * Recalcula ocupação volumétrica ao alterar quantidade ou produto.
     */
    public function update(Request $request, Inventory $inventory)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity'   => 'required|integer|min:1|max:9999999',
            'notes'      => 'nullable|string|max:500',
            'entry_date' => 'nullable|date',
        ]);

        try {
            $this->inventoryService->updateEntry($inventory, $validated['quantity'], $validated);

            Logger::log('inventory_update', "O usuário alterou os dados de uma entrada de estoque (ID #{$inventory->id})");

            return redirect()->route('inventory.index')->with('success', 'Entrada atualizada com sucesso.');

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     * Reverte a quantidade corretamente e libera espaço na posição.
     */
    public function destroy(Inventory $inventory)
    {
        $invId = $inventory->id;

        try {
            $this->inventoryService->deleteEntry($inventory);

            Logger::log('inventory_delete', "O usuário removeu o registro de entrada ID #{$invId}");

            return redirect()->route('inventory.index')->with('success', 'Entrada removida.');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
