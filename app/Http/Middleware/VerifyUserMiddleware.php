<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class VerifyUserMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();

        if (!$user || !$user->email_verified || !$user->phone_verified) {
            return response()->json(['message' => 'Your account is not verified'], 403);
        }

        return $next($request);
    }
}
