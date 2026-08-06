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

        if (Str::contains($host, 'admin')) {
            $sessionDomain = env('ADMIN_SESSION_DOMAIN');
            if ($sessionDomain === 'null') {
                $sessionDomain = null;
            } elseif ($sessionDomain) {
                // If current host environment does not match configured domain (e.g. test vs com.au), default to null
                $configBase = Str::after($sessionDomain, 'admin.');
                if (!Str::contains($host, $configBase)) {
                    $sessionDomain = null;
                }
            }

            config([
                'session.cookie' => env('ADMIN_SESSION_COOKIE', 'admin_session'),
                'session.domain' => $sessionDomain,
                'session.same_site' => env('ADMIN_SESSION_SAME_SITE', config('session.same_site')),
            ]);
        }

        return $next($request);
    }
}
