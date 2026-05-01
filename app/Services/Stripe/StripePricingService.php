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

        if (! is_string($secretKey) || $secretKey === '') {
            throw new RuntimeException('Stripe is not configured.');
        }

        $this->stripe = new StripeClient($secretKey);
    }

    /**
     * End of the current billing period as Unix seconds.
     *
     * Pre-Basil Stripe: top-level `current_period_end` on the subscription.
     * Basil (2025-03-31+): `items.data[].current_period_end` on each subscription item; we use the latest timestamp.
     */
    private static function stripeSubscriptionPeriodEndUnix(object $subscription): ?int
    {
        $fromTop = self::coerceUnixTimestamp($subscription->current_period_end ?? null);
        if ($fromTop !== null && $fromTop > 0) {
            return $fromTop;
        }

        $items = $subscription->items ?? null;
        if (! is_object($items) || ! isset($items->data)) {
            return null;
        }

        $data = $items->data ?? null;
        if (! is_iterable($data)) {
            return null;
        }

        $best = null;
        foreach ($data as $item) {
            if (! is_object($item)) {
                continue;
            }
            $ts = self::coerceUnixTimestamp($item->current_period_end ?? null);
            if ($ts !== null && $ts > 0 && ($best === null || $ts > $best)) {
                $best = $ts;
            }
        }

        return $best;
    }

    private static function coerceUnixTimestamp(mixed $raw): ?int
    {
        if (is_int($raw)) {
            return $raw;
        }

        if (is_float($raw)) {
            return (int) $raw;
        }

        if (is_string($raw) && is_numeric($raw)) {
            return (int) $raw;
        }

        return null;
    }

    public function createCheckoutSessionUrl(?User $user, string $plan): string
    {
        $plan = strtolower($plan);

        if ($plan === 'business') {
            throw new RuntimeException('Business checkout is not available yet.');
        }

        $planConfig = $this->getPlanConfig($plan);
        $isSubscriptionPlan = in_array($plan, ['premium', 'business'], true);

        $baseUrl = rtrim((string) config('app.url', env('APP_URL', 'http://localhost')), '/');

        $pricingPath = $user?->filamentPricingPath() ?? '/app/pricing';
        $successUrl = $baseUrl.$pricingPath.'?checkout_success=1&session_id={CHECKOUT_SESSION_ID}';
        $cancelUrl = $baseUrl.$pricingPath.'?checkout_cancelled=1';

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

        if (! is_string($session->url)) {
            throw new RuntimeException('Stripe checkout session URL missing.');
        }

        return $session->url;
    }

    public function handleCheckoutSessionCompleted(object $session): void
    {
        $metadata = $this->extractMetadata($session->metadata ?? null);

        $userId = Arr::get($metadata, 'user_id');
        $plan = (string) Arr::get($metadata, 'plan', '');

        if ($plan === '') {
            return;
        }

        if (! in_array($plan, self::PLANS, true)) {
            return;
        }

        $user = null;

        if (is_string($userId) && $userId !== '') {
            $user = User::find($userId);
        }

        if (! $user) {
            $customerDetails = $session->customer_details ?? null;
            $customerEmail = is_object($customerDetails) && isset($customerDetails->email) ? (string) $customerDetails->email : null;

            if (! $customerEmail) {
                return;
            }

            $user = User::where('email', $customerEmail)->first();
            if (! $user) {
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

        $subscription = $this->resolveSubscriptionObjectFromCheckoutSession($session);
        if ($subscription !== null) {
            $this->syncUserFromSubscription($user, $subscription, $plan, false);

            return;
        }

        // Fallback: store the session plan, but leave subscription fields empty.
        $user->update([
            'plan' => $plan,
            'premium_granted_by_admin' => false,
            'stripe_customer_id' => is_string($session->customer) ? $session->customer : null,
            'stripe_subscription_id' => null,
            'stripe_status' => 'active',
            'stripe_current_period_end' => null,
            'stripe_cancel_at_period_end' => false,
        ]);
    }

    public function syncUserFromCheckoutSessionId(string $sessionId, ?User $authenticatedUser = null): bool
    {
        if ($sessionId === '') {
            return false;
        }

        $session = $this->stripe->checkout->sessions->retrieve($sessionId, [
            'expand' => ['subscription'],
        ]);
        $metadata = $this->extractMetadata($session->metadata ?? null);

        if ($authenticatedUser) {
            $metadataUserId = (string) Arr::get($metadata, 'user_id', '');
            $metadataEmail = (string) Arr::get($metadata, 'user_email', '');

            $matchesUserId = $metadataUserId !== '' && (string) $authenticatedUser->id === $metadataUserId;
            $matchesEmail = $metadataEmail !== '' && strcasecmp($authenticatedUser->email, $metadataEmail) === 0;

            if (! $matchesUserId && ! $matchesEmail) {
                return false;
            }
        }

        $this->handleCheckoutSessionCompleted($session);

        return true;
    }

    public function handleSubscriptionUpdated(object $subscription): void
    {
        $metadata = $this->extractMetadata($subscription->metadata ?? null);
        $userId = Arr::get($metadata, 'user_id');
        $plan = (string) Arr::get($metadata, 'plan', '');
        $userEmail = (string) Arr::get($metadata, 'user_email', '');

        $user = null;

        if (is_string($userId) && $userId !== '') {
            $user = User::find($userId);
        }

        if (! $user && $userEmail !== '') {
            $user = User::where('email', $userEmail)->first();
        }

        if (! $user) {
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

        if (! $user) {
            return;
        }

        if (! in_array($plan, self::PLANS, true)) {
            $plan = (string) ($user->plan ?: 'free');
            if (! in_array($plan, self::PLANS, true)) {
                $plan = 'personal';
            }
        }

        $this->syncUserFromSubscription($user, $subscription, $plan, false);
    }

    public function handleSubscriptionDeleted(object $subscription): void
    {
        $metadata = $this->extractMetadata($subscription->metadata ?? null);
        $userId = Arr::get($metadata, 'user_id');
        $userEmail = (string) Arr::get($metadata, 'user_email', '');

        $user = null;
        if (is_string($userId) && $userId !== '') {
            $user = User::find($userId);
        }
        if (! $user && $userEmail !== '') {
            $user = User::where('email', $userEmail)->first();
        }

        if (! $user) {
            $customerId = is_string($subscription->customer ?? null) ? (string) $subscription->customer : null;
            if ($customerId) {
                $customer = $this->stripe->customers->retrieve($customerId, []);
                $emailFromCustomer = is_string($customer->email ?? null) ? (string) $customer->email : null;
                if ($emailFromCustomer) {
                    $user = User::where('email', $emailFromCustomer)->first();
                }
            }
        }

        if (! $user) {
            return;
        }

        $this->syncUserFromSubscription($user, $subscription, 'free', true);
    }

    private function syncUserFromSubscription(User $user, object $subscription, string $plan, bool $deleted): void
    {
        $periodEnd = null;
        $periodTs = self::stripeSubscriptionPeriodEndUnix($subscription);
        if ($periodTs !== null && $periodTs > 0) {
            $periodEnd = Carbon::createFromTimestamp($periodTs)->toDateString();
        }

        $status = is_string($subscription->status) ? $subscription->status : null;
        $customerId = is_string($subscription->customer) ? $subscription->customer : null;
        $cancelAtPeriodEnd = ! $deleted && isset($subscription->cancel_at_period_end)
            ? (bool) $subscription->cancel_at_period_end
            : false;

        $user->update([
            'plan' => $deleted ? 'free' : $plan,
            'premium_granted_by_admin' => false,
            'stripe_customer_id' => $customerId ?: $user->stripe_customer_id,
            'stripe_subscription_id' => is_string($subscription->id) ? $subscription->id : $user->stripe_subscription_id,
            'stripe_status' => $status,
            'stripe_current_period_end' => $deleted ? null : $periodEnd,
            'stripe_cancel_at_period_end' => $deleted ? false : $cancelAtPeriodEnd,
        ]);
    }

    /**
     * Pull latest subscription fields from Stripe (period end, cancel-at-period-end, status).
     */
    public function refreshSubscriptionFromStripe(User $user): void
    {
        $subscriptionId = $user->stripe_subscription_id;
        if (! is_string($subscriptionId) || $subscriptionId === '') {
            return;
        }

        $subscription = $this->stripe->subscriptions->retrieve($subscriptionId, [
            'expand' => ['items.data'],
        ]);
        $metadata = $this->extractMetadata($subscription->metadata ?? null);
        $plan = (string) Arr::get($metadata, 'plan', '');
        if (! in_array($plan, self::PLANS, true)) {
            $plan = (string) ($user->plan ?: 'personal');
            if (! in_array($plan, self::PLANS, true)) {
                $plan = 'personal';
            }
        }

        $this->syncUserFromSubscription($user, $subscription, $plan, false);
    }

    /**
     * Undo cancel-at-period-end so billing continues after the current period.
     */
    public function resumeSubscription(User $user): void
    {
        $subscriptionId = $user->stripe_subscription_id;
        if (! is_string($subscriptionId) || $subscriptionId === '') {
            throw new RuntimeException('No Stripe subscription to resume.');
        }

        try {
            $this->stripe->subscriptions->update($subscriptionId, [
                'cancel_at_period_end' => false,
            ]);
        } catch (ApiErrorException $e) {
            throw new RuntimeException('Unable to resume subscription.', 0, $e);
        }

        $this->refreshSubscriptionFromStripe($user);
    }

    /**
     * Immediately cancel an active Stripe subscription and drop the account to Personal in-app.
     * Used when an admin revokes Premium even though billing is still active.
     */
    public function cancelSubscriptionImmediately(User $user): void
    {
        $subscriptionId = $user->stripe_subscription_id;
        if (! is_string($subscriptionId) || $subscriptionId === '') {
            throw new RuntimeException('No Stripe subscription to cancel.');
        }

        try {
            $this->stripe->subscriptions->cancel($subscriptionId);
        } catch (ApiErrorException $e) {
            throw new RuntimeException('Stripe could not cancel the subscription.', 0, $e);
        }

        self::applyLocalPremiumRevoke($user->fresh());
    }

    /** Clear Stripe subscription Premium without calling Stripe (e.g. no API key configured). */
    public static function applyLocalPremiumRevoke(User $user): void
    {
        $user->forceFill([
            'plan' => 'personal',
            'premium_granted_by_admin' => false,
            'stripe_subscription_id' => null,
            'stripe_status' => 'canceled',
            'stripe_current_period_end' => null,
            'stripe_cancel_at_period_end' => false,
        ])->save();
    }

    /**
     * Stop auto-renewal; Stripe keeps the subscription active until the current period ends.
     */
    public function cancelSubscriptionAtPeriodEnd(User $user): void
    {
        $subscriptionId = $user->stripe_subscription_id;
        if (! is_string($subscriptionId) || $subscriptionId === '') {
            throw new RuntimeException('No Stripe subscription to cancel.');
        }

        try {
            $this->stripe->subscriptions->update($subscriptionId, [
                'cancel_at_period_end' => true,
            ]);
        } catch (ApiErrorException $e) {
            throw new RuntimeException('Unable to schedule subscription cancellation.', 0, $e);
        }

        $this->refreshSubscriptionFromStripe($user);
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
     * Checkout Session.subscription may be a subscription id string or an expanded Subscription object.
     */
    private function resolveSubscriptionObjectFromCheckoutSession(object $session): ?object
    {
        $ref = $session->subscription ?? null;

        if (is_string($ref) && str_starts_with($ref, 'sub_')) {
            try {
                return $this->stripe->subscriptions->retrieve($ref, [
                    'expand' => ['items.data'],
                ]);
            } catch (ApiErrorException) {
                return null;
            }
        }

        if (is_object($ref) && isset($ref->id) && is_string($ref->id) && str_starts_with($ref->id, 'sub_')) {
            if (self::stripeSubscriptionPeriodEndUnix($ref) !== null) {
                return $ref;
            }

            try {
                return $this->stripe->subscriptions->retrieve($ref->id, [
                    'expand' => ['items.data'],
                ]);
            } catch (ApiErrorException) {
                return null;
            }
        }

        return null;
    }

    private function extractMetadata(mixed $metadata): array
    {
        if (is_array($metadata)) {
            return $metadata;
        }

        if (is_object($metadata)) {
            if (method_exists($metadata, 'toArray')) {
                $converted = $metadata->toArray();

                return is_array($converted) ? $converted : [];
            }

            return get_object_vars($metadata);
        }

        return [];
    }

    /**
     * @return array{price_id:string}
     */
    private function getPlanConfig(string $plan): array
    {
        if (! in_array($plan, self::PLANS, true)) {
            throw new RuntimeException('Invalid plan.');
        }

        $priceId = Config::get("services.stripe.prices.{$plan}");

        if (! is_string($priceId) || $priceId === '') {
            throw new RuntimeException("Stripe price for plan '{$plan}' is missing.");
        }

        // Guard against accidental values like "price_5.99".
        if (! preg_match('/^price_[A-Za-z0-9_]+$/', $priceId)) {
            throw new RuntimeException("Stripe price for plan '{$plan}' is invalid.");
        }

        return [
            'price_id' => $priceId,
        ];
    }
}
