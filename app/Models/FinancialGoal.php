<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FinancialGoal extends Model
{
    protected $fillable = [
        'name',
        'target_amount',
        'current_amount',
        'target_date',
        'user_id',
        'notes',
        'progress',
    ];

    // Add this relationship
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}