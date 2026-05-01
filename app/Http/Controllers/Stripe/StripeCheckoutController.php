<?php

namespace App\Http\Controllers\Stripe;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\Stripe\StripePricingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class StripeCheckoutController extends Controller
{
    public function __construct(private readonly StripePricingService $stripePricingService) {}

    public function checkout(string $plan): RedirectResponse
    {
        /** @var User|null $user */
        $user = Auth::user();
        $plan = strtolower($plan);

        if ($plan === 'business') {
            $target = $user ? $user->filamentPricingPath() : '/#pricing';

            return redirect()->to($target)->with('business_plan_coming_soon', true);
        }

        if ($user && $this->shouldBlockDowngradeCheckout($user, $plan)) {
            return redirect($user->filamentPricingPath())->with('checkout_blocked_downgrade', true);
        }

        if ($user && $this->shouldBlockDuplicatePlanCheckout($user, $plan)) {
            return redirect($user->filamentPricingPath())->with('checkout_blocked_active_plan', true);
        }

        try {
            $url = $this->stripePricingService->createCheckoutSessionUrl($user, $plan);
        } catch (RuntimeException $e) {
            $previous = $e->getPrevious();

            Log::warning('Stripe checkout start failed', [
                'plan' => $plan,
                'error' => $e->getMessage(),
                'previous_error' => $previous?->getMessage(),
                'previous_type' => $previous ? get_class($previous) : null,
            ]);

            return redirect('/#pricing')->with('checkout_error', true);
        }

        return redirect()->away($url);
    }

    public function resumeSubscription(): RedirectResponse
    {
        /** @var User|null $user */
        $user = Auth::user();

        if (! $user?->stripe_subscription_id) {
            return redirect($user?->filamentPricingPath() ?? '/app/pricing');
        }

        try {
            $this->stripePricingService->resumeSubscription($user);
        } catch (RuntimeException $e) {
            Log::warning('Stripe subscription resume failed', [
                'user_id' => $user->id,
                'message' => $e->getMessage(),
            ]);

            return redirect($user->filamentPricingPath())->with('subscription_resume_error', true);
        }

        return redirect($user->filamentPricingPath())->with('subscription_resumed', true);
    }

    private function shouldBlockDuplicatePlanCheckout(User $user, string $plan): bool
    {
        if ($user->plan !== $plan) {
            return false;
        }

        if (! is_string($user->stripe_subscription_id) || $user->stripe_subscription_id === '') {
            return false;
        }

        $status = (string) ($user->stripe_status ?? '');

        return in_array($status, ['active', 'trialing'], true);
    }

    private function shouldBlockDowngradeCheckout(User $user, string $plan): bool
    {
        $current = strtolower((string) ($user->plan ?? 'free'));
        if (! in_array($current, ['premium', 'business'], true)) {
            return false;
        }

        $ranks = [
            'personal' => 1,
            'premium' => 2,
            'business' => 3,
        ];
        $currentRank = $ranks[$current] ?? 0;
        $targetRank = $ranks[$plan] ?? 0;

        return $targetRank > 0 && $targetRank < $currentRank;
    }
}
