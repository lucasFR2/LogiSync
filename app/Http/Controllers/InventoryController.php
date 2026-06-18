<?php
namespace App\Http\Controllers;

use App\Models\Inventory;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Helpers\Logger;

class InventoryController extends Controller
{
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
        $products = \App\Models\Product::select('id', 'name', 'barcode', 'quantity')
            ->orderBy('name')
            ->get();
        $suppliers = \App\Models\Supplier::select('id', 'name')->orderBy('name')->get();
        return view('inventory.create', compact('products', 'suppliers'));
    }

    /**
     * Store a newly created resource in storage.
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

        $product = \App\Models\Product::findOrFail($validated['product_id']);

        $inventoryData = [
            'product_id'         => $product->id,
            'quantity'           => $validated['quantity'],
            'checked_quantity'   => $validated['checked_quantity'],
            'remaining_quantity' => $validated['checked_quantity'],
            'supplier_id'        => $validated['supplier_id'] ?? null,
            'lot_number'         => $validated['lot_number'] ?? null,
            'expiry_date'        => $validated['expiry_date'] ?? null,
            'notes'              => $validated['notes'] ?? null,
            'conference_status'  => $validated['checked_quantity'] == $validated['quantity'] ? 'confirmada' : 'divergente',
            'conference_notes'   => $validated['conference_notes'] ?? null,
            'user_id'            => auth()->id(),
        ];

        $dt = null;
        if (!empty($validated['entry_date'])) {
            try {
                $dt = Carbon::parse($validated['entry_date']);
                $inventoryData['entry_date'] = $dt->toDateTimeString();
            } catch (\Exception $e) {
                $dt = null;
            }
        }

        $inventory = Inventory::create($inventoryData);

        if ($dt) {
            $inventory->timestamps = false;
            $inventory->entry_date = $dt->toDateTimeString();
            $inventory->created_at = $dt;
            $inventory->updated_at = $dt;
            $inventory->save();
        }

        $product->increment('quantity', $validated['checked_quantity']);

        $logMsg = "O usuário registrou uma entrada manual de {$validated['quantity']} unidades (conferidas: {$validated['checked_quantity']}) para o produto: {$product->name}";
        if ($inventoryData['conference_status'] === 'divergente') {
            $logMsg .= " [DIVERGENTE]";
        }
        Logger::log('inventory_create', $logMsg);

        return redirect()->route('inventory.index')
            ->with('success', 'Entrada registrada com sucesso.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Inventory $inventory)
    {
        $products = \App\Models\Product::select('id', 'name', 'barcode', 'quantity')
            ->orderBy('name')
            ->get();
        return view('inventory.edit', compact('inventory', 'products'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Inventory $inventory)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity'   => 'required|integer|min:1|max:9999999',
            'notes'      => 'nullable|string|max:500',
            'entry_date' => 'nullable|date',
        ]);

        $oldQuantity = $inventory->quantity;
        $newQuantity = $validated['quantity'];

        // If product changed, adjust both products quantities
        if ($inventory->product_id != $validated['product_id']) {
            $oldProduct = \App\Models\Product::find($inventory->product_id);
            $newProduct = \App\Models\Product::findOrFail($validated['product_id']);
            if ($oldProduct) {
                $oldProduct->decrement('quantity', $oldQuantity);
            }
            $newProduct->increment('quantity', $newQuantity);
        } else {
            // Same product, apply difference
            $diff = $newQuantity - $oldQuantity;
            if ($diff > 0) {
                \App\Models\Product::findOrFail($inventory->product_id)->increment('quantity', $diff);
            } elseif ($diff < 0) {
                \App\Models\Product::findOrFail($inventory->product_id)->decrement('quantity', abs($diff));
            }
        }

        $inventory->product_id = $validated['product_id'];
        $inventory->quantity = $newQuantity;
        $inventory->notes = $validated['notes'] ?? null;

        if (!empty($validated['entry_date'])) {
            try {
                $dt = Carbon::parse($validated['entry_date']);
                $inventory->entry_date = $dt->toDateTimeString();
                $inventory->timestamps = false;
                $inventory->created_at = $dt;
                $inventory->updated_at = $dt;
            } catch (\Exception $e) {
                // ignore
            }
        }

        $inventory->save();

        Logger::log('inventory_update', "O usuário alterou os dados de uma entrada de estoque (ID #{$inventory->id})");

        return redirect()->route('inventory.index')->with('success', 'Entrada atualizada com sucesso.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Inventory $inventory)
    {
        // Revert product quantity
        $product = \App\Models\Product::find($inventory->product_id);
        if ($product) {
            $product->decrement('quantity', $inventory->quantity);
        }

        $invId = $inventory->id;
        $inventory->delete();

        Logger::log('inventory_delete', "O usuário removeu o registro de entrada ID #{$invId}");

        return redirect()->route('inventory.index')->with('success', 'Entrada removida.');
    }

    // Other resource methods can be added later if needed
}

