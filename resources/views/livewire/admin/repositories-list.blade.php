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
        TABLE
    </x-admin.container>

    <!-- Add Category Form Modal -->
    <x-jet-modal wire:model="showAdd" persisted="true">
        @livewire('admin.repository-form', [null, 'addRepositoryCancel'], key("epositoryAdd-{$addIncrement}"))
    </x-jet-modal>
</div>
