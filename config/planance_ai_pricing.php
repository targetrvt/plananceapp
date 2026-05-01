<?php

/** @var float Bank-style USD→EUR for estimates (OpenAI invoices in USD; Planance budgets in EUR). */
$usdToEur = (float) env('PLANANCE_AI_USD_TO_EUR', 0.93);

/*
| USD and EUR per million tokens — update to match OpenAI list prices.
| gpt-4o-mini EUR row uses the USD list rate × PLANANCE_AI_USD_TO_EUR unless you edit below.
*/

return [
    'usd_to_eur' => $usdToEur,

    'models' => [
        'gpt-4o-mini' => ['input_usd_per_1m' => 0.15, 'output_usd_per_1m' => 0.60],
        'default' => ['input_usd_per_1m' => 0.15, 'output_usd_per_1m' => 0.60],
    ],

    'models_eur' => [
        'gpt-4o-mini' => [
            'input_eur_per_1m' => 0.15 * $usdToEur,
            'output_eur_per_1m' => 0.60 * $usdToEur,
        ],
        'default' => [
            'input_eur_per_1m' => 0.15 * $usdToEur,
            'output_eur_per_1m' => 0.60 * $usdToEur,
        ],
    ],
];
