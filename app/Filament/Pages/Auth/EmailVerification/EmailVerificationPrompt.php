<?php

namespace App\Filament\Pages\Auth\EmailVerification;

use Filament\Actions\Action;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Contracts\Support\Htmlable;

class EmailVerificationPrompt extends \Filament\Pages\Auth\EmailVerification\EmailVerificationPrompt
{
    protected function sendEmailVerificationNotification(MustVerifyEmail $user): void
    {
        if ($user->hasVerifiedEmail()) {
            return;
        }

        $user->sendEmailVerificationNotification();
    }

    public function resendNotificationAction(): Action
    {
        return parent::resendNotificationAction()
            ->label(__('verification.prompt.resend'));
    }

    public function getTitle(): string | Htmlable
    {
        return __('verification.prompt.title');
    }

    public function getHeading(): string | Htmlable
    {
        return __('verification.prompt.title');
    }
}
