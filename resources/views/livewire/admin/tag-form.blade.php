<x-modal.content title="{{ $title }}">
    <div class="space-y-4">
        <div class="flex flex-wrap items-center space-x-1">
            <x-jet-label for="category_id" value="{{ __('Category') }}" />
            <x-select id="category_id" class="mt-1 block w-full" type="color" wire:model.defer="tag.category_id">
                <option>- {{ __('Choose a category') }} -</option>
                @foreach ($categories as $category_id => $category_name)
                    <option value="{{ $category_id }}">{{ $category_name }}</option>
                @endforeach
            </x-select>
            <x-jet-input-error for="category_id" class="mt-2 w-full" />
        </div>
        <div>
            <x-jet-label for="name" value="{{ __('Name') }}" />
            <x-jet-input id="name" type="text" class="mt-1 block w-full" wire:model.defer="tag.name" />
            <x-jet-input-error for="name" class="mt-2" />
        </div>

    </div>
    <x-slot name="footer">
        @if($cancelEvent != '')
            <x-jet-secondary-button wire:click="$emit('{{ $cancelEvent }}')">{{ __('Cancel') }}</x-jet-seconday-button>
        @endif
        <x-jet-button wire:click="save">{{ Str::headline($action) }}</x-jet-button>
    </x-slot>
</x-modal.content>
