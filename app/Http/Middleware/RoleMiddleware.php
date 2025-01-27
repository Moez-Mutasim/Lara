<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
    {

        $user = auth()->user();

        if (!$user) {
            return response()->json(['message' => 'Access denied: unauthenticated'], 403);
        }

        if (!in_array(strtolower($user->role), array_map('strtolower', $roles))) {
            return response()->json(['message' => 'Access denied: insufficient permissions'], 403);
        }

        return $next($request);
    }
}
