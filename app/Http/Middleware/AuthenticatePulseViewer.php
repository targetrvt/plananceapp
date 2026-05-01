<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Guests hitting the Pulse dashboard are sent to the Filament admin login (same web session).
 */
class AuthenticatePulseViewer
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! auth()->check()) {
            return redirect()->guest(route('filament.admin.auth.login'));
        }

        return $next($request);
    }
}
