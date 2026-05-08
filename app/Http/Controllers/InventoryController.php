<?php
namespace App\Http\Controllers;

use App\Models\Inventory;
use Illuminate\Http\Request;
use Carbon\Carbon;

class InventoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $inventories = Inventory::with('product')
            ->latest()
            ->paginate(10);

        return view('inventory.index', compact('inventories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $products = \App\Models\Product::orderBy('name')->get();
        return view('inventory.create', compact('products'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity'   => 'required|integer|min:1|max:9999999',
            'notes'      => 'nullable|string|max:500',
            'entry_date' => 'nullable|date',
        ]);

        $product = \App\Models\Product::findOrFail($validated['product_id']);

        $inventoryData = [
            'product_id' => $product->id,
            'quantity'   => $validated['quantity'],
            'notes'      => $validated['notes'] ?? null,
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

        $product->increment('quantity', $validated['quantity']);

        return redirect()->route('inventory.index')
            ->with('success', 'Entrada registrada com sucesso.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Inventory $inventory)
    {
        $products = \App\Models\Product::orderBy('name')->get();
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

        $inventory->delete();

        return redirect()->route('inventory.index')->with('success', 'Entrada removida.');
    }

    // Other resource methods can be added later if needed
}

