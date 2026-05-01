<x-filament-panels::page>
    <link rel="stylesheet" href="{{ asset('css/planance-pricing-plans.css') }}">

    <div class="pricing-page space-y-6 p-6">
        @php
            $user = auth()->user();
            $currentPlan = strtolower((string) ($user?->plan ?? 'free'));
            $currentPlanTierClass = in_array($currentPlan, ['personal', 'premium', 'business'], true) ? $currentPlan : 'free';

            $plans = [
                'personal' => [
                    'title' => __('messages.landing.pricing.personal.title'),
                    'description' => __('messages.landing.pricing.personal.description'),
                    'features' => [
                        __('messages.landing.pricing.personal.features.unlimited_categories'),
                        __('messages.landing.pricing.personal.features.expense_tracking'),
                        __('messages.landing.pricing.personal.features.goal_setting'),
                        __('messages.landing.pricing.personal.features.basic_reports'),
                        __('messages.landing.pricing.personal.features.email_support'),
                    ],
                ],
                'premium' => [
                    'title' => __('messages.landing.pricing.premium.title'),
                    'description' => __('messages.landing.pricing.premium.description'),
                    'price' => '9.99',
                    'features' => [
                        __('messages.landing.pricing.premium.features.everything_personal'),
                        __('messages.landing.pricing.premium.features.receipt_scanning'),
                        __('messages.landing.pricing.premium.features.advanced_analytics'),
                        __('messages.landing.pricing.premium.features.custom_categories'),
                        __('messages.landing.pricing.premium.features.export_data'),
                        __('messages.landing.pricing.premium.features.priority_support'),
                    ],
                ],
                'business' => [
                    'title' => __('messages.landing.pricing.business.title'),
                    'description' => __('messages.landing.pricing.business.description'),
                    'price' => '19.99',
                    'features' => [
                        __('messages.landing.pricing.business.features.everything_premium'),
                        __('messages.landing.pricing.business.features.multiple_users'),
                        __('messages.landing.pricing.business.features.team_collaboration'),
                        __('messages.landing.pricing.business.features.role_permissions'),
                        __('messages.landing.pricing.business.features.dedicated_support'),
                    ],
                ],
            ];
            $billing = $this->getSubscriptionBillingInsights();
            $billingDate = $billing['period_end_formatted'] ?? __('messages.pricing_page.date_pending');
        @endphp

        <div class="pricing-header flex items-center justify-between gap-4">
            <div class="pricing-header-intro">
                <h1 class="text-3xl font-semibold text-gray-900 dark:text-white">{{ __('messages.pricing_page.choose_plan') }}</h1>
                <div class="pricing-header-cards-row">
                    <div class="pricing-current-plan-card pricing-current-plan-card--{{ $currentPlanTierClass }}">
                        <div class="pricing-current-plan-card-inner">
                            <span class="pricing-current-plan-card-icon" aria-hidden="true">
                                @switch($currentPlanTierClass)
                                    @case('personal')
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                                        </svg>
                                        @break
                                    @case('premium')
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 0 1 1.04 0l2.125 5.111a.563.563 0 0 0 .475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 0 0-.182.557l1.285 5.385a.562.562 0 0 1-.84.61l-4.725-2.885a.562.562 0 0 0-.586 0L6.982 20.54a.562.562 0 0 1-.84-.61l1.285-5.386a.562.562 0 0 0-.182-.557l-4.204-3.602a.562.562 0 0 1 .321-.988l5.518-.442a.563.563 0 0 0 .475-.345L11.48 3.5Z" />
                                        </svg>
                                        @break
                                    @case('business')
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.008v.008h-.008v-.008Zm0 3h.008v.008h-.008v-.008Zm0 3h.008v.008h-.008v-.008Z" />
                                        </svg>
                                        @break
                                    @default
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 11.25v8.25a1.5 1.5 0 0 1-1.5 1.5H5.25a1.5 1.5 0 0 1-1.5-1.5v-8.25M12 4.875A2.625 2.625 0 1 0 9.375 7.5H12m0-2.625V7.5m0-2.625A2.625 2.625 0 1 1 14.625 7.5H12m0 0V21m-8.625-9.75h18c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125h-18c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z" />
                                        </svg>
                                @endswitch
                            </span>
                            <div class="pricing-current-plan-card-text">
                                <p class="pricing-current-plan-card-label">{{ __('messages.pricing_page.current_plan') }}</p>
                                <p class="pricing-current-plan-card-value">{{ $this->getCurrentPlanLabel() }}</p>
                            </div>
                        </div>
                    </div>
                    @if ($billing['show'])
                        @if ($billing['cancel_at_period_end'])
                        <div class="pricing-subscription-banner pricing-subscription-banner--ending">
                            <div class="pricing-subscription-banner-main">
                                <span class="pricing-subscription-banner-icon" aria-hidden="true">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5a2.25 2.25 0 002.25-2.25M3 16.5h18M3 12h18M9 3.75v2.25M15 3.75v2.25" />
                                    </svg>
                                </span>
                                <div class="pricing-subscription-banner-copy">
                                    <p class="pricing-subscription-banner-title">{{ __('messages.pricing_page.access_until', ['date' => $billingDate]) }}</p>
                                    <p class="pricing-subscription-banner-meta">{{ __('messages.pricing_page.scheduled_cancel_intro') }}</p>
                                </div>
                            </div>
                            @if ($billing['period_end_formatted'])
                                <div class="pricing-subscription-banner-foot">
                                    <span class="pricing-subscription-banner-foot-icon" aria-hidden="true">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
                                        </svg>
                                    </span>
                                    <p class="pricing-subscription-banner-foot-text">{{ __('messages.pricing_page.renew_charge_hint', ['date' => $billing['period_end_formatted']]) }}</p>
                                </div>
                            @endif
                        </div>
                        @else
                        <div class="pricing-subscription-banner pricing-subscription-banner--active">
                            <div class="pricing-subscription-banner-main">
                                <span class="pricing-subscription-banner-icon" aria-hidden="true">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </span>
                                <div class="pricing-subscription-banner-copy">
                                    <p class="pricing-subscription-banner-title">{{ __('messages.pricing_page.next_payment_header', ['date' => $billingDate]) }}</p>
                                    <p class="pricing-subscription-banner-meta">{{ __('messages.pricing_page.next_payment_on', ['date' => $billingDate]) }}</p>
                                </div>
                            </div>
                        </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>

        <div class="pricing-grid grid gap-4 md:grid-cols-3">
            @foreach ($plans as $planKey => $plan)
                @php
                    $isSelected = $this->selectedPlan === $planKey;
                    $isCurrent = $currentPlan === $planKey;
                    $checkoutLockedLower = ! $isCurrent && $this->shouldHideCheckoutForLowerPlan($planKey);
                @endphp

                <article @class([
                    'pricing-card rounded-3xl border bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900',
                    'pricing-card-current opacity-70' => $isCurrent,
                    'pricing-card-featured' => $planKey === 'premium' && ! $checkoutLockedLower,
                    'pricing-card-interactive' => ! $isCurrent && ! $checkoutLockedLower,
                    'pricing-card-tier-locked' => $checkoutLockedLower,
                ])>
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">{{ $plan['title'] }}</h2>
                            <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">{{ $plan['description'] }}</p>
                        </div>
                        @if ($planKey === 'premium')
                            <span class="pricing-badge">{{ __('messages.pricing_page.popular') }}</span>
                        @endif
                    </div>

                    @if ($planKey === 'personal')
                        <div class="pricing-price pricing-price--free mt-6">
                            <span class="text-4xl font-bold tracking-tight text-gray-900 dark:text-white">{{ __('messages.pricing_page.price_free_display') }}</span>
                        </div>
                    @else
                        <div class="pricing-price mt-6 flex items-baseline gap-2">
                            <span class="text-base text-gray-600 dark:text-gray-300">€</span>
                            <span class="text-4xl font-bold text-gray-900 dark:text-white">{{ $plan['price'] }}</span>
                            <span class="text-sm text-gray-600 dark:text-gray-300">{{ __('messages.common.per_month') }}</span>
                        </div>
                        <p class="pricing-subnote mt-2 text-xs text-gray-500 dark:text-gray-400">{{ __('messages.pricing_page.billed_monthly') }}</p>
                    @endif

                    <ul class="mt-5 space-y-3 text-sm">
                        @foreach ($plan['features'] as $feature)
                            <li class="pricing-feature-item flex items-start gap-2">
                                <svg class="mt-0.5 h-4 w-4 text-emerald-500" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                <span class="text-gray-700 dark:text-gray-200">{{ $feature }}</span>
                            </li>
                        @endforeach
                    </ul>

                    <div class="pricing-actions mt-10">
                        @if ($isCurrent)
                            <div class="pricing-actions-current">
                                @if ($billing['show'] && $billing['can_cancel'])
                                    <div class="pricing-action-form pricing-action-filament-wrap">
                                        {{ $this->cancelStripeSubscriptionAction }}
                                    </div>
                                @endif
                                @if ($billing['show'] && $billing['cancel_at_period_end'] && $billing['can_resume'])
                                    <form method="POST" action="{{ route('stripe.subscription.resume') }}" class="pricing-action-form">
                                        @csrf
                                        <button type="submit" class="pricing-renew">
                                            <svg class="pricing-renew-icon" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                <path fill-rule="evenodd" d="M15.312 11.424a5.5 5.5 0 01-9.201 2.466l-.312-.311h2.433a.75.75 0 000-1.5H3.989a.75.75 0 00-.75.75v4.242a.75.75 0 001.5 0v-2.43l.31.31a7 7 0 0011.712-3.065.75.75 0 00-1.449-.39zm1.23-3.723a.75.75 0 00.219-.53V2.929a.75.75 0 00-1.5 0V5.36l-.31-.31A7 7 0 003.239 8.188a.75.75 0 101.448.389A5.5 5.5 0 0113.89 6.11l.311.31h-2.432a.75.75 0 000 1.5h4.243a.75.75 0 00.53-.219z" clip-rule="evenodd"/>
                                            </svg>
                                            <span>{{ __('messages.pricing_page.renew_subscription') }}</span>
                                        </button>
                                    </form>
                                @endif
                                <div class="pricing-current rounded-2xl bg-success-50 p-3 text-center text-sm font-medium text-success-700 dark:bg-emerald-950 dark:text-emerald-200">
                                    {{ __('messages.pricing_page.current_plan_badge') }}
                                </div>
                            </div>
                        @else
                            @if ($checkoutLockedLower)
                                <div class="pricing-tier-locked rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 text-center text-sm text-gray-600 dark:border-white/10 dark:bg-white/5 dark:text-gray-300">
                                    {{ __('messages.pricing_page.checkout_not_available_lower_tier') }}
                                </div>
                            @elseif ($planKey === 'business')
                                <button
                                    type="button"
                                    disabled
                                    class="pricing-cta inline-flex w-full cursor-not-allowed items-center justify-center rounded-2xl bg-gray-400 px-4 py-2.5 text-sm font-semibold text-white opacity-80 dark:bg-gray-600">
                                    {{ __('messages.landing.pricing.business_coming_soon_cta') }}
                                </button>
                            @else
                                <a
                                    href="{{ route('stripe.checkout', ['plan' => $planKey]) }}"
                                    class="pricing-cta inline-flex w-full items-center justify-center rounded-2xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-indigo-500 dark:bg-indigo-500 dark:hover:bg-indigo-400">
                                    {{ __('messages.pricing_page.pay_with_stripe') }}
                                </a>
                            @endif
                        @endif
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</x-filament-panels::page>

