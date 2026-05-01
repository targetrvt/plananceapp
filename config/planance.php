<?php

return [
    /**
     * Shown when AI features are gated off for the account (request access manually).
     */
    'contact_ai_email' => env('PLANANCE_CONTACT_AI_EMAIL', 'plananceapp@gmail.com'),

    /** Used by DatabaseSeeder (reads via config() so it works after `config:cache`). */
    'admin_email' => env('ADMIN_EMAIL', ''),
    'admin_password' => env('ADMIN_PASSWORD', ''),
];
