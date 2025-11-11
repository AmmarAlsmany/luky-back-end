<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

class CheckPermission
{
    /**
     * Handle an incoming request.
     * 
     * This middleware checks if the authenticated user has the required permission.
     * It supports multiple permissions separated by '|' (OR logic).
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $permission
     */
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        if (!auth()->check()) {
            return $this->unauthorized($request, 'Authentication required');
        }

        $user = auth()->user();

        // Check if user is super_admin (has all permissions)
        if ($user->hasRole('super_admin')) {
            return $next($request);
        }

        // Support multiple permissions with OR logic (permission1|permission2)
        $permissions = explode('|', $permission);
        
        foreach ($permissions as $perm) {
            if ($user->can(trim($perm))) {
                return $next($request);
            }
        }

        // Log unauthorized access attempt
        Log::warning('Unauthorized access attempt', [
            'user_id' => $user->id,
            'user_email' => $user->email,
            'required_permission' => $permission,
            'route' => $request->path(),
            'method' => $request->method(),
        ]);

        return $this->unauthorized($request, 'You do not have permission to perform this action');
    }

    /**
     * Return unauthorized response
     */
    private function unauthorized(Request $request, string $message): Response
    {
        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'success' => false,
                'message' => $message,
                'error_code' => 'permission_denied'
            ], 403);
        }

        return redirect()->back()
            ->with('error', $message);
    }
}
