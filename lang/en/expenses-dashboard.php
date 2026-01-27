<?php

return [
    'navigation' => [
        'label' => 'Expenses Dashboard',
        'group' => 'Overview',
    ],
    'title' => 'Expenses Dashboard',
    'form' => [
        'new_expense' => [
            'section' => 'New Expense',
            'amount' => [
                'label' => 'Amount',
                'placeholder' => '0.00',
            ],
            'date' => [
                'label' => 'Date',
            ],
            'category' => [
                'label' => 'Category',
            ],
            'description' => [
                'label' => 'Description',
                'placeholder' => 'Expense description',
            ],
        ],
    ],
    'actions' => [
        'quick_add' => [
            'label' => 'Quick Add',
            'modal_heading' => 'Add New Expense',
            'modal_description' => 'Quickly add a new expense to your records.',
            'submit_label' => 'Save Expense',
        ],
        'add_expense' => [
            'label' => 'Add Expense',
        ],
        'filter' => [
            'label' => 'Filter',
            'modal_heading' => 'Filter Dashboard',
            'timeframe' => [
                'label' => 'Timeframe',
            ],
            'start_date' => [
                'label' => 'Start Date',
            ],
            'end_date' => [
                'label' => 'End Date',
            ],
            'category' => [
                'label' => 'Category',
            ],
            'reset' => [
                'label' => 'Reset Filters',
            ],
        ],
    ],
    'notifications' => [
        'expense_added' => 'Expense added successfully',
        'filters_applied' => 'Filters applied successfully',
        'filters_reset' => 'Filters reset to defaults',
    ],
    'infolist' => [
        'date' => [
            'label' => 'Date',
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
];

