<footer {!! $attributes->merge(['class' => 'bg-gray-100 border-t py-4 mt-4']) !!}>
    <div class="container max-w-5xl py-8 lg:py-12">
        <div class="flex flex-wrap items-center justify-between space-y-8 lg:space-y-0">
            <div class="lg:mr-8">
                <h3 class="font-bold text-lg lg:text-xl tracking-tighter text-primary mb-2">{{ __('About') }}</h3>
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
                    <div>
                        <h3 class="text-primary uppercase font-semibold tracking-wide text-sm">{{ __('Source') }}</h3>
                        <div class="prose prose-sm">
                            <p><a class="font-semibold" href="{{ config('repositories.repository_link') }}" target="_blank" rel="noopener">Code for this application is available on our github.</a></p>
                        </div>
                    </div>
                @endif
                @if ($last_updated_at)
                    <div>
                        <h3 class="text-primary uppercase font-semibold tracking-wide text-sm">{{ __('Data last updated') }}</h3>
                        <div class="prose prose-sm">
                            <p>{{ $last_updated_at }}</p>
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
