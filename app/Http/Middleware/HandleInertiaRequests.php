<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    public function share(Request $request): array
    {
        $user = $request->user();

        $authData = [];
        if ($user) {
            $version = cache('auth_version', 1);
            $cacheKey = "user_auth_data_{$user->id}_v{$version}";
            $authData = cache()->remember($cacheKey, now()->addMinutes(5), function () use ($user) {
                return [
                    'roles' => $user->getRoleNames()->values(),
                    'permissions' => $user->getAllPermissions()->pluck('name')->values(),
                ];
            });
        }

        $currentLocale = app()->getLocale();

        return array_merge(parent::share($request), [
            'auth' => [
                'user' => $user,
                'roles' => $authData['roles'] ?? [],
                'permissions' => $authData['permissions'] ?? [],
            ],
            'configs' => [
                'app_name' => config('app.name'),
                'logo' => asset('images/logo.jpeg'),
                'currency' => config('app.currency'),
                'currency_symbol' => config('app.currency_symbol'),
            ],
            'locale' => [
                'current' => $currentLocale,
                'supported' => config('app.supported_locales'),
                'user_locale' => $user?->locale,
            ],
            'csrf_token' => csrf_token(),
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
            ],
        ]);
    }
}
