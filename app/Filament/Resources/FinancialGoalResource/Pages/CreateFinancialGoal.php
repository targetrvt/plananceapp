<?php

namespace App\Filament\Resources\FinancialGoalResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\FinancialGoalResource;

class CreateFinancialGoal extends CreateRecord
{
    protected static string $resource = FinancialGoalResource::class;
}