<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $this->getLocale($request);

        if ($locale && in_array($locale, config('app.supported_locales', ['en', 'bn']))) {
            app()->setLocale($locale);
        }

        return $next($request);
    }

    protected function getLocale(Request $request): ?string
    {
        if ($request->user() && $request->user()->locale) {
            return $request->user()->locale;
        }

        if ($request->session()->has('locale')) {
            return $request->session()->get('locale');
        }

        if ($request->cookie('locale')) {
            $cookieLocale = $request->cookie('locale');
            if (in_array($cookieLocale, config('app.supported_locales', ['en', 'bn']))) {
                $request->session()->put('locale', $cookieLocale);

                return $cookieLocale;
            }
        }

        if ($request->has('locale')) {
            $locale = $request->get('locale');
            if (in_array($locale, config('app.supported_locales', ['en', 'bn']))) {
                $request->session()->put('locale', $locale);

                return $locale;
            }
        }

        return config('app.locale', 'en');
    }
}
