<footer {!! $attributes->merge(['class' => 'bg-gray-100 border-t py-4 mt-4']) !!}>
    <div class="container grid items-end grid-cols-1 lg:grid-cols-2 gap-4 lg:gap-8 ">
        <div>
            <h3 class="text-primary mb-1 uppercase font-semibold tracking-wide text-sm">{{ __('About') }}</h3>
            <div class="prose prose-sm max-w-none leading-tight">
                <p>
                    <strong>{{ config('app.name') }}</strong> is a curated list of bioinformatics training material. All material is:
                </p>
                <ul>
                    <li>In a GitHub or GitLab repository</li>
                    <li>Free to use</li>
                    <li>Written in markdown or similar</li>
                </ul>
                <p>
                    <strong>NOTE:</strong> This list of courses is selected only based on the above criteria. There are no checks on quality.
                </p>
            </div>
        </div>
        <div class="flex flex-col items-start lg:text-right lg:items-end space-y-2">
            @if (config('repositories.repository_link', '') != '')
                <div>
                    <h3 class="text-primary uppercase font-semibold tracking-wide text-sm">{{ __('Source') }}</h3>
                    <div class="prose prose-sm">
                        <p><a class="font-semibold" href="{{ config('repositories.repository_link') }}" target="_blank" rel="noopener">Code source</a> for this application is available on github.</p>
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
        <div class="lg:col-span-2 text-center border-t pt-4">
            <img src="{{ url('/sib-mark.svg') }}" class="inline-block max-w-full h-6" />
        </div>
    </div>

</footer>
