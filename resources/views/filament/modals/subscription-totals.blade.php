<div class="p-4">
    <div class="text-center">
        <h2 class="text-xl font-bold mb-4">Monthly Subscriptions Total</h2>
        
        <div class="bg-primary-50 dark:bg-primary-900/50 p-6 rounded-lg">
            <p class="text-gray-600 dark:text-gray-400 mb-2">Your active subscriptions cost you:</p>
            <p class="text-3xl font-bold text-primary-600 dark:text-primary-400">€{{ number_format($total, 2) }}/month</p>
            <p class="text-sm text-gray-500 dark:text-gray-500 mt-2">That's €{{ number_format($total * 12, 2) }} annually</p>
        </div>
        
        <div class="mt-6 text-sm text-gray-600 dark:text-gray-400">
            <p>This includes only your active subscriptions.</p>
        </div>
    </div>
</div>