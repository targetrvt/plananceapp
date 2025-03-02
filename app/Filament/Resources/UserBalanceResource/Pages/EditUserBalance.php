<?php

namespace App\Filament\Resources\UserBalanceResource\Pages;

use App\Filament\Resources\UserBalanceResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUserBalance extends EditRecord
{
    protected static string $resource = UserBalanceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
