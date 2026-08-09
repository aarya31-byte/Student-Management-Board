<?php

use App\Http\Middleware\Authenticate;
use App\Http\Middleware\EnsureOrgAccess;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // HandleCors is already in Laravel's default global middleware
        // stack — config/cors.php is picked up automatically, no
        // registration needed here.
        $middleware->alias([
            'auth.jwt' => Authenticate::class,
            'org.access' => EnsureOrgAccess::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->shouldRenderJsonWhen(fn () => true);

        // Every error response — regardless of cause — is normalized to
        // {"detail": ...}, matching the shape the frontend already reads
        // (see backend_details.md §7: `data.detail`).
        $exceptions->render(function (ValidationException $e, $request) {
            return response()->json(['detail' => $e->errors()], $e->status);
        });

        $exceptions->render(function (AuthenticationException $e, $request) {
            return response()->json(['detail' => 'Unauthenticated.'], 401);
        });

        $exceptions->render(function (AuthorizationException $e, $request) {
            return response()->json(['detail' => $e->getMessage() ?: 'Forbidden.'], 403);
        });

        $exceptions->render(function (HttpException $e, $request) {
            return response()->json([
                'detail' => $e->getMessage() ?: 'An error occurred.',
            ], $e->getStatusCode());
        });

        $exceptions->render(function (Throwable $e, $request) {
            $debug = config('app.debug');

            return response()->json([
                'detail' => $debug ? $e->getMessage() : 'Internal server error.',
            ], 500);
        });
    })->create();
