<?php

namespace App\Observers;

use App\Models\MonthlySubscription;
use App\Models\Transaction;
use Carbon\Carbon;

class MonthlySubscriptionObserver
{
    /**
     * Handle the MonthlySubscription "created" event.
     */
    public function created(MonthlySubscription $subscription): void
    {
        // If subscription is created with paid status and auto-create transaction is enabled
        if ($subscription->status === 'paid' && $subscription->auto_create_transaction) {
            $this->createTransactionForSubscription($subscription);
        }
    }

    /**
     * Handle the MonthlySubscription "updated" event.
     */
    public function updated(MonthlySubscription $subscription): void
    {
        // If status changed to paid
        if ($subscription->isDirty('status') && $subscription->status === 'paid') {
            // Update the last_paid_date if it wasn't set explicitly
            if (!$subscription->isDirty('last_paid_date')) {
                $subscription->last_paid_date = now();
                $subscription->saveQuietly(); // Prevent infinite loop
            }
            
            // Create transaction if auto-create is enabled
            if ($subscription->auto_create_transaction) {
                $this->createTransactionForSubscription($subscription);
            }
        }
    }

    /**
     * Create a new transaction for the subscription payment
     */
    private function createTransactionForSubscription(MonthlySubscription $subscription): void
    {
        Transaction::create([
            'user_id' => $subscription->user_id,
            'type' => 'expense',
            'amount' => $subscription->amount,
            'date' => $subscription->last_paid_date ?? now(),
            'description' => "Payment for {$subscription->name} subscription",
            'category' => $subscription->category,
        ]);
    }
}