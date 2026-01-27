<?php

namespace App\Filament\Resources\MonthlySubscriptionResource\Pages;

use App\Filament\Resources\MonthlySubscriptionResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions;

class ListMonthlySubscriptions extends ListRecords
{
    protected static string $resource = MonthlySubscriptionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label(__('monthly-subscription.actions.create.label')),
        ];
    }
}