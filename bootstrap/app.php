<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withCommands([
        \App\Domain\Affiliate\Console\Commands\CheckAffiliateDestinations::class,
        \App\Domain\Affiliate\Console\Commands\PruneAffiliateClicks::class,
    ])
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web(append: [
            \App\Domain\Theme\Http\Middleware\RegisterActiveTheme::class,
            \App\Domain\Language\Http\Middleware\SetLocale::class,
            \App\Domain\Affiliate\Http\Middleware\CaptureTrafficSource::class,
            \App\Http\Middleware\RedirectThemeOnePage::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
