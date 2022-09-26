<div x-data>
    {{-- Header --}}
    <div class="mb-4 lg:mb-6">
        <div x-data @keydown.slash.window.prevent="$refs.input.focus()" class="flex-1">
            {{-- ("/" === e.key || "p" === e.key && e.metaKey || "k" === e.key && e.metaKey || "k" === e.key && e.ctrlKey) --}}
            <input class="w-full border-gray-400 rounded-full" type="text" placeholder="{{ __('Global search ("/" to focus)') }}" x-ref="input" wire:model.debounce.500ms="search" />
        </div>
    </div>

    @if(count($repositories) > 0)
        <div class="hidden lg:flex items-end">

            @foreach($grouped_tags as $cid => $category)
            <button
                wire:click="toggleCategory({{ $cid }})"
                class="
                    text-sm inline-flex ml-2 space-x-2 p-3 border-l border-r border-t rounded-t tag-category-{{ $cid }}
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
                @foreach($grouped_tags as $cid => $category)
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

        </div>
        <div class="space-y-4 lg:space-y-0 repositories-list mt-3 lg:bg-white lg:shadow w-full lg:table">
            <div class="hidden lg:table-header-group">
                <x-list.header title="{{ __('Repository') }}" sort_by='name' sortable current_sort_by="{{ $sort_by }}" current_sort_direction="{{ $sort_direction }}" />
                <x-list.header title="" />
                <x-list.header title="{{ __('Author') }}" sort_by='author' sortable current_sort_by="{{ $sort_by }}" current_sort_direction="{{ $sort_direction }}" />
                <x-list.header title="{{ __('Topics') }}"  />
                <x-list.header title="{{ __('Stargazers') }}" sort_by='stargazers' sortable current_sort_by="{{ $sort_by }}" current_sort_direction="{{ $sort_direction }}" />
                <x-list.header title="{{ __('Last push') }}" sort_by='last_push' sortable current_sort_by="{{ $sort_by }}" current_sort_direction="{{ $sort_direction }}" />
                <x-list.header title="{{ __('License') }}" sort_by='license' sortable current_sort_by="{{ $sort_by }}" current_sort_direction="{{ $sort_direction }}" />
            </div>
            @foreach($repositories as $repository)
                <div class="lg:table-row border-b hover:bg-gray-50 bg-white shadow lg:shadow-none">
                    <div class="lg:table-cell pt-4 lg:pt-2 p-2 lg:border-b lg:align-middle leading-tight">
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
                                        <span>{{ __('Profile') }}</span>
                                        <x-heroicon-m-arrow-top-right-on-square class="w-3 h-3" />
                                    </a>
                                @endif
                                @if ($repository->author->website != "")
                                    <a class="text-xs text-blue-500 hover:text-blue-700 underline flex items-center space-x-1" href="{{ url($repository->author->website) }}" target="_blank">
                                        <span>{{ __('Website') }}</span>
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
        <div class="bg-orange-100 border border-orange-600 rounded text-orange-600 p-4">
            {{ __('No results.')}}
        </div>
    @endif
</div>
