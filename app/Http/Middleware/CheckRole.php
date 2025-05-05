<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        // Debug information
        Log::info('CheckRole Middleware Started', [
            'user' => $request->user() ? [
                'id' => $request->user()->id,
                'email' => $request->user()->email,
                'role' => $request->user()->role,
            ] : 'No user',
            'required_role' => $role,
            'url' => $request->url(),
            'method' => $request->method(),
            'route' => $request->route()->getName(),
            'middleware' => $request->route()->middleware(),
        ]);

        if (!$request->user()) {
            Log::warning('No authenticated user');
            abort(403, 'Unauthorized action.');
        }

        $hasRole = $request->user()->hasRole($role);
        Log::info('Role check result', [
            'user_role' => $request->user()->role,
            'required_role' => $role,
            'has_role' => $hasRole
        ]);

        if (!$hasRole) {
            Log::warning('User does not have required role', [
                'user_role' => $request->user()->role,
                'required_role' => $role
            ]);
            abort(403, 'Unauthorized action.');
        }

        Log::info('CheckRole Middleware Passed', [
            'user_role' => $request->user()->role,
            'required_role' => $role
        ]);

        return $next($request);
    }
}
