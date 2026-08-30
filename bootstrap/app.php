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
        // ADICIONE ESTA LINHA:
        $middleware->alias([
            'permissions' => \App\Http\Middleware\CheckPermissions::class,
            'search.log' => \App\Http\Middleware\SearchLogger::class,
            'temp.password' => \App\Http\Middleware\CheckTemporaryPassword::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
    