<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AvailabilityMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!$request->route('resource')->availability) {
            return response()->json(['message' => 'Resource unavailable'], 400);
        }

        return $next($request);
    }
}
