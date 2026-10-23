<?php

use App\Http\Middleware\LegacyUrls;
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
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
