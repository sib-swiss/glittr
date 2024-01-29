<div class="block w-full mt-1">
    <ul class="mb-1" x-data="{ sortable: null }" x-init="
        sortable = new Sortable($el, {
            group: 'tags',
            draggable: '.tag',
            onUpdate: function (evt)
            {
                $wire.sort(sortable.toArray())
            }
        })
    ">
        @foreach ($selected as $index => $sel)
            <li class="flex items-center space-x-2 p-2 bg-white border tag" data-id="{{ $index }}">
                <div class="flex-1 flex items-center cursor-move">
                    <x-heroicon-o-chevron-up-down class="w-6 h-6 mr-4" />
                    <div class="leading-tight">
                        <div class="font-bold tracking-tight">
                            {{ $sel['category'] }}
                        </div>
                        <div class="">
                            {{ $sel['tag'] }}
                        </div>
                    </div>
                </div>
                <button type="button"  class="p-4" wire:click.prevent="remove({{ $index }})">
                    <x-heroicon-m-x-circle class="w-6 h-6"  />
                </button>
            </li>
        @endforeach
    </ul>
    <div class="text-sm font-light">
        {{ __('The first topic will be used as the main topic for exports.') }}
    </div>
    <div class="flex items-center space-x-2 mt-1">
        <x-select class="w-1/2 " wire:model.live="add" @keydown.enter="add">
            <option>- {{ __('Select a topic') }} -</option>
            @foreach ($categories as $category)
            <optgroup label="{{ $category->name }}">
                    @foreach ($category->tags as $tag)
                        <option value="{{ $tag->id }}">
                            {{ $tag->name }}
                        </option>
                    @endforeach
                </optgroup>
            @endforeach
        </x-select>

        <x-button type="button" class="{{ $add > 0 ? 'opacity-100':'opacity-50' }} flex items-center justify-center space-x-1 w-1/2" wire:click.prevent="add">
            <x-heroicon-o-plus class="w-5 h-5" />
            <span>Add this topic</span>
        </x-button>
    </div>
    @if ($mainTag)
        <div class="text-sm my-2">
            {{ __('The main topic for this repository will be: ') }}<strong>{{ $mainTag['category'] }}: {{ $mainTag['tag']}}</strong>
        </div>
    @endif
</div>
