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
            \App\Http\Middleware\SetLocale::class,
            \App\Http\Middleware\HandleInertiaRequests::class,
            \Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets::class,
            \App\Http\Middleware\SecurityHeaders::class,
        ]);

        $middleware->alias([
            'route.permission' => \App\Http\Middleware\AuthorizeRoutePermission::class,
            'registration.enabled' => \App\Http\Middleware\EnsureRegistrationEnabled::class,
        ]);

        // Hosting behind a load balancer / reverse proxy (HTTPS termination)
        $middleware->trustProxies(at: '*');
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->respond(function (\Symfony\Component\HttpFoundation\Response $response, \Throwable $e, \Illuminate\Http\Request $request) {
            $status = $response->getStatusCode();

            // Expired session / CSRF token: go back with a readable message instead of a bare 419
            if ($status === 419) {
                return back()->with('error', __('Your session expired. Please try again.'));
            }

            // Friendly, translated error pages (keep Laravel's debug page in local development)
            if (in_array($status, [403, 404, 500, 503], true) && ! ($request->expectsJson() && ! $request->header('X-Inertia'))
                && (! config('app.debug') || in_array($status, [403, 404], true))) {
                return \Inertia\Inertia::render('Error', [
                    'status' => $status,
                    'message' => $status === 403 && $e->getMessage() !== 'This action is unauthorized.' ? $e->getMessage() : null,
                ])->toResponse($request)->setStatusCode($status);
            }

            return $response;
        });
    })->create();
