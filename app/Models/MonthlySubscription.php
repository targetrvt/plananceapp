<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MonthlySubscription extends Model
{
    protected $fillable = [
        'name',
        'amount',
        'billing_date',
        'category',
        'is_active',
        'status',
        'last_paid_date',
        'description',
        'billing_cycle',
        'auto_create_transaction',
        'start_date',
        'end_date',
        'user_id',
    ];

    protected $casts = [
        'billing_date' => 'date',
        'start_date' => 'date',
        'end_date' => 'date',
        'last_paid_date' => 'date',
        'is_active' => 'boolean',
        'auto_create_transaction' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * Calculate the annual cost of this subscription
     */
    public function getAnnualCostAttribute(): float
    {
        $multiplier = match($this->billing_cycle) {
            'monthly' => 12,
            'quarterly' => 4,
            'biannual' => 2,
            'annual' => 1,
            default => 12,
        };
        
        return $this->amount * $multiplier;
    }
    
    /**
     * Calculate the monthly cost (normalized for quarterly/annual subscriptions)
     */
    public function getMonthlyCostAttribute(): float
    {
        $divisor = match($this->billing_cycle) {
            'monthly' => 1,
            'quarterly' => 3,
            'biannual' => 6,
            'annual' => 12,
            default => 1,
        };
        
        return $this->amount / $divisor;
    }
}