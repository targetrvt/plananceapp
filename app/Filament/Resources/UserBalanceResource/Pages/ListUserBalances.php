<?php

namespace App\Filament\Resources\UserBalanceResource\Pages;

use App\Filament\Resources\UserBalanceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListUserBalances extends ListRecords
{
    protected static string $resource = UserBalanceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label(__('user-balance.actions.create.label')),
        ];
    }
}
