<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Transaction;

class Budget extends Model
{
    // Define which attributes are mass assignable
    protected $fillable = [
        'name',       // Budget name
        'amount',     // Budget amount
        'user_id',    // Foreign key for user
        'start_date', // Budget start date
        'end_date',   // Budget end date
        'warning_90_sent_at',
        'alert_100_in_app_sent_at',
        'alert_100_email_sent_at',
    ];

    protected $casts = [
        'warning_90_sent_at' => 'datetime',
        'alert_100_in_app_sent_at' => 'datetime',
        'alert_100_email_sent_at' => 'datetime',
    ];

    /**
     * Get the user that owns the budget.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function spentAmount(): float
    {
        return (float) Transaction::query()
            ->where('user_id', $this->user_id)
            ->where('type', 'expense')
            ->whereBetween('date', [$this->start_date, $this->end_date])
            ->where('created_at', '>=', $this->created_at)
            ->sum('amount');
    }

    public function usagePercentage(): float
    {
        if ((float) $this->amount <= 0) {
            return 0;
        }

        return ($this->spentAmount() / (float) $this->amount) * 100;
    }
}
