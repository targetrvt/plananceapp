@php
    /** @var \App\Models\User|null $user */
    $user = auth()->user();
@endphp
@if ($user instanceof \App\Models\User)
    @php
        $hasPremium = $user->hasPremiumSubscription();
        $plan = strtolower((string) ($user->plan ?? 'free'));

        $label = $hasPremium
            ? (string) __('messages.landing.pricing.premium.title')
            : match ($plan) {
                'personal' => (string) __('messages.landing.pricing.personal.title'),
                'premium' => (string) __('messages.landing.pricing.premium.title'),
                'business' => (string) __('messages.landing.pricing.business.title'),
                default => (string) __('messages.pricing_page.plan_free'),
            };

        $color = $hasPremium ? 'success' : 'gray';
    @endphp
    <x-filament::badge
        :color="$color"
        size="xs"
        class="planance-active-plan-badge !min-w-0 !px-[5px] !py-px !text-[0.6875rem] !leading-tight !tracking-normal whitespace-nowrap"
    >
        {{ $label }}
    </x-filament::badge>
@endif
