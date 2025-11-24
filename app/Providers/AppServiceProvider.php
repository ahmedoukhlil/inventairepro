<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

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
        // Fix pour MySQL : limite la longueur des chaînes pour les index
        // MySQL avec utf8mb4 a une limite de 767 bytes pour les index
        // 191 caractères * 4 bytes = 764 bytes < 767
        Schema::defaultStringLength(191);
    }
}
