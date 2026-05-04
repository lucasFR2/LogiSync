<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        
        $products = Product::query()
            ->when($search, function ($query, $search) {
                return $query->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%");
            })
            ->paginate(10);

        return view('products.index', compact('products', 'search'));
    }

    public function create()
    {
        return view('products.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'sku' => 'required|string|unique:products',
            'quantity' => 'required|integer|min:0',
            'unit_price' => 'required|numeric|min:0',
            'reorder_level' => 'required|integer|min:0',
        ]);

        Product::create($validated);

        return redirect()->route('products.index')
            ->with('success', 'Produto criado com sucesso!');
    }

    public function show(Product $product)
    {
        $inventories = $product->inventories()->latest()->paginate(10);
        
        return view('products.show', compact('product', 'inventories'));
    }

    public function edit(Product $product)
    {
        return view('products.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'sku' => 'required|string|unique:products,sku,' . $product->id,
            'quantity' => 'required|integer|min:0',
            'unit_price' => 'required|numeric|min:0',
            'reorder_level' => 'required|integer|min:0',
        ]);

        $product->update($validated);

        return redirect()->route('products.index')
            ->with('success', 'Produto atualizado com sucesso!');
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()->route('products.index')
            ->with('success', 'Produto deletado com sucesso!');
    }

    // Inventory methods
    public function inventories()
    {
        $inventories = Inventory::with('product')
            ->latest()
            ->paginate(15);

        return view('inventory.index', compact('inventories'));
    }

    public function addInventory(Request $request, Product $product)
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string',
        ]);

        $inventory = new Inventory([
            'quantity' => $validated['quantity'],
            'type' => 'entrada',
            'notes' => $validated['notes'] ?? null,
        ]);

        $product->inventories()->save($inventory);
        $product->increment('quantity', $validated['quantity']);

        return redirect()->back()
            ->with('success', 'Entrada registrada com sucesso!');
    }
}
