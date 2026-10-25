<?php

use App\Http\Middleware\EnsureAdmin;
use App\Http\Middleware\SecurityHeaders;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(web: __DIR__.'/../routes/web.php', commands: __DIR__.'/../routes/console.php', health: '/up')
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias(['admin' => EnsureAdmin::class]);
        $middleware->web(append: [SecurityHeaders::class]);
        $middleware->redirectGuestsTo(fn ($request) => route('admin.login'));
        $middleware->redirectUsersTo(fn ($request) => route('admin.dashboard'));
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Laravel's default exception rendering handles 404/403 and validation errors.
    })->create();
