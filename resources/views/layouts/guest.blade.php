<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <link rel="icon" type="image/x-icon" href="/favicon.ico">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="stylesheet" href="https://fonts.bunny.net/css?family=source-sans-3:200,400,600,700&display=swap">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Styles -->
        <style>
            [x-cloak] { display: none; }
            <x-categories-colors />
        </style>
        @livewireStyles
        @stack('head')
    </head>
    <body class="font-sans antialiased scroll-smooth bg-white text-gray-800 min-h-screen flex flex-col text-sm lg:text-base">
        <div class="flex-1 flex flex-col ">
            <div class="h-2 bg-glittr {{ $sidebar ? '2xl:mr-[360px]' : '' }}">
            </div>
            {{ $slot }}
        </div>
        @livewireScripts
    </body>
</html>
