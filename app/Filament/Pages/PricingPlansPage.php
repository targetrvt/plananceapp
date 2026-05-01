<?php

namespace App\Filament\Pages;

use App\Filament\PremiumPanelEntryTransition;
use App\Services\Stripe\StripePricingService;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class PricingPlansPage extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';

    protected static ?string $navigationLabel = null;

    protected static ?string $navigationGroup = null;

    protected static ?int $navigationSort = 999;

    protected static ?string $slug = 'pricing';

    protected static string $view = 'filament.pages.pricing-plans';

    public string $selectedPlan = 'personal';

    public function mount(): void
    {
        $plan = strtolower((string) request()->query('plan', 'personal'));
        $allowed = ['personal', 'premium'];
        $this->selectedPlan = in_array($plan, $allowed, true) ? $plan : 'personal';

        $checkoutSuccess = request()->boolean('checkout_success');
        $checkoutCancelled = request()->boolean('checkout_cancelled');

        if ($checkoutSuccess && request()->filled('session_id')) {
            $sessionId = (string) request()->query('session_id');

            try {
                /** @var StripePricingService $stripePricingService */
                $stripePricingService = app(StripePricingService::class);
                $stripePricingService->syncUserFromCheckoutSessionId($sessionId, auth()->user());
            } catch (\Throwable $e) {
                Log::warning('Failed to sync plan from checkout success redirect.', [
                    'session_id' => $sessionId,
                    'message' => $e->getMessage(),
                ]);
            }

            auth()->user()?->refresh();
        }

        $user = auth()->user();
        if ($user && is_string($user->stripe_subscription_id) && $user->stripe_subscription_id !== '') {
            try {
                /** @var StripePricingService $stripePricingService */
                $stripePricingService = app(StripePricingService::class);
                $stripePricingService->refreshSubscriptionFromStripe($user);
                $user->refresh();
            } catch (\Throwable $e) {
                Log::warning('Pricing page: failed to refresh Stripe subscription.', [
                    'user_id' => $user->id,
                    'message' => $e->getMessage(),
                ]);
            }
        }

        if (session()->pull('subscription_resumed')) {
            Notification::make()
                ->success()
                ->title((string) __('messages.pricing_page.subscription_resumed'))
                ->send();
        }

        if (session()->pull('subscription_resume_error')) {
            Notification::make()
                ->danger()
                ->title((string) __('messages.pricing_page.subscription_resume_failed'))
                ->send();
        }

        if (session()->pull('checkout_blocked_active_plan')) {
            Notification::make()
                ->warning()
                ->title((string) __('messages.pricing_page.checkout_blocked_same_plan'))
                ->send();
        }

        if (session()->pull('checkout_blocked_downgrade')) {
            Notification::make()
                ->warning()
                ->title((string) __('messages.pricing_page.checkout_blocked_downgrade'))
                ->send();
        }

        if (session()->pull('premium_required_redirect')) {
            Notification::make()
                ->warning()
                ->title((string) __('messages.pricing_page.premium_panel_locked_title'))
                ->body((string) __('messages.pricing_page.premium_panel_locked_body'))
                ->send();
        }

        if (session()->pull('business_plan_coming_soon')) {
            Notification::make()
                ->warning()
                ->title((string) __('messages.pricing_page.business_plan_coming_soon_title'))
                ->body((string) __('messages.pricing_page.business_plan_coming_soon_body'))
                ->send();
        }

        if ($checkoutSuccess) {
            Notification::make()
                ->success()
                ->title((string) __('messages.pricing_page.payment_started'))
                ->send();
        } elseif ($checkoutCancelled) {
            Notification::make()
                ->warning()
                ->title((string) __('messages.pricing_page.checkout_cancelled'))
                ->send();
        }

        if ($checkoutSuccess || $checkoutCancelled) {
            if (
                $checkoutSuccess
                && Filament::getCurrentPanel()?->getId() === 'app'
                && auth()->user()?->hasPremiumSubscription()
            ) {
                session()->flash(PremiumPanelEntryTransition::SESSION_KEY, true);
                $this->redirect(Dashboard::getUrl(panel: 'premium'));
            } else {
                $this->redirect(static::getUrl());
            }
        }
    }

    public function getCurrentPlanLabel(): string
    {
        $plan = strtolower((string) (auth()->user()?->plan ?? 'free'));

        return match ($plan) {
            'personal' => (string) __('messages.landing.pricing.personal.title'),
            'premium' => (string) __('messages.landing.pricing.premium.title'),
            'business' => (string) __('messages.landing.pricing.business.title'),
            default => (string) __('messages.pricing_page.plan_free'),
        };
    }

    /**
     * Ordering for plan comparison (personal < premium < business).
     */
    public function planTierRank(string $plan): int
    {
        return match (strtolower($plan)) {
            'business' => 3,
            'premium' => 2,
            'personal' => 1,
            default => 0,
        };
    }

    /**
     * Hide Stripe checkout on tiers below the user's current paid plan (premium/business).
     */
    public function shouldHideCheckoutForLowerPlan(string $planKey): bool
    {
        $user = auth()->user();
        if (! $user) {
            return false;
        }

        $current = strtolower((string) ($user->plan ?? 'free'));
        if (! in_array($current, ['premium', 'business'], true)) {
            return false;
        }

        $currentRank = $this->planTierRank($current);
        $targetRank = $this->planTierRank($planKey);

        return $targetRank > 0 && $targetRank < $currentRank;
    }

    /**
     * Stripe subscription context for the pricing cards (dates, cancel-at-period-end).
     *
     * @return array{
     *     show: bool,
     *     period_end_formatted: string|null,
     *     cancel_at_period_end: bool,
     *     status: string|null,
     *     can_resume: bool,
     *     can_cancel: bool,
     * }
     */
    public function getSubscriptionBillingInsights(): array
    {
        $user = auth()->user();

        $empty = [
            'show' => false,
            'period_end_formatted' => null,
            'cancel_at_period_end' => false,
            'status' => null,
            'can_resume' => false,
            'can_cancel' => false,
        ];

        if (! $user) {
            return $empty;
        }

        $hasSubscription = is_string($user->stripe_subscription_id) && $user->stripe_subscription_id !== '';
        $plan = (string) ($user->plan ?? 'free');

        if (! $hasSubscription || in_array($plan, ['', 'free'], true)) {
            return $empty;
        }

        $periodEnd = $user->stripe_current_period_end;
        $formatted = null;
        if ($periodEnd) {
            $formatted = $periodEnd->copy()->locale($user->preferredLocale())->isoFormat('LL');
        }

        $status = is_string($user->stripe_status) ? $user->stripe_status : null;
        $cancelAtEnd = (bool) $user->stripe_cancel_at_period_end;
        $activeLike = in_array((string) $status, ['active', 'trialing'], true);
        $paidPlan = in_array($plan, ['premium', 'business'], true);

        return [
            'show' => true,
            'period_end_formatted' => $formatted,
            'cancel_at_period_end' => $cancelAtEnd,
            'status' => $status,
            'can_resume' => $cancelAtEnd && $activeLike && $hasSubscription,
            'can_cancel' => $paidPlan && $activeLike && ! $cancelAtEnd && $hasSubscription,
        ];
    }

    public function cancelStripeSubscriptionAction(): Action
    {
        return Action::make('cancelStripeSubscription')
            ->label((string) __('messages.pricing_page.cancel_subscription'))
            ->icon('heroicon-o-trash')
            ->color('danger')
            ->outlined()
            ->visible(fn (): bool => $this->getSubscriptionBillingInsights()['can_cancel'])
            ->requiresConfirmation()
            ->modalHeading((string) __('messages.pricing_page.cancel_confirm_heading'))
            ->modalDescription(function (): string {
                $insights = $this->getSubscriptionBillingInsights();
                $date = $insights['period_end_formatted'] ?? (string) __('messages.pricing_page.date_pending');

                return (string) __('messages.pricing_page.cancel_subscription_confirm', ['date' => $date]);
            })
            ->modalSubmitActionLabel((string) __('messages.pricing_page.cancel_confirm_submit'))
            ->modalCancelActionLabel((string) __('messages.pricing_page.cancel_confirm_back'))
            ->action(function (): void {
                $user = auth()->user();
                if (! $user?->stripe_subscription_id) {
                    return;
                }

                try {
                    app(StripePricingService::class)->cancelSubscriptionAtPeriodEnd($user);
                } catch (RuntimeException $e) {
                    Log::warning('Stripe subscription cancel-at-period-end failed', [
                        'user_id' => $user->id,
                        'message' => $e->getMessage(),
                    ]);

                    Notification::make()
                        ->danger()
                        ->title((string) __('messages.pricing_page.subscription_cancel_failed'))
                        ->send();

                    return;
                }

                $user->refresh();

                Notification::make()
                    ->success()
                    ->title((string) __('messages.pricing_page.subscription_cancel_scheduled'))
                    ->send();
            });
    }

    public function getTitle(): string
    {
        return (string) __('messages.navigation.pricing');
    }

    public static function getNavigationLabel(): string
    {
        return (string) __('messages.navigation.pricing');
    }

    public static function getNavigationGroup(): ?string
    {
        return (string) __('filament.navigation.groups.Pricing');
    }
}
