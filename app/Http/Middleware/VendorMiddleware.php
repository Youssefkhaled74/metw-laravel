<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VendorMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth('vendor')->check()) {
            return redirect()->route('vendor.login');
        }

        // Check if the authenticated user is a vendor
        $user = auth('vendor')->user();
        if ($user instanceof \App\Models\Vendor) {
            return $next($request);
        }

        abort(403, 'Access denied. Vendor access required.');
    }
}
