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
        if (App::runningInConsole()) {
            // Evita carregar proveïdors o serveis que necessiten petició HTTP
            $this->app->register(\Knuckles\Scribe\ScribeServiceProvider::class, false);
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
