<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                // Redirigir según el rol del usuario autenticado
                $user = Auth::user();
                if ($user->role === 'admin') {
                    return redirect()->route('filament.admin.pages.dashboard');
                }
                return redirect()->route('dashboard');
            }
        }

        return $next($request);
    }
}