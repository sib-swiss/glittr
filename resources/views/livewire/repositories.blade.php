<div x-data="{
        filterOpen: false,
        handleFocus(e) {
            if('/' === e.key || 'p' === e.key && e.metaKey || 'k' === e.key && e.metaKey || 'k' === e.key && e.ctrlKey) {
                this.$refs.input.focus()
                e.preventDefault()
            }
        },
        toggleFilter() {
            this.filterOpen = ! this.filterOpen
            if (this.filterOpen) {
                document.body.classList.add('overflow-y-hidden');
                document.body.classList.add('2xl:overflow-y-auto');
            } else {
                document.body.classList.remove('overflow-y-hidden');
                document.body.classList.remove('2xl:overflow-y-auto');
            }
        }
    }"
    class="flex flex-col flex-1"
>
    @php
    $now = Carbon\Carbon::now();
    @endphp
    <x-scrolltop class="2xl:right-[360px]" />
    <div class="flex-1 2xl:mr-[360px] relative flex flex-col">
        <div class="container relative flex-1">
            {{-- Header --}}
            <x-page-header :container="false">
                <x-slot name="text">
                    <div class="pb-2">
                        <p>
                            Is your (favourite) course not in there? Is a link dead? Did you find a typo?<br />Any contribution to this list is highly appreciated!
                        </p>
                        <div class="flex items-center justify-center">
                            <a class="relative overflow-hidden rounded-lg text-base uppercase no-underline px-20 py-6 bg-white group font-bold" href="{{ route('contribute') }}">
                                <span class="absolute inset-[3px] z-10 grid place-items-center rounded-lg bg-white group-hover:bg-opacity-95 duration-200 transition">{{ __('Contribute !') }}</span>
                                <span aria-hidden class="absolute inset-0 z-0 scale-x-[2.0] blur before:absolute before:inset-0 before:top-1/2 before:aspect-square before:-translate-y-1/2 before:rotate-0 before:animate-disco  before:bg-gradient-conic before:from-glittr-violet before:via-glittr-orange before:to-glittr-yellow" />
                            </a>
                        </div>
                    </div>
                </x-slot>
            </x-page-header>
            <div id="items-list" class="mt-4 bg-gray-50 p-4 border-t border-b">
                <div x-data @keydown.window="handleFocus" class="relative">
                    <input class="w-full border-gray-400 rounded pl-14 pr-14 h-14 py-4 lg:text-lg focus:border-blue-500" type="text" placeholder="{{ __('Global search ("/" to focus)') }}" x-ref="input" wire:model.debounce.500ms="search" />
                    <div class="w-14 h-14 absolute left-0 top-0 flex items-center justify-center text-gray-400">
                        <x-heroicon-o-magnifying-glass class="w-6 h-6" />
                    </div>
                    @if ($search != '')
                        <div class="w-14 h-14 absolute right-0 top-0 flex items-center justify-center">
                            <button type="button" title="{{ __('Clear search') }}" class=" text-gray-800" @click="$wire.set('search', ''); $refs.input.focus();">
                                <x-heroicon-o-x-circle class="w-6 h-6" />
                            </button>
                        </div>
                    @endif
                </div>
                @if (count($repositories) > 0)
                    <div class="mt-2 2xl:hidden">
                        <x-jet-button type="button" class="flex text-sm lg:text-base w-full justify-center" @click="toggleFilter">
                            <x-heroicon-o-adjustments-horizontal class="w-5 h-5 mr-3" />
                            <span>{{ __('Filter by topic') }}</span>
                        </x-jet-button>
                    </div>
                @endif
                @if (count($selected_tags) > 0)
                    <div class="flex items-center mt-2 -mx-1 flex-wrap">
                        <div class="px-1 font-semibold uppercase tracking-wide text-sm">{{ __('Selected topcis')}} ({{ count($selected_tags) }})</div>
                        <div class="px-1">
                            <button title="{{ __('Clear selected topics') }}" wire:click="clearTags" class="text-sm p-2 bg-gray-100 border-gray-800 border text-left flex items-stretch">
                                <span>{{ $selected_tags->pluck('name')->implode(', ') }}</span>
                                <span class="border-l ml-2 pl-2 flex items-center justify-center">
                                    <x-heroicon-o-x-mark class="w-4 h-4" />
                                </span>
                            </button>
                        </div>
                    </div>
                @endif
            </div>
            @if(count($repositories) > 0)
                <div  class="py-2 mt-2 flex flex-wrap justify-between items-center">
                    <div class="w-full lg:w-auto lg:flex-1 pb-2 lg:pb-0 lg:text-left text-center">
                        <h2 class="text-lg font-bold">
                            {{ $repositories->total() }} {{ __('Repositories') }}
                        </h2>
                        <div class="text-sm uppercase tracking-wide text-gray-500">
                            {{ __('Page') }} {{ $repositories->currentPage() }} / {{ $repositories->lastPage() }}
                        </div>
                    </div>

                    <div class="mr-4 lg:hidden flex flex-col items-start">
                        <label for="sortSelect" class="text-sm uppercase font-semibold tracking-wide">
                            {{ __('Sort by') }}
                        </label>
                        <x-select
                            class="w-full"
                            id="sortSelect"
                            @change="
                                option = $el.options[$el.selectedIndex];
                                $wire.changeSort(option.dataset.column, option.dataset.direction)
                            "
                        >
                            @foreach ($sorting_columns as $sortId => $sorting_column)
                                <option
                                    data-column="{{ $sorting_column['column']}}"
                                    data-direction="{{ $sorting_column['direction']}}"
                                    value="{{ $sortId }}"
                                    @if ($sorting_column['selected']) selected @endif
                                >
                                    {{ $sorting_column['label'] }}
                                </option>
                            @endforeach
                        </x-select>
                    </div>

                    @if (count(config('repositories.paginations', [])) > 0)
                        <div class="ml-4 flex flex-col items-end text-right">
                            <label for="paginationSelect" class="text-sm uppercase font-semibold tracking-wide">
                                {{ __('Per page') }}
                            </label>
                            <x-select id="paginationSelect" class="" wire:model="per_page">
                                @foreach (config('repositories.paginations') as $pagination_nb)
                                    <option value="{{ $pagination_nb }}">{{ $pagination_nb }}</option>
                                @endforeach
                            </x-select>
                        </div>
                    @endif
                </div>
                <div  class="repositories-list  lg:bg-white w-full lg:table lg:border lg:border-gray-200">
                    <div class="hidden lg:table-header-group">
                        <x-list.header title="{{ __('Repository') }}" sort_by='name' sortable current_sort_by="{{ $sort_by }}" current_sort_direction="{{ $sort_direction }}" />
                        <x-list.header title="" />
                        <x-list.header title="{{ __('Author') }}" sort_by='author' sortable current_sort_by="{{ $sort_by }}" current_sort_direction="{{ $sort_direction }}" />
                        <x-list.header title="{{ __('Topics') }}"  />
                        <x-list.header title="{{ __('Stargazers') }}" sort_by='stargazers' sortable current_sort_by="{{ $sort_by }}" current_sort_direction="{{ $sort_direction }}" />
                        <x-list.header title="{{ __('Days since last push') }}" sort_by='last_push' sortable current_sort_by="{{ $sort_by }}" current_sort_direction="{{ $sort_direction }}" />
                        <x-list.header title="{{ __('License') }}" sort_by='license' sortable current_sort_by="{{ $sort_by }}" current_sort_direction="{{ $sort_direction }}" />
                    </div>
                    @foreach($repositories as $repository)
                        <div class="lg:table-row border lg:border-b hover:bg-gray-50 bg-white grid grid-cols-2 {{ $loop->first ? 'mt-4' : 'mt-8' }}  lg:mt-0">
                            <div class="lg:table-cell order-1 lg:order-none col-span-2 lg:col-span-1 pt-4 lg:pt-2 p-2 lg:border-b lg:border-gray-200 lg:align-middle leading-tight">
                                <div class="flex justify-between">
                                    <a class="text-blue-500 hover:text-blue-600 text-base lg:text-lg font-bold tracking-tight underline mr-1 lg:mr-0" href="{{ $repository->url }}" target="_blank" rel="noopener">
                                        <span>{{ $repository->name }}</span>
                                        <x-heroicon-m-arrow-top-right-on-square class="w-3 h-3 inline" />
                                    </a>
                                    @if ($repository->stargazers)
                                    <div class="lg:hidden flex items-center justify-end space-x-1">
                                        <span class="text-sm font-semibold">{{ $repository->stargazers }}</span>
                                        <x-heroicon-o-star class="w-4 h-4 text-yellow-500" />
                                    </div>
                                    @endif
                                </div>
                                <div class="text-sm xl:text-base text-gray-600 flex justify-between">
                                    <div>{{ $repository->description }}</div>

                                </div>
                            </div>
                            <div class="@if(!$repository->website || $repository->website == '') hidden @endif mb-2 lg:mb-0 lg:table-cell order-4 lg:order-none col-span-2 lg:col-span-1 p-2 lg:border-b lg:align-middle">
                                @if ($repository->website != "")
                                    <a class="block text-center lg:inline-block text-sm uppercase tracking-wider py-2 px-4 rounded bg-blue-500 hover:bg-blue-700 text-white" href="{{ $repository->website }}" target="_blank" rel="noopener">
                                        <span>{{ __('Website') }}</span>
                                    </a>
                                @endif
                            </div>
                            <div class="@if(!$repository->author) hidden @endif  border-t border-b  lg:table-cell order-last lg:order-none col-span-2 lg:col-span-1 py-4 lg:py-2 px-2  lg:border-t-0 lg:align-middle bg-gray-50 lg:bg-transparent">
                                @if ($repository->author)
                                    <div class="lg:hidden text-xs font-medium text-gray-600 tracking-wide uppercase mb-1">{{ __('Author') }}</div>
                                    <div class="font-bold tracking-tight">
                                        {{ $repository->author->display_name }}
                                    </div>
                                    <div class="flex space-x-2">
                                        @if ($repository->author->url != "")
                                            <a class="text-sm text-blue-500 hover:text-blue-700 underline flex items-center space-x-1" href="{{ url($repository->author->url) }}" target="_blank" rel="noopener">
                                                <span>{{ __('Profile') }}</span>
                                                <x-heroicon-m-arrow-top-right-on-square class="w-3 h-3" />
                                            </a>
                                        @endif
                                        @if ($repository->author->website != "")
                                            <a class="text-sm text-blue-500 hover:text-blue-700 underline flex items-center space-x-1" href="{{ url($repository->author->website) }}" target="_blank" rel="noopener">
                                                <span>{{ __('Website') }}</span>
                                                <x-heroicon-m-arrow-top-right-on-square class="w-3 h-3" />
                                            </a>
                                        @endif
                                    </div>
                                @endif
                            </div>
                            <div class="lg:table-cell lg:w-80 bg-gray-50 lg:bg-transparent order-last lg:order-none col-span-2 lg:col-span-1 p-2 lg:border-b lg:align-middle">
                                <div class="inline-flex flex-wrap" x-data="{ more: false }">
                                    @foreach ($repository->tags as $index => $tag)
                                        <span @if (($index + 1) > $max_tags) x-show="more" x-transition @endif class="mr-2 my-1 tag-category-{{ $tag->category_id}} text-sm font-medium tracking-wide border rounded py-1 px-2 bg-category-color text-white border-category-color">
                                            {{ $tag->name }}
                                        </span>
                                    @endforeach

                                    @if ($repository->tags->count() > $max_tags)
                                        <button class="underline text-blue-500 hover:text-blue-600 text-sm font-semibold p-2" x-cloak type="button" x-show="!more" @click="more = true">
                                            {{ __('+ :nb more', ['nb' => ($repository->tags->count() - $max_tags)])}}
                                        </button>
                                    @endif
                                </div>
                            </div>
                            <div class="hidden lg:table-cell lg:w-32 order-2 lg:order-none col-span-2 lg:col-span-1 p-2 lg:border-b lg:align-middle">
                                @if ($repository->stargazers)
                                <div class="flex items-center space-x-2">
                                    <x-heroicon-o-star class="w-4 h-4 text-yellow-500" />
                                    <span class="text-sm font-semibold">{{ $repository->stargazers }}</span>
                                </div>
                                @endif
                            </div>
                            <div class="lg:table-cell lg:w-32 order-3 lg:order-none p-2 lg:border-b lg:align-middle">
                                <div class="lg:hidden text-xs font-medium tracking-wide text-gray-400 uppercase">{{ __('Days since last push') }}</div>
                                @if ($repository->last_push)
                                    <div class="flex items-center space-x-2">
                                        <div class="w-2 h-2 rounded-full {{ $repository->getPushStatusClass() }}"></div>
                                        <span class="text-sm font-semibold">{{ $repository->days_since_last_push ?? '-' }}</span>
                                    </div>
                                @else
                                    -
                                @endif
                            </div>
                            <div class="lg:table-cell lg:w-32 order-3 p-2 lg:order-none lg:border-b lg:align-middle font-semibold lg:text-left text-right">
                                <div class="lg:hidden text-xs font-medium tracking-wide text-gray-400 uppercase">{{ __('License') }}</div>
                                {{ $repository->license ?? '-' }}
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="mt-4">
                    {{ $repositories->links() }}
                </div>
            @else
                <div class="bg-orange-100 border border-orange-600 text-orange-600 p-4 mt-8 text-center">
                    {{ __('No results matched your search: ') }}<strong>{{ $search }}</strong>
                    <p>
                        <button type="button" class="font-bold underline mt-2" @click.prevent="$wire.set('search', '')">
                            {{ __('Reset search term') }}
                        </button>
                    </p>
                </div>
            @endif
        </div>
        <x-page-footer>

        </x-page-footer>
    </div>
    <div x-cloak class="2xl:hidden fixed inset-0 z-10 bg-white/60 backdrop-blur-sm" x-show="filterOpen" @click="toggleFilter"></div>
    <div x-cloak
        class="fixed z-20 inset-y-0 right-0 w-full max-w-[80%] 2xl:w-[360px] transition bg-gray-50 2xl:translate-x-0 duration-300 border-l shadow-md flex flex-col"
        :class="filterOpen ? 'translate-x-0' : 'translate-x-full'"
        >
        @if (count($repositories) > 0)
            <div class="p-4 bg-gray-800 text-white tracking-widest font-semibold uppercase flex items-center">
                <div class="flex items-center justify-center font-semibold space-x-2 ">
                    <x-heroicon-o-tag class="w-6 h-6" />
                    <span>{{ __('Topics') }}</span>
                </div>
                <div class="ml-auto 2xl:hidden">
                    <button @click="toggleFilter">
                        <x-heroicon-o-x-mark class="w-6 h-6" />
                    </button>
                </div>
            </div>
            <div class="flex-1  overflow-y-auto {{ $split_tags_filter ? 'space-y-4' : ''}}">
                @foreach($grouped_tags as $cid => $category)
                    <div class="tag-category-{{ $cid }} ">
                        <label for="filter-category-{{ $cid }}" class="cursor-pointer p-4 border-t border-b border-category-color bg-category-color text-white text-sm font-semibold flex items-center">
                            <x-jet-checkbox id="filter-category-{{ $cid }}" wire:model="categories.{{ $cid }}.selected" />
                            <span class="mx-2">{{ $category['category']['name'] }}</span>
                            <span class="ml-auto font-bold">
                                {{ $category['category']['total'] }}
                            </span>
                        </label>
                        <div class="grid {{ $split_tags_filter ? 'grid-cols-2' : 'grid-cols-1'}}">
                            @foreach($category['tags'] as $tagIndex => $tag)
                                <label
                                    for="filter-tag-{{ $tagIndex }}"
                                    class="cursor-pointer text-sm lg:text-base px-4 py-3 flex items-center border-b bg-white hover:bg-gray-50 border-r">

                                    <x-jet-checkbox value="1" id="filter-tag-{{ $tagIndex }}" wire:model="tags.{{ $tagIndex }}.selected" />
                                    <span class="mx-2 leading-tight text-sm font-semibold">{{ $tag['name'] }}</span>
                                    <span class="ml-auto font-bold text-xs p-1 rounded bg-gray-100">{{ $tag['filtered'] }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="p-2 2xl:hidden">
                <button type="button" @click="toggleFilter" class="block w-full text-center tracking-wide text-base font-medium p-4 bg-gray-800 text-white ">
                    {{ __('Show :nb repositories', ['nb' => $repositories->total()])}}
                </button>
            </div>
        @endif
    </div>
</div>
