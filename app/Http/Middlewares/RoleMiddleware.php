<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!$this->userHasRole($roles)) {
            \Log::warning('Unauthorized access attempt', [
                'user_id' => auth()->id(),
                'required_roles' => $roles,
                'user_role' => auth()->user()->role ?? 'guest',
                'route' => $request->path(),
            ]);

            if ($request->expectsJson()) {
                return response()->json(['message' => 'Forbidden'], 403);
            }

            return redirect('/unauthorized')->with('error', 'You do not have access to this page.');
        }

        return $next($request);
    }

    protected function userHasRole(array $roles): bool
    {
        $userRole = auth()->user()->role ?? null;

        $hierarchy = ['admin' => 3, 'manager' => 2, 'user' => 1];

        foreach ($roles as $role) {
            if (isset($hierarchy[$userRole]) && $hierarchy[$userRole] >= $hierarchy[$role]) {
                return true;
            }
        }

        return false;
    }
}
