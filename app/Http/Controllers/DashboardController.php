<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $data = ['user' => $user];

        // Logistics Stats (for Logistics and Admin)
        if ($user->role !== 'Recursos Humanos (RH)') {
            // Using aggregate sum for better performance
            $data['totalStock'] = \App\Models\Product::sum('quantity');
            $data['pendingOrders'] = \App\Models\Invoice::where('status', 'rascunho')->count();
            $data['lowStockCount'] = \App\Models\Product::baixoEstoque()->count();
        }

        // Removed $data['employees'] as it is not used in the dashboard view

        if ($user->role === 'Administrador') {
            // Eager load only necessary user fields
            $data['recentLogs'] = ActivityLog::with('user:id,name,role')->latest()->take(10)->get();
        }

        return view('dashboard', $data);
    }
}
