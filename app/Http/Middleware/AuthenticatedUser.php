<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AuthenticatedUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            return $next($request);
        }

        // Si es una solicitud de API (JSON), retornar respuesta JSON
        if ($request->expectsJson()) {
            return response()->json([
                'error' => true,
                'message' => 'Acceso no autorizado. Debes iniciar sesión.',
            ], 401); // Código de estado "Unauthorized"
        }

        // Si no es una solicitud de API, redirigir al formulario de inicio de sesión
        return redirect()->route('login');
    }
}
