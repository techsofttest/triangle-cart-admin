<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Str;

class SetSessionForAdmin
{
    /**
     * Handle an incoming request.
     */
    public function handle($request, Closure $next)
    {
        $host = $request->getHost();
        \Illuminate\Support\Facades\Log::info('SetSessionForAdmin running', [
            'host' => $host,
            'path' => $request->path(),
            'is_admin_subdomain' => Str::contains($host, 'admin'),
            'is_api' => $request->is('api/*')
        ]);

        if (Str::contains($host, 'admin') && !$request->is('api/*')) {
            $sessionDomain = config('session.admin_domain');
            if ($sessionDomain === 'null' || !$sessionDomain) {
                $sessionDomain = $request->getHost();
            } else {
                // If current host environment does not match configured domain (e.g. test vs com.au), fallback to request host
                $configBase = Str::after($sessionDomain, 'admin.');
                if (!Str::contains($host, $configBase)) {
                    $sessionDomain = $request->getHost();
                }
            }

            config([
                'session.cookie' => config('session.admin_cookie', 'admin_session'),
                'session.domain' => $sessionDomain,
                'session.same_site' => config('session.admin_same_site', 'lax'),
            ]);
        }

        return $next($request);
    }
}
