<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SetLocale
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $fallback = config('app.locale', 'en');

        // Use session locale if available, else from header
        $sessionLocale = null;
        if ($request->hasSession()) {
            $sessionLocale = $request->session()->get('locale');
        }

        $headerLocale = $request->header('Accept-Language');
        $locale = $sessionLocale ?: ($headerLocale ?: $fallback);

        if (in_array($locale, ['en', 'ar'])) {
            app()->setLocale($locale);
        } else {
            app()->setLocale($fallback);
        }

        return $next($request);
    }

}
