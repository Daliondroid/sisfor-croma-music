<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, $role): Response
    {
        // Mengecek nilai langsung dari kolom 'role' di tabel users
        if ($request->user() && $request->user()->role === $role) {
            return $next($request);
        }
        
        abort(403, 'Akses Ditolak: Anda bukan ' . $role);
    }
}