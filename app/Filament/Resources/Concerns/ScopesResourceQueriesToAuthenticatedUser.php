<?php

namespace App\Filament\Resources\Concerns;

use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Builder;

trait ScopesResourceQueriesToAuthenticatedUser
{
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        if (in_array(Filament::getCurrentPanel()?->getId(), ['app', 'premium'], true)) {
            return $query->where($query->getModel()->getTable().'.user_id', auth()->id());
        }

        return $query;
    }
}
