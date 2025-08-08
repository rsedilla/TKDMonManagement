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
    ->withMiddleware(function (Middleware $middleware): void {
        // Rate limiting middleware
        $middleware->throttleApi();
        
        // DDoS Protection (high priority - runs first)
        $middleware->web(prepend: [
            \App\Http\Middleware\DDoSProtection::class,
        ]);
        
        // Security headers and logging
        $middleware->web(append: [
            \Illuminate\Http\Middleware\HandleCors::class,
            \App\Http\Middleware\SecurityHeaders::class,
            \App\Http\Middleware\SecurityLogger::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
