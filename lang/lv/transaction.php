<?php

return [
    'navigation' => [
        'label' => 'Darījumi',
        'group' => 'Pārvaldība',
    ],
    'form' => [
        'receipt_upload' => [
            'section' => 'Čeka augšupielāde',
            'upload_receipt' => [
                'label' => 'Augšupielādēt čeku',
                'helper' => 'Augšupielādējiet čeka attēlu, lai automātiski izvilktu darījuma detaļas',
            ],
        ],
        'transaction_details' => [
            'section' => 'Darījuma detaļas',
            'type' => [
                'label' => 'Tips',
                'options' => [
                    'income' => 'Ienākumi',
                    'expense' => 'Izdevumi',
                ],
            ],
            'amount' => [
                'label' => 'Summa',
            ],
            'date' => [
                'label' => 'Datums',
            ],
            'category' => [
                'label' => 'Kategorija',
            ],
            'description' => [
                'label' => 'Apraksts',
            ],
        ],
    ],
    'table' => [
        'date' => [
            'label' => 'Datums',
        ],
        'type' => [
            'label' => 'Tips',
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
    'filter' => [
        'type' => [
            'label' => 'Tips',
            'options' => [
                'income' => 'Ienākumi',
                'expense' => 'Izdevumi',
            ],
        ],
    ],
    'actions' => [
        'create' => [
            'label' => 'Jauns darījums',
        ],
        'view_receipt' => [
            'label' => 'Skatīt čeku',
        ],
    ],
];

