<?php

return [
    'navigation' => [
        'label' => 'Mērķu pārskats',
    ],
    'title' => 'Mērķu pārskats',
    'stats' => [
        'total_target' => 'Kopējais mērķis',
        'total_saved' => 'Kopā uzkrāts',
        'remaining_to_goals' => 'Atlikušais līdz mērķiem',
        'overdue_goals' => 'Nokavētie mērķi',
    ],
    'sections' => [
        'eyebrow' => 'Uzkrājumu mērķi',
        'lede' => 'Pārskats pēc datuma: cik tuvu esi katram mērķim.',
        'goals_overview' => 'Katrs mērķis',
        'active_count' => ':count mērķi',
    ],
    'ribbon' => [
        'overall_label' => 'Uzkrāts pret visiem mērķiem',
    ],
    'table' => [
        'target' => 'Mērķa summa',
        'saved' => 'Uzkrāts',
        'remaining' => 'Atlikums',
        'progress' => 'Progress',
        'days_to_deadline' => 'Līdz termiņam',
        'days_until_labels' => '{0} Termiņš šodien|{1} Atlikusi 1 diena|[2,*]Atlikušas :count dienas',
        'days_over_labels' => '{1} Nokavēts par 1 dienu|[2,*]Nokavēts par :count dienām',
        'days_when_complete' => '—',
    ],
    'empty' => [
        'title' => 'Vēl nav mērķu',
        'description' => 'Izveido savu pirmo finanšu mērķi, lai sekotu uzkrājumiem pret svarīgām summām.',
    ],
    'status' => [
        'in_progress' => 'Procesā',
        'almost_there' => 'Gandrīz sasniegts',
        'reached' => 'Sasniegts',
        'overdue' => 'Nokavēts',
    ],
    'actions' => [
        'create_goal' => [
            'label' => 'Jauns mērķis',
        ],
        'manage_goals' => [
            'label' => 'Pārvaldīt mērķus',
        ],
        'edit_goal' => [
            'label' => 'Labot',
        ],
    ],
    'quick_add' => [
        'trigger_label' => 'Pievienot naudu',
        'modal_heading' => 'Naudas iemaksa mērķī',
        'modal_goal_label' => 'Mērķis',
        'modal_description' => 'Tiek ierakstīts izdevums kategorijā „Uzkrājums (mērķis)” un summa tiek pieskaitīta šim mērķim.',
        'submit' => 'Pievienot mērķim',
        'amount_label' => 'Summa',
        'date_label' => 'Datums',
        'note_label' => 'Piezīme (neobligāti)',
        'notification_title' => 'Iemaksa saglabāta',
    ],
    'contribution' => [
        'default_description' => 'Uzkrājums mērķim: :goal',
    ],
    'withdraw' => [
        'trigger_label' => 'Izņemt',
        'modal_heading' => 'Izņemšana no mērķa',
        'modal_description' => 'Tiek ierakstīts ienākums kategorijā „Uzkrājums (mērķis)”, samazinās mērķa uzkrājums un summa atgriežas bilancē.',
        'submit' => 'Izņemt no mērķa',
        'amount_label' => 'Izņemjamā summa',
        'notification_title' => 'Izņemšana saglabāta',
        'default_description' => 'Izņemšana no mērķa: :goal',
        'exceeds_saved' => 'Summa pārsniedz mērķī pašlaik uzkrāto.',
    ],
];
