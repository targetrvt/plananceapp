<?php

namespace App\Providers;

use App\Models\MonthlySubscription;
use App\Models\Transaction;
use Illuminate\Support\ServiceProvider;
use App\Observers\MonthlySubscriptionObserver;
use App\Observers\TransactionObserver;
use Illuminate\Auth\Notifications\VerifyEmail;
use BezhanSalleh\FilamentLanguageSwitch\LanguageSwitch;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\URL;

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
        $locale = session('locale');

        if (! $locale && auth()->check()) {
            $locale = auth()->user()->locale;
            session(['locale' => $locale]);
        }

        app()->setLocale($locale ?: 'en');
    });
    MonthlySubscription::observe(MonthlySubscriptionObserver::class);
    Transaction::observe(TransactionObserver::class);
    LanguageSwitch::configureUsing(function (LanguageSwitch $switch) {
        $switch->locales(['lv', 'en']); // Also accepts a closure
    });

    VerifyEmail::createUrlUsing(function ($notifiable) {
        return URL::temporarySignedRoute(
            'filament.app.auth.email-verification.verify',
            now()->addMinutes(60),
            [
                'id' => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ]
        );
    });

    VerifyEmail::toMailUsing(function (object $notifiable, string $url) {
        return (new MailMessage)
            ->subject(__('verification.subject'))
            ->greeting(__('verification.greeting'))
            ->line(__('verification.line'))
            ->action(__('verification.action'), $url)
            ->line(__('verification.outro'))
            ->salutation(__('verification.salutation'));
    });

}
}