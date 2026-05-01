<?php

return [
    'navigation' => [
        'label' => 'Ienākumu pārskats',
        'group' => 'Pārskats',
    ],
    'title' => 'Ienākumu pārskats',
    'form' => [
        'new_income' => [
            'section' => 'Jauns ienākums',
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
                'placeholder' => 'Ienākuma apraksts',
            ],
        ],
    ],
    'actions' => [
        'quick_add' => [
            'label' => 'Ātrā pievienošana',
            'modal_heading' => 'Pievienot jaunu ienākumu',
            'modal_description' => 'Ātri pievienojiet jaunu ienākumu ierakstam.',
            'submit_label' => 'Saglabāt ienākumu',
        ],
        'add_income' => [
            'label' => 'Pievienot ienākumu',
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
        'income_added' => 'Ienākums veiksmīgi pievienots',
        'filters_applied' => 'Filtri veiksmīgi piemēroti',
        'filters_reset' => 'Filtri atiestatīti uz noklusējumiem',
    ],
    'ai_tips' => [
        'card_title' => 'Šī perioda AI ieskati',
        'card_description' => 'Izmanto šī perioda ienākumus (ar jūsu kategorijas filtru) un visus izdevumus tajos pašos datumos. Nav finanšu konsultācija.',
        'generate' => 'Ģenerēt ienākumu padomus',
        'generating' => 'Ģenerē…',
        'empty_hint' => 'Pēc ģenerēšanas padomi parādīsies šeit. Mainiet periodu vai kategoriju un mēģiniet vēlreiz.',
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
