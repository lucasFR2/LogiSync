<?php

namespace App\Helpers;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class Logger
{
    public static function log($action, $description, $details = null)
    {
        ActivityLog::create([
            'user_id'     => Auth::id(),
            'action'      => $action,
            'description' => $description,
            'details'     => $details,
            'ip_address'  => Request::ip(),
            'user_agent'  => Request::userAgent(),
        ]);
    }
}
