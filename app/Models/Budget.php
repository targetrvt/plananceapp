<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Budget extends Model
{
    // Define which attributes are mass assignable
    protected $fillable = [
        'name',       // Budget name
        'amount',     // Budget amount
        'user_id',    // Foreign key for user
        'start_date', // Budget start date
        'end_date',   // Budget end date
    ];

    /**
     * Get the user that owns the budget.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
