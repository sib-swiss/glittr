<div x-data>
    <div class="tags-filter lg:columns-3 text-sm gap-4 lg:gap-8">
        @foreach($filter_tags as $cid => $category)
            <div class="break-inside-avoid tag-category-{{ $cid }} mb-8">
                <div class="flex space-between">
                    <div class="bg-category-color text-white p-3">
                        {{ $category['category']['name'] }}
                    </div>
                    <div class="">
                    </div>
                </div>
                <div class="p-4 bg-white shadow text-gray-900">
                    <div class="flex flex-wrap -mx-2">
                        @foreach($category['tags'] as $tagIndex => $tag)
                        <div class="p-2">
                            <button
                                wire:click="toggleTag({{ $tagIndex }})"
                                class="
                                    text-xs font-medium tracking-wide border rounded py-2 px-4 inline-flex space-x-2
                                    {{ $tag['selected'] ?
                                        'bg-category-color text-white hover:bg-category-color/80'
                                        : 'bg-category-color/10 text-category-color border-category-color hover:bg-category-color/60 hover:text-white hover:border-category-color'}}
                                ">
                                <span>{{ $tag['name'] }}</span>
                                <span>|</span>
                                <span class="font-bold">{{ $tag['filtered'] }}</span>
                            </button>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endforeach

    </div>
    <div class="repositories-list">
        @foreach($repositories as $repository)
            <div class="flex items-start">
                <div>{{ $repository->url }}</div>
                <div class="flex flex-wrap space-x-2">
                    @foreach($repository->tags as $tag)
                        <div class="text-sm tracking-wide p-2 border">{{ $tag->name }}</div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
</div>
