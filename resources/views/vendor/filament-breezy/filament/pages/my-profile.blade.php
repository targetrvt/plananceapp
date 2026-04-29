<x-filament::page>
    <div class="space-y-6 divide-y divide-gray-900/10 dark:divide-white/10">
        @foreach ($this->getRegisteredMyProfileComponents() as $component)
            @unless(is_null($component))
                @livewire($component)
            @endunless
        @endforeach

        <x-filament-breezy::grid-section
            md="2"
            :title="__('profile.email_notifications.section_title')"
            :description="__('profile.email_notifications.section_description')"
        >
            <x-filament::card>
                <form method="POST" action="{{ route('profile.email-noticications.update') }}" class="space-y-6">
                    @csrf

                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            @if (auth()->user()?->hasVerifiedEmail())
                                <x-filament::badge color="success">
                                    {{ __('profile.email_notifications.email_verified') }}
                                </x-filament::badge>
                            @else
                                <x-filament::button
                                    tag="a"
                                    href="{{ url('/app/email-verification/prompt') }}"
                                    color="warning"
                                    size="sm"
                                >
                                    {{ __('profile.email_notifications.verify_email') }}
                                </x-filament::button>
                            @endif
                        </div>

                        <x-filament::button
                            color="gray"
                            size="sm"
                            x-on:click="document.querySelector('.fi-topbar-database-notifications-btn')?.click()"
                            type="button"
                        >
                            {{ __('profile.email_notifications.open_notifications') }}
                        </x-filament::button>
                    </div>

                    <div class="rounded-xl border border-gray-200 divide-y divide-gray-200 dark:border-white/10 dark:divide-white/10">
                        <div class="flex items-center justify-between gap-4 p-4" x-data="{ value: '{{ (auth()->user()?->notify_budget_warnings ?? true) ? 'on' : 'off' }}' }">
                            <div>
                                <p class="text-sm font-medium text-gray-950 dark:text-white">{{ __('profile.email_notifications.budget_warnings.title') }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('profile.email_notifications.budget_warnings.description') }}</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <input type="hidden" name="notify_budget_warnings" x-model="value">
                                <button
                                    type="button"
                                    x-on:click="value = 'on'"
                                    class="fi-btn fi-size-xs rounded-lg px-3 py-1.5 text-xs font-medium transition"
                                    x-bind:class="value === 'on'
                                        ? 'bg-primary-600 text-white'
                                        : 'bg-gray-100 text-gray-700 dark:bg-white/10 dark:text-gray-200'"
                                >
                                    {{ __('profile.email_notifications.on') }}
                                </button>
                                <button
                                    type="button"
                                    x-on:click="value = 'off'"
                                    class="fi-btn fi-size-xs rounded-lg px-3 py-1.5 text-xs font-medium transition"
                                    x-bind:class="value === 'off'
                                        ? 'bg-primary-600 text-white'
                                        : 'bg-gray-100 text-gray-700 dark:bg-white/10 dark:text-gray-200'"
                                >
                                    {{ __('profile.email_notifications.off') }}
                                </button>
                            </div>
                        </div>

                        <div class="flex items-center justify-between gap-4 p-4" x-data="{ value: '{{ (auth()->user()?->notify_budget_limit_email ?? true) ? 'on' : 'off' }}' }">
                            <div>
                                <p class="text-sm font-medium text-gray-950 dark:text-white">{{ __('profile.email_notifications.budget_limit_email.title') }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('profile.email_notifications.budget_limit_email.description') }}</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <input type="hidden" name="notify_budget_limit_email" x-model="value">
                                <button
                                    type="button"
                                    x-on:click="value = 'on'"
                                    class="fi-btn fi-size-xs rounded-lg px-3 py-1.5 text-xs font-medium transition"
                                    x-bind:class="value === 'on'
                                        ? 'bg-primary-600 text-white'
                                        : 'bg-gray-100 text-gray-700 dark:bg-white/10 dark:text-gray-200'"
                                >
                                    {{ __('profile.email_notifications.on') }}
                                </button>
                                <button
                                    type="button"
                                    x-on:click="value = 'off'"
                                    class="fi-btn fi-size-xs rounded-lg px-3 py-1.5 text-xs font-medium transition"
                                    x-bind:class="value === 'off'
                                        ? 'bg-primary-600 text-white'
                                        : 'bg-gray-100 text-gray-700 dark:bg-white/10 dark:text-gray-200'"
                                >
                                    {{ __('profile.email_notifications.off') }}
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-between">
                        @if (session('status') === 'email-notifications-updated')
                            <p class="text-sm text-success-600 dark:text-success-400">{{ __('profile.email_notifications.saved') }}</p>
                        @else
                            <span></span>
                        @endif

                        <x-filament::button type="submit" size="sm">
                            {{ __('profile.email_notifications.save_settings') }}
                        </x-filament::button>
                    </div>
                </form>
            </x-filament::card>
        </x-filament-breezy::grid-section>
    </div>
</x-filament::page>
