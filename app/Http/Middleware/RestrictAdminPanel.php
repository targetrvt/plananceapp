<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RestrictAdminPanel
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check() || auth()->user()->email !== 'admin@admin.com') {
            abort(403, 'Unauthorized: Only ADMIN can access this panel.');
        }

        return $next($request);
    }
}