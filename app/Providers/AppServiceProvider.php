<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // 🔥 THE FIX: Use app()->environment() instead of env()
        if (app()->environment('production')) {
            URL::forceScheme('https');
        }
    }
}