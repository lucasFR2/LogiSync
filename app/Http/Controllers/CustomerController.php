<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Helpers\Logger;
use Illuminate\Http\Request;

use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class CustomerController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:customers.manage'),
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Customer::query();
        if ($search = $request->get('search')) {
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('document', 'like', "%{$search}%");
        }
        $customers = $query->orderBy('name')->paginate(15)->withQueryString();
        return view('customers.index', compact('customers'));
    }

    public function create()
    {
        return view('customers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'document' => 'required|string|max:30|unique:customers,document',
            'type' => 'required|in:individual,company',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:30',
            'state_registration' => 'nullable|string|max:30',
            'address' => 'nullable|string|max:255',
            'number' => 'nullable|string|max:20',
            'neighborhood' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:2',
            'zip_code' => 'nullable|string|max:15',
        ]);

        $customer = Customer::create($validated);
        Logger::log('create_customer_record', "O usuário cadastrou o cliente: {$customer->name} (#{$customer->id})");

        return redirect()->route('customers.index')->with('success', 'Cliente cadastrado com sucesso.');
    }

    public function show(Customer $customer)
    {
        return view('customers.show', compact('customer'));
    }

    public function edit(Customer $customer)
    {
        return view('customers.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'document' => 'required|string|max:30|unique:customers,document,' . $customer->id,
            'type' => 'required|in:individual,company',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:30',
            'state_registration' => 'nullable|string|max:30',
            'address' => 'nullable|string|max:255',
            'number' => 'nullable|string|max:20',
            'neighborhood' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:2',
            'zip_code' => 'nullable|string|max:15',
        ]);

        $customer->update($validated);
        Logger::log('update_customer_record', "O usuário alterou o cliente: {$customer->name} (#{$customer->id})");

        return redirect()->route('customers.index')->with('success', 'Dados do cliente atualizados.');
    }

    public function destroy(Customer $customer)
    {
        $custName = $customer->name;
        $custId = $customer->id;
        $customer->delete();
        Logger::log('delete_customer_record', "O usuário removeu o cliente: {$custName} (#{$custId})");

        return redirect()->route('customers.index')->with('success', 'Cliente removido com sucesso.');
    }
}
