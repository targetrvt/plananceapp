<?php

return [
    'navigation' => [
        'label' => 'Income Dashboard',
        'group' => 'Overview',
    ],
    'title' => 'Income Dashboard',
    'form' => [
        'new_income' => [
            'section' => 'New Income',
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
                'placeholder' => 'Income description',
            ],
        ],
    ],
    'actions' => [
        'quick_add' => [
            'label' => 'Quick Add',
            'modal_heading' => 'Add New Income',
            'modal_description' => 'Quickly add a new income entry to your records.',
            'submit_label' => 'Save Income',
        ],
        'add_income' => [
            'label' => 'Add Income',
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
        'income_added' => 'Income added successfully',
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
