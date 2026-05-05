<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    // Listar todos os produtos
    public function index()
    {
        $products = Product::paginate(15);
        return view('products.index', compact('products'));
    }

    // Mostrar formulário de criação
    public function create()
    {
        return view('products.create');
    }

    // Gravar novo produto no banco
    public function store(Request $request)
    {
        // Validar os dados
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'required|string|unique:products,sku',
            'barcode' => 'nullable|string|unique:products,barcode',
            'description' => 'nullable|string',
            'cost_price' => 'nullable|numeric|min:0',
            'unit_price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:0',
            'max_stock' => 'nullable|integer|min:0',
            'reorder_level' => 'required|integer|min:0',
            'package_quantity' => 'nullable|numeric|min:1',
            'weight' => 'nullable|numeric|min:0',
            'height' => 'nullable|numeric|min:0',
            'width' => 'nullable|numeric|min:0',
            'depth' => 'nullable|numeric|min:0',
            'category' => 'nullable|string',
            'unit' => 'nullable|string',
            'warehouse_location' => 'nullable|string',
            'supplier' => 'nullable|string',
            'status' => 'required|in:ativo,inativo,descontinuado',
        ]);

        // Criar o produto
        Product::create($validated);

        return redirect()->route('products.index')
                        ->with('success', 'Produto cadastrado com sucesso!');
    }

    // Mostrar um produto
    public function show(Product $product)
    {
        return view('products.show', compact('product'));
    }

    // Mostrar formulário de edição
    public function edit(Product $product)
    {
        return view('products.edit', compact('product'));
    }

    // Atualizar produto no banco
    public function update(Request $request, Product $product)
    {
        // Validar os dados
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'required|string|unique:products,sku,' . $product->id,
            'barcode' => 'nullable|string|unique:products,barcode,' . $product->id,
            'description' => 'nullable|string',
            'cost_price' => 'nullable|numeric|min:0',
            'unit_price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:0',
            'max_stock' => 'nullable|integer|min:0',
            'reorder_level' => 'required|integer|min:0',
            'package_quantity' => 'nullable|numeric|min:1',
            'weight' => 'nullable|numeric|min:0',
            'height' => 'nullable|numeric|min:0',
            'width' => 'nullable|numeric|min:0',
            'depth' => 'nullable|numeric|min:0',
            'category' => 'nullable|string',
            'unit' => 'nullable|string',
            'warehouse_location' => 'nullable|string',
            'supplier' => 'nullable|string',
            'status' => 'required|in:ativo,inativo,descontinuado',
        ]);

        // Atualizar o produto
        $product->update($validated);

        return redirect()->route('products.show', $product)
                        ->with('success', 'Produto atualizado com sucesso!');
    }

    // Deletar produto
    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()->route('products.index')
                        ->with('success', 'Produto deletado com sucesso!');
    }
}
