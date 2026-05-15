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
        $data = \Illuminate\Support\Facades\Cache::remember('dashboard_data_' . $user->id, 300, function () use ($user) {
            $dashboardData = ['user' => $user];

            if ($user->hasRole('admin') || $user->hasRole('rh')) {
                $dashboardData['employees'] = User::select('id', 'name', 'role')
                    ->orderBy('name')
                    ->get();
            }

            if ($user->hasRole('admin')) {
                $dashboardData['recentLogs'] = ActivityLog::with(['user:id,name'])
                    ->latest()
                    ->take(5)
                    ->get();
            }

            // Logistics Stats (for Logistics and Admin)
            if ($user->role !== 'Recursos Humanos (RH)') {
                $dashboardData['totalStock'] = \App\Models\Product::sum('quantity');
                $dashboardData['pendingOrders'] = \App\Models\Invoice::where('status', 'rascunho')->count();
                $dashboardData['lowStockCount'] = \App\Models\Product::baixoEstoque()->count();
            }

            if ($user->role === 'Administrador') {
                $dashboardData['recentLogs'] = ActivityLog::with('user:id,name,role')->latest()->take(10)->get();
            }
            
            return $dashboardData;
        });

        return view('dashboard', $data);
    }
}
