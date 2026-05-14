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
        
        // General user info
        $data = ['user' => $user];

        // Logistics Stats (for Logistics and Admin)
        if ($user->role !== 'Recursos Humanos (RH)') {
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
                $data['entriesData'][] = Product::whereDate('created_at', $date)->count();
                $data['exitsData'][] = Invoice::whereDate('created_at', $date)->count();
            }
        }

        // Recent Logs (for everyone, but filtered if needed)
        // Admin sees everything, others might see their own or relevant ones
        $data['recentLogs'] = ActivityLog::with('user:id,name,role')
            ->latest()
            ->take(10)
            ->get();

        return view('dashboard', $data);
    }
}
