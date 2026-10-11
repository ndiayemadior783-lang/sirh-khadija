<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class ThrottleLogin
{
    public function handle(Request $request, Closure $next): Response
    {
        $ip = $request->ip();
        $maxAttempts = 5;
        $decayMinutes = 15;

        $key = 'login_attempts_' . $ip;
        $attempts = Cache::get($key, 0);

        if ($attempts >= $maxAttempts) {
            $minutesLeft = (int) ((Cache::get($key . '_timer', now()) - now()->timestamp) / 60) + 1;
            return back()->withErrors([
                'email' => "Trop de tentatives. Reessayez dans {$minutesLeft} minute(s)."
            ]);
        }

        $response = $next($request);

        if ($request->hasSession() && $request->session()->has('errors')) {
            $attempts++;
            Cache::put($key, $attempts, now()->addMinutes($decayMinutes));
            if ($attempts === 1) {
                Cache::put($key . '_timer', now()->addMinutes($decayMinutes)->timestamp, now()->addMinutes($decayMinutes));
            }
        } elseif ($response->getStatusCode() < 400) {
            Cache::forget($key);
            Cache::forget($key . '_timer');
        }

        return $response;
    }
}
