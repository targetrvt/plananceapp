<?php

namespace App\Filament\Resources\Concerns;

use Illuminate\Database\Eloquent\Builder;

trait ScopesGlobalSearchToCurrentUser
{
    public static function getGlobalSearchEloquentQuery(): Builder
    {
        $query = parent::getGlobalSearchEloquentQuery();

        $userId = auth()->id();
        if ($userId !== null) {
            $query->where($query->getModel()->getTable().'.user_id', $userId);
        }

        return $query;
    }
}
