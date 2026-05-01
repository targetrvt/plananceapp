<?php

namespace App\Policies;

use App\Models\AiUsageLog;
use App\Models\User;
use BezhanSalleh\FilamentShield\Support\Utils;

class AiUsageLogPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole(Utils::getSuperAdminName());
    }

    public function view(User $user, AiUsageLog $aiUsageLog): bool
    {
        return $user->hasRole(Utils::getSuperAdminName());
    }
}
