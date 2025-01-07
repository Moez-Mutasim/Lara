<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AvailabilityMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $resource = $request->route('resource');

        if (!$resource || !$resource->availability) {
            return response()->json(['message' => 'Resource unavailable'], 400);
        }

        return $next($request);
    }
}
