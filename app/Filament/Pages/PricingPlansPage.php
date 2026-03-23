<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class PricingPlansPage extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';
    protected static ?string $navigationLabel = null;
    protected static ?string $navigationGroup = 'Overview';
    protected static ?int $navigationSort = 2;

    protected static ?string $slug = 'pricing';

    protected static string $view = 'filament.pages.pricing-plans';

    public string $selectedPlan = 'personal';

    public function mount(): void
    {
        $plan = strtolower((string) request()->query('plan', 'personal'));
        $allowed = ['personal', 'premium', 'business'];
        $this->selectedPlan = in_array($plan, $allowed, true) ? $plan : 'personal';
    }

    public function getCurrentPlanLabel(): string
    {
        $plan = auth()->user()?->plan ?? 'free';

        return match ($plan) {
            'personal' => (string) __('messages.pricing.personal.title'),
            'premium' => (string) __('messages.pricing.premium.title'),
            'business' => (string) __('messages.pricing.business.title'),
            default => 'Free',
        };
    }

    public function getTitle(): string
    {
        return 'Pricing';
    }
}

