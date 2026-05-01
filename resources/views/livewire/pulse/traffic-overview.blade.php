<x-pulse::card :cols="$cols" :rows="$rows" :class="$class" wire:poll.5s="">
    <x-pulse::card-header
        name="{{ __('admin.pulse.traffic_heading') }}"
        details="past {{ $this->periodForHumans() }} · {{ __('admin.pulse.traffic_details_short') }}"
    >
        <x-slot:icon>
            <x-pulse::icons.cursor-arrow-rays />
        </x-slot:icon>
    </x-pulse::card-header>

    @if ($trafficSampleRate < 1)
        <p class="px-6 text-xs text-amber-600 dark:text-amber-400">
            {{ __('admin.pulse.sampled_notice', ['rate' => $trafficSampleRate]) }}
        </p>
    @endif

    <x-pulse::scroll :expand="$expand">
        <div class="space-y-6">
            <div>
                <h3 class="px-6 mb-2 text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                    {{ __('admin.pulse.top_client_ips') }}
                </h3>
                @if ($byIp->isEmpty())
                    <x-pulse::no-results />
                @else
                    <x-pulse::table>
                        <x-pulse::thead>
                            <tr>
                                <x-pulse::th>IP</x-pulse::th>
                                <x-pulse::th class="text-right">{{ __('admin.pulse.hits') }}</x-pulse::th>
                            </tr>
                        </x-pulse::thead>
                        <tbody>
                            @foreach ($byIp as $row)
                                <tr wire:key="ip-{{ $row->key }}">
                                    <x-pulse::td>
                                        <code class="text-xs text-gray-900 dark:text-gray-100">{{ $row->key }}</code>
                                    </x-pulse::td>
                                    <x-pulse::td numeric class="text-gray-700 dark:text-gray-300 font-bold">
                                        @if ($trafficSampleRate < 1)
                                            <span title="{{ __('admin.pulse.raw') }} {{ number_format($row->count) }}">~{{ number_format($row->count * (1 / $trafficSampleRate)) }}</span>
                                        @else
                                            {{ number_format($row->count) }}
                                        @endif
                                    </x-pulse::td>
                                </tr>
                            @endforeach
                        </tbody>
                    </x-pulse::table>
                @endif
            </div>

            <div>
                <h3 class="px-6 mb-2 text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                    {{ __('admin.pulse.ip_and_user') }}
                </h3>
                @if ($byIpUser->isEmpty())
                    <x-pulse::no-results />
                @else
                    <x-pulse::table>
                        <x-pulse::thead>
                            <tr>
                                <x-pulse::th>IP</x-pulse::th>
                                <x-pulse::th>{{ __('admin.pulse.user') }}</x-pulse::th>
                                <x-pulse::th class="text-right">{{ __('admin.pulse.hits') }}</x-pulse::th>
                            </tr>
                        </x-pulse::thead>
                        <tbody>
                            @foreach ($byIpUser as $row)
                                <tr wire:key="ipu-{{ $row->ip }}-{{ $row->user_label }}">
                                    <x-pulse::td>
                                        <code class="text-xs text-gray-900 dark:text-gray-100">{{ $row->ip }}</code>
                                    </x-pulse::td>
                                    <x-pulse::td class="max-w-[1px] truncate text-xs text-gray-700 dark:text-gray-300" title="{{ $row->user_label }}">
                                        {{ $row->user_label }}
                                    </x-pulse::td>
                                    <x-pulse::td numeric class="text-gray-700 dark:text-gray-300 font-bold">
                                        @if ($trafficSampleRate < 1)
                                            <span title="{{ __('admin.pulse.raw') }} {{ number_format($row->count) }}">~{{ number_format($row->count * (1 / $trafficSampleRate)) }}</span>
                                        @else
                                            {{ number_format($row->count) }}
                                        @endif
                                    </x-pulse::td>
                                </tr>
                            @endforeach
                        </tbody>
                    </x-pulse::table>
                @endif
            </div>

            <div>
                <h3 class="px-6 mb-2 text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                    {{ __('admin.pulse.request_flags') }}
                </h3>
                @if ($flags->isEmpty())
                    <x-pulse::no-results />
                @else
                    <x-pulse::table>
                        <x-pulse::thead>
                            <tr>
                                <x-pulse::th>{{ __('admin.pulse.flag') }}</x-pulse::th>
                                <x-pulse::th class="text-right">{{ __('admin.pulse.hits') }}</x-pulse::th>
                            </tr>
                        </x-pulse::thead>
                        <tbody>
                            @foreach ($flags as $row)
                                <tr wire:key="flag-{{ $row->key }}">
                                    <x-pulse::td>
                                        <code class="text-xs text-gray-900 dark:text-gray-100">{{ $row->key }}</code>
                                    </x-pulse::td>
                                    <x-pulse::td numeric class="text-gray-700 dark:text-gray-300 font-bold">
                                        @if ($trafficSampleRate < 1)
                                            <span title="{{ __('admin.pulse.raw') }} {{ number_format($row->count) }}">~{{ number_format($row->count * (1 / $trafficSampleRate)) }}</span>
                                        @else
                                            {{ number_format($row->count) }}
                                        @endif
                                    </x-pulse::td>
                                </tr>
                            @endforeach
                        </tbody>
                    </x-pulse::table>
                @endif
            </div>
        </div>
    </x-pulse::scroll>
</x-pulse::card>
