<footer {!! $attributes->merge(['class' => 'bg-gray-100 border-t py-4 mt-4']) !!}>
    <div class="container py-8 lg:py-12">
        <div class="flex flex-wrap items-center justify-between space-y-8 lg:space-y-0">
            <div class="lg:mr-8">
                <h3 class="inline-block mb-2 text-2xl font-bold tracking-tighter text-glittr">{{ __('About') }}</h3>
                <div class="leading-snug prose-sm prose lg:prose lg:leading-snug">
                    {!! $about_text !!}
                </div>
            </div>
            <div class="flex flex-col items-stretch w-full space-y-4 lg:w-auto lg:text-right lg:items-end">
                <div class="space-y-4 lg:space-y-8">
                    @if ($show_repository_link)
                    <div class="flex items-center justify-start lg:justify-end">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 mr-2 lg:order-2 lg:ml-2 lg:mr-0" viewBox="0 0 496 512"><!--! Font Awesome Pro 6.2.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2022 Fonticons, Inc. --><path d="M165.9 397.4c0 2-2.3 3.6-5.2 3.6-3.3.3-5.6-1.3-5.6-3.6 0-2 2.3-3.6 5.2-3.6 3-.3 5.6 1.3 5.6 3.6zm-31.1-4.5c-.7 2 1.3 4.3 4.3 4.9 2.6 1 5.6 0 6.2-2s-1.3-4.3-4.3-5.2c-2.6-.7-5.5.3-6.2 2.3zm44.2-1.7c-2.9.7-4.9 2.6-4.6 4.9.3 2 2.9 3.3 5.9 2.6 2.9-.7 4.9-2.6 4.6-4.6-.3-1.9-3-3.2-5.9-2.9zM244.8 8C106.1 8 0 113.3 0 252c0 110.9 69.8 205.8 169.5 239.2 12.8 2.3 17.3-5.6 17.3-12.1 0-6.2-.3-40.4-.3-61.4 0 0-70 15-84.7-29.8 0 0-11.4-29.1-27.8-36.6 0 0-22.9-15.7 1.6-15.4 0 0 24.9 2 38.6 25.8 21.9 38.6 58.6 27.5 72.9 20.9 2.3-16 8.8-27.1 16-33.7-55.9-6.2-112.3-14.3-112.3-110.5 0-27.5 7.6-41.3 23.6-58.9-2.6-6.5-11.1-33.3 2.6-67.9 20.9-6.5 69 27 69 27 20-5.6 41.5-8.5 62.8-8.5s42.8 2.9 62.8 8.5c0 0 48.1-33.6 69-27 13.7 34.7 5.2 61.4 2.6 67.9 16 17.7 25.8 31.5 25.8 58.9 0 96.5-58.9 104.2-114.8 110.5 9.2 7.9 17 22.9 17 46.4 0 33.7-.3 75.4-.3 83.6 0 6.5 4.6 14.4 17.3 12.1C428.2 457.8 496 362.9 496 252 496 113.3 383.5 8 244.8 8zM97.2 352.9c-1.3 1-1 3.3.7 5.2 1.6 1.6 3.9 2.3 5.2 1 1.3-1 1-3.3-.7-5.2-1.6-1.6-3.9-2.3-5.2-1zm-10.8-8.1c-.7 1.3.3 2.9 2.3 3.9 1.6 1 3.6.7 4.3-.7.7-1.3-.3-2.9-2.3-3.9-2-.6-3.6-.3-4.3.7zm32.4 35.6c-1.6 1.3-1 4.3 1.3 6.2 2.3 2.3 5.2 2.6 6.5 1 1.3-1.3.7-4.3-1.3-6.2-2.2-2.3-5.2-2.6-6.5-1zm-11.4-14.7c-1.6 1-1.6 3.6 0 5.9 1.6 2.3 4.3 3.3 5.6 2.3 1.6-1.3 1.6-3.9 0-6.2-1.4-2.3-4-3.3-5.6-2z"/></svg>
                        <div class="prose-sm prose">
                            <p><a class="font-semibold" href="{{ $repository_link }}" target="_blank" rel="noopener">Code for this application is available on our github.</a></p>
                        </div>
                    </div>
                    @endif

                    <div class="prose-sm prose ">
                        Developed, maintained and curated by:
                        <div class="flex flex-wrap space-y-1 lg:space-x-4 lg:space-y-0">
                            @foreach (config('glittr.contributors', []) as $contributor)
                                <div class="w-full lg:flex-1 lg:text-center">
                                    <div class="whitespace-nowrap"><strong>{{ $contributor['name'] }}</strong></div>
                                    <div class="flex items-center space-x-2 lg:justify-center">
                                        @if(isset($contributor['links']['twitter']))
                                            <a href="{{ $contributor['links']['twitter'] }}" title="{{ $contributor['name'] }} Twitter profile" target="_blank" rel="_noreferrer" class="p-1">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" viewBox="0 0 512 512"><!--! Font Awesome Pro 6.2.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2022 Fonticons, Inc. --><path d="M459.37 151.716c.325 4.548.325 9.097.325 13.645 0 138.72-105.583 298.558-298.558 298.558-59.452 0-114.68-17.219-161.137-47.106 8.447.974 16.568 1.299 25.34 1.299 49.055 0 94.213-16.568 130.274-44.832-46.132-.975-84.792-31.188-98.112-72.772 6.498.974 12.995 1.624 19.818 1.624 9.421 0 18.843-1.3 27.614-3.573-48.081-9.747-84.143-51.98-84.143-102.985v-1.299c13.969 7.797 30.214 12.67 47.431 13.319-28.264-18.843-46.781-51.005-46.781-87.391 0-19.492 5.197-37.36 14.294-52.954 51.655 63.675 129.3 105.258 216.365 109.807-1.624-7.797-2.599-15.918-2.599-24.04 0-57.828 46.782-104.934 104.934-104.934 30.213 0 57.502 12.67 76.67 33.137 23.715-4.548 46.456-13.32 66.599-25.34-7.798 24.366-24.366 44.833-46.132 57.827 21.117-2.273 41.584-8.122 60.426-16.243-14.292 20.791-32.161 39.308-52.628 54.253z"/></svg>
                                            </a>
                                        @endif
                                        @if(isset($contributor['links']['orcid']))
                                            <a href="{{ $contributor['links']['orcid'] }}" title="{{ $contributor['name'] }} ORCID profile " target="_blank" rel="_noreferrer" class="p-1">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" width="32" height="32" viewBox="0 0 32 32">
                                                <path d="M16 0c-8.839 0-16 7.161-16 16s7.161 16 16 16c8.839 0 16-7.161 16-16s-7.161-16-16-16zM9.823 5.839c0.704 0 1.265 0.573 1.265 1.26 0 0.688-0.561 1.265-1.265 1.265-0.692-0.004-1.26-0.567-1.26-1.265 0-0.697 0.563-1.26 1.26-1.26zM8.864 9.885h1.923v13.391h-1.923zM13.615 9.885h5.197c4.948 0 7.125 3.541 7.125 6.703 0 3.439-2.687 6.699-7.099 6.699h-5.224zM15.536 11.625v9.927h3.063c4.365 0 5.365-3.312 5.365-4.964 0-2.687-1.713-4.963-5.464-4.963z"/>
                                            </svg>
                                        </a>
                                        @endif
                                        @if(isset($contributor['links']['linkedin']))
                                        <a href="{{ $contributor['links']['linkedin'] }}" title="{{ $contributor['name'] }} on LinkedIn" target="_blank" rel="_noreferrer" class="p-1">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" viewBox="0 0 448 512"><!--! Font Awesome Pro 6.2.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2022 Fonticons, Inc. --><path d="M416 32H31.9C14.3 32 0 46.5 0 64.3v383.4C0 465.5 14.3 480 31.9 480H416c17.6 0 32-14.5 32-32.3V64.3c0-17.8-14.4-32.3-32-32.3zM135.4 416H69V202.2h66.5V416zm-33.2-243c-21.3 0-38.5-17.3-38.5-38.5S80.9 96 102.2 96c21.2 0 38.5 17.3 38.5 38.5 0 21.3-17.2 38.5-38.5 38.5zm282.1 243h-66.4V312c0-24.8-.5-56.7-34.5-56.7-34.6 0-39.9 27-39.9 54.9V416h-66.4V202.2h63.7v29.2h.9c8.9-16.8 30.6-34.5 62.9-34.5 67.2 0 79.7 44.3 79.7 101.9V416z"/></svg>
                                        </a>
                                        @endif
                                    </div>
                                </div>
                                @if (!$loop->last)
                                <div class="w-[1px] mx-2 bg-gray-300 hidden lg:block"></div>
                                @endif
                            @endforeach
                        </div>
                    </div>

                    @if ($last_updated_at)
                        <div class="flex items-center justify-start lg:justify-end">
                            <div class="prose-sm prose">
                                <p>Data last updated: <strong>{{ $last_updated_at }}</strong></p>
                            </div>

                        </div>
                     @endif
                </div>
            </div>
        </div>
        <div class="flex flex-col items-center justify-center pt-4 mt-8 leading-tight text-center border-t lg:text-right lg:flex-row">
            <a href="https://www.sib.swiss" class="mb-4 lg:order-2 lg:ml-4 lg:mb-0">
                <img src="{{ url('/sib-web-logo.svg') }}" class="h-16 max-w-full" alt="Swiss Institute of Bioinformatics" />
            </a>
            <div class="leading-snug prose-sm prose">
                {!! $footer_text !!}
            </div>
        </div>
    </div>
    <!--<div class="py-4 text-center">
        <img src="{{ url('/sib-mark.svg') }}" class="inline-block h-6 max-w-full" />
    </div>-->
</footer>
