@props([
    'sortable' => false,
    'current_sort_by' => null,
    'current_sort_direction' => null,
    'sort_by' => '',
    'title',
])
@php
    $class = '';
    if ($sortable) {
        $class = 'cursor-pointer';
    }
    $class .= 'lg:align-middle lg:table-cell bg-gray-800 px-2 py-4 text-white text-sm font-semibold uppercase tracking-wider';
@endphp
<div
    {!! $attributes->merge(['class' => $class]) !!}
    @if ($sortable) wire:click="sortBy('{{ $sort_by }}')" role="button" title="Sort by {{ $title }}" @endif
    >
    <div class="flex items-center justify-between">
        <span class="lg:whitespace-nowrap mr-1">{!! $title !!}</span>
        @if ($sortable)
            @if ($current_sort_by == $sort_by)
                @if ($current_sort_direction == 'asc')
                    <x-heroicon-o-chevron-up class="w-3 h-3" />
                @else
                    <x-heroicon-o-chevron-down class="w-3 h-3" />
                @endif
            @else
                <x-heroicon-o-chevron-up-down class="w-4 h-4 " />
            @endif
        @endif
    </div>
</div>
