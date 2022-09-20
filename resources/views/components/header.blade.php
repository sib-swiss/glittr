@props(['title' => null])

<header class="bg-white shadow">
    <div {{ $attributes->merge(['class' => 'max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 flex items-center justify-between']) }}>
        @if($title)
            <x-header.title>
                {{ $title }}
            </x-header.title>
        @endif
        {{ $slot }}
    </div>
</header>
