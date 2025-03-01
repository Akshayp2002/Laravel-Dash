<?php

use App\Exceptions\QueryMessageException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {

        // $exceptions->render(function (HttpException $e, Request $request) {
        //     if ($request->is('api/*')) {
        //         return response()->json([
        //             'status'  => 'false',
        //             'message' => 'Not Found',
        //             'code'    => '404',
        //         ], 404);
        //     }
        // });

        // $exceptions->render(function (HttpException $e, Request $request) {
        //     if ($request->is('api/*')) {
        //         return response()->json([
        //             'status'  => 'false',
        //             'message' => 'Not Found',
        //             'code'    => '404',
        //         ], 404);
        //     }
        // });

        // $exceptions->render(function (QueryException $e, Request $request) {
        //     if ($request->is('api/*')) {
        //         return response()->json([
        //             'status'  => false,
        //             'message' => $e->getMessage(),
        //             'code'    => $e->getCode(),
        //         ], 500);
        //         // throw new QueryMessageException($e);
        //     }
        // });

        // $exceptions->render(function (Exception $e, Request $request) {
        //     if ($request->is('api/*')) {
        //         return response()->json([
        //             'status'  => false,
        //             'message' => $e->getMessage(),
        //             'code'    => $e->getCode(),
        //         ], 500);
        //     }
        // });
    })->create();
