<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web(append: [
            \App\Http\Middleware\IdentifyTenant::class,
        ]);

        $middleware->api(append: [
            \App\Http\Middleware\IdentifyTenant::class,
        ]);

        $middleware->alias([
            'tenant'        => \App\Http\Middleware\IdentifyTenant::class,
            'owner'         => \App\Http\Middleware\EnsureOwnerAccess::class,
            'feature'       => \App\Http\Middleware\EnsurePlanFeature::class,
            'subscription'  => \App\Http\Middleware\CheckSubscriptionStatus::class,
            'permissions'   => \App\Http\Middleware\CheckPermissions::class,
            'search.log'    => \App\Http\Middleware\SearchLogger::class,
            'temp.password' => \App\Http\Middleware\CheckTemporaryPassword::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
        $exceptions->render(function (\PDOException $e, \Illuminate\Http\Request $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'status' => 'database_offline',
                    'message' => 'O serviço de base de dados está temporariamente indisponível ou a inicializar.',
                    'retry_after' => 10,
                ], 503);
            }
            return response()->view('errors.database', ['exception' => $e], 503);
        });

        $exceptions->render(function (\Illuminate\Database\QueryException $e, \Illuminate\Http\Request $request) {
            $prev = $e->getPrevious();
            $msg = $e->getMessage();
            $isConnectionError = ($prev instanceof \PDOException)
                || in_array((int)$e->getCode(), [2002, 1045, 1049], true)
                || str_contains($msg, '2002')
                || str_contains($msg, 'Connection refused')
                || str_contains($msg, 'No such file or directory')
                || str_contains($msg, 'Access denied');

            if ($isConnectionError) {
                if ($request->expectsJson() || $request->is('api/*')) {
                    return response()->json([
                        'status' => 'database_offline',
                        'message' => 'O serviço de base de dados está temporariamente indisponível ou a inicializar.',
                        'retry_after' => 10,
                    ], 503);
                }
                return response()->view('errors.database', ['exception' => $e], 503);
            }
        });
    })->create();
