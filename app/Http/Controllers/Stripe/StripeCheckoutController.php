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
    public function __construct(private readonly StripePricingService $stripePricingService)
    {
    }

    public function checkout(string $plan): RedirectResponse
    {
        /** @var User|null $user */
        $user = Auth::user();

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
}

