<div>
    <x-header title="Repositories">
        <x-header.actions>
            <x-jet-button wire:click="$set('showAdd', true)" class="space-x-2">
                <x-heroicon-o-plus class="w-6 h-6" />
                <span>{{ __('Add a repository') }}</span>
            </x-jet-button>
        </x-header.actions>
    </x-header>
    <x-admin.container>
        <x-table class="mb-4">
            <thead>
                <x-table.header>ID</x-table.header>
                <x-table.header></x-table.header>
            </thead>
            <tbody>
                @foreach($repositories as $repository)
                <x-table.row>
                    <x-table.cell>{{ $repository->id }}</x-table.cell>
                    <x-table.cell width="60">
                        <x-jet-button wire:click="editRepository({{ $repository->id }})">
                            <x-heroicon-m-pencil class="w-4 h-4" />
                        </x-jet-button>
                    </x-table.cell>
                </x-table.row>
                @endforeach
            </tbody>
        </x-table>
        {{ $repositories->links() }}
    </x-admin.container>

    <!-- Add Repository Form Modal -->
    <x-jet-modal wire:model="showAdd" persisted="true">
        @livewire('admin.repository-form', [null, 'addRepositoryCancel'], key("repositoryAdd-{$addIncrement}"))
    </x-jet-modal>
    <!-- Edit Repository Form Modal -->
    <x-jet-modal wire:model="showEdit" persisted="true">
        @if($repositoryBeingUpdated)
            @livewire('admin.repository-form', [$repositoryBeingUpdated, 'editRepositoryCancel'], key("repositoryEdit-{$repositoryBeingUpdated}"))
        @endif
    </x-jet-modal>

</div>
