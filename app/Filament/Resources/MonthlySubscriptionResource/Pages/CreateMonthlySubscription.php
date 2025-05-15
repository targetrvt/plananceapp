<?php

namespace App\Filament\Resources\MonthlySubscriptionResource\Pages;

use App\Filament\Resources\MonthlySubscriptionResource;
use Filament\Resources\Pages\CreateRecord;

class CreateMonthlySubscription extends CreateRecord
{
    protected static string $resource = MonthlySubscriptionResource::class;

    protected function afterCreate(): void
    {
        // If auto_create_transaction is enabled, create the first transaction
        if ($this->record->auto_create_transaction) {
            \App\Models\Transaction::create([
                'user_id' => auth()->id(),
                'type' => 'expense',
                'amount' => $this->record->amount,
                'date' => $this->record->billing_date,
                'category' => 'subscription',
                'description' => 'Subscription: ' . $this->record->name,
            ]);
        }
    }
}