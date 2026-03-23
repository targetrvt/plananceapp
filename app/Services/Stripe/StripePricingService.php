<?php

namespace App\Services\Stripe;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;
use RuntimeException;
use Stripe\Exception\ApiErrorException;
use Stripe\StripeClient;

class StripePricingService
{
    public const PLANS = ['personal', 'premium', 'business'];

    private StripeClient $stripe;

    public function __construct()
    {
        $secretKey = Config::get('services.stripe.secret');

        if (!is_string($secretKey) || $secretKey === '') {
            throw new RuntimeException('Stripe is not configured.');
        }

        $this->stripe = new StripeClient($secretKey);
    }

    public function createCheckoutSessionUrl(?User $user, string $plan): string
    {
        $plan = strtolower($plan);
        $planConfig = $this->getPlanConfig($plan);
        $isSubscriptionPlan = in_array($plan, ['premium', 'business'], true);

        $baseUrl = rtrim((string) config('app.url', env('APP_URL', 'http://localhost')), '/');

        $successUrl = $baseUrl . '/app/pricing?checkout_success=1&session_id={CHECKOUT_SESSION_ID}';
        $cancelUrl = $baseUrl . '/app/pricing?checkout_cancelled=1';

        $metadata = [
            'plan' => $plan,
        ];

        if ($user) {
            $metadata['user_id'] = (string) $user->id;
            $metadata['user_email'] = (string) $user->email;
        }

        $params = [
            'mode' => $isSubscriptionPlan ? 'subscription' : 'payment',
            'line_items' => [
                [
                    'price' => $planConfig['price_id'],
                    'quantity' => 1,
                ],
            ],
            'success_url' => $successUrl,
            'cancel_url' => $cancelUrl,
            // Ensure we can map webhook events back to your user record.
            'metadata' => $metadata,
        ];

        if ($isSubscriptionPlan) {
            $params['subscription_data'] = [
                'metadata' => $metadata,
            ];
        }

        if ($user) {
            // If we already have a Stripe customer ID, reuse it.
            if ($user->stripe_customer_id) {
                $params['customer'] = $user->stripe_customer_id;
            } else {
                // Stripe will create a customer for us using this email.
                $params['customer_email'] = $user->email;
            }
        }

        try {
            $session = $this->stripe->checkout->sessions->create($params);
        } catch (ApiErrorException $e) {
            // Do not leak Stripe internals to end users.
            throw new RuntimeException('Unable to start checkout for this plan.', 0, $e);
        }

        if (!is_string($session->url)) {
            throw new RuntimeException('Stripe checkout session URL missing.');
        }

        return $session->url;
    }

    public function handleCheckoutSessionCompleted(object $session): void
    {
        $metadata = is_array($session->metadata) ? $session->metadata : [];

        $userId = Arr::get($metadata, 'user_id');
        $plan = (string) Arr::get($metadata, 'plan', '');

        if ($plan === '') {
            return;
        }

        if (!in_array($plan, self::PLANS, true)) {
            return;
        }

        $user = null;

        if (is_string($userId) && $userId !== '') {
            $user = User::find($userId);
        }

        if (!$user) {
            $customerDetails = $session->customer_details ?? null;
            $customerEmail = is_object($customerDetails) && isset($customerDetails->email) ? (string) $customerDetails->email : null;

            if (!$customerEmail) {
                return;
            }

            $user = User::where('email', $customerEmail)->first();
            if (!$user) {
                // Demo behavior: create a user if they paid as a guest.
                $user = User::create([
                    'name' => strtok($customerEmail, '@') ?: 'Stripe User',
                    'email' => $customerEmail,
                    'password' => Hash::make(bin2hex(random_bytes(16))),
                    'plan' => $plan,
                    'email_verified_at' => now(),
                ]);
            }
        }

        // subscription is present for mode=subscription.
        if (!empty($session->subscription) && is_string($session->subscription)) {
            $subscription = $this->stripe->subscriptions->retrieve($session->subscription, []);
            $this->syncUserFromSubscription($user, $subscription, $plan, false);
            return;
        }

        // Fallback: store the session plan, but leave subscription fields empty.
        $user->update([
            'plan' => $plan,
            'stripe_customer_id' => is_string($session->customer) ? $session->customer : null,
            'stripe_subscription_id' => null,
            'stripe_status' => 'active',
            'stripe_current_period_end' => null,
        ]);
    }

    public function handleSubscriptionUpdated(object $subscription): void
    {
        $metadata = is_array($subscription->metadata) ? $subscription->metadata : [];
        $userId = Arr::get($metadata, 'user_id');
        $plan = (string) Arr::get($metadata, 'plan', '');
        $userEmail = (string) Arr::get($metadata, 'user_email', '');

        $user = null;

        if (is_string($userId) && $userId !== '') {
            $user = User::find($userId);
        }

        if (!$user && $userEmail !== '') {
            $user = User::where('email', $userEmail)->first();
        }

        if (!$user) {
            $customerId = is_string($subscription->customer ?? null) ? (string) $subscription->customer : null;
            if ($customerId) {
                $customer = $this->stripe->customers->retrieve($customerId, []);
                $emailFromCustomer = is_string($customer->email ?? null) ? (string) $customer->email : null;
                if ($emailFromCustomer) {
                    $planCandidate = in_array($plan, self::PLANS, true) ? $plan : 'personal';
                    $user = $this->findOrCreateUserByEmail($emailFromCustomer, $planCandidate);
                }
            }
        }

        if (!$user) {
            return;
        }

        if (!in_array($plan, self::PLANS, true)) {
            $plan = (string) ($user->plan ?: 'free');
            if (!in_array($plan, self::PLANS, true)) {
                $plan = 'personal';
            }
        }

        $this->syncUserFromSubscription($user, $subscription, $plan, false);
    }

    public function handleSubscriptionDeleted(object $subscription): void
    {
        $metadata = is_array($subscription->metadata) ? $subscription->metadata : [];
        $userId = Arr::get($metadata, 'user_id');
        $userEmail = (string) Arr::get($metadata, 'user_email', '');

        $user = null;
        if (is_string($userId) && $userId !== '') {
            $user = User::find($userId);
        }
        if (!$user && $userEmail !== '') {
            $user = User::where('email', $userEmail)->first();
        }

        if (!$user) {
            $customerId = is_string($subscription->customer ?? null) ? (string) $subscription->customer : null;
            if ($customerId) {
                $customer = $this->stripe->customers->retrieve($customerId, []);
                $emailFromCustomer = is_string($customer->email ?? null) ? (string) $customer->email : null;
                if ($emailFromCustomer) {
                    $user = User::where('email', $emailFromCustomer)->first();
                }
            }
        }

        if (!$user) {
            return;
        }

        $this->syncUserFromSubscription($user, $subscription, 'free', true);
    }

    private function syncUserFromSubscription(User $user, object $subscription, string $plan, bool $deleted): void
    {
        $periodEnd = null;
        if (!empty($subscription->current_period_end) && is_numeric($subscription->current_period_end)) {
            $periodEnd = Carbon::createFromTimestamp((int) $subscription->current_period_end)->toDateString();
        }

        $status = is_string($subscription->status) ? $subscription->status : null;
        $customerId = is_string($subscription->customer) ? $subscription->customer : null;

        $user->update([
            'plan' => $deleted ? 'free' : $plan,
            'stripe_customer_id' => $customerId ?: $user->stripe_customer_id,
            'stripe_subscription_id' => is_string($subscription->id) ? $subscription->id : $user->stripe_subscription_id,
            'stripe_status' => $status,
            'stripe_current_period_end' => $deleted ? null : $periodEnd,
        ]);
    }

    private function findOrCreateUserByEmail(string $email, string $plan): User
    {
        $email = strtolower(trim($email));

        $user = User::where('email', $email)->first();
        if ($user) {
            return $user;
        }

        $user = User::create([
            'name' => strtok($email, '@') ?: 'Stripe User',
            'email' => $email,
            'password' => Hash::make(bin2hex(random_bytes(16))),
            'plan' => $plan,
            'email_verified_at' => now(),
        ]);

        return $user;
    }

    /**
     * @return array{price_id:string}
     */
    private function getPlanConfig(string $plan): array
    {
        if (!in_array($plan, self::PLANS, true)) {
            throw new RuntimeException('Invalid plan.');
        }

        $priceId = Config::get("services.stripe.prices.{$plan}");

        if (!is_string($priceId) || $priceId === '') {
            throw new RuntimeException("Stripe price for plan '{$plan}' is missing.");
        }

        // Guard against accidental values like "price_5.99".
        if (!preg_match('/^price_[A-Za-z0-9_]+$/', $priceId)) {
            throw new RuntimeException("Stripe price for plan '{$plan}' is invalid.");
        }

        return [
            'price_id' => $priceId,
        ];
    }
}

