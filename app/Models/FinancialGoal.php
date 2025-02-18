<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FinancialGoal extends Model
{
    protected $fillable = [
        'name',
        'target_amount',
        'current_amount',
        'target_date',
        'notes',
        'progress'
    ];
}
