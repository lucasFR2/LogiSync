<?php
namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Http\Request;
use App\Models\Inventory;
use App\Models\IncomingInvoice;
use Illuminate\Support\Facades\DB;
use App\Helpers\Logger;

class ProductController extends Controller
{
    // Listar todos os produtos
    public function index(Request $request)
    {
        $search = $request->query('search');
        $filterBy = $request->query('filter', 'all');
        $status = $request->query('status_filter');
        $priceMin = $request->query('price_min');
        $priceMax = $request->query('price_max');
        $stockFilter = $request->query('stock_filter');

        $query = Product::query();

        // Basic Search
        if ($search) {
            if ($filterBy === 'all') {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('barcode', 'like', "%{$search}%")
                      ->orWhere('category', 'like', "%{$search}%");
                });
            } else {
                $query->where($filterBy, 'like', "%{$search}%");
            }
        }

        // Status Filter
        if ($status) {
            $query->where('status', $status);
        }

        // Price Range
        if ($priceMin !== null && $priceMin !== '') {
            $query->where('unit_price', '>=', $priceMin);
        }
        if ($priceMax !== null && $priceMax !== '') {
            $query->where('unit_price', '<=', $priceMax);
        }

        // Stock Filter
        if ($stockFilter) {
            if ($stockFilter === 'low') {
                // quantity <= reorder_level
                $query->whereColumn('quantity', '<=', 'reorder_level');
            } elseif ($stockFilter === 'medium') {
                // reorder_level < quantity <= max_stock
                $query->whereColumn('quantity', '>', 'reorder_level')
                      ->whereColumn('quantity', '<=', 'max_stock');
            } elseif ($stockFilter === 'high') {
                // quantity > max_stock
                $query->whereColumn('quantity', '>', 'max_stock');
            }
        }

        // Preserve all query parameters for pagination
        $products = $query->paginate(15)->withQueryString();

        return view('products.index', compact('products', 'search', 'filterBy', 'status', 'priceMin', 'priceMax', 'stockFilter'));
    }
    

    public function inventories()
    {
        $inventories = Inventory::with(['product.supplier', 'supplier'])
            ->latest()
            ->paginate(15);

        $totalEntries   = Inventory::count();
        $monthEntries   = Inventory::where('created_at', '>=', now()->startOfMonth())->count();
        $todayEntries   = Inventory::where('created_at', '>=', now()->startOfDay())->count();
        $activeSKUs     = Inventory::distinct('product_id')->count('product_id');

        return view('inventory.index', compact('inventories', 'totalEntries', 'monthEntries', 'todayEntries', 'activeSKUs'));
    }

    public function createInventory()
    {
        $products  = Product::with('supplier')->orderBy('name')->get();
        $suppliers = Supplier::orderBy('name')->get();
        return view('inventory.create', compact('products', 'suppliers'));
    }

    public function storeInventory(Request $request)
    {
        $validated = $request->validate([
            'product_id'  => 'required|exists:products,id',
            'quantity'    => 'required|integer|min:1',
            'notes'       => 'nullable|string|max:500',
            'entry_date'  => 'nullable|date',
            'lot_number'  => 'nullable|string|max:100',
            'expiry_date' => 'nullable|date',
            'supplier_id' => 'nullable|exists:suppliers,id',
        ]);

        $product = Product::findOrFail($validated['product_id']);

        Inventory::create([
            'product_id'  => $product->id,
            'quantity'    => $validated['quantity'],
            'notes'       => $validated['notes'],
            'entry_date'  => $validated['entry_date'] ?? now(),
            'lot_number'  => $validated['lot_number'] ?? null,
            'expiry_date' => $validated['expiry_date'] ?? null,
            'supplier_id' => $validated['supplier_id'] ?? null,
            'user_id'     => auth()->id(),
            'type'        => 'entrada',
            'status'      => 'confirmada',
        ]);

        $product->increment('quantity', $validated['quantity']);

        return redirect()->route('inventory.index')
            ->with('success', 'Entrada de estoque registrada com sucesso!');
    }

    public function bulkCreate(IncomingInvoice $manifestation)
    {
        if ($manifestation->entry_status === 'imported') {
            return redirect()->route('manifestations.show', $manifestation)
                             ->with('error', 'Esta Nota Fiscal já foi importada para o estoque.');
        }

        $manifestation->load('items');
        $products = Product::orderBy('name')->get();
        $categories = Category::orderBy('name')->get();

        return view('inventory.bulk_import', compact('manifestation', 'products', 'categories'));
    }

    public function bulkStore(Request $request, IncomingInvoice $manifestation)
    {
        if ($manifestation->entry_status === 'imported') {
            return redirect()->route('manifestations.show', $manifestation)
                             ->with('error', 'Esta Nota Fiscal já foi importada.');
        }

        $request->validate([
            'items' => 'required|array',
            'items.*.product_id' => 'required|string', // Can be numeric ID or 'new'
            'items.*.quantity' => 'required|numeric|min:0.001',
        ]);

        DB::transaction(function () use ($request, $manifestation) {
            foreach ($request->items as $itemId => $data) {
                $qty = (float) $data['quantity'];
                
                if ($data['product_id'] === 'new') {
                    // Automagic creation of product based on XML item and user inputs
                    $xmlItem = $manifestation->items()->find($itemId);
                    $product = Product::create([
                        'name' => $data['new_name'] ?? $xmlItem->description,
                        'barcode' => $data['new_barcode'] ?? $xmlItem->barcode,
                        'description' => 'Produto importado via NF-e ' . $manifestation->number,
                        'unit_price' => $xmlItem->unit_price,
                        'purchase_price' => $xmlItem->unit_price,
                        'cost_price' => $xmlItem->unit_price,
                        'quantity' => 0, // will be incremented
                        'reorder_level' => 10,
                        'unit' => $xmlItem->unit,
                        'category' => $data['new_category'] ?? 'Importado Automático',
                        'supplier_id' => $manifestation->supplier_id,
                        'status' => 'ativo'
                    ]);
                } else {
                    $product = Product::findOrFail($data['product_id']);
                }

                Inventory::create([
                    'product_id' => $product->id,
                    'quantity' => $qty,
                    'notes' => 'Entrada via NF-e ' . $manifestation->number,
                    'entry_date' => now(),
                    'supplier_id' => $manifestation->supplier_id,
                    'user_id' => auth()->id(),
                    'type' => 'entrada',
                    'status' => 'confirmada',
                ]);

                $product->increment('quantity', $qty);

                // Atualizar o item da NF mapeado para o produto
                $manifestation->items()->where('id', $itemId)->update(['product_id' => $product->id]);
            }

            $manifestation->update(['entry_status' => 'imported']);
        });

        return redirect()->route('manifestations.show', $manifestation)
                         ->with('success', 'Estoque atualizado com sucesso a partir da NF-e!');
    }

    // Mostrar formulário de criação
    public function create()
    {
        $suppliers  = Supplier::orderBy('name')->get();
        $categories = Category::orderBy('name')->get();
        return view('products.create', compact('suppliers', 'categories'));
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

        // Verificar conflito de localização exclusiva
        if (!empty($validated['warehouse_location_id'])) {
            $location = \App\Models\WarehouseLocation::find($validated['warehouse_location_id']);
            if ($location && $location->is_occupied) {
                $occupant = Product::where('warehouse_location_id', $location->id)->first();
                return back()->withErrors([
                    'warehouse_location_id' => 'Esta posição (' . $location->full_code . ') já está ocupada pelo produto "' . ($occupant->name ?? '?') . '". Não é permitido dois produtos no mesmo endereço.',
                ])->withInput();
            }
        }

        // Criar o produto
        $product = Product::create($validated);

        // Marcar localização como ocupada
        if ($product->warehouse_location_id) {
            \App\Models\WarehouseLocation::where('id', $product->warehouse_location_id)
                ->update(['is_occupied' => true]);
        }

        // Registrar entrada inicial de estoque se quantidade > 0
        if ($validated['quantity'] > 0) {
            Inventory::create([
                'product_id' => $product->id,
                'quantity'   => $validated['quantity'],
                'notes'      => 'Entrada inicial - Cadastro do produto',
            ]);
        }

        Logger::log('create_product', "O usuário cadastrou o produto: {$product->name} (#{$product->id})");

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
        $suppliers  = Supplier::orderBy('name')->get();
        $categories = Category::orderBy('name')->get();
        return view('products.edit', compact('product', 'suppliers', 'categories'));
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

        // Verificar conflito de localização exclusiva
        $newLocId = $validated['warehouse_location_id'] ?? null;
        $oldLocId = $product->warehouse_location_id;

        if ($newLocId && $newLocId != $oldLocId) {
            $location = \App\Models\WarehouseLocation::find($newLocId);
            if ($location && $location->is_occupied) {
                $occupant = Product::where('warehouse_location_id', $location->id)->where('id', '!=', $product->id)->first();
                if ($occupant) {
                    return back()->withErrors([
                        'warehouse_location_id' => 'Esta posição (' . $location->full_code . ') já está ocupada pelo produto "' . $occupant->name . '". Não é permitido dois produtos no mesmo endereço.',
                    ])->withInput();
                }
            }
        }

        // Liberar localização antiga
        if ($oldLocId && $oldLocId != $newLocId) {
            \App\Models\WarehouseLocation::where('id', $oldLocId)
                ->update(['is_occupied' => false]);
        }

        // Atualizar o produto
        $product->update($validated);

        // Marcar nova localização como ocupada
        if ($newLocId && $newLocId != $oldLocId) {
            \App\Models\WarehouseLocation::where('id', $newLocId)
                ->update(['is_occupied' => true]);
        }

        Logger::log('update_product', "O usuário alterou o produto: {$product->name} (#{$product->id})");

        return redirect()->route('products.show', $product)
            ->with('success', 'Produto atualizado com sucesso!');
    }

    // Deletar produto
    public function destroy(Product $product)
    {
        $prodName = $product->name;
        $prodId = $product->id;
        $product->delete();

        Logger::log('delete_product', "O usuário removeu o produto: {$prodName} (#{$prodId})");

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

        Logger::log('inventory_entry', "O usuário registrou entrada de {$validated['quantity']} unidades para o produto: {$product->name} (#{$product->id})");

        return redirect()->route('products.show', $product)
            ->with('success', 'Entrada registrada com sucesso! Quantidade atualizada.');
    }
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
        $category = \App\Models\Category::create($validated);

        return response()->json([
            'success' => true,
            'id' => $category->id,
            'name' => $category->name,
            'description' => $category->description ?? '',
            'message' => 'Categoria adicionada com sucesso!'
        ]);
    }

    // Buscar localizações (AJAX) — returns occupied status + occupant
    public function searchLocations(Request $request)
    {
        $search   = $request->query('q', '');
        $aisle    = $request->query('aisle', '');
        $freeOnly = $request->query('free_only', '0');

        $query = \App\Models\WarehouseLocation::with('products:id,name,warehouse_location_id');

        if (!empty($search)) {
            $query->where('full_code', 'like', "%{$search}%");
        }
        if (!empty($aisle)) {
            $query->where('aisle', $aisle);
        }
        if ($freeOnly === '1') {
            $query->where('is_occupied', false);
        }

        $locations = $query->orderBy('full_code')
            ->limit(50)
            ->get()
            ->map(function ($loc) {
                $occupant = $loc->products->first();
                return [
                    'id'            => $loc->id,
                    'full_code'     => $loc->full_code,
                    'aisle'         => $loc->aisle,
                    'column'        => $loc->column,
                    'level'         => $loc->level,
                    'is_occupied'   => $loc->is_occupied,
                    'occupant_name' => $occupant?->name,
                ];
            });

        return response()->json($locations);
    }
}
