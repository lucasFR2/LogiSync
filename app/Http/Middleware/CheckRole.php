<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRole
{
    public function handle(Request $request, Closure $next, string $role): mixed
    {
        $user = $request->user();
        if (!$user) {
            abort(403);
        }

        $roles = array_values(array_filter(array_map('trim', explode(',', $role))));
        $allowed = false;
        foreach ($roles as $requiredRole) {
            if ($user->hasRole($requiredRole)) {
                $allowed = true;
                break;
            }
        }

        if (!$allowed) {
            abort(403);
        }

        return $next($request);
    }
}
