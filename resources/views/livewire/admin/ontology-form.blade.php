<x-modal.content title="{{ $title }}">
    <form wire:submit="save">
        <div class="space-y-4">
            <div>
                <x-label for="name" value="{{ __('Name') }}" />
                <x-input id="name" type="text" class="mt-1 block w-full" wire:model="ontology.name" />
                <x-input-error for="name" class="mt-2" />
            </div>
            <div>
                <x-label for="term_set" value="{{ __('Term Set (URL used for bioschemas)') }}" />
                <x-input id="term_set" type="text" class="mt-1 block w-full" wire:model="ontology.term_set" />
                <x-input-error for="term_set" class="mt-2" />
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
