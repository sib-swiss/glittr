<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Settings') }}
        </h2>
    </x-slot>

    <x-admin.container>
        @livewire('admin.settings')
    </x-admin.container>
</x-app-layout>
