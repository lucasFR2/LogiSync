<?php

namespace App\Http\Controllers;

use App\Models\Carrier;
use App\Helpers\Logger;
use Illuminate\Http\Request;

use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class CarrierController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:transportadoras.gerenciar'),
        ];
    }

    public function index(Request $request)
    {
        $search = $request->query('search');
        $query  = Carrier::query();

        if ($search) {
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('cnpj', 'like', "%{$search}%");
        }

        $carriers = $query->orderBy('name')->paginate(15)->withQueryString();
        return view('carriers.index', compact('carriers', 'search'));
    }

    public function create()
    {
        return view('carriers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'               => 'required|string|max:255',
            'cnpj'               => 'nullable|string|max:30|unique:carriers,cnpj',
            'state_registration' => 'nullable|string|max:30',
            'contact'            => 'nullable|string|max:255',
            'email'              => 'nullable|email|max:255',
            'phone'              => 'nullable|string|max:30',
            'antt'               => 'nullable|string|max:30',
            'vehicle_plate'      => 'nullable|string|max:10',
            'vehicle_uf'         => 'nullable|string|max:2',
            'vehicle_type'       => 'nullable|string|max:100',
            'street'             => 'nullable|string|max:255',
            'number'             => 'nullable|string|max:20',
            'complement'         => 'nullable|string|max:255',
            'neighborhood'       => 'nullable|string|max:255',
            'city'               => 'nullable|string|max:100',
            'state'              => 'nullable|string|max:2',
            'zip_code'           => 'nullable|string|max:15',
        ]);

        $carrier = Carrier::create($validated);
        Logger::log('create_carrier', "O usuário cadastrou a transportadora: {$carrier->name} (#{$carrier->id})");

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'id'    => $carrier->id,
                'name'  => $carrier->name,
                'cnpj'  => $carrier->cnpj,
                'antt'  => $carrier->antt,
                'state_registration' => $carrier->state_registration,
                'street'  => $carrier->street,
                'number'  => $carrier->number,
                'city'    => $carrier->city,
                'state'   => $carrier->state,
                'vehicle_plate' => $carrier->vehicle_plate,
                'vehicle_uf'    => $carrier->vehicle_uf,
            ]);
        }

        return redirect()->route('carriers.index')
                         ->with('success', 'Transportadora cadastrada com sucesso!');
    }

    public function show(Carrier $carrier)
    {
        return view('carriers.show', compact('carrier'));
    }

    public function edit(Carrier $carrier)
    {
        return view('carriers.edit', compact('carrier'));
    }

    public function update(Request $request, Carrier $carrier)
    {
        $validated = $request->validate([
            'name'               => 'required|string|max:255',
            'cnpj'               => 'nullable|string|max:30|unique:carriers,cnpj,' . $carrier->id,
            'state_registration' => 'nullable|string|max:30',
            'contact'            => 'nullable|string|max:255',
            'email'              => 'nullable|email|max:255',
            'phone'              => 'nullable|string|max:30',
            'antt'               => 'nullable|string|max:30',
            'vehicle_plate'      => 'nullable|string|max:10',
            'vehicle_uf'         => 'nullable|string|max:2',
            'vehicle_type'       => 'nullable|string|max:100',
            'street'             => 'nullable|string|max:255',
            'number'             => 'nullable|string|max:20',
            'complement'         => 'nullable|string|max:255',
            'neighborhood'       => 'nullable|string|max:255',
            'city'               => 'nullable|string|max:100',
            'state'              => 'nullable|string|max:2',
            'zip_code'           => 'nullable|string|max:15',
        ]);

        $carrier->update($validated);
        Logger::log('update_carrier', "O usuário alterou a transportadora: {$carrier->name} (#{$carrier->id})");

        return redirect()->route('carriers.index')
                         ->with('success', 'Transportadora atualizada com sucesso!');
    }

    public function destroy(Carrier $carrier)
    {
        $name = $carrier->name;
        $id   = $carrier->id;
        $carrier->delete();
        Logger::log('delete_carrier', "O usuário removeu a transportadora: {$name} (#{$id})");

        return redirect()->route('carriers.index')
                         ->with('success', 'Transportadora removida com sucesso!');
    }

    /**
     * Lista para selects AJAX
     */
    public function list()
    {
        $carriers = Carrier::orderBy('name')->get([
            'id', 'name', 'cnpj', 'state_registration',
            'street', 'number', 'city', 'state',
            'vehicle_plate', 'vehicle_uf', 'antt',
        ]);
        return response()->json($carriers);
    }
}
