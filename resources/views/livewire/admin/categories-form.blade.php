<x-modal.content title="{{ Str::headline($action. ' Category') }}">
    <div class="space-y-4">
        <div>
            <x-jet-label for="name" value="{{ __('Name') }}" />
            <x-jet-input id="name" type="text" class="mt-1 block w-full" wire:model.defer="category.name" />
            <x-jet-input-error for="name" class="mt-2" />
        </div>
        <div class="flex flex-wrap items-center space-x-1">
            <x-jet-input id="color" type="color" wire:model.defer="category.color" />
            <x-jet-label for="color" value="{{ __('Color') }}" />
            <x-jet-input-error for="color" class="mt-2 w-full" />
        </div>
    </div>
    <x-slot name="footer">
        <x-jet-secondary-button wire:click="cancel">{{ __('Cancel') }}</x-jet-seconday-button>
        <x-jet-button wire:click="save">{{ Str::headline($action) }}</x-jet-button>
    </x-slot>
</x-modal.content>
