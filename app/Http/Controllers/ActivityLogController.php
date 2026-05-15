<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $query = ActivityLog::with('user')->latest();

        if ($request->filled('user')) {
            $query->where('user_id', $request->user);
        }

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        $logs = $query->paginate(20)->withQueryString();
        
        // Select only ID and Name for the user filter dropdown
        $users = User::select('id', 'name')->orderBy('name')->get();
        
        // Optimize fetching unique actions
        $actions = ActivityLog::distinct()->pluck('action');

        return view('logs.index', compact('logs', 'users', 'actions'));
    }
}
