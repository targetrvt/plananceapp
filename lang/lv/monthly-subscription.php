<?php

return [
    'navigation' => [
        'label' => 'Abonementi',
        'group' => 'Pārvaldība',
    ],
    'form' => [
        'subscription_details' => [
            'section' => 'Abonementa detaļas',
            'section_description' => 'Ievadiet pamatinformāciju par šo abonementu',
            'name' => [
                'label' => 'Abonementa nosaukums',
                'placeholder' => 'Netflix, Spotify, utt.',
            ],
            'category' => [
                'label' => 'Kategorija',
            ],
            'description' => [
                'label' => 'Apraksts',
                'placeholder' => 'Papildu informācija par šo abonementu',
            ],
        ],
        'billing_information' => [
            'section' => 'Norēķinu informācija',
            'description' => 'Pārvaldiet maksājumu detaļas un norēķinu grafiku',
            'amount' => [
                'label' => 'Norēķinu summa',
                'helper' => 'Summa, kas tiek iekasēta šim norēķinu ciklam',
                'placeholder' => '0.00',
            ],
            'billing_cycle' => [
                'label' => 'Norēķinu cikls',
                'helper' => 'Mēneša ekvivalents: €:amount',
                'options' => [
                    'monthly' => 'Mēnesī',
                    'quarterly' => 'Ceturksnī (ik 3 mēneši)',
                    'biannual' => 'Pusgadā (ik 6 mēneši)',
                    'annual' => 'Gadā (gadā)',
                ],
            ],
            'billing_date' => [
                'label' => 'Nākamais norēķinu datums',
                'helper' => 'Kad ir nākamais maksājums?',
            ],
            'status' => [
                'label' => 'Statuss',
                'options' => [
                    'paid' => 'Samaksāts',
                    'unpaid' => 'Nav samaksāts',
                ],
            ],
            'last_paid_date' => [
                'label' => 'Pēdējā maksājuma datums',
            ],
        ],
        'advanced_options' => [
            'section' => 'Papildu opcijas',
            'is_active' => [
                'label' => 'Aktīvs abonements',
                'helper' => 'Neaktīvi abonementi neparādīsies ziņojumos',
            ],
            'auto_create_transaction' => [
                'label' => 'Automātiski izveidot darījumu',
                'helper' => 'Automātiski izveidot darījumu, kad maksājums ir termiņā',
            ],
            'start_date' => [
                'label' => 'Sākuma datums',
                'helper' => 'Kad sākās šis abonements?',
            ],
            'end_date' => [
                'label' => 'Beigu datums',
                'helper' => 'Neobligāti: Atstājiet tukšu nepārtrauktiem abonementiem',
            ],
        ],
    ],
    'table' => [
        'name' => [
            'label' => 'Nosaukums',
        ],
        'amount' => [
            'label' => 'Summa',
            'monthly_equivalent' => '€:amount/mēn',
        ],
        'billing_date' => [
            'label' => 'Nākamais maksājums',
            'overdue' => 'Nokavēts!',
            'due_today' => 'Jāmaksā šodien!',
            'days_left' => ':days dienas atlikušas',
        ],
        'category' => [
            'label' => 'Kategorija',
        ],
        'is_active' => [
            'label' => 'Aktīvs',
        ],
        'status' => [
            'label' => 'Samaksāts',
        ],
        'billing_cycle' => [
            'label' => 'Cikls',
        ],
    ],
    'filter' => [
        'category' => [
            'label' => 'Kategorija',
            'indicator' => 'Kategorija',
        ],
        'active' => [
            'label' => 'Aktīvie abonementi',
        ],
        'status' => [
            'label' => 'Maksājuma statuss',
            'indicator' => 'Statuss',
            'options' => [
                'paid' => 'Samaksāts',
                'unpaid' => 'Nav samaksāts',
            ],
        ],
        'upcoming' => [
            'label' => 'Termiņš 30 dienās',
            'indicator' => 'Drīzumā termiņš',
        ],
    ],
    'actions' => [
        'create' => [
            'label' => 'Jauns mēneša abonements',
        ],
        'toggle_paid' => [
            'mark_paid' => 'Atzīmēt kā samaksātu',
            'mark_unpaid' => 'Atzīmēt kā nesamaksātu',
        ],
        'duplicate' => [
            'label' => 'Dublēt',
        ],
        'calculate_total' => [
            'label' => 'Mēneša kopā',
            'modal_heading' => 'Abonementu izmaksu kopsavilkums',
            'close' => 'Aizvērt',
        ],
        'add_subscription' => [
            'label' => 'Pievienot abonementu',
        ],
    ],
    'notifications' => [
        'payment_recorded' => 'Maksājums reģistrēts! Nākamais maksājums: :date',
        'marked_unpaid' => 'Abonements atzīmēts kā nesamaksāts',
        'duplicated' => 'Abonements dublēts',
        'marked_paid' => 'Abonementi atzīmēti kā samaksāti',
        'marked_unpaid_bulk' => 'Abonementi atzīmēti kā nesamaksāti',
        'set_inactive' => 'Abonementi iestatīti kā neaktīvi',
        'set_active' => 'Abonementi iestatīti kā aktīvi',
    ],
    'empty_state' => [
        'heading' => 'Vēl nav abonementu',
        'description' => 'Sāciet izsekot saviem atkārtotajiem maksājumiem, pievienojot savu pirmo abonementu.',
    ],
];

