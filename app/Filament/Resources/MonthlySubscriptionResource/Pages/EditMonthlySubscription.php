<?php

namespace App\Filament\Resources\MonthlySubscriptionResource\Pages;

use App\Filament\Resources\MonthlySubscriptionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMonthlySubscription extends EditRecord
{
    protected static string $resource = MonthlySubscriptionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}