<?php

return [
    'navigation' => [
        'label' => 'Abonementu pārskats',
        'group' => 'Pārskats',
    ],
    'title' => 'Abonementu pārskats',
    'stats' => [
        'monthly_spending' => [
            'label' => 'Mēneša izdevumi',
            'description' => 'Gada ekvivalents:',
        ],
        'active_subscriptions' => [
            'label' => 'Aktīvie abonementi',
            'description' => 'Vidējās mēneša izmaksas:',
        ],
        'upcoming_payments' => [
            'label' => 'Gaidāmie maksājumi',
            'description' => 'Kopējā gaidāmā summa:',
        ],
    ],
    'sections' => [
        'subscription_categories' => 'Abonementu kategorijas',
        'top_subscriptions' => 'Top abonementi',
        'payment_timeline' => 'Maksājumu laika grafiks',
        'payment_calendar' => 'Maksājumu kalendārs',
        'upcoming_payments' => 'Gaidāmie maksājumi',
    ],
    'empty_states' => [
        'no_active_subscriptions' => 'Nav atrasti aktīvie abonementi',
        'no_upcoming_payments' => 'Nav gaidāmo maksājumu',
        'all_set_message' => 'Viss ir kārtībā nākamajās 30 dienās!',
    ],
    'actions' => [
        'add_subscription' => [
            'label' => 'Pievienot abonementu',
        ],
        'view_all' => [
            'label' => 'Pārvaldīt abonementus',
        ],
        'edit' => 'Rediģēt',
        'mark_as_paid' => 'Atzīmēt kā samaksāts',
    ],
    'calendar' => [
        'subscription_density' => 'Abonementu blīvums',
        'low' => 'Zems',
        'medium' => 'Vidējs',
        'high' => 'Augsts',
        'very_high' => 'Ļoti augsts',
        'today' => 'Šodien',
        'next_30_days' => 'Nākamās 30 dienas',
        'days_left' => 'Atlikušas :count dienas',
        'payment_calendar_legend' => 'Maksājumu kalendāra leģenda',
    ],
    'charts' => [
        'monthly_payments' => 'Mēneša maksājumi',
        'average_monthly_cost' => 'Vidējās mēneša izmaksas',
        'monthly_cost' => 'Mēneša izmaksas (€)',
        'per_month' => 'mēnesī',
        'category' => 'Kategorija:',
        'total_monthly' => 'Kopā mēnesī',
        'above_average' => ':percentage% virs vidējā',
        'below_average' => ':percentage% zem vidējā',
        'equal_to_average' => 'Vienāds ar vidējo',
        'subscriptions' => 'Abonementi:',
        'subscription' => 'abonements',
        'subscriptions_plural' => 'abonementi',
    ],
    'payment_card' => [
        'days_left' => 'Atlikušas :count dienas',
    ],
    'modal' => [
        'close' => 'Aizvērt',
        'no_payments_scheduled' => 'Nav ieplānotu maksājumu',
    ],
    'billing_cycles' => [
        'monthly' => 'Mēneša',
        'quarterly' => 'Ceturkšņa',
        'biannual' => 'Pusgada',
        'annual' => 'Gada',
    ],
];

