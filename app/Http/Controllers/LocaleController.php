<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class LocaleController extends Controller
{
    public function set(Request $request)
    {
        $request->validate([
            'locale' => 'required|string|in:en,bn,ar,es,hi',
        ]);

        $locale = $request->locale;

        if (Auth::check()) {
            $user = Auth::user();
            $user->locale = $locale;
            $user->save();
        }

        Session::put('locale', $locale);

        $response = response()->json([
            'success' => true,
            'locale' => $locale,
        ]);

        $response->withCookie(cookie('locale', $locale, 60 * 24 * 365, '/', null, false, false, 'lax'));

        return $response;
    }

    public function setFromUrl(Request $request, $locale)
    {
        if (! in_array($locale, ['en', 'bn', 'ar', 'es', 'hi'])) {
            $locale = 'en';
        }

        if (Auth::check()) {
            $user = Auth::user();
            $user->locale = $locale;
            $user->save();
        }

        Session::put('locale', $locale);

        $response = redirect()->back()->withInput();
        $response->withCookie(cookie('locale', $locale, 60 * 24 * 365, '/', null, false, false, 'lax'));

        return $response;
    }

    public function get()
    {
        $locale = app()->getLocale();

        return response()->json([
            'locale' => $locale,
            'supported_locales' => config('app.supported_locales'),
        ]);
    }
}
