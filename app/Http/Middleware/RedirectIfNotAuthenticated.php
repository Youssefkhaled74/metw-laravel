<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfNotAuthenticated
{
    public function handle(Request $request, Closure $next, $guard = null)
    {
        if (!Auth::guard($guard)->check()) {
            $type = $request->segment(1); // 'admin' or 'vendor'
            return redirect()->route("$type.login");
        }

        return $next($request);
    }
}
