<?php
namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Http\Request;
use App\Models\Inventory;

class ProductController extends Controller
{
    // Listar todos os produtos
    public function index()
    {
        $products = Product::paginate(15);
        return view('products.index', compact('products'));
    }
    

    public function inventories()
    {
        $inventories = Inventory::with('product')
            ->latest()
            ->paginate(10);

        return view('inventory.index', compact('inventories'));
    }

    // Mostrar formulário de criação
    public function create()
    {
        $suppliers = Supplier::all();
        return view('products.create', compact('suppliers'));
    }

    // Gravar novo produto no banco
    public function store(Request $request)
    {
        // Validar os dados
        $validated = $request->validate([
            'name'               => 'required|string|max:255',
            'barcode'            => 'nullable|string|unique:products,barcode|regex:/^[0-9]{1,20}$/',
            'description'        => 'nullable|string',
            'cost_price'         => 'nullable|numeric|min:0.01|max:999999.99',
            'unit_price'         => 'required|numeric|min:0.01|max:999999.99',
            'selling_price'      => 'nullable|numeric|min:0.01|max:999999.99',
            'quantity'           => 'required|integer|min:0|max:9999999',
            'max_stock'          => 'nullable|integer|min:1|max:9999999',
            'reorder_level'      => 'required|integer|min:0|max:9999999',
            'package_quantity'   => 'nullable|numeric|min:1|max:999999.99',
            'weight'             => 'nullable|numeric|min:0|max:999999.99',
            'height'             => 'nullable|numeric|min:0|max:999999.99',
            'width'              => 'nullable|numeric|min:0|max:999999.99',
            'depth'              => 'nullable|numeric|min:0|max:999999.99',
            'category'           => 'nullable|string',
            'unit'               => 'nullable|string',
            'warehouse_location' => 'nullable|string',
            'supplier_id'        => 'nullable|exists:suppliers,id',
            'status'             => 'required|in:ativo,inativo,descontinuado',
        ], [
            'barcode.regex'      => 'Código de barras deve conter apenas números',
            'cost_price.min'     => 'Custo unitário deve ser maior que R$ 0.00',
            'unit_price.min'     => 'Preço de venda deve ser maior que R$ 0.00',
            'max_stock.min'      => 'Estoque máximo deve ser pelo menos 1 unidade',
            'supplier_id.exists' => 'Fornecedor inválido',
        ]);

        // Criar o produto
        $product = Product::create($validated);

        // Registrar entrada inicial de estoque se quantidade > 0
        if ($validated['quantity'] > 0) {
            Inventory::create([
                'product_id' => $product->id,
                'quantity'   => $validated['quantity'],
                'notes'      => 'Entrada inicial - Cadastro do produto',
            ]);
        }

        return redirect()->route('products.index')
            ->with('success', 'Produto cadastrado com sucesso!');
    }

    // Mostrar um produto
    public function show(Product $product)
    {
        $inventories = $product->inventories()->latest()->paginate(10);
        return view('products.show', compact('product', 'inventories'));
    }

    // Mostrar formulário de edição
    public function edit(Product $product)
    {
        $suppliers = Supplier::all();
        return view('products.edit', compact('product', 'suppliers'));
    }

    // Atualizar produto no banco
    public function update(Request $request, Product $product)
    {
        // Validar os dados
        $validated = $request->validate([
            'name'               => 'required|string|max:255',
            'barcode'            => 'nullable|string|unique:products,barcode,' . $product->id . '|regex:/^[0-9]{1,20}$/',
            'description'        => 'nullable|string',
            'cost_price'         => 'nullable|numeric|min:0.01|max:999999.99',
            'unit_price'         => 'required|numeric|min:0.01|max:999999.99',
            'selling_price'      => 'nullable|numeric|min:0.01|max:999999.99',
            'quantity'           => 'required|integer|min:0|max:9999999',
            'max_stock'          => 'nullable|integer|min:1|max:9999999',
            'reorder_level'      => 'required|integer|min:0|max:9999999',
            'package_quantity'   => 'nullable|numeric|min:1|max:999999.99',
            'weight'             => 'nullable|numeric|min:0|max:999999.99',
            'height'             => 'nullable|numeric|min:0|max:999999.99',
            'width'              => 'nullable|numeric|min:0|max:999999.99',
            'depth'              => 'nullable|numeric|min:0|max:999999.99',
            'category'           => 'nullable|string',
            'unit'               => 'nullable|string',
            'warehouse_location' => 'nullable|string',
            'supplier_id'        => 'nullable|exists:suppliers,id',
            'status'             => 'required|in:ativo,inativo,descontinuado',
        ], [
            'barcode.regex'      => 'Código de barras deve conter apenas números',
            'cost_price.min'     => 'Custo unitário deve ser maior que R$ 0.00',
            'unit_price.min'     => 'Preço de venda deve ser maior que R$ 0.00',
            'max_stock.min'      => 'Estoque máximo deve ser pelo menos 1 unidade',
            'supplier_id.exists' => 'Fornecedor inválido',
        ]);

        // Atualizar o produto
        $product->update($validated);

        function store(Request $request)
        {
            $request->validate([
                'max_stock' => 'required|integer|min:1',
            ], [
                'max_stock.min' => 'Mensagem',
            ]);
        }

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

    // Registrar entrada de estoque
    public function addInventory(Request $request, Product $product)
    {
        // Validar os dados
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1|max:9999999',
            'notes'    => 'nullable|string|max:500',
        ], [
            'quantity.required' => 'A quantidade é obrigatória',
            'quantity.min'      => 'A quantidade deve ser pelo menos 1 unidade',
            'quantity.max'      => 'A quantidade não pode exceder 9.999.999 unidades',
        ]);

        // Criar registro de entrada
        Inventory::create([
            'product_id' => $product->id,
            'quantity'   => $validated['quantity'],
            'notes'      => $validated['notes'] ?? null,
        ]);

        // Atualizar quantidade do produto
        $product->increment('quantity', $validated['quantity']);

        return redirect()->route('products.show', $product)
            ->with('success', 'Entrada registrada com sucesso! Quantidade atualizada.');
    }
}
