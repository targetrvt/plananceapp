<?php

namespace App\Filament\Resources\MonthlySubscriptionResource\Pages;

use Filament\Actions;
use Illuminate\Database\Eloquent\Model;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\MonthlySubscriptionResource;

class EditMonthlySubscription extends EditRecord
{
    protected static string $resource = MonthlySubscriptionResource::class;
    protected function resolveRecord($key): Model
    {
        return static::getResource()::getModel()::where('user_id', auth()->id())->findOrFail($key);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}