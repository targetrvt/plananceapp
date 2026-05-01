<?php

declare(strict_types=1);

namespace App\Services\Finance;

final class TransactionImportResult
{
    /**
     * @param  array<int, string>  $errors
     */
    public function __construct(
        public readonly int $imported,
        public readonly int $skipped,
        public readonly array $errors,
    ) {}
}
