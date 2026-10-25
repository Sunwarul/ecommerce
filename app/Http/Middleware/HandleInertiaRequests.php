<?php

namespace App\Http\Middleware;

use App\Models\ContactMessage;
use App\Models\Setting;
use App\Support\Website;
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
        $logo = Setting::get('general_logo');
        $favicon = Setting::get('general_favicon');

        return array_merge(parent::share($request), [
            'auth' => [
                'user' => $user,
                'roles' => $authData['roles'] ?? [],
                'permissions' => $authData['permissions'] ?? [],
            ],
            'configs' => [
                'app_name' => config('app.name'),
                'shop_name' => Setting::get('general_application_name') ?: config('app.name'),
                'logo' => $logo ? asset('storage/'.$logo) : asset('images/logo.jpeg'),
                'favicon' => $favicon ? asset('storage/'.$favicon) : asset('images/favicon.svg'),
                'currency' => config('app.currency'),
                'currency_symbol' => Setting::get('general_currency_symbol') ?: config('app.currency_symbol'),
                // number display: Bangla digits (০১২) while the UI is Bangla, and lakh/crore grouping (12,34,567)
                'bn_digits' => (bool) Setting::get('locale_bn_digits', false),
                'number_grouping' => Setting::get('locale_number_grouping') ?: 'international',
                'website_enabled' => Website::raw()['enabled'],
            ],
            'unread_contact_messages' => fn () => $user?->can('Contact Message Index') ? ContactMessage::unread()->count() : 0,
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
