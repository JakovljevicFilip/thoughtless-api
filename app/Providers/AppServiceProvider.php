<?php

namespace App\Providers;

use App\Actions\Auth\LoginMobileAction;
use App\Actions\Auth\LoginWebAction;
use App\Actions\Auth\ResetForgotPasswordAction;
use App\Actions\Auth\ScheduleAccountRemovalAction;
use App\Actions\Auth\VerifyForgotForgotPasswordAction;
use App\Contracts\Auth\LoginMobileContract;
use App\Contracts\Auth\LoginWebContract;
use App\Contracts\Auth\ResetForgotPasswordContract;
use App\Contracts\Auth\ScheduleAccountRemovalContract;
use App\Contracts\Auth\VerifyForgotPasswordContract;
use App\Tokens\EmailVerification\EmailVerificationTokenFactory;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Carbon;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(LoginMobileContract::class, LoginMobileAction::class);
        $this->app->bind(LoginWebContract::class, LoginWebAction::class);
        $this->app->bind(VerifyForgotPasswordContract::class, VerifyForgotForgotPasswordAction::class);
        $this->app->bind(ResetForgotPasswordContract::class, ResetForgotPasswordAction::class);
        $this->app->bind(ScheduleAccountRemovalContract::class, ScheduleAccountRemovalAction::class);
    }

    public function boot(): void
    {

        VerifyEmail::toMailUsing(function ($notifiable, string $url) {
            $queryString = (string) parse_url($url, PHP_URL_QUERY);

            $q = [];
            parse_str($queryString, $q);

            $expires = isset($q['expires']) ? (int) $q['expires'] : null;

            $expiresAt = $expires !== null
                ? Carbon::createFromTimestamp($expires)
                : now()->addHour();

            $token = app(EmailVerificationTokenFactory::class)->make($notifiable, $expiresAt);

            $frontendUrl = rtrim(config('app.frontend_url'), '/')
                . '/verify/' . $notifiable->getKey() . '/' . $token;

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
