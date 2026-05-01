<?php

namespace App\Http\Middleware;

use App\Filament\Pages\Dashboard;
use App\Filament\PremiumPanelEntryTransition;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectPremiumUsersFromFreePanel
{
    /** @var list<string> */
    private static array $allowedPathPrefixes = [
        'app/pricing',
        'app/email-verification',
        'app/password-reset',
        'app/logout',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if ($user === null || ! $user->hasPremiumSubscription()) {
            return $next($request);
        }

        $path = $request->path();

        foreach (self::$allowedPathPrefixes as $prefix) {
            if ($path === $prefix || str_starts_with($path, $prefix.'/')) {
                return $next($request);
            }
        }

        return redirect()->to(Dashboard::getUrl(panel: 'premium'))
            ->with(PremiumPanelEntryTransition::SESSION_KEY, true);
    }
}
