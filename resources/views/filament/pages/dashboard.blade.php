<x-filament-panels::page>
    <div class="space-y-4 p-6">
        @php
            $user = auth()->user();
            $currentPlan = $user?->plan ?? 'free';

            $planLabel = match ($currentPlan) {
                'personal' => __('messages.pricing.personal.title'),
                'premium' => __('messages.pricing.premium.title'),
                'business' => __('messages.pricing.business.title'),
                default => 'Free',
            };
        @endphp

        <div class="rounded-xl border bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Your subscription</h2>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">
                Current plan: <span class="font-medium">{{ $planLabel }}</span>
            </p>

            <div class="mt-4">
                <a
                    href="/app/pricing"
                    class="inline-flex items-center justify-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500">
                    Manage plan
                </a>
            </div>
        </div>
    </div>
</x-filament-panels::page>
