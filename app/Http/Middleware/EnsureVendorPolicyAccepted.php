<?php

namespace App\Http\Middleware;

use App\Models\PolicyAcceptance;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureVendorPolicyAccepted
{
    public function handle(Request $request, Closure $next): Response
    {
        $vendor = auth('vendor')->user();

        if (! auth('vendor')->check() || ! $vendor instanceof \App\Models\Vendor) {
            return redirect()->route('vendor.login');
        }

        $acceptance = $vendor->policyAcceptance()->first();

        if (! $acceptance || ! $acceptance->isCurrent()) {
            if (! $request->session()->has('url.intended')) {
                $request->session()->put('url.intended', $request->fullUrl());
            }

            return redirect()->route('vendor.policy-acceptance.show');
        }

        return $next($request);
    }
}
