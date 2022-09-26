<div>
    @if (count($submissions) > 0)
       @foreach($submissions as $submission)
       @endforeach
       {{ $submissions->links() }}
    @else
        <div class="text-lg space-y-4 flex flex-col items-center border p-4 rounded border-green-500 text-green-500 bg-green-50">
            <x-heroicon-o-hand-thumb-up class="w-8 h-8" />
            <div>{{ __('No pending submissions') }}</div>
        </div>
    @endif
</div>
