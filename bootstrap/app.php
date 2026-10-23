<?php

use App\Http\Middleware\LegacyUrls;
use App\Http\Middleware\PreprodLock;
use App\Http\Middleware\TrackSource;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Avant le routage : redirections 301 + slash final
        $middleware->prepend(LegacyUrls::class);
        // Verrou préprod : mot de passe + noindex (premier exécuté, avant tout le reste)
        $middleware->prepend(PreprodLock::class);
        // Provenance des visiteurs (UTM, référent) pour qualifier les demandes
        $middleware->web(append: [TrackSource::class]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
