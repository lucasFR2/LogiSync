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
        $validated = $request->validate([
            'name'               => 'required|string|max:255',
            'barcode'            => 'nullable|string|unique:products,barcode|regex:/^[0-9]{1,20}$/',
            'description'        => 'nullable|string',
            'cost_price'         => 'nullable|numeric|min:0|max:999999.99',
            'unit_price'         => 'required|numeric|min:0|max:999999.99',
            'purchase_price'     => 'nullable|numeric|min:0|max:999999.99',
            'tax_percent'        => 'nullable|numeric|min:0|max:100',
            'shipping_cost'      => 'nullable|numeric|min:0|max:999999.99',
            'margin_percent'     => 'nullable|numeric|min:0|max:9999.99',
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
            'warehouse_location_id' => 'nullable|exists:warehouse_locations,id',
            'supplier_id'        => 'nullable|exists:suppliers,id',
            'status'             => 'required|in:ativo,inativo,descontinuado',
        ], [
            'barcode.regex'      => 'Código de barras deve conter apenas números',
            'unit_price.min'     => 'Preço de venda não pode ser negativo',
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
            'cost_price'         => 'nullable|numeric|min:0|max:999999.99',
            'unit_price'         => 'required|numeric|min:0|max:999999.99',
            'purchase_price'     => 'nullable|numeric|min:0|max:999999.99',
            'tax_percent'        => 'nullable|numeric|min:0|max:100',
            'shipping_cost'      => 'nullable|numeric|min:0|max:999999.99',
            'margin_percent'     => 'nullable|numeric|min:0|max:9999.99',
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
            'warehouse_location_id' => 'nullable|exists:warehouse_locations,id',
            'supplier_id'        => 'nullable|exists:suppliers,id',
            'status'             => 'required|in:ativo,inativo,descontinuado',
        ], [
            'barcode.regex'      => 'Código de barras deve conter apenas números',
            'unit_price.min'     => 'Preço de venda não pode ser negativo',
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
<<<<<<< Updated upstream
=======

    // Adicionar nova categoria (via AJAX)
    public function storeCategory(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:categories,name',
            'description' => 'nullable|string|max:500',
        ], [
            'name.required' => 'O nome da categoria é obrigatório',
            'name.unique' => 'Já existe uma categoria com esse nome',
            'name.max' => 'O nome da categoria não pode exceder 100 caracteres',
            'description.max' => 'A descrição não pode exceder 500 caracteres',
        ]);

        // Salvar categoria no banco de dados
        $category = Category::create($validated);

        return response()->json([
            'success' => true,
            'id' => $category->id,
            'name' => $category->name,
            'description' => $category->description ?? '',
            'message' => 'Categoria adicionada com sucesso!'
        ]);
    }

    // Buscar localizações (AJAX)
    public function searchLocations(Request $request)
    {
        $search = $request->query('q', '');
        
        $query = \App\Models\WarehouseLocation::query();

        if (!empty($search)) {
            $query->where('full_code', 'like', "%{$search}%");
        }

        $locations = $query->where('is_occupied', false)
            ->limit(20)
            ->get(['id', 'full_code', 'aisle', 'column', 'level']);

        return response()->json($locations);
    }
>>>>>>> Stashed changes
}
