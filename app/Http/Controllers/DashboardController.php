<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ActivityLog;
use App\Models\Product;
use App\Models\Invoice;
use App\Models\WarehouseLocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Initialize with default values
        $data = [
            'user' => $user,
            'totalStock' => 0,
            'pendingOrders' => 0,
            'lowStockCount' => 0,
            'occupancyRate' => 0,
            'totalLocations' => 0,
            'occupiedLocations' => 0,
            'categoriesStats' => collect(),
            'entriesData' => array_fill(0, 7, 0),
            'exitsData' => array_fill(0, 7, 0),
            'chartLabels' => [],
            'recentLogs' => collect()
        ];
        
        // Populate stats for appropriate roles
        if (!$user->hasRole('rh')) {
            // Stats
            $data['totalStock'] = Product::sum('quantity');
            $data['pendingOrders'] = Invoice::where('status', 'rascunho')->count();
            $data['lowStockCount'] = Product::baixoEstoque()->count();

            // Warehouse Occupancy
            $totalLocations = WarehouseLocation::count();
            $occupiedLocations = WarehouseLocation::where('is_occupied', true)->count();
            $data['occupancyRate'] = $totalLocations > 0 ? round(($occupiedLocations / $totalLocations) * 100, 1) : 0;
            $data['totalLocations'] = $totalLocations;
            $data['occupiedLocations'] = $occupiedLocations;

            // Category Distribution for Chart
            $data['categoriesStats'] = Product::select('category', DB::raw('count(*) as count'))
                ->whereNotNull('category')
                ->groupBy('category')
                ->get();

            // Stock Flow (Last 7 days)
            $data['entriesData'] = [];
            $data['exitsData'] = [];
            $data['chartLabels'] = [];
            
            for ($i = 6; $i >= 0; $i--) {
                $date = now()->subDays($i)->format('Y-m-d');
                $label = now()->subDays($i)->translatedFormat('D');
                
                $data['chartLabels'][] = $label;
                
                // Count from Inventory movements
                $data['entriesData'][] = \App\Models\Inventory::where('type', 'entrada')
                    ->whereDate('created_at', $date)
                    ->count();
                    
                $data['exitsData'][] = \App\Models\Inventory::where('type', 'saida')
                    ->whereDate('created_at', $date)
                    ->count();
            }
        }

        // Recent Logs
        $data['recentLogs'] = ActivityLog::with('user:id,name,role')
            ->latest()
            ->take(10)
            ->get();

        return view('dashboard', $data);
    }
}
