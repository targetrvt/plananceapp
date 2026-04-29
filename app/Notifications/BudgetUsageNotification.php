<?php

namespace App\Notifications;

use App\Models\Budget;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BudgetUsageNotification extends Notification
{
    use Queueable;

    public function __construct(
        protected Budget $budget,
        protected float $spent,
        protected int $threshold,
        protected bool $sendDatabase = true,
        protected bool $sendMail = false
    ) {
    }

    public function via(object $notifiable): array
    {
        $channels = [];

        if ($this->sendDatabase) {
            $channels[] = 'database';
        }

        if ($this->sendMail) {
            $channels[] = 'mail';
        }

        return $channels;
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(__('budget-notification.mail.subject'))
            ->line(__('budget-notification.mail.limit_reached', [
                'id' => $this->budget->id,
                'name' => $this->budget->name,
            ]))
            ->line(__('budget-notification.mail.budget_amount', [
                'amount' => number_format((float) $this->budget->amount, 2),
            ]))
            ->line(__('budget-notification.mail.spent_amount', [
                'amount' => number_format($this->spent, 2),
            ]))
            ->action(__('budget-notification.mail.open_budgets'), url('/app/budgets'));
    }

    public function toArray(object $notifiable): array
    {
        $title = $this->threshold >= 100
            ? __('budget-notification.in_app.exceeded_title')
            : __('budget-notification.in_app.warning_title');

        $message = $this->threshold >= 100
            ? __('budget-notification.in_app.exceeded_message', [
                'id' => $this->budget->id,
                'name' => $this->budget->name,
            ])
            : __('budget-notification.in_app.warning_message', [
                'id' => $this->budget->id,
                'name' => $this->budget->name,
            ]);

        return [
            'title' => $title,
            'message' => $message,
            'budget_id' => $this->budget->id,
            'budget_name' => $this->budget->name,
            'budget_amount' => (float) $this->budget->amount,
            'spent_amount' => $this->spent,
            'threshold' => $this->threshold,
        ];
    }
}
