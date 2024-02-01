<div>
    <x-header title="Ontologies">
        <x-header.actions>
            <x-button wire:click="add" class="space-x-2">
                <x-heroicon-o-plus class="w-6 h-6" />
                <span>{{ __('Ontology') }}</span>
            </x-button>
        </x-header.actions>
    </x-header>
    <x-admin.container>
        <x-table class="mb-4">
            <thead>
                <x-table.header>{{ __('ID') }}</x-table.header>
                <x-table.header>{{ __('Name') }}</x-table.header>
                <x-table.header></x-table.header>
            </thead>
            <tbody>
                @foreach($ontologies as $ontology)
                <x-table.row class="bg-white hover:bg-gray-50">
                    <x-table.cell>{{ $ontology->id }}</x-table.cell>
                    <x-table.cell>{{ $ontology->name }}</x-table.cell>
                    <x-table.cell>
                        <x-button title="{{ __('Edit ontology') }}" wire:click="edit({{ $ontology->id }})">
                            <x-heroicon-m-pencil class="w-4 h-4" />
                        </x-button>
                        @if ($ontology->tags->count() === 0)
                            <x-danger-button title="{{ __('Remove ontology') }}" wire:click="delete({{ $ontology->id }})">
                                <x-heroicon-o-trash class="w-4 h-4" />
                            </x-danger-button>
                        @endif
                    </x-table.cell>
                </x-table.row>
                @endforeach
            </tbody>
        </x-table>
        <!-- Edit Ontology Form Modal -->
        <x-modal wire:model.live="showEdit" persisted="true">
            @if ($idBeingUpdated)
                @livewire('admin.ontology-form', [$idBeingUpdated, 'editOntologyCancel'], key("ontologyUpdate-{$idBeingUpdated}"))
            @endif
        </x-modal>

        <!-- Add Ontology Form Modal -->
        <x-modal wire:model.live="showAdd" persisted="true">
            @livewire('admin.ontology-form', [null, 'addOntologyCancel'], key("ontologyAdd-{$addIncrement}"))
        </x-modal>

        <!-- Delete Ontology Confirmation Modal -->
        <x-confirmation-modal wire:model.live="confirmingDeletion">
            <x-slot name="title">
                {{ __('Delete') }}
            </x-slot>

            <x-slot name="content">
                {{ __('Are you sure you would like to delete this ontology?') }}
            </x-slot>

            <x-slot name="footer">
                <x-secondary-button wire:click="$set('confirmingDeletion', false)" wire:loading.attr="disabled">
                    {{ __('Cancel') }}
                </x-secondary-button>

                <x-danger-button class="ml-3" wire:click="deleteConfirm" wire:loading.attr="disabled">
                    {{ __('Delete') }}
                </x-danger-button>
            </x-slot>
        </x-confirmation-modal>
    </x-admin.container>
</div>
