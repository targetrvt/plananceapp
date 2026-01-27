<x-filament-breezy::grid-section md=2 :title="__('profile.browser_sessions.title')" :description="__('profile.browser_sessions.description')">
    <x-filament::card>
        <div class="space-y-6">
            @if(count($sessions) > 0)
                <div class="space-y-4">
                    @foreach($sessions as $session)
                        <div class="flex items-center justify-between p-4 border rounded-lg {{ $session['is_current_device'] ? 'bg-primary-50 dark:bg-primary-900/20 border-primary-300 dark:border-primary-700' : 'bg-gray-50 dark:bg-gray-800 border-gray-200 dark:border-gray-700' }}">
                            <div class="flex-1">
                                <div class="flex items-center gap-2">
                                    <div>
                                        @if($session['device'] === 'Mobile')
                                            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                            </svg>
                                        @else
                                            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                            </svg>
                                        @endif
                                    </div>
                                    <div>
                                        <div class="font-medium text-gray-900 dark:text-gray-100">
                                            {{ $session['browser'] }} {{ __('on') }} {{ $session['platform'] }}
                                            @if($session['is_current_device'])
                                                <span class="ml-2 text-xs font-normal text-primary-600 dark:text-primary-400">({{ __('profile.browser_sessions.this_device') }})</span>
                                            @endif
                                        </div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">
                                            {{ $session['ip_address'] }} • {{ __('profile.browser_sessions.last_active') }}: {{ \Carbon\Carbon::createFromTimestamp($session['last_activity'])->diffForHumans() }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                        {{ __('profile.browser_sessions.logout_other_sessions_description') }}
                    </p>
                    {{ $this->logoutOtherSessionsAction }}
                </div>
            @else
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    {{ __('profile.browser_sessions.no_active_sessions') }}
                </p>
            @endif
        </div>
    </x-filament::card>
    <x-filament-actions::modals />
</x-filament-breezy::grid-section>

