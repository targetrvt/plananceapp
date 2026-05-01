<?php

return [
    'navigation' => [
        'label' => 'Goals Overview',
    ],
    'title' => 'Goals Overview',
    'stats' => [
        'total_target' => 'Total Target',
        'total_saved' => 'Total Saved',
        'remaining_to_goals' => 'Remaining to Goals',
        'overdue_goals' => 'Overdue Goals',
    ],
    'sections' => [
        'eyebrow' => 'Savings goals',
        'lede' => 'See how your savings stack up against every target date.',
        'goals_overview' => 'Each goal',
        'active_count' => ':count goals',
    ],
    'ribbon' => [
        'overall_label' => 'Saved vs all targets',
    ],
    'table' => [
        'target' => 'Target',
        'saved' => 'Saved',
        'remaining' => 'Remaining',
        'progress' => 'Progress',
        'days_to_deadline' => 'Days to deadline',
        'days_until_labels' => '{0} Due today|{1} 1 day left|[2,*]:count days left',
        'days_over_labels' => '{1} 1 day overdue|[2,*]:count days overdue',
        'days_when_complete' => '—',
    ],
    'empty' => [
        'title' => 'No goals yet',
        'description' => 'Create your first financial goal to track savings toward what matters.',
    ],
    'status' => [
        'in_progress' => 'In progress',
        'almost_there' => 'Almost there',
        'reached' => 'Reached',
        'overdue' => 'Overdue',
    ],
    'actions' => [
        'create_goal' => [
            'label' => 'New goal',
        ],
        'manage_goals' => [
            'label' => 'Manage goals',
        ],
        'edit_goal' => [
            'label' => 'Edit',
        ],
    ],
    'quick_add' => [
        'trigger_label' => 'Add money',
        'modal_heading' => 'Add money to goal',
        'modal_goal_label' => 'Goal',
        'modal_description' => 'This records a “Savings (goal)” expense and adds the amount to this goal.',
        'submit' => 'Add to goal',
        'amount_label' => 'Amount',
        'date_label' => 'Date',
        'note_label' => 'Note (optional)',
        'notification_title' => 'Contribution saved',
    ],
    'contribution' => [
        'default_description' => 'Savings contribution: :goal',
    ],
    'withdraw' => [
        'trigger_label' => 'Withdraw',
        'modal_heading' => 'Withdraw from goal',
        'modal_description' => 'This records a “Savings (goal)” income, reduces the amount saved in this goal, and returns the money to your balance.',
        'submit' => 'Withdraw from goal',
        'amount_label' => 'Amount to withdraw',
        'notification_title' => 'Withdrawal saved',
        'default_description' => 'Withdrawal from goal: :goal',
        'exceeds_saved' => 'That amount is more than you currently have saved in this goal.',
    ],
];
