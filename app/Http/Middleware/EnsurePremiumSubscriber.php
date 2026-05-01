<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePremiumSubscriber
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user !== null && $user->hasPremiumSubscription()) {
            return $next($request);
        }

        return redirect('/app/pricing')->with('premium_required_redirect', true);
    }
}
