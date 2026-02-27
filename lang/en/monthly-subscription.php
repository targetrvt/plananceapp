<?php

return [
    'navigation' => [
        'label' => 'Subscriptions',
        'group' => 'Management',
    ],
    'form' => [
        'subscription_details' => [
            'section' => 'Subscription Details',
            'section_description' => 'Enter the basic information about this subscription',
            'name' => [
                'label' => 'Subscription Name',
                'placeholder' => 'Netflix, Spotify, etc.',
            ],
            'category' => [
                'label' => 'Category',
            ],
            'description' => [
                'label' => 'Description',
                'placeholder' => 'Additional details about this subscription',
            ],
        ],
        'billing_information' => [
            'section' => 'Billing Information',
            'description' => 'Manage payment details and billing schedule',
            'amount' => [
                'label' => 'Billing Amount',
                'helper' => 'The amount charged for this billing cycle',
                'placeholder' => '0.00',
            ],
            'billing_cycle' => [
                'label' => 'Billing Cycle',
                'helper' => 'Monthly equivalent: €:amount',
                'options' => [
                    'monthly' => 'Monthly',
                    'quarterly' => 'Quarterly (Every 3 Months)',
                    'biannual' => 'Biannual (Every 6 Months)',
                    'annual' => 'Annual (Yearly)',
                ],
            ],
            'billing_date' => [
                'label' => 'Next Billing Date',
                'helper' => 'When is the next payment due?',
            ],
            'status' => [
                'label' => 'Status',
                'options' => [
                    'paid' => 'Paid',
                    'unpaid' => 'Unpaid',
                ],
            ],
            'last_paid_date' => [
                'label' => 'Last Payment Date',
            ],
        ],
        'advanced_options' => [
            'section' => 'Advanced Options',
            'is_active' => [
                'label' => 'Active Subscription',
                'helper' => 'Inactive subscriptions won\'t appear in reports',
            ],
            'auto_create_transaction' => [
                'label' => 'Auto-create Transaction',
                'helper' => 'Automatically create a transaction when payment is due',
            ],
            'start_date' => [
                'label' => 'Start Date',
                'helper' => 'When did this subscription begin?',
            ],
            'end_date' => [
                'label' => 'End Date',
                'helper' => 'Optional: Leave empty for ongoing subscriptions',
            ],
        ],
    ],
    'table' => [
        'name' => [
            'label' => 'Name',
        ],
        'amount' => [
            'label' => 'Amount',
            'monthly_equivalent' => '€:amount/mo',
        ],
        'billing_date' => [
            'label' => 'Next Payment',
            'paid' => 'Paid',
            'overdue' => 'Overdue!',
            'due_today' => 'Due today!',
            'days_left' => ':days days left',
        ],
        'category' => [
            'label' => 'Category',
        ],
        'is_active' => [
            'label' => 'Active',
        ],
        'status' => [
            'label' => 'Paid',
        ],
        'billing_cycle' => [
            'label' => 'Cycle',
        ],
    ],
    'filter' => [
        'category' => [
            'label' => 'Category',
            'indicator' => 'Category',
        ],
        'active' => [
            'label' => 'Active Subscriptions',
        ],
        'status' => [
            'label' => 'Payment Status',
            'indicator' => 'Status',
            'options' => [
                'paid' => 'Paid',
                'unpaid' => 'Unpaid',
            ],
        ],
        'upcoming' => [
            'label' => 'Due in 30 days',
            'indicator' => 'Due Soon',
        ],
    ],
    'actions' => [
        'create' => [
            'label' => 'New Monthly Subscription',
        ],
        'toggle_paid' => [
            'mark_paid' => 'Mark Paid',
            'mark_unpaid' => 'Mark Unpaid',
        ],
        'duplicate' => [
            'label' => 'Duplicate',
        ],
        'calculate_total' => [
            'label' => 'Monthly Total',
            'modal_heading' => 'Subscription Cost Summary',
            'close' => 'Close',
        ],
        'add_subscription' => [
            'label' => 'Add Subscription',
        ],
    ],
    'notifications' => [
        'payment_recorded' => 'Payment recorded! Next payment: :date',
        'marked_unpaid' => 'Subscription marked as unpaid',
        'duplicated' => 'Subscription duplicated',
        'marked_paid' => 'Subscriptions marked as paid',
        'marked_unpaid_bulk' => 'Subscriptions marked as unpaid',
        'set_inactive' => 'Subscriptions set as inactive',
        'set_active' => 'Subscriptions set as active',
    ],
    'empty_state' => [
        'heading' => 'No subscriptions yet',
        'description' => 'Start tracking your recurring payments by adding your first subscription.',
    ],
];

