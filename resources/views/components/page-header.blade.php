@props(['text' => false, 'container' => true])
<header {!! $attributes->merge(['class' => 'py-4']) !!} id="header">
    <div class="{{ $container ? 'container':'test' }} flex flex-wrap lg:flex-nowrap items-center justify-center lg:justify-start space-y-4 lg:space-y-0">
        <a href="{{ route('homepage')}}" class="px-4 lg:pl-0" aria-label="{{ __('Glittr homepage') }}">
            <picture>
                <source media="(min-width: 1536px)" srcset="{{ url('/logo-horizontal.svg') }}" />
                <img
                    src="{{ url('/logo-vertical.svg') }}"
                    class="block h-[150px] 2xl:h-36 w-auto max-w-full"
                    width="111"
                    height="65"
                    alt="{{ __('Glittr logo') }}"
                    fetchpriority="high"
                    loading="eager"
                    decoding="async"
                />
            </picture>
        </a>

        @if ($text)
            <div class="lg:flex-1 lg:ml-4">
                {{ $text }}
            </div>
        @endif
    </div>
</header>
