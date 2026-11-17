<?php

use App\Domain\Affiliate\Console\Commands\CheckAffiliateDestinations;
use App\Domain\Affiliate\Console\Commands\ImportAffiliateProductLinks;
use App\Domain\Affiliate\Console\Commands\PruneAffiliateClicks;
use App\Domain\Affiliate\Http\Middleware\CaptureTrafficSource;
use App\Domain\Language\Http\Middleware\SetLocale;
use App\Domain\Theme\Http\Middleware\RegisterActiveTheme;
use App\Http\Middleware\RedirectThemeOnePage;
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
        CheckAffiliateDestinations::class,
        PruneAffiliateClicks::class,
        ImportAffiliateProductLinks::class,
    ])
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web(append: [
            RegisterActiveTheme::class,
            SetLocale::class,
            CaptureTrafficSource::class,
            RedirectThemeOnePage::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
