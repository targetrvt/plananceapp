<x-filament-panels::page.simple>
    <p class="text-center text-sm text-gray-500 dark:text-gray-400">
        {{ __('verification.prompt.sent', ['email' => filament()->auth()->user()->getEmailForVerification()]) }}
    </p>

    <p class="text-center text-sm text-gray-500 dark:text-gray-400">
        {{ __('verification.prompt.not_received') }}

        {{ $this->resendNotificationAction }}
    </p>
</x-filament-panels::page.simple>
