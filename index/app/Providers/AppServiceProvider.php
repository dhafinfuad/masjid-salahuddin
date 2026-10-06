<?php

namespace App\Providers;

use App\Mail\ResendTransport;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        require_once app_path('Helpers/helpers.php');
    }

    public function boot(): void
    {
        if (! app()->isLocal()) {
            URL::forceScheme('https');
        }

        Mail::extend('resend', function (array $config = []) {
            $key = env('RESEND_KEY') ?? env('RESEND_API_KEY') ?? config('services.resend.key', '');
            return new ResendTransport((string) $key);
        });

        Mail::extend('googlescript', function (array $config = []) {
            $url = $config['webhook_url'] ?? env('GOOGLE_SCRIPT_WEBHOOK_URL', '');
            return new \App\Mail\GoogleScriptTransport((string) $url);
        });
    }
}
