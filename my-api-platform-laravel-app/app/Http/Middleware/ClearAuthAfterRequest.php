<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ClearAuthAfterRequest
{
    public function handle(Request $request, Closure $next)
    {
        return $next($request);
    }

    public function terminate($request, $response)
    {
        if (app()->runningUnitTests()) {
            try {
                auth()->logout();
            } catch (\Throwable $e) {
                // ignore
            }
        }
    }
}
