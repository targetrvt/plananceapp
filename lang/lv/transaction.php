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
            'financial_goal' => [
                'label' => 'Mērķis',
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
    'import' => [
        'action_label' => 'Importēt no faila',
        'modal_heading' => 'Darījumu imports',
        'modal_description' => 'Augšupielādējiet CSV, Excel tabulu vai izraksta PDF (piem., bankā). CSV/Excel vajag vismaz datumu un summu kolonnas (Debet/Kredits vai Tips uzlabos precizitāti). PDF izmanto teksta izvilkumu un AI, lai veidotu darījumus.',
        'file_label' => 'Fails',
        'helper' => 'Atbalstītās kolonnu nosaukumu nozīmes: Date/datums/summa/amount/type/tips, apraksts, debet/debit-kredīta kredīt. Kategorijas atslēgas var būt arī angļu valodā. Mērķa „savings\" iemaksas no faila nepiesaista mērķim drošības dēļ.',
        'messages' => [
            'success_title' => 'Imports pabeigts',
            'failed_title' => 'Imports neizdevās',
            'summary' => 'Importēti :imported rindiņas; izlaistas :skipped.',
            'unreadable' => 'Failu neizdevās nolasīt.',
            'openai_missing' => 'Nav OpenAI API atslēgas—PDF importam tā nepieciešama.',
            'pdf_empty_text' => 'No šī PDF neizdevās izvilkt tekstu (mēģiniet bankā eksportēt CSV vai citu izrakstu PDF).',
            'ai_failed' => 'Šobrīd neizdevās ar AI apstrādāt PDF.',
            'ai_invalid_json' => 'AI atbilde bija nederīga; samaziniet failu vai izmantojiet bankas CSV.',
            'row_invalid_numbered' => ':n. rinda izlaista (trūkst datuma/summas vai tipa).',
            'row_save_failed' => ':n. rindu neizdevās saglabāt.',
        ],
    ],
    'export' => [
        'bulk_csv_label' => 'Eksports CSV',
    ],
];
