<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\Localization;
use Illuminate\Session\TokenMismatchException;
use Symfony\Component\HttpKernel\Exception\HttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up'
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->redirectGuestsTo('/login');
        $middleware->append(Localization::class);
    })
    ->withExceptions(function (Exceptions $exceptions) {
       
        // Handle page expired exception
        $exceptions->renderable(function (HttpException $e, $request) {
            if ($e->getStatusCode() === 419) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'message' => 'Page has expired. Please refresh and try again.'
                    ], 419);
                }

                if ($request->user()) {
                    return redirect()->route('login')
                        ->withErrors(['error' => trans('Session has expired. Please login again.')]);
                }

                return redirect()->route('login');
            }

            return null;
        });
    })
    ->create();