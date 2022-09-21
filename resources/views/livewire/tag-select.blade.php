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
            <li class="flex items-center space-x-2 p-2 border tag" data-id="{{ $index }}">
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
                <button class="p-4" wire:click="remove({{ $index }})">
                    <x-heroicon-m-x-circle class="w-6 h-6"  />
                </button>
            </li>
        @endforeach
    </ul>
    <div class="text-sm font-light">
        {{ __('The first tag will be used as the main tag for exports.') }}
    </div>

    <x-select class="block w-full mt-1" wire:model="add">
        <option>- {{ __('Add a tag') }} -</option>
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
    @if ($mainTag)
    <div class="text-sm my-2">
        {{ __('The main tag for this repository will be: ') }}<strong>{{ $mainTag['category'] }}: {{ $mainTag['tag']}}</strong>
    </div>
    @endif
</div>
