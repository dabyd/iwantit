<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\App;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Solo registrar Scribe si la clase existe (no está en producción)
        if (class_exists(\Knuckles\Scribe\ScribeServiceProvider::class)) {
            $this->app->register(\Knuckles\Scribe\ScribeServiceProvider::class);
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
