<?php

namespace App\Providers;

use App\Models\Branch;
use App\Support\SettingLoader;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (Schema::hasTable('settings')) {
            SettingLoader::load();
        }
        Inertia::share([
            'branches' => fn() => auth()->check()
                ? Branch::active()->select('id', 'name')->get()
                : [],
        ]);



    }
}
