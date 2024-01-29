<x-modal.content title="{{ $title }}">
    <form wire:submit="save">
        <div class="space-y-4">
            <div>
                <x-label for="name" value="{{ __('Name') }}" />
                <x-input id="name" type="text" class="mt-1 block w-full" wire:model="category.name" />
                <x-input-error for="name" class="mt-2" />
            </div>
            <div class="flex flex-wrap items-center space-x-1">
                <x-input id="color" type="color" wire:model="category.color" />
                <x-label for="color" value="{{ __('Color') }}" />
                <x-input-error for="color" class="mt-2 w-full" />
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
