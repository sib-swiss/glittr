<div>
    <x-header title="List of tags">
        <x-header.actions>
            <x-button wire:click="$set('showAddTag', true)" class="space-x-2">
                <x-heroicon-o-plus class="w-6 h-6" />
                <span>{{ __('Tag') }}</span>
            </x-button>
            <x-button wire:click="$set('showAddCategory', true)" class="space-x-2">
                <x-heroicon-o-plus class="w-6 h-6" />
                <span>{{ __('Category') }}</span>
            </x-button>
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
            @foreach ($categories as $category)
                <div class="category" data-id="{{ $category->id }}">
                    <div class="flex items-center bg-gray-50 border rounded space-x-4 pr-2">
                        <div class="w-4 lg:w-12 self-stretch rounded-l" style="background-color: {{ $category->color }};"></div>
                        <div class="py-4 flex-1 font-semibold md:text-lg">{{ $category->name }}</div>
                        <div class="flex items-center">
                            @if (0 === count($category->tags))
                                <x-danger-button class="mr-2" wire:click="confirmCategoryDeletion({{ $category->id }})">
                                    <x-heroicon-o-trash class="w-6 h-6" />
                                </x-danger-button>
                            @endif
                            <x-button class="mr-2" type="button" title="{{ __('Edit category') }}" wire:click="editCategory({{ $category->id}})">
                                Edit Category
                            </x-button>
                            <x-secondary-button class="mr-2 category-drag-handle" type="button" title="{{ __('Reorder categories') }}">
                                <x-heroicon-o-chevron-up-down class="w-4 h-4" />
                            </x-secondary-button>
                        </div>
                    </div>
                    <div
                        class="ml-4 lg:ml-12"
                        data-category="{{ $category->id }}"
                        x-init="
                        new Sortable($el, {
                            group: 'tags-{{ $category->id }}',
                            draggable: '.tag',
                            handle: '.tag-drag-handle',
                            onSort: function (evt)
                            {
                                //console.log('category id', evt.to.getAttribute('data-category'))
                                $wire.sortTags(Sortable.get(evt.to).toArray())
                            },

                        })
                    ">
                        @foreach ($category->tags as $tag)
                            <div class="bg-white hover:bg-gray-50 py-2 px-4 border-t tag flex space-x-4" data-id="{{ $tag->id }}">
                                <div class="w-10">
                                    <span class="text-xs font-semibold py-1 px-3 rounded-lg {{ $tag->repositories_count > 0 ? 'bg-green-100 text-green-800' : 'bg-orange-100 text-orange-800'}}">
                                        {{ $tag->repositories_count }}
                                    </span>
                                </div>
                                <div class="flex-1">{{ $tag->name }}</div>
                                <div class="flex items-center justify-end space-x-2">
                                    @if (0 === $tag->repositories_count)
                                        <x-danger-button wire:click="confirmTagDeletion({{ $tag->id }})">
                                            <x-heroicon-m-trash class="w-4 h-4" />
                                        </x-danger-button>
                                    @endif
                                    <x-button wire:click="editTag({{ $tag->id }})">
                                        <x-heroicon-m-pencil class="w-4 h-4" />
                                    </x-button>
                                    <x-secondary-button class="tag-drag-handle">
                                        <x-heroicon-m-chevron-up-down class="w-4 h-4" />
                                    </x-secondary-button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach

        </div>
    </x-admin.container>

    <!-- Edit Category Form Modal -->
    <x-modal wire:model.live="showEditCategory" persisted="true">
        @if ($categoryIdBeingUpdated)
            @livewire('admin.category-form', [$categoryIdBeingUpdated, 'editCategoryCancel'], key("CategoryUpdate-{$categoryIdBeingUpdated}"))
        @endif
    </x-modal>

    <!-- Add Category Form Modal -->
    <x-modal wire:model.live="showAddCategory" persisted="true">
        @livewire('admin.category-form', [null, 'addCategoryCancel'], key("categoryAdd-{$categoryAddIncrement}"))
    </x-modal>

    <!-- Delete Category Confirmation Modal -->
    <x-confirmation-modal wire:model.live="confirmingCategoryDeletion">
        <x-slot name="title">
            {{ __('Delete Category') }}
        </x-slot>

        <x-slot name="content">
            {{ __('Are you sure you would like to delete this category?') }}
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="$toggle('confirmingCategoryDeletion')" wire:loading.attr="disabled">
                {{ __('Cancel') }}
            </x-secondary-button>

            <x-danger-button class="ml-3" wire:click="deleteCategory" wire:loading.attr="disabled">
                {{ __('Delete') }}
            </x-danger-button>
        </x-slot>
    </x-confirmation-modal>

     <!-- Edit Tag Form Modal -->
     <x-modal wire:model.live="showEditTag" persisted="true">
        @if ($tagIdBeingUpdated)
            @livewire('admin.tag-form', [$tagIdBeingUpdated, 'editTagCancel'], key("tagUpdate-{$tagIdBeingUpdated}"))
        @endif
    </x-modal>

    <!-- Add Tag Form Modal -->
    <x-modal wire:model.live="showAddTag" persisted="true">
        @livewire('admin.tag-form', [null, 'addTagCancel'], key("tagAdd-{$tagAddIncrement}"))
    </x-modal>

    <!-- Delete Tag Confirmation Modal -->
    <x-confirmation-modal wire:model.live="confirmingTagDeletion">
        <x-slot name="title">
            {{ __('Delete Tag') }}
        </x-slot>

        <x-slot name="content">
            {{ __('Are you sure you would like to delete this tag?') }}
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="$set('confirmingTagDeletion', false)" wire:loading.attr="disabled">
                {{ __('Cancel') }}
            </x-secondary-button>

            <x-danger-button class="ml-3" wire:click="deleteTag" wire:loading.attr="disabled">
                {{ __('Delete') }}
            </x-danger-button>
        </x-slot>
    </x-confirmation-modal>
</div>
