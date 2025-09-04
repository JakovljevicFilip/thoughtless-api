<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Mail\ForgotPasswordMail;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

final class ResetPasswordNotification extends Notification
{
    use Queueable;

    public function __construct(public string $token) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): ForgotPasswordMail
    {
        $resetUrl = rtrim(config('app.frontend_url'), '/') .
            '/reset-password?token=' . $this->token .
            '&email=' . urlencode($notifiable->getEmailForPasswordReset());

        return new ForgotPasswordMail($notifiable, $resetUrl);
    }
}
