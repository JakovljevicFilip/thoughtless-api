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
            $viewData = [
                'suite'     => config('app.suite_name'),
                'verifyUrl' => $url,
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
