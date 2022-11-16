@props(['text' => false, 'container' => true])
<header {!! $attributes->merge(['class' => 'py-4']) !!} id="header">
    <div class="{{ $container ? 'container':'test' }} flex flex-wrap lg:flex-nowrap items-center justify-center lg:justify-between my-4 space-y-4 lg:space-y-0">
        <a href="{{ route('homepage')}}" class=" flex items-center justify-center space-x-2">
            <img src="{{ url('/glitter_logo.png') }}" class="max-w-full h-32" />
        </a>
        <div class="lg:order-2 lg:text-right leading-tight ">
            <a href="{{ route('homepage')}}" class="hidden lg:block">
                <img src="{{ url('/sib-web-logo.svg') }}" class="max-w-full h-20" />
            </a>
        </div>
        @if ($text)
            <div class="prose prose-sm w-full lg:w-auto lg:flex-1  py-2 leading-tight relative lg:mx-8 text-center">
                {{ $text }}
            </div>
        @endif
    </div>
</header>
