<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {

        // ---------------------------------------------------------------------
        // Middlewares globaux appliqués à toutes les requêtes web
        // ---------------------------------------------------------------------
        $middleware->web(append: [
            \App\Http\Middleware\CheckActive::class,
        ]);

        // ---------------------------------------------------------------------
        // Alias utilisables dans les routes (->middleware('alias'))
        // ---------------------------------------------------------------------
        $middleware->alias([
            'role'   => \App\Http\Middleware\CheckRole::class,
            'active' => \App\Http\Middleware\CheckActive::class,
        ]);

    })
    ->withExceptions(function (Exceptions $exceptions) {

        // Personnalisation de la page 403
        $exceptions->render(function (\Illuminate\Auth\Access\AuthorizationException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => $e->getMessage()], 403);
            }
            return response()->view('errors.403', ['message' => $e->getMessage()], 403);
        });

        // Redirection vers login si non authentifié
        $exceptions->render(function (\Illuminate\Auth\AuthenticationException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Non authentifié.'], 401);
            }
            return redirect()->route('login')->withErrors(['email' => 'Veuillez vous connecter.']);
        });

    })
    ->create();

