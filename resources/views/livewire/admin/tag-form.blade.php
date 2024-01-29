<x-modal.content title="{{ $title }}">
    <form wire:submit="save">
        <div class="space-y-4">
            <div class="flex flex-wrap items-center space-x-1">
                <x-label for="category_id" value="{{ __('Category') }}" />
                <x-select id="category_id" class="mt-1 block w-full" type="color" wire:model="tag.category_id">
                    <option>- {{ __('Choose a category') }} -</option>
                    @foreach ($categories as $category_id => $category_name)
                        <option value="{{ $category_id }}">{{ $category_name }}</option>
                    @endforeach
                </x-select>
                <x-input-error for="category_id" class="mt-2 w-full" />
            </div>
            <div>
                <x-label for="name" value="{{ __('Name') }}" />
                <x-input id="name" type="text" class="mt-1 block w-full" wire:model="tag.name" />
                <x-input-error for="name" class="mt-2" />
            </div>

        </div>
        <x-slot name="footer">
            @if($cancelEvent != '')
                <x-secondary-button wire:click.prevent="$dispatch('{{ $cancelEvent }}')">{{ __('Cancel') }}</x-seconday-button>
            @endif
            <x-button wire:click.prevent="save">{{ Str::headline($action) }}</x-button>
        </x-slot>
    </form>
</x-modal.content>
