<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Knuckles\Scribe\ScribeServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Solo registrar Scribe si la clase existe (no está en producción)
        if (class_exists(ScribeServiceProvider::class)) {
            $this->app->register(ScribeServiceProvider::class);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
