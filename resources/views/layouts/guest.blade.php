<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ $title ?? $site_name }}</title>
        <meta name="description" content="{{ $site_description }}">

        {{-- FAVICON --}}
        <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
        <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
        <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
        <link rel="manifest" href="/site.webmanifest">
        <link rel="mask-icon" href="/safari-pinned-tab.svg" color="#8338ec">
        <meta name="msapplication-TileColor" content="#603cba">
        <meta name="theme-color" content="#ffffff">

        {{-- SOCIAL SHARING --}}
        <meta property="og:title" content="{{ config('glittr.og.title', $title ?? $site_name) }}">
        <meta property="og:type" content="website" />
        <meta property="og:description" content="{{ config('glittr.og.description', $site_description) }}">
        <meta property="og:image" content="{{ url('og.jpg') }}">
        <meta property="og:url" content="{{ url('/') }}">
        <meta name="twitter:card" content="summary_large_image">

        <!-- Fonts -->
        <link rel="stylesheet" href="https://fonts.bunny.net/css?family=source-sans-3:200,400,600,700&display=swap">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        @if (config('glittr.google_analytics'))
            <!-- Google Analytics -->
            <script async src="https://www.googletagmanager.com/gtag/js?id={{ config('glittr.google_analytics') }}"></script>
            <script>
                window.dataLayer = window.dataLayer || [];
                function gtag(){dataLayer.push(arguments);}
                gtag('js', new Date());

                gtag('config', '{{ config('glittr.google_analytics') }}');
            </script>
        @endif
        @if (config('glittr.matomo.url') && config('glittr.matomo.site_id'))
            <!-- Matomo -->
            <script>
                var _paq = window._paq = window._paq || [];
                /* tracker methods like "setCustomDimension" should be called before "trackPageView" */
                _paq.push(['trackPageView']);
                _paq.push(['enableLinkTracking']);
                (function() {
                var u="{{ config('glittr.matomo.url') }}";
                _paq.push(['setTrackerUrl', u+'matomo.php']);
                _paq.push(['setSiteId', '{{ config('glittr.matomo.site_id') }}']);
                var d=document, g=d.createElement('script'), s=d.getElementsByTagName('script')[0];
                g.async=true; g.src=u+'matomo.js'; s.parentNode.insertBefore(g,s);
                })();
            </script>
            <!-- End Matomo Code -->
        @endif

        <!-- Styles -->
        <style>
            [x-cloak] { display: none; }
            <x-categories-colors />
        </style>
        @livewireStyles
        @stack('head')
    </head>
    <body class="flex flex-col min-h-screen font-sans text-sm antialiased text-gray-800 bg-white scroll-smooth lg:text-base">
        <div class="flex flex-col flex-1 ">
            <div class="h-2 bg-glittr {{ $sidebar ? '2xl:mr-[360px]' : '' }}">
            </div>
            {{ $slot }}
        </div>
        @livewireScriptConfig
    </body>
</html>
