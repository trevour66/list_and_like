<?php

namespace App\Providers;

use Inertia\Inertia;
use Illuminate\Support\ServiceProvider;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;

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
            'env' => env('APP_MODE', "DEV")
            // 'mini_version' => true,
        ]);


        VerifyEmail::toMailUsing(function (object $notifiable, string $url) {
            return (new MailMessage)
                ->subject('Verify Email Address')
                ->greeting("Hello! ")
                ->line("Please click the button below to verify your email address for your account inside List & Like.")
                ->action("Verify Email Address", $url)
                ->line("If you didn't create an account, no further action is required.");
        });
    }
}
