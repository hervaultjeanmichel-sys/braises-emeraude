<?php

namespace App\Providers;

use Filament\Forms\Components\Toggle;
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
        // Admin : tous les interrupteurs des formulaires — vert = activé, rouge = désactivé
        Toggle::configureUsing(fn (Toggle $toggle) => $toggle->onColor('success')->offColor('danger'));
    }
}
