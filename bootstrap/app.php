<?php

use App\Console\Commands\Member\MemberSubscriptionBillingCommand;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(using: function () {
        $namespace = 'App\\Http\\Controllers';

        // WEB ADMIN
        Route::prefix(config('core.prefix.web.admin'))
            ->middleware(['web'])
            ->namespace("$namespace\\" . config('core.namespace.web.admin'))
            ->group(base_path('routes/web/admin.php'));
    })
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->validateCsrfTokens(["*"]);

        $middleware->alias([
            'auth.web.admin' => \App\Http\Middleware\AuthenticateWebAdmin::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
    })
    ->withSchedule(function (Schedule $schedule) {

        // Member
        $schedule->command('member:subscription-billing-generate')->dailyAt('00:30');

        // Recommendation
        $schedule->command('recommendation:partner')->dailyAt('01:30');

    })->create();
