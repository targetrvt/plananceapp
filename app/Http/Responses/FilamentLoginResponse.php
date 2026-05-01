<?php

namespace App\Http\Responses;

use App\Filament\Pages\Dashboard;
use App\Filament\PremiumPanelEntryTransition;
use App\Models\User;
use Filament\Facades\Filament;
use Filament\Http\Responses\Auth\Contracts\LoginResponse as LoginResponseContract;
use Illuminate\Http\RedirectResponse;
use Livewire\Features\SupportRedirects\Redirector;

class FilamentLoginResponse implements LoginResponseContract
{
    public function toResponse($request): RedirectResponse|Redirector
    {
        $user = Filament::auth()->user();
        $panel = Filament::getCurrentPanel();

        if (
            $user instanceof User &&
            $user->hasPremiumSubscription() &&
            $panel?->getId() === 'app'
        ) {
            return redirect()
                ->intended(Dashboard::getUrl(panel: 'premium'))
                ->with(PremiumPanelEntryTransition::SESSION_KEY, true);
        }

        return redirect()->intended(Filament::getUrl());
    }
}
