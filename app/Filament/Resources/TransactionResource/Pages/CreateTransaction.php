<?php

namespace App\Filament\Resources\TransactionResource\Pages;

use App\Models\UserBalance;
use App\Filament\Resources\TransactionResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateTransaction extends CreateRecord
{
    protected static string $resource = TransactionResource::class;
    
    protected function afterCreate(): void
    {
        // Get the newly created transaction
        $transaction = $this->record;
        
        // Get or create the user balance
        $userBalance = UserBalance::firstOrCreate(
            ['user_id' => auth()->id()],
            ['balance' => 0, 'currency' => 'EUR']
        );
        
        // Update balance based on transaction type
        if ($transaction->type === 'income') {
            $userBalance->balance += $transaction->amount;
        } else if ($transaction->type === 'expense') {
            $userBalance->balance -= $transaction->amount;
        }
        
        // Save the updated balance
        $userBalance->save();
    }
}