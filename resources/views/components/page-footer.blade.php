<footer {!! $attributes->merge(['class' => 'bg-gray-100 border-t py-4 mt-4']) !!}>
    <div class="container max-w-5xl py-8 lg:py-12">
        <div class="flex flex-wrap items-center justify-between space-y-8 lg:space-y-0">
            <div class="lg:mr-8">
                <h3 class="font-bold text-lg lg:text-xl tracking-tighter text-glittr mb-2 inline-block">{{ __('About') }}</h3>
                <div class="prose prose-sm leading-snug">
                    <p>
                        <strong>{{ config('app.name') }}</strong> is a curated list of bioinformatics training material.<br />All material is:
                    </p>
                    <ul>
                        <li>In a GitHub or GitLab repository</li>
                        <li>Free to use</li>
                        <li>Written in markdown or similar</li>
                    </ul>
                    <p>
                        <strong>NOTE:</strong> This list of courses is selected only based on the above criteria.<br />There are no checks on quality.
                    </p>
                </div>
            </div>
            <div class="flex flex-col items-start lg:text-right lg:items-end space-y-2">
                @if (config('repositories.repository_link', '') != '')
                    <div class="flex">
                        <!--<svg xmlns="http://www.w3.org/2000/svg" class="md:order-2 w-12 h-12 mr-4 md:ml-4" viewBox="0 0 496 512">
                            <defs>
                                <linearGradient id="github-icon-gradient">
                                    <stop offset="0%" stop-color="#3A86FF" />
                                    <stop offset="100%" stop-color="#8338EC" />
                                </linearGradient>
                            </defs>
                            <path fill="url(#github-icon-gradient)" d="M165.9 397.4c0 2-2.3 3.6-5.2 3.6-3.3.3-5.6-1.3-5.6-3.6 0-2 2.3-3.6 5.2-3.6 3-.3 5.6 1.3 5.6 3.6zm-31.1-4.5c-.7 2 1.3 4.3 4.3 4.9 2.6 1 5.6 0 6.2-2s-1.3-4.3-4.3-5.2c-2.6-.7-5.5.3-6.2 2.3zm44.2-1.7c-2.9.7-4.9 2.6-4.6 4.9.3 2 2.9 3.3 5.9 2.6 2.9-.7 4.9-2.6 4.6-4.6-.3-1.9-3-3.2-5.9-2.9zM244.8 8C106.1 8 0 113.3 0 252c0 110.9 69.8 205.8 169.5 239.2 12.8 2.3 17.3-5.6 17.3-12.1 0-6.2-.3-40.4-.3-61.4 0 0-70 15-84.7-29.8 0 0-11.4-29.1-27.8-36.6 0 0-22.9-15.7 1.6-15.4 0 0 24.9 2 38.6 25.8 21.9 38.6 58.6 27.5 72.9 20.9 2.3-16 8.8-27.1 16-33.7-55.9-6.2-112.3-14.3-112.3-110.5 0-27.5 7.6-41.3 23.6-58.9-2.6-6.5-11.1-33.3 2.6-67.9 20.9-6.5 69 27 69 27 20-5.6 41.5-8.5 62.8-8.5s42.8 2.9 62.8 8.5c0 0 48.1-33.6 69-27 13.7 34.7 5.2 61.4 2.6 67.9 16 17.7 25.8 31.5 25.8 58.9 0 96.5-58.9 104.2-114.8 110.5 9.2 7.9 17 22.9 17 46.4 0 33.7-.3 75.4-.3 83.6 0 6.5 4.6 14.4 17.3 12.1C428.2 457.8 496 362.9 496 252 496 113.3 383.5 8 244.8 8zM97.2 352.9c-1.3 1-1 3.3.7 5.2 1.6 1.6 3.9 2.3 5.2 1 1.3-1 1-3.3-.7-5.2-1.6-1.6-3.9-2.3-5.2-1zm-10.8-8.1c-.7 1.3.3 2.9 2.3 3.9 1.6 1 3.6.7 4.3-.7.7-1.3-.3-2.9-2.3-3.9-2-.6-3.6-.3-4.3.7zm32.4 35.6c-1.6 1.3-1 4.3 1.3 6.2 2.3 2.3 5.2 2.6 6.5 1 1.3-1.3.7-4.3-1.3-6.2-2.2-2.3-5.2-2.6-6.5-1zm-11.4-14.7c-1.6 1-1.6 3.6 0 5.9 1.6 2.3 4.3 3.3 5.6 2.3 1.6-1.3 1.6-3.9 0-6.2-1.4-2.3-4-3.3-5.6-2z"/>
                        </svg>-->
                        <div class="">
                            <h3 class="text-glittr font-bold tracking-wide text-base inline-block">{{ __('Source') }}</h3>
                            <div class="prose prose-sm">
                                <p><a class="font-semibold" href="{{ config('repositories.repository_link') }}" target="_blank" rel="noopener">Code for this application is available on our github.</a></p>
                            </div>
                        </div>
                    </div>
                @endif
                @if ($last_updated_at)
                    <div class="flex">
                        <!--<svg xmlns="http://www.w3.org/2000/svg" class="md:order-2 w-12 h-12 mr-4 md:ml-4" viewBox="0 0 496 512">
                            <defs>
                                <linearGradient id="date-icon-gradient">
                                    <stop offset="0%" stop-color="#FF006E" />
                                    <stop offset="50%" stop-color="#FB5607" />
                                    <stop offset="100%" stop-color="#FFBE0B" />
                                </linearGradient>
                            </defs>
                            <path fill="url(#date-icon-gradient)" d="M256 512C114.6 512 0 397.4 0 256S114.6 0 256 0S512 114.6 512 256s-114.6 256-256 256zM232 120V256c0 8 4 15.5 10.7 20l96 64c11 7.4 25.9 4.4 33.3-6.7s4.4-25.9-6.7-33.3L280 243.2V120c0-13.3-10.7-24-24-24s-24 10.7-24 24z" />
                        </svg>-->
                        <div class="">
                            <h3 class="text-glittr font-bold tracking-wide text-base inline-block">{{ __('Data last updated') }}</h3>
                            <div class="prose prose-sm">
                                <p>{{ $last_updated_at }}</p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
    <!--<div class="text-center py-4">
        <img src="{{ url('/sib-mark.svg') }}" class="inline-block max-w-full h-6" />
    </div>-->
</footer>
