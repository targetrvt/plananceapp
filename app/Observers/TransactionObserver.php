<?php

namespace App\Observers;

use App\Models\Budget;
use App\Models\Transaction;
use App\Notifications\BudgetUsageNotification;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class TransactionObserver
{
    public function created(Transaction $transaction): void
    {
        if ($transaction->type !== 'expense') {
            return;
        }

        $user = $transaction->user;

        if (! $user) {
            return;
        }

        $transactionDate = Carbon::parse($transaction->date)->toDateString();

        $budgets = Budget::query()
            ->where('user_id', $transaction->user_id)
            ->whereDate('start_date', '<=', $transactionDate)
            ->whereDate('end_date', '>=', $transactionDate)
            ->get();

        foreach ($budgets as $budget) {
            if ((float) $budget->amount <= 0.0) {
                continue;
            }

            $spent = $budget->spentAmount();

            $usage = ($spent / (float) $budget->amount) * 100;
            $sendDatabaseNotification = $user->notify_budget_warnings ?? true;
            $sendBudgetEmail = $user->notify_budget_limit_email ?? true;

            if ($usage >= 100) {
                if ($sendDatabaseNotification && is_null($budget->alert_100_in_app_sent_at)) {
                    $this->sendFilamentDatabaseNotification($user, FilamentNotification::make()
                        ->title(__('budget-notification.in_app.exceeded_title'))
                        ->body(__('budget-notification.in_app.exceeded_message', [
                            'id' => $budget->id,
                            'name' => $budget->name,
                        ]))
                        ->danger());

                    $budget->forceFill(['alert_100_in_app_sent_at' => now()])->save();
                }

                if ($sendBudgetEmail && is_null($budget->alert_100_email_sent_at)) {
                    $user->notify(new BudgetUsageNotification(
                        $budget,
                        $spent,
                        100,
                        false,
                        $sendBudgetEmail
                    ));

                    $budget->forceFill(['alert_100_email_sent_at' => now()])->save();
                }

                continue;
            }

            if ($usage >= 90 && is_null($budget->warning_90_sent_at)) {
                if ($sendDatabaseNotification) {
                    $this->sendFilamentDatabaseNotification($user, FilamentNotification::make()
                        ->title(__('budget-notification.in_app.warning_title'))
                        ->body(__('budget-notification.in_app.warning_message', [
                            'id' => $budget->id,
                            'name' => $budget->name,
                        ]))
                        ->warning());
                }
                $budget->forceFill(['warning_90_sent_at' => now()])->save();
            }
        }
    }

    private function sendFilamentDatabaseNotification(Authenticatable $user, FilamentNotification $notification): void
    {
        $user->notifications()->create([
            'id' => (string) Str::uuid(),
            'type' => \Filament\Notifications\DatabaseNotification::class,
            'data' => $notification->getDatabaseMessage(),
        ]);
    }
}
