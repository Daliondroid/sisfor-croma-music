<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, $role): Response
    {
        $user = $request->user();

        if ($user && ! $user->is_active) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            abort(403, 'Akses Ditolak: Akun Anda telah dinonaktifkan.');
        }

        if ($user && $user->role === $role) {
            return $next($request);
        }

        abort(403, 'Akses Ditolak: Anda bukan '.$role);
    }
}
