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
use Illuminate\Validation\ValidationException;

use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class ProductController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:produtos.visualizar'),
        ];
    }

    protected $productService;
    
    public function __construct(\App\Services\ProductService $productService)
    {
        $this->productService = $productService;
    }

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

        $allowedFilterColumns = ['all', 'name', 'barcode', 'category'];
        if (!in_array($filterBy, $allowedFilterColumns, true)) {
            $filterBy = 'all';
        }

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

        return view('products.index', array_merge($request->all(), compact('products')));
    }
    

    public function inventories()
    {
        $inventories = Inventory::with(['product:id,name,supplier_id,barcode', 'product.supplier:id,name', 'supplier:id,name'])
            ->latest()
            ->paginate(15);

        // Don't cache during development or if precision is needed for recent entries
        $stats = [
            'totalEntries' => Inventory::count(),
            'monthEntries' => Inventory::where('created_at', '>=', now()->startOfMonth())->count(),
            'todayEntries' => Inventory::where('created_at', '>=', now()->startOfDay())->count(),
            'activeSKUs'   => Inventory::distinct('product_id')->count('product_id'),
        ];

        $totalEntries = $stats['totalEntries'];
        $monthEntries = $stats['monthEntries'];
        $todayEntries = $stats['todayEntries'];
        $activeSKUs   = $stats['activeSKUs'];

        return view('inventory.index', compact('inventories', 'totalEntries', 'monthEntries', 'todayEntries', 'activeSKUs'));
    }

    public function createInventory()
    {
        // Fetch products with all necessary fields for the UI attributes
        $products = Product::select('id', 'name', 'supplier_id', 'quantity', 'unit', 'unit_price', 'category', 'warehouse_location', 'barcode')
            ->with('supplier:id,name')
            ->orderBy('name')
            ->get();
            
        $suppliers = Supplier::select('id', 'name')->orderBy('name')->get();
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
            
            // Forçamos a conversão para Carbon e garantimos que a hora seja preservada
            // Se vier nulo, usa o agora. Se vier string, converte.
            try {
                $entryDate = $validated['entry_date'] ? \Carbon\Carbon::parse($validated['entry_date']) : now();
            } catch (\Exception $e) {
                $entryDate = now();
            }

            Inventory::create(array_merge($validated, [
                'entry_date' => $entryDate,
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
        $products = Product::select('id', 'name', 'barcode')->orderBy('name')->get();
        $categories = \App\Models\Category::orderBy('name')->get();

        return view('inventory.bulk_import', compact('manifestation', 'products', 'categories'));
    }

    public function bulkStore(Request $request, \App\Models\IncomingInvoice $manifestation)
    {
        if ($manifestation->entry_status === 'imported') {
            return redirect()->route('manifestations.show', $manifestation)->with('error', 'Esta Nota Fiscal já foi importada.');
        }

        $request->validate([
            'items' => 'required|array',
            'items.*.product_id' => 'required|string', // Can be numeric ID or 'new'
            'items.*.quantity' => 'required|numeric|min:0.001',
        ]);

        $items = $request->input('items', []);
        $errors = [];

        foreach ($items as $itemId => $data) {
            $productId = (string) ($data['product_id'] ?? '');
            $quantity = $data['quantity'] ?? null;

            if ($productId === 'new') {
                $validator = validator($data, [
                    'new_name' => 'nullable|string|max:255',
                    'new_barcode' => 'nullable|string|max:20|regex:/^[0-9]{1,20}$/',
                    'new_category' => 'nullable|string|max:100',
                    'quantity' => 'required|numeric|min:0.001',
                ], [
                    'new_barcode.regex' => 'Código de barras deve conter apenas números',
                ]);

                if ($validator->fails()) {
                    foreach ($validator->errors()->toArray() as $field => $messages) {
                        foreach ($messages as $message) {
                            $errors["items.$itemId.$field"][] = $message;
                        }
                    }
                }
            } else {
                if ($productId === '' || !ctype_digit($productId) || !Product::whereKey((int) $productId)->exists()) {
                    $errors["items.$itemId.product_id"][] = 'Produto inválido.';
                }

                if ($quantity === null) {
                    $errors["items.$itemId.quantity"][] = 'Quantidade é obrigatória.';
                }
            }

            if (!$manifestation->items()->whereKey($itemId)->exists()) {
                $errors["items.$itemId"][] = 'Item da NF-e inválido.';
            }
        }

        if (!empty($errors)) {
            throw ValidationException::withMessages($errors);
        }

        try {
            DB::transaction(function () use ($request, $manifestation) {
            foreach ($request->items as $itemId => $data) {
                $qty = (float) $data['quantity'];
                
                if ($data['product_id'] === 'new') {
                    // Automagic creation of product based on XML item and user inputs
                    $xmlItem = $manifestation->items()->find($itemId);
                    if (!$xmlItem) {
                        throw ValidationException::withMessages([
                            "items.$itemId" => ['Item da NF-e inválido.'],
                        ]);
                    }
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
        $suppliers  = Supplier::select('id', 'name')->orderBy('name')->get();
        $categories = Category::select('id', 'name')->orderBy('name')->get();
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
        $product->load('supplier', 'location', 'auditLogs.user');
        $inventories = $product->inventories()->with('user:id,name')->latest()->paginate(10);
        return view('products.show', compact('product', 'inventories'));
    }

    // Mostrar formulário de edição
    public function edit(Product $product)
    {
        $suppliers  = Supplier::select('id', 'name')->orderBy('name')->get();
        $categories = Category::select('id', 'name')->orderBy('name')->get();
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

    /**
     * Tela de seleção de produtos para etiquetas
     */
    public function labelSelection(Request $request)
    {
        $search = $request->query('search');
        $query = Product::where('status', 'ativo');

        if ($search) {
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('barcode', 'like', "%{$search}%");
        }

        $products = $query->orderBy('name')->get();

        return view('products.labels_select', compact('products'));
    }

    /**
     * Gerar etiquetas em lote (PDF)
     */
    public function printLabels(Request $request)
    {
        $ids = $request->input('product_ids');

        $query = Product::where('status', 'ativo');

        if ($ids && is_array($ids)) {
            $query->whereIn('id', $ids);
        } else {
            // Se nenhum for selecionado e vier da seleção, volta com erro
            if ($request->has('from_selection')) {
                return redirect()->back()->with('error', 'Por favor, selecione ao menos um produto.');
            }
            // Comportamento padrão: todos com estoque (se acessado diretamente)
            $query->where('quantity', '>', 0);
        }

        $products = $query->orderBy('name')->get();

        if ($products->isEmpty()) {
            return redirect()->back()->with('error', 'Nenhum produto encontrado para gerar etiquetas.');
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('products.labels', compact('products'));
        $pdf->setPaper('A4', 'portrait');

        return $pdf->stream('etiquetas_logisync_' . now()->format('Ymd_His') . '.pdf');
    }
}
