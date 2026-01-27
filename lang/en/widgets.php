<?php

return [
    'user_balance' => [
        'current_balance' => [
            'label' => 'Current Balance',
            'description' => 'Your available funds',
        ],
        'total_income' => [
            'label' => 'Total Income',
            'description' => 'All time income',
        ],
        'total_expenses' => [
            'label' => 'Total Expenses',
            'description' => 'All time expenses',
        ],
    ],
    'expenses_summary' => [
        'monthly_expenses' => [
            'label' => 'Monthly Expenses',
            'increase' => ':percentage% increase from last month',
            'decrease' => ':percentage% decrease from last month',
        ],
        'yesterday_expenses' => [
            'label' => 'Yesterday\'s Expenses',
            'description' => 'on :date',
        ],
        'top_category' => [
            'label' => 'Top Expense Category',
            'no_expenses' => 'No expenses',
            'description' => 'This month',
        ],
    ],
    'expense_trends' => [
        'heading' => 'Expense Trends',
        'description' => 'Your monthly expense overview for the past 6 months. Click on a data point to see details.',
        'monthly_expenses' => 'Monthly Expenses',
        'average' => 'Average',
        'notification' => [
            'title' => ':month Expense Details',
            'total_expenses' => 'Total Expenses: **€:amount**',
            'transactions' => 'Transactions: **:count**',
            'top_categories' => 'Top Categories:',
            'no_expenses' => 'No expenses recorded for this month.',
        ],
    ],
];

