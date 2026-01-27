<?php

return [
    'navigation' => [
        'label' => 'Transactions',
        'group' => 'Management',
    ],
    'form' => [
        'receipt_upload' => [
            'section' => 'Receipt Upload',
            'upload_receipt' => [
                'label' => 'Upload Receipt',
                'helper' => 'Upload a receipt image to automatically extract transaction details',
            ],
        ],
        'transaction_details' => [
            'section' => 'Transaction Details',
            'type' => [
                'label' => 'Type',
                'options' => [
                    'income' => 'Income',
                    'expense' => 'Expense',
                ],
            ],
            'amount' => [
                'label' => 'Amount',
            ],
            'date' => [
                'label' => 'Date',
            ],
            'category' => [
                'label' => 'Category',
            ],
            'description' => [
                'label' => 'Description',
            ],
        ],
    ],
    'table' => [
        'date' => [
            'label' => 'Date',
        ],
        'type' => [
            'label' => 'Type',
        ],
        'amount' => [
            'label' => 'Amount',
        ],
        'category' => [
            'label' => 'Category',
        ],
        'description' => [
            'label' => 'Description',
        ],
    ],
    'filter' => [
        'type' => [
            'label' => 'Type',
            'options' => [
                'income' => 'Income',
                'expense' => 'Expense',
            ],
        ],
    ],
    'actions' => [
        'create' => [
            'label' => 'New Transaction',
        ],
        'view_receipt' => [
            'label' => 'View Receipt',
        ],
    ],
];

