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
                <x-table.header>URL</x-table.header>
                <x-table.header>API</x-table.header>
                <x-table.header>Website</x-table.header>
                <x-table.header>Author</x-table.header>
                <x-table.header>Updated</x-table.header>
                <x-table.header></x-table.header>
            </thead>
            <tbody>
                @foreach($repositories as $repository)
                <x-table.row class="{{ $repository->enabled ? 'bg-white':'bg-red-50' }}">
                    <x-table.cell>{{ $repository->id }}</x-table.cell>
                    <x-table.cell>
                        <div class="text-xs tracking-wider uppercase">
                            {{ $repository->api }}
                        </div>
                    </x-table.cell>
                    <x-table.cell>
                        {{ $repository->url }}
                        <div class="text-sm text-gray-600">
                            {{ $repository->description }}
                        </div>
                    </x-table.cell>
                    <x-table.cell>{{ $repository->website }}</x-table.cell>
                    <x-table.cell>{{ $repository->author ? $repository->author->display_name : '-' }}</x-table.cell>
                    <x-table.cell>
                        @if ($repository->refreshed_at)
                        <div class="text-xs whitespace-nowrap">
                            <strong>{{ $repository->refreshed_at->format('Y-m-d')}}</strong>
                            <div class="text-gray-700">{{ $repository->refreshed_at->format('H:i:s')}}</div>
                        </div>
                        @else
                        -
                        @endif
                    </x-table.cell>
                    <x-table.cell width="60">
                        <div class="flex items-center space-x-2">
                            @if ($repository->enabled)
                                <x-jet-secondary-button title="Disable repository" wire:click="disableRepository({{ $repository->id }})">
                                    <x-heroicon-m-eye-slash class="w-4 h-4" />
                                </x-jet-secondary-button>
                            @else
                                <x-jet-secondary-button title="Disable repository" wire:click="enableRepository({{ $repository->id }})">
                                    <x-heroicon-m-eye class="w-4 h-4" />
                                </x-jet-secondary-button>
                            @endif
                            <x-jet-button wire:click="editRepository({{ $repository->id }})">
                                <x-heroicon-m-pencil class="w-4 h-4" />
                            </x-jet-button>
                            <x-jet-danger-button wire:click="confirmRepositoryDeletion({{ $repository->id }})">
                                <x-heroicon-o-trash class="w-4 h-4" />
                            </x-jet-danger-button>
                        </div>
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
    <!-- Confirm Repository removal -->
    <x-jet-confirmation-modal wire:model="confirmingRepositoryDeletion">
        <x-slot name="title">
            {{ __('Delete Repository') }}
        </x-slot>

        <x-slot name="content">
            {{ __('Are you sure you would like to delete this repository?') }}
        </x-slot>

        <x-slot name="footer">
            <x-jet-secondary-button wire:click="$toggle('confirmingRepositoryDeletion')" wire:loading.attr="disabled">
                {{ __('Cancel') }}
            </x-jet-secondary-button>

            <x-jet-danger-button class="ml-3" wire:click="deleteRepository" wire:loading.attr="disabled">
                {{ __('Delete') }}
            </x-jet-danger-button>
        </x-slot>
    </x-jet-confirmation-modal>

</div>
