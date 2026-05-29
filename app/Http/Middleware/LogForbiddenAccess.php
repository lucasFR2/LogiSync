<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Helpers\Logger;

class LogForbiddenAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Se a resposta for 403 Forbidden, registrar o evento
        if ($response->getStatusCode() === 403) {
            $user = auth()->user();
            $username = $user ? $user->name . " (ID: {$user->id})" : 'Usuário desconectado';
            
            Logger::log(
                'forbidden_access',
                "Acesso negado para {$username}. Rota: {$request->method()} {$request->path()}. IP: {$request->ip()}"
            );
        }

        return $response;
    }
}

