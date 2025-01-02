<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SuppressDeprecationWarnings
{
    public function handle(Request $request, Closure $next)
    {
        // Suprimir advertencias de deprecación solo para peticiones API
        if ($request->is('api/*')) {
            error_reporting(E_ALL & ~E_DEPRECATED & ~E_USER_DEPRECATED);
        }

        return $next($request);
    }
}
