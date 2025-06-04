<?php

namespace App\Providers;

use App\Models\MonthlySubscription;
use Illuminate\Support\ServiceProvider;
use App\Observers\MonthlySubscriptionObserver;
use BezhanSalleh\FilamentLanguageSwitch\LanguageSwitch;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
{
    view()->composer('*', function ($view) {
        app()->setLocale(session('locale', 'en'));
    });
    MonthlySubscription::observe(MonthlySubscriptionObserver::class);
    LanguageSwitch::configureUsing(function (LanguageSwitch $switch) {
        $switch->locales(['lv', 'en']); // Also accepts a closure
    });

}
}