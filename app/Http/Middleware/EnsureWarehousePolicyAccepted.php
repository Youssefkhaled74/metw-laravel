<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureWarehousePolicyAccepted
{
    public function handle(Request $request, Closure $next): Response
    {
        $shipmentCompany = auth('shipment')->user();

        if (! auth('shipment')->check() || ! $shipmentCompany instanceof \App\Models\ShipmentCompany) {
            return redirect()->route('shipment.login');
        }

        $acceptance = $shipmentCompany->policyAcceptance()->first();

        if (! $acceptance || ! $acceptance->isCurrent()) {
            if (! $request->session()->has('url.intended')) {
                $request->session()->put('url.intended', $request->fullUrl());
            }

            return redirect()->route('shipment.policy-acceptance.show');
        }

        return $next($request);
    }
}
