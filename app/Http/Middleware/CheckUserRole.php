<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckUserRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!$request->user()) {
            return response()->json(['message' => 'Non authentifié.'], 401);
        }
        
        if ($role === 'admin' && !$request->user()->is_admin) {
            return response()->json(['message' => 'Accès non autorisé.'], 403);
        }
        
        return $next($request);
    }
}
