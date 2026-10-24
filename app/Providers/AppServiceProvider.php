<?php

namespace App\Providers;

use App\Models\Branch;
use App\Support\SettingLoader;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void {}

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        RateLimiter::for('api', fn (Request $request) => Limit::perMinute(120)->by($request->user()?->id ?: $request->ip()));
        // Outbound SMS costs money: cap bulk sends / tests per user
        RateLimiter::for('sms', fn (Request $request) => Limit::perMinute(10)->by($request->user()?->id ?: $request->ip()));

        // Super Admin passes every permission check, including permissions added later.
        Gate::before(fn ($user) => $user->hasRole('Super Admin') ? true : null);

        if ($this->app->isProduction() && str_starts_with((string) config('app.url'), 'https://')) {
            URL::forceScheme('https');
        }

        // Settings are cached; a missing table (fresh install before migrate) must not break boot.
        try {
            SettingLoader::load();
        } catch (\Throwable $e) {
            // ignore until migrations have run
        }
        Inertia::share([
            'branches' => fn () => auth()->check()
                ? Branch::active()->select('id', 'name')->get()
                : [],
        ]);

    }
}
