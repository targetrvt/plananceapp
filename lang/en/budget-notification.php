<?php

return [
    'mail' => [
        'subject' => 'Budget limit reached',
        'limit_reached' => 'Budget #:id (:name) reached 100% of its limit.',
        'budget_amount' => 'Budget amount: EUR :amount',
        'spent_amount' => 'Spent amount: EUR :amount',
        'open_budgets' => 'Open budgets',
    ],
    'in_app' => [
        'exceeded_title' => 'Budget exceeded',
        'warning_title' => 'Budget almost reached',
        'exceeded_message' => 'Budget #:id (:name) reached 100%.',
        'warning_message' => 'Budget #:id (:name) reached 90%.',
    ],
];
