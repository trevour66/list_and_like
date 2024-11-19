<?php

namespace App\Providers;

use Inertia\Inertia;
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
        $mini_version = false;
        if ((config('app.miniversion') ?? '') ==  'MINI') {
            $mini_version = true;
        }

        Inertia::share([
            'mini_version' => $mini_version,
            // 'mini_version' => true,
        ]);
    }
}
