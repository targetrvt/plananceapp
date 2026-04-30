<?php

return [
    'navigation' => [
        'label' => 'Budgets Overview',
        'group' => 'Overview',
    ],
    'title' => 'Budgets Overview',
    'stats' => [
        'total_budgeted' => 'Total Budgeted',
        'total_spent' => 'Total Spent',
        'total_remaining' => 'Total Remaining',
        'exceeded_budgets' => 'Exceeded Budgets',
    ],
    'sections' => [
        'budgets_overview' => 'Budget Usage',
        'active_count' => ':count budgets',
    ],
    'table' => [
        'budget' => 'Budget',
        'spent' => 'Spent',
        'remaining' => 'Remaining',
        'usage' => 'Usage',
    ],
    'empty' => [
        'title' => 'No budgets yet',
        'description' => 'Create your first budget to start tracking your spending limits.',
    ],
    'status' => [
        'ok' => 'Healthy',
        'warning' => 'Approaching limit',
        'over_limit' => 'Over limit',
    ],
    'actions' => [
        'create_budget' => [
            'label' => 'Create Budget',
        ],
        'manage_budgets' => [
            'label' => 'Manage Budgets',
        ],
    ],
];
