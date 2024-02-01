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
            <div class="flex flex-wrap items-center space-x-1">
                <x-label for="ontology_id" value="{{ __('Ontology') }}" />
                <x-select id="ontology_id" class="mt-1 block w-full" type="color" wire:model="tag.ontology_id">
                    <option>- {{ __('Choose an Ontology') }} -</option>
                    @foreach ($ontologies as $ontology_id => $ontology_name)
                        <option value="{{ $ontology_id }}">{{ $ontology_name }}</option>
                    @endforeach
                </x-select>
                <x-input-error for="ontology_id" class="mt-2 w-full" />
            </div>
            <div>
                <x-label for="ontology_class" value="{{ __('Ontology Class') }}" />
                <x-input id="ontology_class" type="text" class="mt-1 block w-full" wire:model="tag.ontology_class" />
                <x-input-error for="ontology_class" class="mt-2" />
            </div>
            <div>
                <x-label for="term_code" value="{{ __('Term Code') }}" />
                <x-input id="term_code" type="text" class="mt-1 block w-full" wire:model="tag.term_code" />
                <x-input-error for="term_code" class="mt-2" />
                <div class="text-sm text-gray-500">
                    {{ __('Used in bioschemas export if the linked Ontology has a Term Set URL defined.') }}
                </div>
            </div>
            <div>
                <x-label for="link" value="{{ __('Link (URL)') }}" />
                <x-input id="link" type="text" class="mt-1 block w-full" wire:model="tag.link" />
                <x-input-error for="link" class="mt-2" />
            </div>
            <div>
                <x-label for="tag_description" value="{{ __('Description') }}" />
                <x-textarea rows="3" id="tag_description" class="mt-1 block w-full" wire:model="tag.description" />
                <x-input-error for="description" class="mt-2" />
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
