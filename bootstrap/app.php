<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {

        /**
         * 1) TURN ON STATEFUL SPA AUTH FOR API ROUTES
         *
         * This lets requests to routes in routes/api.php authenticate via:
         *   - session cookies (from your SPA origin in SANCTUM_STATEFUL_DOMAINS)
         *   - OR Bearer tokens (mobile / third-party)
         *
         * Under the hood this wires EnsureFrontendRequestsAreStateful for the API group
         * and AuthenticateSession for the web group.
         */
        $middleware->statefulApi();

        /**
         * 2) OPTIONAL: If you want to be explicit about the API group contents,
         *    you can replace the API group with your own list that *prepends*
         *    EnsureFrontendRequestsAreStateful. (If you keep this, you can remove it.)
         *
         *    Uncomment to be explicit:
         */
        // $middleware->group('api', [
        //     EnsureFrontendRequestsAreStateful::class,            // ← enables cookie auth for SPA origins
        //     // 'throttle:api',                                   // optional rate limiting
        //     \Illuminate\Routing\Middleware\SubstituteBindings::class,
        // ]);

        /**
         * 3) A dedicated "spa" middleware group you can use on routes that should
         *    ALWAYS run with session + CSRF (cookie/web flow), e.g. GET /me.
         */
        $middleware->group('spa', [
            // Treat requests from SPA origins as "stateful"
            EnsureFrontendRequestsAreStateful::class,

            // Standard "web" stack bits:
            \Illuminate\Cookie\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\Session\Middleware\AuthenticateSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ]);

        // You can still tweak built-in groups if you need:
        // $middleware->api(append: ['throttle:api']);
        // $middleware->web(append: [AuthenticateSession::class]); // already handled by statefulApi()
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })
    ->create();
