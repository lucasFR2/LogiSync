<?php
namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        $query = Supplier::query();
        if ($search = $request->get('search')) {
            $query->where('name', 'like', "%{$search}%");
        }
        $suppliers = $query->orderBy('name')->paginate(15);
        return view('suppliers.index', compact('suppliers'));
    }

    public function create()
    {
        return view('suppliers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'cnpj' => 'nullable|string|max:30|unique:suppliers,cnpj',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:30',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:2',
        ]);

        try {
            $supplier = Supplier::create($validated);
        } catch (\Illuminate\Database\QueryException $e) {
            // Handle unique constraint race or other DB errors
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'message' => 'Erro ao criar fornecedor. CNPJ já existe ou dados inválidos.'
                ], 409);
            }

            return redirect()->back()->withInput()->withErrors(['cnpj' => 'CNPJ já cadastrado.']);
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'supplier' => $supplier,
                'message' => 'Fornecedor criado com sucesso.'
            ], 201);
        }

        return redirect()->route('suppliers.index')->with('success', 'Fornecedor criado com sucesso.');
    }

    public function edit(Supplier $supplier)
    {
        return view('suppliers.edit', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'cnpj' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:30',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:2',
        ]);

        $supplier->update($validated);

        return redirect()->route('suppliers.index')->with('success', 'Fornecedor atualizado.');
    }

    public function destroy(Supplier $supplier)
    {
        $supplier->delete();
        return redirect()->route('suppliers.index')->with('success', 'Fornecedor removido.');
    }
}
