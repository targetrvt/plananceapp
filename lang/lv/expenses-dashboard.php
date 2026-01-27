<?php

return [
    'navigation' => [
        'label' => 'Izdevumu pārskats',
        'group' => 'Pārskats',
    ],
    'title' => 'Izdevumu pārskats',
    'form' => [
        'new_expense' => [
            'section' => 'Jauns izdevums',
            'amount' => [
                'label' => 'Summa',
                'placeholder' => '0.00',
            ],
            'date' => [
                'label' => 'Datums',
            ],
            'category' => [
                'label' => 'Kategorija',
            ],
            'description' => [
                'label' => 'Apraksts',
                'placeholder' => 'Izdevuma apraksts',
            ],
        ],
    ],
    'actions' => [
        'quick_add' => [
            'label' => 'Ātrā pievienošana',
            'modal_heading' => 'Pievienot jaunu izdevumu',
            'modal_description' => 'Ātri pievienojiet jaunu izdevumu saviem ierakstiem.',
            'submit_label' => 'Saglabāt izdevumu',
        ],
        'add_expense' => [
            'label' => 'Pievienot izdevumu',
        ],
        'filter' => [
            'label' => 'Filtrs',
            'modal_heading' => 'Filtrēt pārskatu',
            'timeframe' => [
                'label' => 'Laika ietvars',
            ],
            'start_date' => [
                'label' => 'Sākuma datums',
            ],
            'end_date' => [
                'label' => 'Beigu datums',
            ],
            'category' => [
                'label' => 'Kategorija',
            ],
            'reset' => [
                'label' => 'Atiestatīt filtrus',
            ],
        ],
    ],
    'notifications' => [
        'expense_added' => 'Izdevums veiksmīgi pievienots',
        'filters_applied' => 'Filtri veiksmīgi piemēroti',
        'filters_reset' => 'Filtri atiestatīti uz noklusējumiem',
    ],
    'infolist' => [
        'date' => [
            'label' => 'Datums',
        ],
        'amount' => [
            'label' => 'Summa',
        ],
        'category' => [
            'label' => 'Kategorija',
        ],
        'description' => [
            'label' => 'Apraksts',
        ],
    ],
];

