<!DOCTYPE html>

@php
    $locale = app()->getLocale();
    try {
        $fontCss = \App\Support\Fonts::stylesheetUrl();
        $fontVars = \App\Support\Fonts::cssVariables();
        $favicon = \App\Models\Setting::get('general_favicon');
    } catch (\Throwable $e) {
        // fresh install before migrations: fall back to the bundled fonts
        $fontCss = 'https://fonts.bunny.net/css?family=figtree:400,500,600,700|noto-sans-bengali:400,500,600,700&display=swap';
        $fontVars = '';
        $favicon = null;
    }
@endphp

<html lang="{{ str_replace('_', '-', $locale) }}" data-locale="{{ $locale }}" dir="{{ in_array($locale, ['ar']) ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#10b981">

    <title inertia>{{ config('app.name', 'E-Commerce') }}</title>

    <link rel="icon" href="{{ $favicon ? asset('storage/'.$favicon) : asset('images/favicon.svg') }}">

    <!-- Fonts (chosen in Settings > Bangla & appearance) -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="{{ $fontCss }}" rel="stylesheet" />
    <style>{!! $fontVars !!}</style>

    <!-- Apply the saved dark theme before CSS/JS load to avoid a light flash -->
    <script>try{var t=JSON.parse(localStorage.getItem('app_theme')||'{}');if(t.darkTheme)document.documentElement.classList.add('app-dark');}catch(e){}</script>

    <!-- Scripts -->
    @routes
    @vite(['resources/js/app.js'])
    @inertiaHead
</head>

<body class="font-sans antialiased">
    @inertia
</body>

</html>
