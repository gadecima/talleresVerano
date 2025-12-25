<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!auth()->check()) {
            return redirect('/login');
        }

        $user = auth()->user();

        // Cargar la relación si no está cargada
        if (!$user->relationLoaded('role')) {
            $user->load('role');
        }

        \Log::debug('EnsureUserRole middleware', [
            'user_id' => $user->id,
            'user_email' => $user->email,
            'role_id' => $user->role_id,
            'role' => $user->role ? $user->role->slug : 'null',
            'required_roles' => $roles,
        ]);

        if (!$user->role || !in_array($user->role->slug, $roles)) {
            abort(403, 'No tienes permiso para acceder a este recurso');
        }

        return $next($request);
    }
}
