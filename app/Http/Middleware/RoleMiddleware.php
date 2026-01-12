<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    /**
     * Map Laravel role names to Oracle role names
     */
    private function mapRole(string $role): array
    {
        return match (strtolower($role)) {
            'admin' => ['supervisor', 'admin'],
            'employee' => ['staff', 'employee'],
            'owner' => ['owner'],
            default => [$role],
        };
    }

    public function handle(Request $request, Closure $next, $roles)
    {
        $user = $request->user();
        if (!$user) {
            abort(403);
        }

        // Get user's role from database (Oracle)
        $userRole = strtolower($user->user_role ?? '');

        // Parse allowed roles from route definition
        $routeRoles = array_map('trim', explode('|', $roles));

        // Expand route roles to include Oracle equivalents
        $allowed = [];
        foreach ($routeRoles as $role) {
            $allowed = array_merge($allowed, $this->mapRole($role));
        }

        // Check if user's role is in allowed list
        if (!in_array($userRole, $allowed, true)) {
            abort(403);
        }

        return $next($request);
    }
}
