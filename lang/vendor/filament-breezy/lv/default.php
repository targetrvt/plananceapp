<?php

return [
    'password_confirm' => [
        'heading' => 'Apstiprināt paroli',
        'description' => 'Lūdzu, apstipriniet savu paroli, lai pabeigtu šo darbību.',
        'current_password' => 'Pašreizējā parole',
    ],
    'two_factor' => [
        'heading' => 'Divfaktoru autentifikācijas izaicinājums',
        'description' => 'Lūdzu, apstipriniet piekļuvi savam kontam, ievadot kodu, ko sniedz jūsu autentifikācijas lietotne.',
        'code_placeholder' => 'XXX-XXX',
        'recovery' => [
            'heading' => 'Divfaktoru autentifikācijas izaicinājums',
            'description' => 'Lūdzu, apstipriniet piekļuvi savam kontam, ievadot vienu no jūsu avārijas atjaunošanas kodiem.',
        ],
        'recovery_code_placeholder' => 'abcdef-98765',
        'recovery_code_text' => 'Pazaudēta ierīce?',
        'recovery_code_link' => 'Izmantot atjaunošanas kodu',
        'back_to_login_link' => 'Atgriezties pie pieteikšanās',
    ],
    'profile' => [
        'account' => 'Konts',
        'profile' => 'Profils',
        'my_profile' => 'Mans profils',
        'subheading' => 'Pārvaldiet savu lietotāja profilu šeit.',
        'personal_info' => [
            'heading' => 'Personīgā informācija',
            'subheading' => 'Pārvaldiet savu personīgo informāciju.',
            'submit' => [
                'label' => 'Atjaunināt',
            ],
            'notify' => 'Profils veiksmīgi atjaunināts!',
        ],
        'password' => [
            'heading' => 'Parole',
            'subheading' => 'Jābūt vismaz 8 rakstzīmēm garai.',
            'submit' => [
                'label' => 'Atjaunināt',
            ],
            'notify' => 'Parole veiksmīgi atjaunināta!',
        ],
        '2fa' => [
            'title' => 'Divfaktoru autentifikācija',
            'description' => 'Pārvaldiet divfaktoru autentifikāciju savam kontam (ieteicams).',
            'actions' => [
                'enable' => 'Iespējot',
                'regenerate_codes' => 'Pārveidot atjaunošanas kodus',
                'disable' => 'Atspējot',
                'confirm_finish' => 'Apstiprināt un pabeigt',
                'cancel_setup' => 'Atcelt iestatīšanu',
            ],
            'setup_key' => 'Iestatīšanas atslēga',
            'must_enable' => 'Jums jāiespējo divfaktoru autentifikācija, lai izmantotu šo lietotni.',
            'not_enabled' => [
                'title' => 'Jūs neesat iespējojis divfaktoru autentifikāciju.',
                'description' => 'Kad divfaktoru autentifikācija ir iespējota, autentifikācijas laikā jums tiks prasīts drošs, nejaušs žetons. Jūs varat izmantot autentifikācijas lietotnes savā viedtālrunī, piemēram, Google Authenticator, Microsoft Authenticator utt., lai to atvieglotu',
            ],
            'finish_enabling' => [
                'title' => 'Pabeidziet divfaktoru autentifikācijas iespējošanu.',
                'description' => "Lai pabeigtu divfaktoru autentifikācijas iespējošanu, skenējiet šādu QR kodu, izmantojot sava tālruņa autentifikācijas lietotni, vai ievadiet iestatīšanas atslēgu un norādiet ģenerēto OTP kodu.",
            ],
            'enabled' => [
                'notify' => 'Divfaktoru autentifikācija iespējota.',
                'title' => 'Jūs esat iespējojis divfaktoru autentifikāciju!',
                'description' => 'Divfaktoru autentifikācija tagad ir iespējota. Tas palīdz padarīt jūsu kontu drošāku.',
                'store_codes' => 'Šos kodus var izmantot, lai atjaunotu piekļuvi savam kontam, ja jūsu ierīce ir pazaudēta. Brīdinājums! Šie kodi tiks parādīti tikai vienu reizi.',
            ],
            'disabling' => [
                'notify' => 'Divfaktoru autentifikācija ir atspējota.',
            ],
            'regenerate_codes' => [
                'notify' => 'Ir ģenerēti jauni atjaunošanas kodi.',
            ],
            'confirmation' => [
                'success_notification' => 'Kods apstiprināts. Divfaktoru autentifikācija iespējota.',
                'invalid_code' => 'Ievadītais kods nav derīgs.',
            ],
        ],
        'sanctum' => [
            'title' => 'API žetoni',
            'description' => 'Pārvaldiet API žetonus, kas ļauj trešās puses pakalpojumiem piekļūt šai lietotnei jūsu vārdā.',
            'create' => [
                'notify' => 'Žetons veiksmīgi izveidots!',
                'message' => 'Jūsu žetons tiek parādīts tikai vienu reizi pēc izveides. Ja pazaudēsit savu žetonu, jums būs jādzēš tas un jāizveido jauns.',
                'submit' => [
                    'label' => 'Izveidot',
                ],
            ],
            'update' => [
                'notify' => 'Žetons veiksmīgi atjaunināts!',
            ],
            'copied' => [
                'label' => 'Esmu nokopējis savu žetonu',
            ],
        ],
    ],
    'clipboard' => [
        'link' => 'Kopēt starpliktuvē',
        'tooltip' => 'Nokopēts!',
    ],
    'fields' => [
        'avatar' => 'Avatārs',
        'email' => 'E-pasts',
        'login' => 'Pieteikšanās',
        'name' => 'Vārds',
        'password' => 'Parole',
        'password_confirm' => 'Apstiprināt paroli',
        'new_password' => 'Jauna parole',
        'new_password_confirmation' => 'Apstiprināt paroli',
        'token_name' => 'Žetona nosaukums',
        'token_expiry' => 'Žetona derīguma termiņš',
        'abilities' => 'Spējas',
        '2fa_code' => 'Kods',
        '2fa_recovery_code' => 'Atjaunošanas kods',
        'created' => 'Izveidots',
        'expires' => 'Beidzas',
    ],
    'or' => 'Vai',
    'cancel' => 'Atcelt',
];

