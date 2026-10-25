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
        // Public contact form: per visitor IP and per sender address, answered with a normal form error
        RateLimiter::for('contact', function (Request $request) {
            $tooMany = fn () => back()->withInput()->withErrors(['message' => __('Too many messages. Please wait a while before sending another one.')]);

            return [
                Limit::perMinute(2)->by('contact-min:'.$request->ip())->response($tooMany),
                Limit::perHour(6)->by('contact-hour:'.$request->ip())->response($tooMany),
                Limit::perDay(5)->by('contact-sender:'.mb_strtolower((string) ($request->input('email') ?: $request->input('phone'))))->response($tooMany),
                // a site-wide ceiling so a botnet cannot flood the inbox
                Limit::perHour(100)->by('contact-global')->response($tooMany),
            ];
        });
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
