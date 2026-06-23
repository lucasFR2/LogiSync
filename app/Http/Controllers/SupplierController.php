<?php
namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Helpers\Logger;
use Illuminate\Http\Request;

use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class SupplierController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:fornecedores.gerenciar'),
        ];
    }

    // Listar todos os fornecedores
    public function index(Request $request)
    {
        $search = $request->query('search');

        $query = Supplier::query();

        if ($search) {
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('cnpj', 'like', "%{$search}%");
        }

        $suppliers = $query->paginate(15)->appends(['search' => $search]);

        return view('suppliers.index', compact('suppliers', 'search'));
    }

    // Mostrar formulário de criação
    public function create()
    {
        return view('suppliers.create');
    }

    // Gravar novo fornecedor (AJAX)
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'               => 'required|string|max:255',
            'email'              => 'nullable|email|max:255',
            'phone'              => 'nullable|string|max:30',
            'street'             => 'nullable|string|max:255',
            'number'             => 'nullable|string|max:20',
            'complement'         => 'nullable|string|max:255',
            'neighborhood'       => 'nullable|string|max:255',
            'zip_code'           => 'nullable|string|max:20',
            'city'               => 'nullable|string|max:100',
            'state'              => 'nullable|string|max:2',
            'cnpj'               => 'nullable|string|max:30|unique:suppliers,cnpj',
            'state_registration' => 'nullable|string|max:30',
        ]);

        $supplier = Supplier::create($validated);
        Logger::log('create_supplier', "O usuário cadastrou o fornecedor: {$supplier->name} (#{$supplier->id})");

        // Se é requisição AJAX, retorna JSON
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Fornecedor cadastrado com sucesso!',
                'supplier' => $supplier,
            ]);
        }

        return redirect()->route('suppliers.index')
                        ->with('success', 'Fornecedor cadastrado com sucesso!');
    }

    // Mostrar um fornecedor
    public function show(Supplier $supplier)
    {
        return view('suppliers.show', compact('supplier'));
    }

    // Mostrar formulário de edição
    public function edit(Supplier $supplier)
    {
        return view('suppliers.edit', compact('supplier'));
    }

    // Atualizar fornecedor
    public function update(Request $request, Supplier $supplier)
    {
        $validated = $request->validate([
            'name'               => 'required|string|max:255',
            'email'              => 'nullable|email|max:255',
            'phone'              => 'nullable|string|max:30',
            'street'             => 'nullable|string|max:255',
            'number'             => 'nullable|string|max:20',
            'complement'         => 'nullable|string|max:255',
            'neighborhood'       => 'nullable|string|max:255',
            'zip_code'           => 'nullable|string|max:20',
            'city'               => 'nullable|string|max:100',
            'state'              => 'nullable|string|max:2',
            'cnpj'               => 'nullable|string|max:30|unique:suppliers,cnpj,' . $supplier->id,
            'state_registration' => 'nullable|string|max:30',
        ]);

        $supplier->update($validated);
        Logger::log('update_supplier', "O usuário alterou o fornecedor: {$supplier->name} (#{$supplier->id})");

        return redirect()->route('suppliers.index')
                        ->with('success', 'Fornecedor atualizado com sucesso!');
    }

    // Deletar fornecedor
    public function destroy(Supplier $supplier)
    {
        $supName = $supplier->name;
        $supId = $supplier->id;
        $supplier->delete();
        Logger::log('delete_supplier', "O usuário removeu o fornecedor: {$supName} (#{$supId})");

        return redirect()->route('suppliers.index')
                        ->with('success', 'Fornecedor deletado com sucesso!');
    }

    // Listar fornecedores (AJAX para select)
    public function list()
    {
        $suppliers = Supplier::all(['id', 'name']);
        return response()->json($suppliers);
    }
}
