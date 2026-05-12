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

    protected $productService;

    public function __construct(\App\Services\ProductService $productService)
    {
        $this->productService = $productService;
    }

    // Listar todos os produtos
    public function index(Request $request)
    {
        $products = Product::query()
            ->when($request->search, function($query, $search) use ($request) {
                $filterBy = $request->query('filter', 'all');
                if ($filterBy === 'all') {
                    $query->where(fn($q) => $q->where('name', 'like', "%$search%")->orWhere('barcode', 'like', "%$search%")->orWhere('category', 'like', "%$search%"));
                } else {
                    $query->where($filterBy, 'like', "%$search%");
                }
            })
            ->when($request->status_filter, fn($q, $v) => $q->where('status', $v))
            ->when($request->price_min, fn($q, $v) => $q->where('unit_price', '>=', $v))
            ->when($request->price_max, fn($q, $v) => $q->where('unit_price', '<=', $v))
            ->when($request->stock_filter, function($query, $stockFilter) {
                if ($stockFilter === 'low') $query->whereColumn('quantity', '<=', 'reorder_level');
                elseif ($stockFilter === 'medium') $query->whereColumn('quantity', '>', 'reorder_level')->whereColumn('quantity', '<=', 'max_stock');
                elseif ($stockFilter === 'high') $query->whereColumn('quantity', '>', 'max_stock');
            })
            ->paginate(15)
            ->withQueryString();

        return view('products.index', array_merge($request->all(), compact('products')));
    }
    

    public function inventories()
    {
        $inventories = Inventory::with(['product:id,name,supplier_id', 'product.supplier:id,name', 'supplier:id,name'])
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
        $products  = Product::orderBy('name')->get(['id', 'name', 'supplier_id']);
        $suppliers = Supplier::orderBy('name')->get(['id', 'name']);
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

        DB::transaction(function() use ($validated) {
            $product = Product::findOrFail($validated['product_id']);
            Inventory::create(array_merge($validated, [
                'entry_date' => $validated['entry_date'] ?? now(),
                'user_id'    => auth()->id(),
                'type'       => 'entrada',
                'status'     => 'confirmada',
            ]));
            $product->increment('quantity', $validated['quantity']);
        });

        return redirect()->route('inventory.index')->with('success', 'Entrada de estoque registrada com sucesso!');
    }

    public function bulkCreate(\App\Models\IncomingInvoice $manifestation)
    {
        if ($manifestation->entry_status === 'imported') {
            return redirect()->route('manifestations.show', $manifestation)->with('error', 'Esta Nota Fiscal já foi importada.');
        }

        $manifestation->load('items');
        $products = Product::orderBy('name')->get(['id', 'name']);
        $categories = \App\Models\Category::orderBy('name')->get(['id', 'name']);

        return view('inventory.bulk_import', compact('manifestation', 'products', 'categories'));
    }

    public function bulkStore(Request $request, \App\Models\IncomingInvoice $manifestation)
    {
        if ($manifestation->entry_status === 'imported') {
            return redirect()->route('manifestations.show', $manifestation)->with('error', 'Esta Nota Fiscal já foi importada.');
        }

        $request->validate(['items' => 'required|array', 'items.*.product_id' => 'required', 'items.*.quantity' => 'required|numeric|min:0.001']);

        try {
            DB::transaction(function () use ($request, $manifestation) {
                foreach ($request->items as $itemId => $data) {
                    $qty = (float) $data['quantity'];
                    if ($data['product_id'] === 'new') {
                        $xmlItem = $manifestation->items()->find($itemId);
                        $product = Product::create([
                            'name' => $data['new_name'] ?? $xmlItem->description,
                            'barcode' => $data['new_barcode'] ?? $xmlItem->barcode,
                            'unit_price' => $xmlItem->unit_price,
                            'quantity' => 0,
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
                        'supplier_id' => $manifestation->supplier_id,
                        'user_id' => auth()->id(),
                        'type' => 'entrada',
                        'status' => 'confirmada',
                    ]);

                    $product->increment('quantity', $qty);
                    $manifestation->items()->where('id', $itemId)->update(['product_id' => $product->id]);
                }
                $manifestation->update(['entry_status' => 'imported']);
            });
            return redirect()->route('manifestations.show', $manifestation)->with('success', 'Estoque atualizado com sucesso!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    // Mostrar formulário de criação
    public function create()
    {
        $suppliers  = Supplier::orderBy('name')->get(['id', 'name']);
        $categories = \App\Models\Category::orderBy('name')->get(['id', 'name']);
        return view('products.create', compact('suppliers', 'categories'));
    }

    // Gravar novo produto no banco
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'               => 'required|string|max:255',
            'barcode'            => 'nullable|string|unique:products,barcode|regex:/^[0-9]{1,20}$/',
            'unit_price'         => 'required|numeric|min:0',
            'quantity'           => 'required|integer|min:0',
            'reorder_level'      => 'required|integer|min:0',
            'status'             => 'required|in:ativo,inativo,descontinuado',
            'warehouse_location_id' => 'nullable|exists:warehouse_locations,id',
            'supplier_id'        => 'nullable|exists:suppliers,id',
        ]);

        try {
            $product = $this->productService->createProduct($request->all());
            Logger::log('create_product', "O usuário cadastrou o produto: {$product->name}");
            return redirect()->route('products.index')->with('success', 'Produto cadastrado com sucesso!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    // Mostrar um produto
    public function show(Product $product)
    {
        $inventories = $product->inventories()->with('user:id,name')->latest()->paginate(10);
        return view('products.show', compact('product', 'inventories'));
    }

    // Mostrar formulário de edição
    public function edit(Product $product)
    {
        $suppliers  = Supplier::orderBy('name')->get(['id', 'name']);
        $categories = \App\Models\Category::orderBy('name')->get(['id', 'name']);
        return view('products.edit', compact('product', 'suppliers', 'categories'));
    }

    // Atualizar produto no banco
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name'       => 'required|string|max:255',
            'unit_price' => 'required|numeric|min:0',
            'quantity'   => 'required|integer|min:0',
            'status'     => 'required|in:ativo,inativo,descontinuado',
        ]);

        try {
            $this->productService->updateProduct($product, $request->all());
            Logger::log('update_product', "O usuário alterou o produto: {$product->name}");
            return redirect()->route('products.show', $product)->with('success', 'Produto atualizado com sucesso!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    // Deletar produto
    public function destroy(Product $product)
    {
        $name = $product->name;
        $product->delete();
        Logger::log('delete_product', "O usuário removeu o produto: {$name}");
        return redirect()->route('products.index')->with('success', 'Produto deletado!');
    }

    // Registrar entrada de estoque rápida
    public function addInventory(Request $request, Product $product)
    {
        $validated = $request->validate(['quantity' => 'required|integer|min:1']);
        DB::transaction(function() use ($product, $validated, $request) {
            Inventory::create([
                'product_id' => $product->id,
                'quantity'   => $validated['quantity'],
                'notes'      => $request->notes,
                'user_id'    => auth()->id(),
                'type'       => 'entrada',
                'status'     => 'confirmada',
            ]);
            $product->increment('quantity', $validated['quantity']);
        });
        Logger::log('inventory_entry', "Entrada de {$validated['quantity']} para {$product->name}");
        return redirect()->route('products.show', $product)->with('success', 'Estoque atualizado!');
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
