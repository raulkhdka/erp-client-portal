<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Allow all roles when running in console (e.g. php artisan route:list)
        if (app()->runningInConsole()) {
            return $next($request);
        }

        Log::info('RoleMiddleware called - START', ['requested_roles' => $roles]);
        Log::info('RoleMiddleware called', [
            'requested_roles' => $roles,
            'user_authenticated' => Auth::check(),
            'user_role' => Auth::check() ? Auth::user()->role : 'not-authenticated'
        ]);

        if (!Auth::check()) {
            Log::warning('User not authenticated, redirecting to login');
            return redirect()->route('login');
        }

        $userRole = Auth::user()->role;
        Log::info('Checking user role', ['user_role' => $userRole, 'allowed_roles' => $roles]);

        if (!in_array($userRole, $roles)) {
            Log::warning('User role not authorized', [
                'user_role' => $userRole,
                'allowed_roles' => $roles
            ]);
            abort(403, 'Unauthorized access.');
        }

        Log::info('User authorized, proceeding with request');
        return $next($request);
    }
}
