<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ShipmentCompanyMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // if (!auth()->check()) {
        //     return redirect()->route('shipment.login');
        // }

        // // Check if the authenticated user is a shipment company
        // $user = auth()->user();
        // if ($user instanceof \App\Models\ShipmentCompany) {
        //     return $next($request);
        // }

        // abort(403, 'Access denied. Shipment company access required.');
        if (!auth()->guard('shipment')->check()) {
            return redirect()->route('shipment.login'); // make sure this route exists
        }

        return $next($request);

    }
}
