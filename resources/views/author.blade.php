<x-guest-layout sidebar="true" :title="$title">
    @livewire('repositories', ['locked_author_id' => $author->id])
    <x-page-footer />
</x-guest-layout>
