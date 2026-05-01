<?php

namespace App\Filament\Pages;

use Filament\Actions\Action;
use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;

class TrafficPulse extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar-square';

    protected static ?string $navigationGroup = null;

    protected static ?int $navigationSort = -5;

    protected static string $view = 'filament.pages.traffic-pulse';

    public static function getNavigationLabel(): string
    {
        return __('admin.navigation.pulse');
    }

    public function getTitle(): string|Htmlable
    {
        return __('admin.navigation.pulse');
    }

    /**
     * Super admins already pass {@see \App\Http\Middleware\RestrictAdminPanel};
     * Pulse data itself is gated by <code>viewPulse</code> on the iframe request.
     */
    public static function canAccess(): bool
    {
        return auth()->user()?->hasRole('super_admin') ?? false;
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('openStandalonePulse')
                ->label(__('admin.pulse.open_standalone'))
                ->icon('heroicon-o-arrow-top-right-on-square')
                ->url(fn (): string => route('pulse'))
                ->openUrlInNewTab()
                ->color('gray'),
        ];
    }
}
