@props(['text' => false])
<header class="py-4" id="header">
    <div class="flex flex-wrap lg:flex-nowrap items-strecth lg:items-start justify-between my-4 space-y-4 lg:space-y-0">
        <a href="{{ route('homepage')}}" class="hidden lg:block">
            <img src="{{ url('/sib-web-logo.svg') }}" class="max-w-full h-20" />
        </a>
        <div class="lg:order-2 lg:text-right leading-tight flex items-center justify-center space-x-2">
            <a href="{{ route('homepage')}}" class="lg:hidden">
                <img src="{{ url('/sib-emblem.svg') }}" class="h-12" />
            </a>
            <div class="leading-tight">
                <h1 class="font-bold text-xl lg:text-2xl tracking-tighter text-primary">
                    {{ config('app.name') }}
                </h1>
                <div class="font-semibold tracking-tight">
                    Curated list of Bioinformatics training materials
                </div>
            </div>
        </div>
        @if ($text)
            <div class="prose prose-sm w-full lg:w-auto lg:flex-1 border-t border-b py-2 leading-tight relative lg:mx-8 text-center">
                {{ $text }}
            </div>
        @endif
    </div>
</header>
