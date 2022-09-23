<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

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
    <body class="font-sans text-gray-800 antialiased min-h-screen flex flex-col text-sm lg:text-base">
        <header class="bg-white border-b" id="header">
            <div class="container flex items-center space-x-4 py-2">
                <img src="{{ url('/sib-emblem.svg') }}" class="max-w-full h-12" />
                <h1 class="text-lg lg:text-xl font-semibold tracking-tight">
                    {{ __('Training Collection') }}
                </h1>
                @livewire('search-bar')
            </div>
        </header>
        <div class="flex-1 bg-gray-100 py-8 lg:py-12">
            <div class="container">
                {{ $slot }}
            </div>
        </div>
        <footer class="bg-primary text-white text-sm text-center p-2">
            footer
        </footer>
        @livewireScripts
    </body>
</html>
