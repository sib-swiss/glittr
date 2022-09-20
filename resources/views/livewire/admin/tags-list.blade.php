<div>
    <x-header title="List of tags">
        <x-header.actions>
            <x-jet-button wire:click="$set('showAddCategory', true)" class="space-x-2">
                <x-heroicon-o-plus class="w-6 h-6" />
                <span>{{ __('Add a category') }}</span>
            </x-jet-button>
        </x-header.actions>
    </x-header>
    <x-admin.container>
        <div
            class="space-y-4"
            x-data="{
                sortable: null
            }"
            x-init="
            sortable = new Sortable($el, {
                group: 'categories',
                draggable: '.category',
                handle: '.category-drag-handle',
                onSort: function(evt) {
                    $wire.sortCategories(sortable.toArray())
                }
            })
        ">
            @foreach($categories as $category)
                <div class="category" data-id="{{ $category->id }}">
                    <div class="flex items-center bg-gray-50 border rounded space-x-4">
                        <div class="w-12 self-stretch rounded-l" style="background-color: {{ $category->color }};"></div>
                        <div class="py-4 flex-1 font-semibold text-lg">{{ $category->name }}</div>
                        <div class="flex items-center">
                            <x-jet-button class="mr-4" type="button" title="{{ __('Edit category') }}" wire:click="editCategory({{ $category->id}})">
                                <x-heroicon-o-pencil-square class="w-6 h-6" />
                            </x-jet-button>
                            <x-jet-secondary-button class="mr-4 category-drag-handle" type="button" title="{{ __('Reorder categories') }}">
                                <x-heroicon-o-chevron-up-down class="w-6 h-6" />
                            </x-jet-secondary-button>
                        </div>
                    </div>
                    <div
                        class="ml-8 lg:ml-12"
                        data-category="{{ $category->id }}"
                        x-init="
                        new Sortable($el, {
                            group: 'tags',
                            draggable: '.tag',
                            onAdd: function (evt)
                            {
                                console.log('add event', evt)
                                console.log('new cat', evt.to.getAttribute('data-category'))
                                console.log(Sortable.get(evt.to).toArray())
                            },
                            onUpdate: function (evt)
                            {
                                console.log('update event', evt)
                            }
                        })
                    ">
                        @foreach($category->tags as $tag)
                            <div class="bg-white p-2 border-t tag" data-id="{{ $tag->id }}">
                                {{ $tag->name }}
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach

            <!-- Categories Modals -->
            <x-jet-modal wire:model="showEditCategory">
                @if($categoryEditId)
                    @livewire('admin.categories-form', [$categoryEditId, 'edit'], key($categoryEditId))
                @endif
            </x-jet-modal>
            <x-jet-modal wire:model="showAddCategory">
               @livewire('admin.categories-form', [null, 'add'], key($categoryAddId))
            </x-jet-modal>
        </div>
    </x-admin.container>
</div>
