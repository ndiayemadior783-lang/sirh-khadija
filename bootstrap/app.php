<?php

use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpKernel\Exception\HttpException;
use App\Http\Middleware\VerifierRole;
use App\Http\Middleware\ThrottleLogin;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            Route::middleware('web')
                ->group(base_path('routes/auth.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => VerifierRole::class,
            'throttle.login' => ThrottleLogin::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Ne jamais remettre le mot de passe SMTP en session apres une erreur de validation
        $exceptions->dontFlash(['mail_password']);

        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );

        // Erreur SQL : jamais de requete ni de valeurs a l'ecran, meme avec APP_DEBUG=true.
        // Le detail (sans les valeurs, cf. mask_bindings_in_exception_messages) reste dans storage/logs.
        $exceptions->render(function (QueryException $e, Request $request) {
            $code = $e->errorInfo[0] ?? 'HY000';
            $message = "Operation impossible : erreur de base de donnees (code {$code}). Le detail a ete enregistre dans le journal de l application.";

            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json(['message' => $message], 500);
            }

            if (! $request->isMethod('GET')) {
                return back()
                    ->withInput($request->except(['password', 'password_confirmation', 'current_password', 'mail_password']))
                    ->with('error', $message);
            }

            return app(ExceptionHandler::class)->render($request, new HttpException(500));
        });
    })->create();