<x-filament-panels::page>
    <div class="space-y-6 p-6">
        @php
            $user = auth()->user();
            $currentPlan = $user?->plan ?? 'free';
            $checkoutSuccess = request()->boolean('checkout_success', false);
            $checkoutCancelled = request()->boolean('checkout_cancelled', false);

            $plans = [
                'personal' => [
                    'title' => __('messages.pricing.personal.title'),
                    'description' => __('messages.pricing.personal.description'),
                    'price' => '*',
                    'features' => [
                        __('messages.pricing.personal.features.unlimited_categories'),
                        __('messages.pricing.personal.features.expense_tracking'),
                        __('messages.pricing.personal.features.goal_setting'),
                        __('messages.pricing.personal.features.basic_reports'),
                        __('messages.pricing.personal.features.mobile_access'),
                        __('messages.pricing.personal.features.email_support'),
                    ],
                ],
                'premium' => [
                    'title' => __('messages.pricing.premium.title'),
                    'description' => __('messages.pricing.premium.description'),
                    'price' => '**',
                    'features' => [
                        __('messages.pricing.premium.features.everything_personal'),
                        __('messages.pricing.premium.features.receipt_scanning'),
                        __('messages.pricing.premium.features.advanced_analytics'),
                        __('messages.pricing.premium.features.custom_categories'),
                        __('messages.pricing.premium.features.export_data'),
                        __('messages.pricing.premium.features.priority_support'),
                    ],
                ],
                'business' => [
                    'title' => __('messages.pricing.business.title'),
                    'description' => __('messages.pricing.business.description'),
                    'price' => '***',
                    'features' => [
                        __('messages.pricing.business.features.everything_premium'),
                        __('messages.pricing.business.features.multiple_users'),
                        __('messages.pricing.business.features.team_collaboration'),
                        __('messages.pricing.business.features.role_permissions'),
                        __('messages.pricing.business.features.api_access'),
                        __('messages.pricing.business.features.dedicated_support'),
                    ],
                ],
            ];
        @endphp

        @if ($checkoutSuccess)
            <div class="rounded-lg border border-success-200 bg-success-50 p-4 text-success-800">
                Payment started. If the webhook processed successfully, your plan should update shortly.
            </div>
        @endif

        @if ($checkoutCancelled)
            <div class="rounded-lg border border-warning-200 bg-warning-50 p-4 text-warning-800">
                Checkout was cancelled.
            </div>
        @endif

        <div class="flex items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Choose a plan</h1>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">
                    Current plan: <span class="font-medium">{{ $this->getCurrentPlanLabel() }}</span>
                </p>
            </div>
        </div>

        <div class="grid gap-4 lg:grid-cols-3">
            @foreach ($plans as $planKey => $plan)
                @php
                    $isSelected = $this->selectedPlan === $planKey;
                    $isCurrent = $currentPlan === $planKey;
                @endphp

                <div @class([
                    'rounded-xl border bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900',
                    'ring-2 ring-indigo-500' => $isSelected,
                    'opacity-60' => $isCurrent,
                ])>
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $plan['title'] }}</h2>
                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">{{ $plan['description'] }}</p>
                        </div>
                    </div>

                    <div class="mt-4 flex items-baseline gap-2">
                        <span class="text-sm text-gray-600 dark:text-gray-300">€</span>
                        <span class="text-3xl font-bold text-gray-900 dark:text-white">{{ $plan['price'] }}</span>
                        <span class="text-sm text-gray-600 dark:text-gray-300">/mo</span>
                    </div>

                    <ul class="mt-4 space-y-2 text-sm">
                        @foreach ($plan['features'] as $feature)
                            <li class="flex items-start gap-2">
                                <svg class="mt-0.5 h-4 w-4 text-emerald-500" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                <span class="text-gray-700 dark:text-gray-200">{{ $feature }}</span>
                            </li>
                        @endforeach
                    </ul>

                    <div class="mt-6">
                        @if ($isCurrent)
                            <div class="rounded-lg bg-success-50 p-3 text-sm font-medium text-success-700 dark:bg-emerald-950 dark:text-emerald-200">
                                Current plan
                            </div>
                        @else
                            <a
                                href="{{ route('stripe.checkout', ['plan' => $planKey]) }}"
                                class="inline-flex w-full items-center justify-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500 dark:bg-indigo-500 dark:hover:bg-indigo-400">
                                Pay with Stripe (demo)
                            </a>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</x-filament-panels::page>

