<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class Verificar2FA
{
    public function handle(Request $request, Closure $next): Response
    {
        // Verifica que el usuario esté autenticado
        if (!Auth::check()) {
            return redirect('/login');
        }

        // Obtiene el usuario autenticado
        $user = Auth::user();

        // Si el usuario tiene 2FA habilitado pero no lo ha verificado
        if ($user->google2fa_enabled && !session('2fa_verified')) {
            // Redirige a verificación 2FA
            return redirect()->route('2fa.verify.show');
        }

        // Si el usuario no tiene 2FA habilitado o ya lo verificó, continua
        return $next($request);
    }
}