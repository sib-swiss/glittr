@props(['text' => ''])
<header class="" id="header">
    <div class="flex flex-col lg:flex-row items-center justify-between my-4">
        <a href="{{ route('homepage')}}" class="flex items-center space-x-2 justify-center py-4">
            <img src="{{ url('/sib-web-logo.svg') }}" class="max-w-full h-20" />
        </a>
        {{ $text }}
    </div>
</header>
