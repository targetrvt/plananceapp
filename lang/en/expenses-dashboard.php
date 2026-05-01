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
    'ai_tips' => [
        'card_title' => 'AI ideas for this period',
        'card_description' => 'Uses this period’s expenses (with your category filter) plus all income in the same dates. Not financial advice.',
        'generate' => 'Generate savings tips',
        'generating' => 'Generating…',
        'empty_hint' => 'Tips appear here after you generate them. You can change the period or category and run again.',
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
