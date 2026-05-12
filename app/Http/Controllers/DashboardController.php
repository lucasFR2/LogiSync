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

        if ($user->role === 'Administrador' || $user->role === 'Recursos Humanos (RH)') {
            $data['employees'] = User::orderBy('name')->get();
        }

        if ($user->role === 'Administrador') {
            $data['recentLogs'] = ActivityLog::with('user')->latest()->take(5)->get();
        }

        return view('dashboard', $data);
    }
}
