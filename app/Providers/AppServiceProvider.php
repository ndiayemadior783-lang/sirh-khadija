<?php

namespace App\Providers;

use App\Services\MailConfig;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Pas d'arguments de fonctions dans les traces d'erreur (logs, failed_jobs) :
        // evite qu'un identifiant SMTP passe en parametre apparaisse dans les journaux.
        ini_set('zend.exception_ignore_args', '1');

        MailConfig::apply();

        // Le worker de file d'attente tourne en continu : on relit la config SMTP
        // avant chaque job pour prendre en compte les modifications faites dans Parametres.
        Queue::before(fn () => MailConfig::apply());

        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        RateLimiter::for('general', function (Request $request) {
            return Limit::perMinute(120)->by($request->ip());
        });
    }
}