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
                    <div class="pb-2 space-y-4 text-center lg:flex lg:items-center lg:justify-end lg:space-x-4 lg:space-y-0">
                        <div class="leading-tight prose-sm prose lg:prose lg:text-right lg:leading-snug">
                            {!! $header_text !!}

                        </div>
                        <div class="flex items-center justify-center lg:justify-end">
                            <a class="relative px-20 py-6 overflow-hidden text-base font-bold no-underline uppercase bg-white rounded-lg group" href="{{ route('contribute') }}">
                                <span class="absolute inset-[3px] z-10 grid place-items-center rounded-lg bg-white group-hover:bg-opacity-95 duration-200 transition mix-blend-screen">{{ __('Contribute !') }}</span>
                                <span aria-hidden class="absolute inset-0 z-0 scale-x-[2.0] blur before:absolute before:inset-0 before:top-1/2 before:aspect-square before:-translate-y-1/2 before:rotate-0 before:animate-disco  before:bg-gradient-conic before:from-glittr-violet before:via-glittr-orange before:to-glittr-yellow" />
                            </a>
                        </div>
                    </div>
                </x-slot>
            </x-page-header>
            <div id="items-list" class="p-4 mt-4 border-t border-b bg-gray-50">
                <div x-data @keydown.window="handleFocus" class="relative">
                    <input class="w-full py-4 border-gray-400 rounded pl-14 pr-14 h-14 lg:text-lg focus:border-blue-500" type="text" placeholder="{{ __('Global search ("/" to focus)') }}" x-ref="input" wire:model.live.debounce.500ms="search" />
                    <div class="absolute top-0 left-0 flex items-center justify-center text-gray-400 w-14 h-14">
                        <x-heroicon-o-magnifying-glass class="w-6 h-6" />
                    </div>
                    @if ($search != '')
                        <div class="absolute top-0 right-0 flex items-center justify-center w-14 h-14">
                            <button type="button" title="{{ __('Clear search') }}" class="text-gray-800 " @click="$wire.set('search', ''); $refs.input.focus();">
                                <x-heroicon-o-x-circle class="w-6 h-6" />
                            </button>
                        </div>
                    @endif
                </div>
                @if (count($repositories) > 0)
                    <div class="mt-2 2xl:hidden">
                        <x-button type="button" class="flex justify-center w-full text-sm lg:text-base" @click="toggleFilter">
                            <x-heroicon-o-adjustments-horizontal class="w-5 h-5 mr-3" />
                            <span>{{ __('Filter by topic') }}</span>
                        </x-button>
                    </div>
                @endif
                @if (count($selected_tags) > 0)
                    <div class="flex flex-wrap items-center mt-2 -mx-1">
                        <div class="px-1 text-sm font-semibold tracking-wide uppercase">{{ __('Selected topics')}} ({{ count($selected_tags) }})</div>
                        <div class="px-1">
                            <button title="{{ __('Clear selected topics') }}" wire:click="clearTags" class="flex items-stretch p-2 text-sm text-left bg-gray-100 border border-gray-800">
                                <span>{{ $selected_tags->pluck('name')->implode(', ') }}</span>
                                <span class="flex items-center justify-center pl-2 ml-2 border-l">
                                    <x-heroicon-o-x-mark class="w-4 h-4" />
                                </span>
                            </button>
                        </div>
                    </div>
                @endif
            </div>

                <div  class="flex flex-wrap items-center justify-between py-2 mt-2">
                    <div class="w-full pb-2 text-center lg:w-auto lg:flex-1 lg:pb-0 lg:text-left">
                        <h2 class="text-lg font-bold">
                            {{ $repositories->total() }} {{ __('Repositories') }}
                        </h2>
                        <div class="text-sm tracking-wide text-gray-500 uppercase">
                            {{ __('Page') }} {{ $repositories->currentPage() }} / {{ $repositories->lastPage() }}
                        </div>
                    </div>

                    <div class="flex flex-col items-start mr-4 lg:hidden">
                        <label for="sortSelect" class="text-sm font-semibold tracking-wide uppercase">
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

                    @if (count(config('glittr.paginations', [])) > 0)
                        <div class="flex flex-col items-end ml-4 text-right">
                            <label for="paginationSelect" class="text-sm font-semibold tracking-wide uppercase">
                                {{ __('Per page') }}
                            </label>
                            <x-select id="paginationSelect" class="" wire:model.live="per_page">
                                @foreach (config('glittr.paginations') as $pagination_nb)
                                    <option value="{{ $pagination_nb }}">{{ $pagination_nb }}</option>
                                @endforeach
                            </x-select>
                        </div>
                    @endif
                </div>
                <div  class="w-full repositories-list lg:bg-white lg:table lg:border lg:border-gray-200" x-data="{
                    show_filters: @entangle('show_filters').live,
                }">
                    <div class="hidden lg:table-header-group">
                        <x-list.header title="{{ __('Repository') }}" sort_by='name' sortable current_sort_by="{{ $sort_by }}" current_sort_direction="{{ $sort_direction }}" />
                        <x-list.header title="" />
                        <x-list.header title="{{ __('Author') }}" sort_by='author' sortable current_sort_by="{{ $sort_by }}" current_sort_direction="{{ $sort_direction }}" />
                        <x-list.header title="{{ __('Topics') }}"  />
                        <x-list.header title="{{ __('Stargazers') }}" sort_by='stargazers' sortable current_sort_by="{{ $sort_by }}" current_sort_direction="{{ $sort_direction }}" />
                        <x-list.header title="{{ __('Days since last push') }}" sort_by='last_push' sortable current_sort_by="{{ $sort_by }}" current_sort_direction="{{ $sort_direction }}" />
                        <x-list.header title="{{ __('License') }}" sort_by='license' sortable current_sort_by="{{ $sort_by }}" current_sort_direction="{{ $sort_direction }}" />
                    </div>
                    <button class="flex items-center justify-center w-full px-4 py-2 space-x-2 text-sm font-semibold tracking-widest text-white uppercase transition bg-gray-800 border border-transparent rounded-md lg:hidden hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25" x-on:click="show_filters = !show_filters">
                        <span>{{ __('Advanced search') }}</span>
                        <x-heroicon-o-chevron-down class="w-5 h-5 transition duration-200" x-cloak x-bind:class="{
                            'transform rotate-180': show_filters,
                            'transform rotate-0': !show_filters,
                        }" />
                    </button>
                    <div class="grid grid-cols-2 gap-4 py-4 lg:table-header-group bg-gray-50 lg:p-0" :class="{
                        'visible': show_filters,
                        'hidden': !show_filters,
                    }">
                        <div class="col-span-2 p-2 lg:table-cell bg-gray-50 lg:border-b">
                            <label for="filterName" class="text-sm font-semibold tracking-wide uppercase lg:hidden">{{ __('Search by repository name') }}</label>
                            <input type="text" id="filterName" wire:model.live.debounce.300ms="name" class="w-full p-2 border border-gray-300 rounded" placeholder="{{ __('Search by name') }}" />
                        </div>
                        <div class="hidden p-2 lg:table-cell bg-gray-50 lg:border-b"></div>
                        <div class="col-span-2 p-2 lg:table-cell bg-gray-50 lg:border-b">
                            <label for="filterName" class="text-sm font-semibold tracking-wide uppercase lg:hidden">{{ __('Search by author') }}</label>
                            <input type="text" id="filterAuthor" wire:model.live.debounce.300ms="author" class="w-full p-2 border border-gray-300 rounded" placeholder="{{ __('Search by author') }}" />
                        </div>
                        <div class="hidden p-2 lg:table-cell bg-gray-50 lg:border-b"></div>
                        <div class="p-2 lg:table-cell bg-gray-50 lg:border-b">
                            <label for="filterMinStars" class="text-sm font-semibold tracking-wide uppercase lg:hidden">{{ __('Minimum stargazers') }}</label>
                            <input type="number" id="filterMinStars" wire:model.live.debounce.300ms="minStars" min="0" class="w-24 p-2 border border-gray-300 rounded" placeholder="{{ __('Min.') }}" />
                        </div>
                        <div class="p-2 lg:table-cell bg-gray-50 lg:border-b">
                            <label for="filterMaxPush" class="text-sm font-semibold tracking-wide uppercase lg:hidden">{{ __('Maximum days since last push') }}</label>
                            <input type="number" id="filterMaxPush" wire:model.live.debounce.300ms="maxPush" min="0" class="w-24 p-2 border border-gray-300 rounded" placeholder="{{ __('Max.') }}" />
                        </div>
                        <div class="col-span-2 p-2 lg:table-cell bg-gray-50 lg:border-b">
                            <label for="filterLicense" class="text-sm font-semibold tracking-wide uppercase lg:hidden">{{ __('License') }}</label>
                            <select wire:model.live="license" class="w-full p-2 border border-gray-300 rounded">
                                <option value="">{{ __('All') }}</option>
                                @foreach ($licenses as $license)
                                    <option value="{{ $license }}">{{ $license }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    @foreach($repositories as $repository)
                        <div class="lg:table-row border lg:border-b hover:bg-gray-50 bg-white grid grid-cols-2 {{ $loop->first ? 'mt-4' : 'mt-8' }}  lg:mt-0">
                            <div class="order-1 col-span-2 p-2 pt-4 leading-tight lg:table-cell lg:order-none lg:col-span-1 lg:pt-2 lg:border-b lg:border-gray-200 lg:align-middle">
                                <div class="flex justify-between">
                                    <a class="mr-1 text-base font-bold tracking-tight text-blue-500 underline hover:text-blue-600 lg:text-lg lg:mr-0" href="{{ $repository->url }}" target="_blank" rel="noopener">
                                        <span>{{ $repository->name }}</span>
                                        <x-heroicon-m-arrow-top-right-on-square class="inline w-3 h-3" />
                                    </a>
                                    @if ($repository->stargazers)
                                    <div class="flex items-center justify-end space-x-1 lg:hidden">
                                        <span class="text-sm font-semibold">{{ $repository->stargazers }}</span>
                                        <x-heroicon-o-star class="w-4 h-4 text-yellow-500" />
                                    </div>
                                    @endif
                                </div>
                                <div class="flex justify-between text-sm text-gray-600 xl:text-base">
                                    <div>{{ $repository->description }}</div>

                                </div>
                            </div>
                            <div class="@if(!$repository->website || $repository->website == '') hidden @endif mb-2 lg:mb-0 lg:table-cell order-4 lg:order-none col-span-2 lg:col-span-1 p-2 lg:border-b lg:align-middle">
                                @if ($repository->website != "")
                                    <a class="block px-4 py-2 text-sm tracking-wider text-center text-white uppercase bg-blue-500 rounded lg:inline-block hover:bg-blue-700" href="{{ $repository->website }}" target="_blank" rel="noopener">
                                        <span>{{ __('Website') }}</span>
                                    </a>
                                @endif
                            </div>
                            <div class="@if(!$repository->author) hidden @endif  border-t border-b  lg:table-cell order-last lg:order-none col-span-2 lg:col-span-1 py-4 lg:py-2 px-2  lg:border-t-0 lg:align-middle bg-gray-50 lg:bg-transparent">
                                @if ($repository->author)
                                    <div class="mb-1 text-xs font-medium tracking-wide text-gray-600 uppercase lg:hidden">{{ __('Author') }}</div>
                                    <div class="font-bold tracking-tight">
                                        {{ $repository->author->display_name }}
                                    </div>
                                    <div class="flex space-x-2">
                                        @if ($repository->author->url != "")
                                            <a class="flex items-center space-x-1 text-sm text-blue-500 underline hover:text-blue-700" href="{{ url($repository->author->url) }}" target="_blank" rel="noopener">
                                                <span>{{ __('Profile') }}</span>
                                                <x-heroicon-m-arrow-top-right-on-square class="w-3 h-3" />
                                            </a>
                                        @endif
                                        @if ($repository->author->website != "")
                                            <a class="flex items-center space-x-1 text-sm text-blue-500 underline hover:text-blue-700" href="{{ url($repository->author->website) }}" target="_blank" rel="noopener">
                                                <span>{{ __('Website') }}</span>
                                                <x-heroicon-m-arrow-top-right-on-square class="w-3 h-3" />
                                            </a>
                                        @endif
                                    </div>
                                @endif
                            </div>
                            <div class="order-last col-span-2 p-2 lg:table-cell lg:w-80 bg-gray-50 lg:bg-transparent lg:order-none lg:col-span-1 lg:border-b lg:align-middle">
                                <div class="inline-flex flex-wrap" x-data="{ more: false }">
                                    @foreach ($repository->tags as $index => $tag)
                                        <span @if (($index + 1) > $max_tags) x-show="more" x-transition @endif class="mr-2 my-1 tag-category-{{ $tag->category_id}} text-sm font-medium tracking-wide border rounded py-1 px-2 bg-category-color text-white border-category-color">
                                            {{ $tag->name }}
                                        </span>
                                    @endforeach

                                    @if ($repository->tags->count() > $max_tags)
                                        <button class="p-2 text-sm font-semibold text-blue-500 underline hover:text-blue-600" x-cloak type="button" x-show="!more" @click="more = true">
                                            {{ __('+ :nb more', ['nb' => ($repository->tags->count() - $max_tags)])}}
                                        </button>
                                    @endif
                                </div>
                            </div>
                            <div class="order-2 hidden col-span-2 p-2 lg:table-cell lg:w-32 lg:order-none lg:col-span-1 lg:border-b lg:align-middle">
                                @if ($repository->stargazers)
                                <div class="flex items-center space-x-2">
                                    <x-heroicon-o-star class="w-4 h-4 text-yellow-500" />
                                    <span class="text-sm font-semibold">{{ $repository->stargazers }}</span>
                                </div>
                                @endif
                            </div>
                            <div class="order-3 p-2 lg:table-cell lg:w-32 lg:order-none lg:border-b lg:align-middle">
                                <div class="text-xs font-medium tracking-wide text-gray-400 uppercase lg:hidden">{{ __('Days since last push') }}</div>
                                @if ($repository->last_push)
                                    <div class="flex items-center space-x-2">
                                        <div class="w-2 h-2 rounded-full {{ $repository->getPushStatusClass() }}"></div>
                                        <span class="text-sm font-semibold">{{ $repository->days_since_last_push ?? '-' }}</span>
                                    </div>
                                @else
                                    -
                                @endif
                            </div>
                            <div class="order-3 p-2 font-semibold text-right lg:table-cell lg:w-32 lg:order-none lg:border-b lg:align-middle lg:text-left">
                                <div class="text-xs font-medium tracking-wide text-gray-400 uppercase lg:hidden">{{ __('License') }}</div>
                                {{ $repository->license ?? '-' }}
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="mt-4">
                    {{ $repositories->links() }}
                </div>

                @if (count($repositories) == 0)
                    <div class="p-4 mt-8 text-center text-orange-600 bg-orange-100 border border-orange-600">
                        {{ __('No results matched your search: ') }}<strong>{{ $search }}</strong>
                        <p>
                            <button type="button" class="mt-2 font-bold underline" @click.prevent="$wire.set('search', '')">
                                {{ __('Reset search term') }}
                            </button>
                        </p>
                    </div>
                @endif
        </div>
        <x-page-footer>

        </x-page-footer>
    </div>
    <div x-cloak class="fixed inset-0 z-10 2xl:hidden bg-white/60 backdrop-blur-sm" x-show="filterOpen" @click="toggleFilter"></div>
    <div x-cloak
        class="fixed z-20 inset-y-0 right-0 w-full max-w-[80%] 2xl:w-[360px] transition bg-gray-50 2xl:translate-x-0 duration-300 border-l shadow-md flex flex-col"
        :class="filterOpen ? 'translate-x-0' : 'translate-x-full'"
        >
        @if (count($repositories) > 0)
            <div class="flex items-center p-4 font-semibold tracking-widest text-white uppercase bg-gray-800">
                <div class="flex items-center justify-center space-x-2 font-semibold ">
                    <x-heroicon-o-tag class="w-6 h-6" />
                    <span>{{ __('Topics') }}</span>
                </div>
                <div class="ml-auto 2xl:hidden">
                    <button @click="toggleFilter">
                        <x-heroicon-o-x-mark class="w-6 h-6" />
                    </button>
                </div>
            </div>
            <div class="text-white bg-gray-700">
                <label for="filter-tags-and" class="flex items-center p-2 cursor-pointer">
                    <x-checkbox id="filter-tags-and" class="mr-2" value="1" wire:model.live="tags_and" />
                    <div>
                        <div class="text-sm ">Show repositories matching <strong>all</strong> selected topics</div>
                    </div>
                </label>
            </div>
            <div class="flex-1  overflow-y-auto {{ $split_tags_filter ? 'space-y-4' : ''}}">
                @foreach($grouped_tags->sortBy('order') as $cid => $category)
                    <div class="tag-category-{{ $cid }} ">
                        <label for="filter-category-{{ $cid }}" class="flex items-center p-4 text-sm font-semibold text-white border-t border-b cursor-pointer border-category-color bg-category-color">
                            <div wire:key="filter-{{ $cid }}-checkbox">
                                @if (!$tags_and)
                                    <x-checkbox id="filter-category-{{ $cid }}" wire:model.live="categories.{{ $cid }}.selected" />
                                @endif
                            </div>
                            <span class="mx-2">{{ $category['category']['name'] }}</span>
                            <span class="ml-auto font-bold">
                                {{ $category['category']['total'] }}
                            </span>
                        </label>
                        <div class="grid {{ $split_tags_filter ? 'grid-cols-2' : 'grid-cols-1'}}">
                            @foreach(collect($category['tags'])->sortBy('order') as $tagIndex => $tag)
                                <label
                                    for="filter-tag-{{ $tagIndex }}"
                                    class="flex items-center px-4 py-3 text-sm bg-white border-b border-r cursor-pointer lg:text-base hover:bg-gray-50">

                                    <x-checkbox value="1" id="filter-tag-{{ $tagIndex }}" wire:model.live="tags.{{ $tagIndex }}.selected" />
                                    <span class="mx-2 text-sm font-semibold leading-tight">{{ $tag['name'] }}</span>
                                    <span class="p-1 ml-auto text-xs font-bold bg-gray-100 rounded">{{ $tag['filtered'] }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="p-2 2xl:hidden">
                <button type="button" @click="toggleFilter" class="block w-full p-4 text-base font-medium tracking-wide text-center text-white bg-gray-800 ">
                    {{ __('Show :nb repositories', ['nb' => $repositories->total()])}}
                </button>
            </div>
        @endif
    </div>
</div>
