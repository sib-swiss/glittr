<div x-data>
    @if(count($repositories) > 0)
        <div class="hidden lg:flex items-end">
            <h2 class="flex items-center mr-auto self-center uppercase text-gray-400 text-lg tracking-wider">
                <x-heroicon-o-tag class="w-6 h6 mr-2" />
                <span>Categories & Topics</span>
            </h2>
            @foreach($filter_tags as $cid => $category)
            <button
                wire:click="toggleCategory({{ $cid }})"
                class="
                    text-sm inline-flex mr-2 space-x-2 p-3 border-l border-r border-t rounded-t tag-category-{{ $cid }}
                    {{ $category['category']['selected'] ?
                    'bg-category-color text-white hover:bg-category-color/80' :
                    'bg-category-color/10 text-category-color border-category-color hover:bg-category-color/60 hover:text-white hover:border-category-color' }}"
            >
                    <span>{{ $category['category']['name'] }}</span>
                    <span>|</span>
                    <span class="font-bold">{{ $category['category']['total'] }}</span>
            </button>
            @endforeach

        </div>
        <div class="hidden lg:block bg-white border-t border-gray-200 shadow p-4">
            <div class="flex flex-wrap -mx-2">
                @foreach($filter_tags as $cid => $category)
                    @foreach($category['tags'] as $tagIndex => $tag)
                    <div class="p-2 tag-category-{{ $cid }}">
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
                @endforeach
            </div>
        </div>

        <div class="flex justify-between mt-4 lg:mt-8">
            <!--<h2 class="flex items-center mr-auto self-center uppercase text-gray-400 text-lg tracking-wider">
                <x-heroicon-o-archive-box class="w-6 h6 mr-2" />
                <span>Repositories</span>
            </h2>-->
        </div>
        <div class="repositories-list mt-3 bg-white shadow w-full lg:table">
            <div class="hidden lg:table-header-group">
                <div class="lg:align-middle lg:table-cell bg-gray-800 p-2 text-white text-sm tracking-wider">
                    <div class="flex items-center justify-between">
                        <span>Repository</span>
                        <x-heroicon-o-chevron-up-down class="w-4 h-4" />
                    </div>
                </div>
                <div class="lg:align-middle lg:table-cell bg-gray-800 p-2 text-white text-sm tracking-wider">
                    <div class="flex items-center justify-between">
                        <span>Description</span>
                        <x-heroicon-o-chevron-up-down class="w-4 h-4" />
                    </div>
                </div>
                <div class="lg:align-middle lg:table-cell bg-gray-800 p-2 text-white text-sm tracking-wider">
                    <div class="flex items-center justify-between">
                        <span>Author</span>
                        <x-heroicon-o-chevron-up-down class="w-4 h-4" />
                    </div>
                </div>
                <div class="lg:align-middle lg:table-cell bg-gray-800 p-2 text-white text-sm tracking-wider">
                    <div class="flex items-center justify-between">
                        <span>Topics</span>
                    </div>
                </div>
                <div class="lg:align-middle lg:table-cell bg-gray-800 p-2 text-white text-sm tracking-wider">
                    <div class="flex items-center justify-between">
                        <span>Stargazers</span>
                        <x-heroicon-o-chevron-up-down class="w-4 h-4" />
                    </div>
                </div>
                <div class="lg:align-middle lg:table-cell bg-gray-800 p-2 text-white text-sm tracking-wider">
                    <div class="flex items-center justify-between">
                        <span>Last push</span>
                        <x-heroicon-o-chevron-up-down class="w-4 h-4" />
                    </div>
                </div>
                <div class="lg:align-middle lg:table-cell bg-gray-800 p-2 text-white text-sm tracking-wider">
                    <div class="flex items-center justify-between">
                        <span>License</span>
                        <x-heroicon-o-chevron-up-down class="w-4 h-4" />
                    </div>
                </div>
            </div>
            @foreach($repositories as $repository)
                <div class="lg:table-row border-b hover:bg-gray-50">
                    <div class="lg:table-cell p-2 lg:border-b lg:align-middle leading-tight">
                        <div class="flex space-x-2">
                            <a class="text-blue-500 hover:text-blue-600 font-bold tracking-tight underline flex items-center space-x-1" href="{{ $repository->url }}" target="_blank">
                                <span>{{ $repository->name }}</span>
                                <x-heroicon-m-arrow-top-right-on-square class="w-3 h-3" />
                            </a>
                        </div>
                        <div class="text-sm text-gray-600">
                            {{ $repository->description }}
                        </div>
                    </div>
                    <div class="lg:table-cell p-2 lg:border-b lg:align-middle">
                        <div class="flex space-x-2">

                            @if ($repository->website != "")
                                <a class="text-xs uppercase tracking-wider py-2 px-4 rounded bg-blue-500 hover:bg-blue-700 text-white" href="{{ $repository->website }}" target="_blank">
                                    <span>Website</span>
                                </a>
                            @endif
                        </div>

                    </div>
                    <div class="lg:table-cell p-2 lg:border-b lg:align-middle">
                        @if ($repository->author)
                            <div class="text-sm font-bold tracking-tight">
                                {{ $repository->author->display_name }}
                            </div>
                            <div class="flex space-x-2">
                                @if ($repository->author->url != "")
                                    <a class="text-xs text-blue-500 hover:text-blue-700 underline flex items-center space-x-1" href="{{ url($repository->author->url) }}" target="_blank">
                                        <span>Profile</span>
                                        <x-heroicon-m-arrow-top-right-on-square class="w-3 h-3" />
                                    </a>
                                @endif
                                @if ($repository->author->website != "")
                                    <a class="text-xs text-blue-500 hover:text-blue-700 underline flex items-center space-x-1" href="{{ url($repository->author->website) }}" target="_blank">
                                        <span>Website</span>
                                        <x-heroicon-m-arrow-top-right-on-square class="w-3 h-3" />
                                    </a>
                                @endif
                            </div>
                        @endif
                    </div>
                    <div class="lg:table-cell p-2 lg:border-b lg:align-middle">
                        <div class="inline-flex flex-wrap">
                            @foreach($repository->tags as $tag)
                            <span class="mr-2 my-1 tag-category-{{ $tag->category_id}} text-xs font-medium tracking-wide border rounded py-1 px-2 bg-category-color/10 text-category-color border-category-color">
                                {{ $tag->name }}
                            </span>
                            @endforeach
                        </div>
                    </div>
                    <div class="lg:table-cell p-2 lg:border-b lg:align-middle">
                        @if ($repository->stargazers)
                        <div class="flex items-center space-x-2">
                            <x-heroicon-o-star class="w-4 h-4 text-yellow-500" />
                            <span class="text-sm font-semibold">{{ $repository->stargazers }}</span>
                        </div>
                        @endif
                    </div>
                    <div class="lg:table-cell p-2 lg:border-b lg:align-middle text-xs text-gray-600">
                        @if ($repository->last_push)
                            <div class="flex items-center space-x-2">
                                <div class="w-2 h-2 rounded-full {{ $repository->getPushStatusClass() }}"></div>
                                <span>{{ $repository->last_push->diffForHumans() }}</span>
                            </div>
                        @endif
                    </div>
                    <div class="lg:table-cell p-2 lg:border-b lg:align-middle text-sm font-semibold">
                        {{ $repository->license }}
                    </div>
                </div>
            @endforeach
        </div>
        <div class="mt-4">
            {{ $repositories->links() }}
        </div>
    @else
        <div class="bg-orange-100 border border-orange-600 rounded text-orange-600 p-4">No results</div>
    @endif
</div>
