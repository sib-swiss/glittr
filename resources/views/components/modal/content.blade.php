@props(['title' => null])

<div class="flex-1 flex flex-col">
    <div class="px-6 py-4">
        @if($title)
            <div class="text-lg">
                {{ $title }}
            </div>
        @endif

        <div class="mt-4">
            {{ $slot }}
        </div>
    </div>

    <div class="flex flex-row justify-end px-6 py-4 bg-gray-100 text-right space-x-2">
        {{ $footer ?? '' }}
    </div>
</div>
