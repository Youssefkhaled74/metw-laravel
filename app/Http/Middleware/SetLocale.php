<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SetLocale
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $fallback = config('app.locale', 'en');

        $sessionLocale = null;
        if ($request->hasSession()) {
            $sessionLocale = $request->session()->get('locale');
        }

        $headerLocale = $request->header('Accept-Language');
        $locale = $sessionLocale ?: $headerLocale ?: $fallback;
        $locale = strtolower((string) $locale);

        if (Str::startsWith($locale, 'ar')) {
            app()->setLocale('ar');
        } elseif (Str::startsWith($locale, 'en')) {
            app()->setLocale('en');
        } else {
            app()->setLocale($fallback);
        }

        if ($request->hasSession()) {
            $request->session()->put('locale', app()->getLocale());
        }

        return $next($request);
    }

}
