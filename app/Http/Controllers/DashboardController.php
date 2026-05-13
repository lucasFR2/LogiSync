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
        
        // Cache dashboard data for 5 minutes to reduce DB load
        $data = \Illuminate\Support\Facades\Cache::remember("dashboard_data_{$user->id}", 300, function () use ($user) {
            $dashboardData = ['user' => $user];

            if ($user->hasRole('admin') || $user->hasRole('rh')) {
                // Select only necessary columns for the employee list
                $dashboardData['employees'] = User::select('id', 'name', 'role')
                    ->orderBy('name')
                    ->get();
            }

            if ($user->hasRole('admin')) {
                // Eager load user and select specific columns
                $dashboardData['recentLogs'] = ActivityLog::with(['user:id,name'])
                    ->latest()
                    ->take(5)
                    ->get();
            }
            
            return $dashboardData;
        });
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
