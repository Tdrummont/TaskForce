<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title inertia>{{ config('app.name', 'Laravel') }}</title>

            <!-- Favicons -->
        <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">
        <link rel="icon" type="image/png" href="{{ asset('yggdra-32.png') }}" sizes="32x32">
        <link rel="icon" type="image/png" href="{{ asset('yggdra-192.png') }}" sizes="192x192">
        <link rel="apple-touch-icon" href="{{ asset('yggdra-180.png') }}">
        <link rel="mask-icon" href="{{ asset('yggdra.svg') }}" color="#0CE0D8"><!-- opcional se tiver SVG -->
        <meta name="theme-color" content="#0CE0D8"> 
        
        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <script>window.__LOCALE__ = "{{ app()->getLocale() }}";</script>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @routes
        @vite(['resources/js/app.js'])
        @inertiaHead
    </head>
    <body class="font-sans antialiased">
        @inertia
    </body>
</html>
