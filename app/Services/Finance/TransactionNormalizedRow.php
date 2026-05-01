<?php

declare(strict_types=1);

namespace App\Services\Finance;

final readonly class TransactionNormalizedRow
{
    public function __construct(
        public string $type,
        public float $amount,
        public string $date,
        public ?string $description,
        public string $category,
    ) {}
}
