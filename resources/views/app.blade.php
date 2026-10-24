<!DOCTYPE html>

@php
    $locale = app()->getLocale();
@endphp

<html lang="{{ str_replace('_', '-', $locale) }}" data-locale="{{ $locale }}" dir="{{ in_array($locale, ['ar']) ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#10b981">

    <title inertia>{{ config('app.name', 'E-Commerce') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700|noto-sans-bengali:400,500,600,700|noto-sans-arabic:400,500,600,700|noto-sans-devanagari:400,500,600,700&display=swap" rel="stylesheet" />

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
