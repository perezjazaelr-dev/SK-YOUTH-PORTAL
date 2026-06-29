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
        $middleware->alias([
            'admin.staff' => \App\Http\Middleware\EnsureAdminOrStaff::class,
            'admin.only' => \App\Http\Middleware\EnsureAdmin::class,
            'admin.dpo' => \App\Http\Middleware\EnsureAdminOrDpo::class,
            'kk.profile.completed' => \App\Http\Middleware\EnsureKkProfileCompleted::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
