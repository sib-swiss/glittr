<x-guest-layout :title="$title">
    <x-page-header class="border-b"></x-page-header>
    <div class="flex flex-col flex-1">
        <div class="max-w-lg px-4 mx-auto my-8 lg:w-1/3 lg:my-12">

            <div class="grid grid-cols-2 mt-8 bg-white border hover:bg-gray-50">
                <div class="order-1 col-span-2 p-2 pt-4 leading-tight">
                    <div class="flex justify-between">
                        <a class="mr-1 text-lg font-bold tracking-tight text-blue-500 underline hover:text-blue-600" href="{{ $repository->url }}" target="_blank" rel="noopener">
                            <span>{{ $repository->name }}</span>
                            <x-heroicon-m-arrow-top-right-on-square class="inline w-3 h-3" />
                        </a>
                        @if ($repository->stargazers)
                        <div class="flex items-center justify-end space-x-1">
                            <span class="text-base font-semibold">{{ $repository->stargazers }}</span>
                            <x-heroicon-o-star class="w-4 h-4 text-yellow-500" />
                        </div>
                        @endif
                    </div>
                    <div class="flex justify-between text-base text-gray-600">
                        <div>{{ $repository->description }}</div>

                    </div>
                </div>
                <div class="@if(!$repository->website || $repository->website == '') hidden @endif mb-2 order-4 col-span-2 py-4 px-2">
                    @if ($repository->website != "")
                        <a class="block px-4 py-2 text-base tracking-wider text-center text-white uppercase bg-blue-500 rounded hover:bg-blue-700" href="{{ $repository->website }}" target="_blank" rel="noopener">
                            <span>{{ __('Website') }}</span>
                        </a>
                    @endif
                </div>
                <div class="@if(!$repository->author) hidden @endif  border-t border-b  order-last col-span-2 py-4 px-2 bg-gray-50">
                    @if ($repository->author)
                        <div class="mb-1 text-xs font-medium tracking-wide text-gray-600 uppercase">{{ __('Author') }}</div>
                        <div class="font-bold tracking-tight">
                            {{ $repository->author->display_name }}
                        </div>
                        <div class="flex space-x-2">
                            @if ($repository->author->url != "")
                                <a class="flex items-center space-x-1 text-base text-blue-500 underline hover:text-blue-700" href="{{ url($repository->author->url) }}" target="_blank" rel="noopener">
                                    <span>{{ __('Profile') }}</span>
                                    <x-heroicon-m-arrow-top-right-on-square class="w-3 h-3" />
                                </a>
                            @endif
                            @if ($repository->author->website != "")
                                <a class="flex items-center space-x-1 text-base text-blue-500 underline hover:text-blue-700" href="{{ url($repository->author->website) }}" target="_blank" rel="noopener">
                                    <span>{{ __('Website') }}</span>
                                    <x-heroicon-m-arrow-top-right-on-square class="w-3 h-3" />
                                </a>
                            @endif
                        </div>
                    @endif
                </div>
                <div class="order-last col-span-2 p-2 bg-gray-50">
                    <div class="inline-flex flex-wrap" x-data="{ more: false }">
                        @foreach ($repository->tags as $index => $tag)
                            <span @if (($index + 1) > 20) x-show="more" x-transition @endif class="mr-2 my-1 tag-category-{{ $tag->category_id}} text-sm font-medium tracking-wide border rounded py-1 px-2 bg-category-color text-white border-category-color">
                                {{ $tag->name }}
                            </span>
                        @endforeach

                        @if ($repository->tags->count() > 20)
                            <button class="p-2 text-base font-semibold text-blue-500 underline hover:text-blue-600" x-cloak type="button" x-show="!more" @click="more = true">
                                {{ __('+ :nb more', ['nb' => ($repository->tags->count() - 20)])}}
                            </button>
                        @endif
                    </div>
                </div>

                <div class="order-3 p-2 ">
                    <div class="text-xs font-medium tracking-wide text-gray-400 uppercase">{{ __('Days since last push') }}</div>
                    @if ($repository->last_push)
                        <div class="flex items-center space-x-2">
                            <div class="w-2 h-2 rounded-full {{ $repository->getPushStatusClass() }}"></div>
                            <span class="text-sm font-semibold">{{ $repository->days_since_last_push ?? '-' }}</span>
                        </div>
                    @else
                        -
                    @endif
                </div>
                <div class="order-3 p-2 font-semibold text-right ">
                    <div class="text-xs font-medium tracking-wide text-gray-400 uppercase">{{ __('License') }}</div>
                    {{ $repository->license ?? '-' }}
                </div>
            </div>
            <div class="pt-4 mt-4 border-t">
                <div class="mx-auto prose-sm prose">
                    <p>Repository added on: {{ $repository->created_at }}</p>
                    <p>
                        <a href="{{ route('homepage') }}" class="flex items-center space-x-1">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="size-4">
                                <path fill-rule="evenodd" d="M14 8a.75.75 0 0 1-.75.75H4.56l3.22 3.22a.75.75 0 1 1-1.06 1.06l-4.5-4.5a.75.75 0 0 1 0-1.06l4.5-4.5a.75.75 0 0 1 1.06 1.06L4.56 7.25h8.69A.75.75 0 0 1 14 8Z" clip-rule="evenodd" />
                            </svg>
                            <span>{{ __('Browse all repositories') }}</span>
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>
    <x-page-footer />
</x-guest-layout>
