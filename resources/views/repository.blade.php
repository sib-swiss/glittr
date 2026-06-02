<x-guest-layout :title="$title" :description="$description ?? null">
    @push('head')
        <script type="application/ld+json">{!! json_encode($jsonLd, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
    @endpush

    <x-page-header class="border-b" />

    <div class="flex-1">

        {{-- ── Hero ────────────────────────────────────────────────────── --}}
        <div class="container py-8 lg:py-12">

            {{-- Breadcrumb --}}
            <nav class="flex items-center gap-1.5 text-sm mb-8" aria-label="Breadcrumb">
                <a href="{{ route('homepage') }}"
                   class="text-gray-400 hover:text-gray-700 transition-colors">
                    {{ __('Repositories') }}
                </a>
                @if ($repository->author)
                    <x-heroicon-m-chevron-right class="w-3.5 h-3.5 text-gray-300 flex-shrink-0" />
                    <a href="{{ route('author', ['slug' => $repository->author->slug]) }}"
                       class="text-gray-400 hover:text-gray-700 transition-colors truncate max-w-[12rem]">
                        {{ $repository->author->display_name ?: $repository->author->name }}
                    </a>
                @endif
                <x-heroicon-m-chevron-right class="w-3.5 h-3.5 text-gray-300 flex-shrink-0" />
                <span class="text-gray-600 font-medium truncate max-w-[16rem]">{{ $repository->name }}</span>
            </nav>

            <div class="lg:flex lg:items-start lg:justify-between lg:gap-10">

                {{-- Title + meta --}}
                <div class="flex-1 min-w-0">

                    {{-- API badge --}}
                    @if ($repository->api)
                        <div class="mb-3">
                            <span class="inline-flex items-center gap-1.5 text-xs font-semibold tracking-widest uppercase px-2.5 py-1 rounded-full border border-gray-200 text-gray-500 bg-gray-50">
                                @if ($repository->api === 'github')
                                    <svg class="w-3.5 h-3.5" viewBox="0 0 496 512" fill="currentColor">
                                        <path d="M165.9 397.4c0 2-2.3 3.6-5.2 3.6-3.3.3-5.6-1.3-5.6-3.6 0-2 2.3-3.6 5.2-3.6 3-.3 5.6 1.3 5.6 3.6zm-31.1-4.5c-.7 2 1.3 4.3 4.3 4.9 2.6 1 5.6 0 6.2-2s-1.3-4.3-4.3-5.2c-2.6-.7-5.5.3-6.2 2.3zm44.2-1.7c-2.9.7-4.9 2.6-4.6 4.9.3 2 2.9 3.3 5.9 2.6 2.9-.7 4.9-2.6 4.6-4.6-.3-1.9-3-3.2-5.9-2.9zM244.8 8C106.1 8 0 113.3 0 252c0 110.9 69.8 205.8 169.5 239.2 12.8 2.3 17.3-5.6 17.3-12.1 0-6.2-.3-40.4-.3-61.4 0 0-70 15-84.7-29.8 0 0-11.4-29.1-27.8-36.6 0 0-22.9-15.7 1.6-15.4 0 0 24.9 2 38.6 25.8 21.9 38.6 58.6 27.5 72.9 20.9 2.3-16 8.8-27.1 16-33.7-55.9-6.2-112.3-14.3-112.3-110.5 0-27.5 7.6-41.3 23.6-58.9-2.6-6.5-11.1-33.3 2.6-67.9 20.9-6.5 69 27 69 27 20-5.6 41.5-8.5 62.8-8.5s42.8 2.9 62.8 8.5c0 0 48.1-33.6 69-27 13.7 34.7 5.2 61.4 2.6 67.9 16 17.7 25.8 31.5 25.8 58.9 0 96.5-58.9 104.2-114.8 110.5 9.2 7.9 17 22.9 17 46.4 0 33.7-.3 75.4-.3 83.6 0 6.5 4.6 14.4 17.3 12.1C428.2 457.8 496 362.9 496 252 496 113.3 383.5 8 244.8 8z"/>
                                    </svg>
                                @elseif ($repository->api === 'gitlab')
                                    <svg class="w-3.5 h-3.5" viewBox="0 0 380 380" fill="currentColor">
                                        <path d="M282.83 170.73l-.27-.69-26.14-68.22a6.81 6.81 0 00-2.69-3.24 7 7 0 00-8 .43 7 7 0 00-2.32 3.52l-17.65 54H154.29l-17.65-54a6.86 6.86 0 00-2.32-3.52 7 7 0 00-8-.43 6.85 6.85 0 00-2.69 3.24L97.44 170l-.26.69a48.54 48.54 0 0016.1 56.1l.09.07.24.17 39.82 29.82 19.7 14.91 12 9.06a8.07 8.07 0 009.63 0l12-9.06 19.7-14.91 40.06-30 .1-.08a48.56 48.56 0 0016.07-56.04z"/>
                                    </svg>
                                @endif
                                {{ $repository->api }}
                            </span>
                        </div>
                    @endif

                    {{-- Repository name --}}
                    <h1 class="text-3xl lg:text-4xl font-bold tracking-tight text-glittr-black leading-tight mb-3">
                        {{ $repository->name }}
                    </h1>

                    {{-- URL link --}}
                    <a href="{{ $repository->url }}" target="_blank" rel="noopener"
                       class="inline-flex items-center gap-1.5 text-sm text-gray-400 hover:text-glittr-blue transition-colors mb-5 font-mono break-all">
                        {{ $repository->url }}
                        <x-heroicon-m-arrow-top-right-on-square class="w-3.5 h-3.5 flex-shrink-0" />
                    </a>

                    {{-- Description --}}
                    @if ($repository->description)
                        <p class="text-base lg:text-lg text-gray-700 leading-relaxed max-w-2xl mb-6">
                            {{ $repository->description }}
                        </p>
                    @endif

                    {{-- Topics --}}
                    @if ($repository->tags->isNotEmpty())
                        <div class="mt-6">
                            <h2 class="flex items-center gap-1.5 text-xs font-semibold tracking-widest uppercase text-gray-400 mb-3">
                                <x-heroicon-o-hashtag class="w-3.5 h-3.5" />
                                {{ __('Topics') }}
                            </h2>
                            <div class="flex flex-wrap gap-2">
                                @foreach ($repository->tags as $tag)
                                    <a href="{{ route('homepage', ['tags' => $tag->id]) }}"
                                       class="tag-category-{{ $tag->category_id }} text-base font-medium tracking-wide rounded py-1.5 px-4 bg-category-color text-white hover:opacity-80 transition-opacity">
                                        {{ $tag->name }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Right: Stars + action buttons --}}
                <div class="mt-8 lg:mt-0 lg:flex-shrink-0 flex flex-col items-stretch gap-3 lg:w-44">
                    @if ($repository->stargazers)
                        <div class="flex flex-col items-center justify-center gap-1.5 border border-yellow-200 bg-yellow-50 rounded-xl px-8 py-5">
                            <x-heroicon-s-star class="w-6 h-6 text-yellow-400" />
                            <span class="text-3xl font-bold tracking-tight text-gray-800 tabular-nums">
                                {{ number_format($repository->stargazers) }}
                            </span>
                            <span class="text-xs font-semibold uppercase tracking-widest text-gray-400">
                                {{ __('stars') }}
                            </span>
                        </div>
                    @endif

                    <a href="{{ $repository->url }}" target="_blank" rel="noopener"
                       class="inline-flex items-center justify-center gap-2 px-4 py-3 text-sm font-semibold tracking-wide uppercase text-white bg-gray-700 rounded-lg hover:bg-gray-900 transition-colors">
                        <x-heroicon-m-code-bracket class="w-4 h-4" />
                        {{ __('Repository') }}
                    </a>

                    @if ($repository->website && (string) $repository->website !== '')
                        <a href="{{ $repository->website }}" target="_blank" rel="noopener"
                           class="inline-flex items-center justify-center gap-2 px-4 py-3 text-sm font-semibold tracking-wide uppercase text-white bg-blue-500 rounded-lg hover:bg-blue-700 transition-colors">
                            <x-heroicon-m-arrow-top-right-on-square class="w-4 h-4" />
                            {{ __('Website') }}
                        </a>
                    @endif
                </div>
            </div>
        </div>

        {{-- ── Gradient rule ───────────────────────────────────────────── --}}
        <div class="h-px bg-glittr"></div>

        {{-- ── Main content ─────────────────────────────────────────────── --}}
        <div class="container py-8 lg:py-10">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 lg:gap-12">

                {{-- Left: Author + Details ──────────────────────────── --}}
                <div class="space-y-6">

                    {{-- Owner --}}
                    @if ($repository->author)
                        <div>
                            <h2 class="text-xs font-semibold tracking-widest uppercase text-gray-400 mb-3">
                                {{ __('Owner') }}
                            </h2>
                            <div class="p-4 bg-gray-50 rounded-xl border border-gray-100">

                                {{-- Avatar + name --}}
                                <div class="flex items-center gap-3 mb-3">
                                    @if ($repository->author->avatar_url)
                                        <img src="{{ $repository->author->avatar_url }}"
                                             alt="{{ $repository->author->display_name ?: $repository->author->name }}"
                                             class="w-16 h-16 rounded-full flex-shrink-0 ring-2 ring-white shadow-sm"
                                             loading="lazy" decoding="async" />
                                    @else
                                        <div class="w-16 h-16 rounded-full flex-shrink-0 bg-gray-200 flex items-center justify-center text-xl font-bold text-gray-400">
                                            {{ strtoupper(substr($repository->author->name, 0, 1)) }}
                                        </div>
                                    @endif
                                    <div class="min-w-0">
                                        <div class="font-bold text-gray-900 text-base leading-tight">
                                            {{ $repository->author->display_name ?: $repository->author->name }}
                                        </div>
                                        @if ($repository->author->display_name && $repository->author->display_name !== $repository->author->name)
                                            <div class="text-xs text-gray-400 mt-0.5 font-mono truncate">{{ $repository->author->name }}</div>
                                        @endif
                                    </div>
                                </div>

                                {{-- Metadata --}}
                                @if ($repository->author->company || $repository->author->location || $repository->author->twitter_username || ($repository->author->url && (string) $repository->author->url !== '') || ($repository->author->website && (string) $repository->author->website !== ''))
                                    <div class="space-y-2 mb-4">
                                        @if ($repository->author->company)
                                            <div class="flex items-center gap-2 text-sm text-gray-600">
                                                <x-heroicon-o-building-office-2 class="w-4 h-4 text-gray-400 flex-shrink-0" />
                                                <span class="truncate">{{ $repository->author->company }}</span>
                                            </div>
                                        @endif
                                        @if ($repository->author->location)
                                            <div class="flex items-center gap-2 text-sm text-gray-600">
                                                <x-heroicon-o-map-pin class="w-4 h-4 text-gray-400 flex-shrink-0" />
                                                <span class="truncate">{{ $repository->author->location }}</span>
                                            </div>
                                        @endif
                                        @if ($repository->author->twitter_username)
                                            <div class="flex items-center gap-2 text-sm text-gray-600">
                                                <svg class="w-4 h-4 text-gray-400 flex-shrink-0" viewBox="0 0 24 24" fill="currentColor">
                                                    <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-4.714-6.231-5.401 6.231H2.744l7.736-8.873L1.54 2.25H8.08l4.253 5.622 5.91-5.622zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                                                </svg>
                                                <a href="https://twitter.com/{{ $repository->author->twitter_username }}" target="_blank" rel="noopener"
                                                   class="hover:text-blue-500 transition-colors truncate">
                                                    {{ '@' . $repository->author->twitter_username }}
                                                </a>
                                            </div>
                                        @endif
                                        @if ($repository->author->url && (string) $repository->author->url !== '')
                                            <div class="flex items-center gap-2 text-sm text-gray-600">
                                                <svg class="w-4 h-4 text-gray-400 flex-shrink-0" viewBox="0 0 496 512" fill="currentColor">
                                                    <path d="M165.9 397.4c0 2-2.3 3.6-5.2 3.6-3.3.3-5.6-1.3-5.6-3.6 0-2 2.3-3.6 5.2-3.6 3-.3 5.6 1.3 5.6 3.6zm-31.1-4.5c-.7 2 1.3 4.3 4.3 4.9 2.6 1 5.6 0 6.2-2s-1.3-4.3-4.3-5.2c-2.6-.7-5.5.3-6.2 2.3zm44.2-1.7c-2.9.7-4.9 2.6-4.6 4.9.3 2 2.9 3.3 5.9 2.6 2.9-.7 4.9-2.6 4.6-4.6-.3-1.9-3-3.2-5.9-2.9zM244.8 8C106.1 8 0 113.3 0 252c0 110.9 69.8 205.8 169.5 239.2 12.8 2.3 17.3-5.6 17.3-12.1 0-6.2-.3-40.4-.3-61.4 0 0-70 15-84.7-29.8 0 0-11.4-29.1-27.8-36.6 0 0-22.9-15.7 1.6-15.4 0 0 24.9 2 38.6 25.8 21.9 38.6 58.6 27.5 72.9 20.9 2.3-16 8.8-27.1 16-33.7-55.9-6.2-112.3-14.3-112.3-110.5 0-27.5 7.6-41.3 23.6-58.9-2.6-6.5-11.1-33.3 2.6-67.9 20.9-6.5 69 27 69 27 20-5.6 41.5-8.5 62.8-8.5s42.8 2.9 62.8 8.5c0 0 48.1-33.6 69-27 13.7 34.7 5.2 61.4 2.6 67.9 16 17.7 25.8 31.5 25.8 58.9 0 96.5-58.9 104.2-114.8 110.5 9.2 7.9 17 22.9 17 46.4 0 33.7-.3 75.4-.3 83.6 0 6.5 4.6 14.4 17.3 12.1C428.2 457.8 496 362.9 496 252 496 113.3 383.5 8 244.8 8z"/>
                                                </svg>
                                                <a href="{{ url($repository->author->url) }}" target="_blank" rel="noopener"
                                                   class="hover:text-blue-500 transition-colors truncate">
                                                    {{ __('Profile') }}
                                                </a>
                                            </div>
                                        @endif
                                        @if ($repository->author->website && (string) $repository->author->website !== '')
                                            <div class="flex items-center gap-2 text-sm text-gray-600">
                                                <x-heroicon-o-globe-alt class="w-4 h-4 text-gray-400 flex-shrink-0" />
                                                <a href="{{ url($repository->author->website) }}" target="_blank" rel="noopener"
                                                   class="hover:text-blue-500 transition-colors truncate">
                                                    {{ __('Website') }}
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                @endif

                                {{-- See all repositories --}}
                                <div class="pt-3 border-t border-gray-200">
                                    <a href="{{ route('author', ['slug' => $repository->author->slug]) }}"
                                       class="inline-flex items-center gap-1.5 text-sm font-semibold text-blue-500 hover:text-blue-700 transition-colors">
                                        {{ __('See all repositories') }}
                                        <x-heroicon-m-chevron-right class="w-4 h-4" />
                                    </a>
                                </div>

                            </div>
                        </div>
                    @endif

                    {{-- Details --}}
                    <div>
                        <h2 class="text-xs font-semibold tracking-widest uppercase text-gray-400 mb-3">
                            {{ __('Details') }}
                        </h2>
                        <div class="rounded-xl border border-gray-100 overflow-hidden divide-y divide-gray-100">

                            @if ($repository->license)
                                <div class="flex items-center gap-3 px-4 py-3 bg-white">
                                    <x-heroicon-o-scale class="w-4 h-4 text-gray-300 flex-shrink-0" />
                                    <span class="text-xs font-medium tracking-wide uppercase text-gray-400 w-20 flex-shrink-0">{{ __('License') }}</span>
                                    <span class="text-sm font-semibold text-gray-800 truncate">{{ $repository->license }}</span>
                                </div>
                            @endif

                            @if ($repository->last_push)
                                <div class="flex items-center gap-3 px-4 py-3 bg-white">
                                    <div class="w-4 h-4 flex items-center justify-center flex-shrink-0">
                                        <div class="w-2.5 h-2.5 rounded-full {{ $repository->getPushStatusClass() }}"></div>
                                    </div>
                                    <span class="text-xs font-medium tracking-wide uppercase text-gray-400 w-20 flex-shrink-0">{{ __('Last push') }}</span>
                                    <span class="text-sm font-semibold text-gray-800">
                                        {{ $repository->days_since_last_push }} {{ __('days ago') }}
                                    </span>
                                </div>
                            @endif

                            @if ($repository->version)
                                <div class="flex items-center gap-3 px-4 py-3 bg-white">
                                    <x-heroicon-o-tag class="w-4 h-4 text-gray-300 flex-shrink-0" />
                                    <span class="text-xs font-medium tracking-wide uppercase text-gray-400 w-20 flex-shrink-0">{{ __('Version') }}</span>
                                    <span class="text-sm font-semibold text-gray-800 font-mono">{{ $repository->version }}</span>
                                </div>
                            @endif

                            @if ($repository->repository_created_at)
                                <div class="flex items-center gap-3 px-4 py-3 bg-white">
                                    <x-heroicon-o-calendar class="w-4 h-4 text-gray-300 flex-shrink-0" />
                                    <span class="text-xs font-medium tracking-wide uppercase text-gray-400 w-20 flex-shrink-0">{{ __('Created') }}</span>
                                    <span class="text-sm font-semibold text-gray-800">
                                        {{ $repository->repository_created_at->format('M j, Y') }}
                                    </span>
                                </div>
                            @endif

                            <div class="flex items-center gap-3 px-4 py-3 bg-gray-50">
                                <x-heroicon-o-plus-circle class="w-4 h-4 text-gray-300 flex-shrink-0" />
                                <span class="text-xs font-medium tracking-wide uppercase text-gray-400 w-20 flex-shrink-0">{{ __('Added') }}</span>
                                <span class="text-sm text-gray-500">
                                    {{ $repository->created_at->format('M j, Y') }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Right: Contributors ──────────────────────────────── --}}
                <div id="contributors-section" class="lg:col-span-2">
                    <div class="flex items-center gap-2.5 mb-3">
                        <h2 class="text-xs font-semibold tracking-widest uppercase text-gray-400">{{ __('Contributors') }}</h2>
                        @if ($repository->contributors_count > 0)
                            <span class="inline-flex items-center justify-center px-2 py-0.5 text-xs font-semibold rounded-full bg-gray-200 text-gray-600">
                                {{ $repository->contributors_count }}
                            </span>
                        @endif
                    </div>
                    <div class="bg-gray-50 rounded-xl border border-gray-200 px-6 py-6 overflow-hidden">
                        @livewire('repository-contributors', ['repository' => $repository])
                    </div>
                </div>

            </div>
        </div>

    </div>

    <x-page-footer />
</x-guest-layout>
