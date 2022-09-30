<x-guest-layout>
    <div class="container">
        <div class="max-w-5xl mx-auto">
            <x-page-header></x-page-header>
            <div class="space-y-4 lg:space-y-12">
                <div class="border-t pt-4 lg:pt-8">
                    @livewire('submit-form')
                </div>
                <div class="border-t pt-4 lg:pt-8">
                    @livewire('contact-form')
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
