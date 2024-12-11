<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('APICURON Leaderboard') }}
        </h2>
    </x-slot>

    <x-admin.container>
        <apicuron-leaderboard resource="{{ config('apicuron.resource_id') }}"></apicuron-leaderboard>
    </x-admin.container>

    @push('modals')
        @if (config('apicuron.env') != 'production')
            <script src="https://dev.apicuron.org/assets/widgets/apicuron-leaderboard.js"></script>
        @else
            <script src="https://apicuron.org/assets/widgets/apicuron-leaderboard.js"></script>
        @endif
    @endpush
</x-app-layout>
