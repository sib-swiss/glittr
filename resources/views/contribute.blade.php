<x-guest-layout>
    <x-page-header class="border-b"></x-page-header>
    <div class="flex-1 flex flex-col">
        <div class="container max-w-5xl py-8">
            <div class="space-y-4 lg:space-y-12">
                <div class="pt-4 lg:pt-8">
                    @livewire('submit-form')
                </div>
                {{-- TODO: simple contact form
                <div class="border-t pt-4 lg:pt-8">
                    @livewire('contact-form')
                </div>
                --}}
            </div>
        </div>
    </div>
    <x-page-footer />
</x-guest-layout>
