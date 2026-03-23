<?php

namespace App\Http\Controllers\Stripe;

use App\Http\Controllers\Controller;
use App\Services\Stripe\StripePricingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use RuntimeException;
use Stripe\Webhook;
use Stripe\Exception\ApiErrorException;

class StripeWebhookController extends Controller
{
    public function __construct(private readonly StripePricingService $stripePricingService)
    {
    }

    public function handle(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = (string) $request->header('Stripe-Signature');

        $endpointSecret = env('STRIPE_WEBHOOK_SECRET');
        if (!is_string($endpointSecret) || $endpointSecret === '') {
            throw new RuntimeException('Missing STRIPE_WEBHOOK_SECRET env var.');
        }

        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $endpointSecret);
        } catch (\UnexpectedValueException $e) {
            Log::warning('Stripe webhook invalid payload', ['message' => $e->getMessage()]);
            return response('invalid payload', 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            Log::warning('Stripe webhook signature verification failed', ['message' => $e->getMessage()]);
            return response('invalid signature', 400);
        }

        $type = $event->type ?? '';

        try {
            switch ($type) {
                case 'checkout.session.completed':
                    $this->stripePricingService->handleCheckoutSessionCompleted($event->data->object);
                    break;

                case 'customer.subscription.updated':
                    $this->stripePricingService->handleSubscriptionUpdated($event->data->object);
                    break;

                case 'customer.subscription.deleted':
                    $this->stripePricingService->handleSubscriptionDeleted($event->data->object);
                    break;

                default:
                    // Ignore unrelated webhook events.
                    break;
            }
        } catch (ApiErrorException $e) {
            Log::error('Stripe webhook Stripe API error', ['type' => $type, 'message' => $e->getMessage()]);
            return response('stripe api error', 500);
        } catch (\Throwable $e) {
            Log::error('Stripe webhook handler error', ['type' => $type, 'message' => $e->getMessage()]);
            return response('handler error', 500);
        }

        return response('ok', 200);
    }
}

