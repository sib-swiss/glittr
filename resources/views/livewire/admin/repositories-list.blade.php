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
