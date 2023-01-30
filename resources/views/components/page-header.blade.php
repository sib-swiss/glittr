@props(['text' => false, 'container' => true])
<header {!! $attributes->merge(['class' => 'py-4']) !!} id="header">
    <div class="{{ $container ? 'container':'test' }} flex flex-wrap lg:flex-nowrap items-center justify-center lg:justify-start space-y-4 lg:space-y-0">
        <a href="{{ route('homepage')}}" class="px-4 lg:pl-0">
            <img src="{{ url('/logo-horizontal.svg') }}" class="max-w-full h-36 hidden 2xl:block" />
            <img src="{{ url('/logo-vertical.svg') }}" class="max-w-full h-32 block 2xl:hidden" />
        </a>

        @if ($text)
            <div class="lg:flex-1 lg:ml-4">
                {{ $text }}
            </div>
        @endif
    </div>
</header>
