<?php

namespace App\Providers;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        VerifyEmail::toMailUsing(function ($notifiable, string $url) {
            $parsed = parse_url($url);
            parse_str($parsed['query'] ?? '', $query);

            // Generate FE friendly token.
            $token = base64_encode(json_encode([
                'id'   => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
                'sig'  => $query['signature'] ?? null,
                'exp'  => $query['expires'] ?? null,
            ]));

            $frontendUrl = rtrim(config('app.frontend_url'), '/') . '/verify/' . $token;

            $viewData = [
                'suite'     => config('app.suite_name'),
                'verifyUrl' => $frontendUrl,
                'logo'      => asset('icons/favicon-512x512.png'),
                'user'      => $notifiable,
            ];

            return (new MailMessage)
                ->subject('Welcome to ' . config('app.suite_name'))
                ->view('emails.auth.confirmation.confirmation', $viewData)
                ->text('emails.auth.confirmation.confirmation_plain', $viewData);
        });
    }
}
