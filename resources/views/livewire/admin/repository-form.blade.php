<x-modal.content title="{{ $action }} Repository">
    <div class="space-y-4">
        <div>
            <x-jet-label for="url" value="{{ __('Repository Url') }}" />
            <x-jet-input id="url" type="text" class="mt-1 block w-full" wire:model.defer="repository.url" />
            <x-jet-input-error for="repository.url" class="mt-2" />
        </div>
        <div class="flex flex-wrap items-center space-x-1">
            <x-jet-label for="tags" value="{{ __('Tags') }}" />
            @livewire('tag-select', ['selected' => $repository['tags']])
            <x-jet-input-error for="repository.tags" class="mt-2 w-full" />
        </div>

    </div>
    <x-slot name="footer">
        @if($cancelEvent != '')
            <x-jet-secondary-button wire:click="$emit('{{ $cancelEvent }}')">{{ __('Cancel') }}</x-jet-seconday-button>
        @endif
        <x-jet-button wire:click="save">{{ Str::headline($action) }}</x-jet-button>
    </x-slot>
</x-modal.content>
