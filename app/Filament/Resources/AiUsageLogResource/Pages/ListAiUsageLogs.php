<?php

namespace App\Filament\Resources\AiUsageLogResource\Pages;

use App\Filament\Resources\AiUsageLogResource;
use Filament\Resources\Pages\ListRecords;

class ListAiUsageLogs extends ListRecords
{
    protected static string $resource = AiUsageLogResource::class;

    public function getTitle(): string
    {
        return __('admin.ai_usage.plural_label');
    }

    protected function getHeaderActions(): array
    {
        return [];
    }
}
