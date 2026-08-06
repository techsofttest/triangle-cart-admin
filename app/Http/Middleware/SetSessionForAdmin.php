<?php

namespace App\Http\Middleware;

use Closure;

class SetSessionForAdmin
{
    /**
     * Handle an incoming request.
     *
     * This middleware runs only for the Filament admin panel (registered
     * in the panel provider) and sets the session configuration to the
     * pre-defined admin values from config/session.php. It must run
     * before StartSession so the session system uses these values.
     */
    public function handle($request, Closure $next)
    {
        // Only override the minimal session settings required for admin
        // cookies. Values come from config/session.php which should be
        // populated from environment variables.
        $adminCookie = config('session.admin_cookie');
        $adminDomain = config('session.admin_domain');
        $adminSameSite = config('session.admin_same_site');

        if ($adminCookie) {
            config(['session.cookie' => $adminCookie]);
        }

        // Allow explicit null to clear the domain (single host cookie),
        // otherwise set the configured value (typically ".example.com").
        config(['session.domain' => $adminDomain ?? null]);

        if ($adminSameSite) {
            config(['session.same_site' => $adminSameSite]);
        }

        return $next($request);
    }
}
