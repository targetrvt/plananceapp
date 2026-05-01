<?php

namespace App\Providers;

use App\Livewire\CustomChatgptAgent;
use App\Models\MonthlySubscription;
use App\Models\Transaction;
use App\Models\User;
use App\Observers\MonthlySubscriptionObserver;
use App\Observers\TransactionObserver;
use App\Services\Stripe\StripePricingService;
use BezhanSalleh\FilamentLanguageSwitch\LanguageSwitch;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Jeffgreco13\FilamentBreezy\Livewire\PersonalInfo;
use Jeffgreco13\FilamentBreezy\Livewire\TwoFactorAuthentication;
use Jeffgreco13\FilamentBreezy\Livewire\UpdatePassword;
use Livewire\Livewire;
use TomatoPHP\FilamentUsers\Resources\UserResource\Form\UserForm;
use TomatoPHP\FilamentUsers\Resources\UserResource\Table\UserActions;
use TomatoPHP\FilamentUsers\Resources\UserResource\Table\UserTable;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(
            \Filament\Http\Responses\Auth\Contracts\LoginResponse::class,
            \App\Http\Responses\FilamentLoginResponse::class,
        );
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

        $this->app->booted(function (): void {
            Gate::define('viewPulse', function (?User $user = null): bool {
                return $user instanceof User && $user->hasRole('super_admin');
            });
        });

        LanguageSwitch::configureUsing(function (LanguageSwitch $switch) {
            $switch->locales(['lv', 'en']);
            // Avoid locale_get_display_name() — requires ext-intl, often missing on minimal PHP builds (e.g. some Forge images).
            $switch->labels([
                'en' => 'English',
                'lv' => 'Latviešu',
            ]);
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

        UserTable::register([
            IconColumn::make('email_verified')
                ->label(__('admin.users.email_verified'))
                ->getStateUsing(fn (User $record): bool => $record->hasVerifiedEmail())
                ->boolean()
                ->toggleable(),
            TextColumn::make('plan')
                ->label(__('admin.users.plan'))
                ->badge()
                ->sortable()
                ->toggleable(),
            TextColumn::make('stripe_status')
                ->label(__('admin.users.stripe_status'))
                ->sortable()
                ->searchable()
                ->toggleable(),
            TextColumn::make('stripe_current_period_end')
                ->label(__('admin.users.stripe_period_end'))
                ->date()
                ->sortable()
                ->toggleable(),
            TextColumn::make('stripe_customer_id')
                ->label(__('admin.users.stripe_customer_id'))
                ->limit(16)
                ->toggleable(isToggledHiddenByDefault: true)
                ->tooltip(fn (?string $state): ?string => $state),
            TextColumn::make('stripe_subscription_id')
                ->label(__('admin.users.stripe_subscription_id'))
                ->limit(16)
                ->toggleable(isToggledHiddenByDefault: true)
                ->tooltip(fn (?string $state): ?string => $state),
            IconColumn::make('premium_granted_by_admin')
                ->label(__('admin.users.complimentary_badge'))
                ->boolean()
                ->toggleable(),
            IconColumn::make('ai_access')
                ->label(__('admin.users.ai_access'))
                ->boolean()
                ->sortable()
                ->toggleable(),
        ]);

        UserActions::register([
            Action::make('verifyEmail')
                ->label(__('admin.users.verify_email'))
                ->icon('heroicon-o-envelope-open')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading(__('admin.users.verify_email'))
                ->modalDescription(__('admin.users.verify_email_body'))
                ->visible(fn (User $record): bool => ! $record->hasVerifiedEmail())
                ->action(function (User $record): void {
                    $record->forceFill(['email_verified_at' => now()])->save();

                    Notification::make()
                        ->title(__('admin.users.email_verified_notice'))
                        ->success()
                        ->send();
                }),
            Action::make('unverifyEmail')
                ->label(__('admin.users.unverify_email'))
                ->icon('heroicon-o-envelope')
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading(__('admin.users.unverify_email_heading'))
                ->modalDescription(__('admin.users.unverify_email_body'))
                ->visible(fn (User $record): bool => $record->hasVerifiedEmail())
                ->action(function (User $record): void {
                    $record->forceFill(['email_verified_at' => null])->save();

                    Notification::make()
                        ->title(__('admin.users.email_unverified_notice'))
                        ->success()
                        ->send();
                }),
            Action::make('grantComplementaryPremium')
                ->label(__('admin.users.grant_premium'))
                ->icon('heroicon-o-sparkles')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading(__('admin.users.grant_premium_heading'))
                ->modalDescription(__('admin.users.grant_premium_description'))
                ->visible(fn (User $record): bool => ! $record->hasStripeManagedPremiumSubscription()
                    && ! ($record->premium_granted_by_admin && strtolower((string) $record->plan) === 'premium'))
                ->action(function (User $record): void {
                    $record->forceFill([
                        'plan' => 'premium',
                        'premium_granted_by_admin' => true,
                        'stripe_subscription_id' => null,
                        'stripe_status' => 'complimentary',
                        'stripe_current_period_end' => null,
                        'stripe_cancel_at_period_end' => false,
                    ])->save();

                    Notification::make()
                        ->title(__('admin.users.grant_premium'))
                        ->success()
                        ->send();
                }),
            Action::make('revokePremiumAccess')
                ->label(__('admin.users.revoke_premium'))
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading(__('admin.users.revoke_premium_heading'))
                ->modalDescription(__('admin.users.revoke_premium_description'))
                ->visible(fn (User $record): bool => $record->hasPremiumSubscription())
                ->action(function (User $record): void {
                    if ($record->premium_granted_by_admin) {
                        $nextStatus = strtolower((string) ($record->stripe_status ?? '')) === 'complimentary'
                            ? null
                            : $record->stripe_status;

                        $record->forceFill([
                            'premium_granted_by_admin' => false,
                            'plan' => strtolower((string) $record->plan) === 'premium' ? 'personal' : $record->plan,
                            'stripe_status' => $nextStatus,
                        ])->save();

                        Notification::make()
                            ->title(__('admin.users.revoke_premium'))
                            ->success()
                            ->send();

                        return;
                    }

                    $secret = Config::get('services.stripe.secret');
                    if (! is_string($secret) || $secret === '') {
                        StripePricingService::applyLocalPremiumRevoke($record->fresh());

                        Notification::make()
                            ->title(__('admin.users.revoke_premium'))
                            ->warning()
                            ->body(__('admin.users.revoke_premium_no_stripe_key'))
                            ->send();

                        return;
                    }

                    try {
                        app(StripePricingService::class)->cancelSubscriptionImmediately($record->fresh());
                    } catch (\Throwable) {
                        Notification::make()
                            ->title(__('admin.users.revoke_premium'))
                            ->danger()
                            ->body(__('admin.users.revoke_premium_stripe_failed'))
                            ->send();

                        return;
                    }

                    Notification::make()
                        ->title(__('admin.users.revoke_premium'))
                        ->success()
                        ->send();
                }),
        ]);

        UserForm::register([
            Forms\Components\DateTimePicker::make('email_verified_at')
                ->label(__('admin.users.email_verified_at_field'))
                ->helperText(__('admin.users.email_verified_at_hint'))
                ->nullable()
                ->seconds(false)
                ->native(false),
            Forms\Components\Toggle::make('ai_access')
                ->label(__('admin.users.ai_access'))
                ->helperText(__('admin.users.ai_access_hint'))
                ->default(false)
                ->inline(false),
        ]);

        Livewire::component('fi-chatgpt-agent', CustomChatgptAgent::class);

        /*
         * Filament Breezy registers these aliases only when a panel boots (SetUpPanel).
         * Livewire POST /livewire/update runs without Filament, so hydrate must resolve aliases globally.
         */
        Livewire::component('personal_info', PersonalInfo::class);
        Livewire::component('update_password', UpdatePassword::class);
        Livewire::component('two_factor_authentication', TwoFactorAuthentication::class);
    }
}
