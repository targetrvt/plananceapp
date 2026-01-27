<?php

return [
    'navigation' => [
        'label' => 'Subscriptions Dashboard',
        'group' => 'Overview',
    ],
    'title' => 'Subscriptions Dashboard',
    'stats' => [
        'monthly_spending' => [
            'label' => 'Monthly Spending',
            'description' => 'Annual equivalent:',
        ],
        'active_subscriptions' => [
            'label' => 'Active Subscriptions',
            'description' => 'Average monthly cost:',
        ],
        'upcoming_payments' => [
            'label' => 'Upcoming Payments',
            'description' => 'Total upcoming amount:',
        ],
    ],
    'sections' => [
        'subscription_categories' => 'Subscription Categories',
        'top_subscriptions' => 'Top Subscriptions',
        'payment_timeline' => 'Payment Timeline',
        'payment_calendar' => 'Payment Calendar',
        'upcoming_payments' => 'Upcoming Payments',
    ],
    'empty_states' => [
        'no_active_subscriptions' => 'No active subscriptions found',
        'no_upcoming_payments' => 'No upcoming payments',
        'all_set_message' => 'You\'re all set for the next 30 days!',
    ],
    'actions' => [
        'add_subscription' => [
            'label' => 'Add Subscription',
        ],
        'view_all' => [
            'label' => 'Manage Subscriptions',
        ],
        'edit' => 'Edit',
        'mark_as_paid' => 'Mark as Paid',
    ],
    'calendar' => [
        'subscription_density' => 'Subscription Density',
        'low' => 'Low',
        'medium' => 'Medium',
        'high' => 'High',
        'very_high' => 'Very High',
        'today' => 'Today',
        'next_30_days' => 'Next 30 days',
        'days_left' => ':count days left',
        'payment_calendar_legend' => 'Payment Calendar Legend',
    ],
    'charts' => [
        'monthly_payments' => 'Monthly Payments',
        'average_monthly_cost' => 'Average Monthly Cost',
        'monthly_cost' => 'Monthly Cost (€)',
        'per_month' => 'per month',
        'category' => 'Category:',
        'total_monthly' => 'Total Monthly',
        'above_average' => ':percentage% above average',
        'below_average' => ':percentage% below average',
        'equal_to_average' => 'Equal to average',
        'subscriptions' => 'Subscriptions:',
        'subscription' => 'subscription',
        'subscriptions_plural' => 'subscriptions',
    ],
    'payment_card' => [
        'days_left' => ':count days left',
    ],
    'modal' => [
        'close' => 'Close',
        'no_payments_scheduled' => 'No payments scheduled',
    ],
    'billing_cycles' => [
        'monthly' => 'Monthly',
        'quarterly' => 'Quarterly',
        'biannual' => 'Biannual',
        'annual' => 'Annual',
    ],
];

