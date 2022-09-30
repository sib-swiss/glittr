<footer {!! $attributes->merge(['class' => 'bg-gray-100 border-t py-8 lg:py-12 mt-4']) !!}>
    <div class="container grid items-end grid-cols-1 lg:grid-cols-2 gap-4 lg:gap-8 ">
        <div>
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
        <div class="flex flex-col text-center lg:text-right items-center lg:items-end space-y-2">
            @if (config('repositories.repository_link', '') != '')
                <div class="prose prose-sm">
                    <p><a class="font-semibold" href="{{ config('repositories.repository_link') }}" target="_blank">Code source</a> for this application</p>
                </div>
            @endif
            @if ($last_updated_at)
                <div class="prose prose-sm">
                    <p><strong>Last updated:</strong> {{ $last_updated_at }}</p>
                </div>
            @endif
            <div>
                <img src="{{ url('/sib-mark.svg') }}" class="max-w-full h-6" />
            </div>
        </div>
    </div>
</footer>
