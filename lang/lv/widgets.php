<?php

return [
    'user_balance' => [
        'current_balance' => [
            'label' => 'Pašreizējā bilance',
            'description' => 'Jūsu pieejamie līdzekļi',
        ],
        'total_income' => [
            'label' => 'Kopējie ienākumi',
            'description' => 'Visu laiku ienākumi',
        ],
        'total_expenses' => [
            'label' => 'Kopējie izdevumi',
            'description' => 'Visu laiku izdevumi',
        ],
    ],
    'expenses_summary' => [
        'monthly_expenses' => [
            'label' => 'Mēneša izdevumi',
            'increase' => ':percentage% pieaugums no pagājušā mēneša',
            'decrease' => ':percentage% samazinājums no pagājušā mēneša',
        ],
        'yesterday_expenses' => [
            'label' => 'Vakardienas izdevumi',
            'description' => 'uz :date',
        ],
        'top_category' => [
            'label' => 'Galvenā izdevumu kategorija',
            'no_expenses' => 'Nav izdevumu',
            'description' => 'Šajā mēnesī',
        ],
    ],
    'expense_trends' => [
        'heading' => 'Izdevumu tendences',
        'description' => 'Jūsu mēneša izdevumu pārskats pēdējiem 6 mēnešiem. Noklikšķiniet uz datu punkta, lai redzētu detaļas.',
        'monthly_expenses' => 'Mēneša izdevumi',
        'average' => 'Vidēji',
        'notification' => [
            'title' => ':month Izdevumu detaļas',
            'total_expenses' => 'Kopējie izdevumi: **€:amount**',
            'transactions' => 'Darījumi: **:count**',
            'top_categories' => 'Galvenās kategorijas:',
            'no_expenses' => 'Šajā mēnesī nav reģistrēti izdevumi.',
        ],
    ],
];

