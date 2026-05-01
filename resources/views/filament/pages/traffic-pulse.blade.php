<x-filament-panels::page>
    <div class="-mx-4 -mt-6 sm:-mx-6 lg:-mx-8">
        <iframe
            src="{{ route('pulse') }}"
            title="{{ __('admin.navigation.pulse') }}"
            class="block w-full rounded-xl border border-gray-200 bg-white shadow-sm ring-1 ring-gray-950/5 dark:border-gray-700 dark:bg-gray-950 dark:ring-white/10"
            style="height: 82vh; min-height: 48rem;"
            loading="lazy"
        ></iframe>
    </div>
</x-filament-panels::page>
