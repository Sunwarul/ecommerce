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
            // Use version-based cache to ensure fresh data when permissions change
            $version = cache('auth_version', 1);
            $cacheKey = "user_auth_data_{$user->id}_v{$version}";
            $authData = cache()->remember($cacheKey, now()->addMinutes(5), function () use ($user) {
                return [
                    'roles' => $user->getRoleNames()->values(),
                    'permissions' => $user->getAllPermissions()->pluck('name')->values(),
                ];
            });
        }

        return [
            ...parent::share($request),
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
            'flash' => [
                'success' => fn() => $request->session()->get('success'),
                'error' => fn() => $request->session()->get('error'),
            ],
        ];
    }
}
