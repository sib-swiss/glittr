@props([
    'sortable' => false,
    'current_sort_by' => null,
    'current_sort_direction' => null,
    'sort_by' => '',
    'title',
])

<div
    @if ($sortable) wire:click="sortBy('{{ $sort_by }}')" role="button" title="Sort by {{ $title }}" @endif
    class="@if ($sortable) cursor-pointer @endif lg:align-middle lg:table-cell bg-gray-800 p-2 text-white text-sm tracking-wider">
    <div class="flex items-center justify-between">
        <span class="mr-1">{{ $title }}</span>
        @if ($sortable)
            @if ($current_sort_by == $sort_by)
                @if ($current_sort_direction == 'asc')
                    <x-heroicon-o-chevron-up class="w-3 h-3" />
                @else
                    <x-heroicon-o-chevron-down class="w-3 h-3" />
                @endif
            @else
                <x-heroicon-o-chevron-up-down class="w-4 h-4 opacity-50" />
            @endif
        @endif
    </div>
</div>
