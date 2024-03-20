<x-guest-layout :title="$title">
    <x-page-header class="border-b"></x-page-header>
    <div class="flex flex-col flex-1">
        <div class="container max-w-5xl py-8 lg:py-12 xl:py-20">
            <div class="prose-sm prose lg:prose-base max-w-none">
                {!! $terms !!}
            </div>
        </div>
    </div>
    <x-page-footer />
</x-guest-layout>
