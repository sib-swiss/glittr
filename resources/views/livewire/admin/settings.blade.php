<div>
    <form wire:submit="save">
        <div x-data="{ show: 'general' }">
            <div class="flex items-center justify-center my-8 border-b">
                <button
                    role="button"
                    class="p-4 text-sm font-semibold tracking-wider text-white uppercase transition border rounded-t-lg hover:bg-gray-800"
                    x-bind:class="{ 'bg-gray-900': show == 'general', 'bg-gray-500': show != 'general' }"
                    x-on:click.prevent="show = 'general'"
                    >
                    General
                </button>
                <button
                    role="button"
                    class="p-4 text-sm font-semibold tracking-wider text-white uppercase transition border rounded-t-lg hover:bg-gray-800"
                    x-bind:class="{ 'bg-gray-900': show == 'terms', 'bg-gray-500': show != 'terms' }"
                    x-on:click.prevent="show = 'terms'"
                >
                    Terms
                </button>
            </div>

            <div class="space-y-4" x-cloack x-show="show == 'terms'">
                <div>
                    <x-label for="terms" value="{{ __('Terms of use') }}" />
                    <x-textarea id="terms" type="text" rows="20" class="block w-full mt-1"
                    wire:model="terms" />
                    <x-input-error for="terms" class="mt-1" />
                </div>
            </div>
            <div class="space-y-4"  x-cloack x-show="show == 'general'">
                <div>
                    <x-label for="site_name" value="{{ __('Site name') }}" />
                    <x-input id="site_name" type="text" class="block w-full mt-1" wire:model="site_name" />
                    <x-input-error for="site_name" class="mt-1" />
                </div>
                <div>
                    <x-label for="site_description" value="{{ __('Site meta description') }}" />
                    <x-textarea id="site_description" type="text" rows="2" class="block w-full mt-1"
                        wire:model="site_description" />
                    <x-input-error for="site_description" class="mt-1" />
                </div>
                <div>
                    <x-label for="homepage_page_title" value="{{ __('Homepage page title') }}" />
                    <x-input id="homepage_page_title" type="text" class="block w-full mt-1"
                        wire:model="homepage_page_title" />
                    <x-input-error for="homepage_page_title" class="mt-1" />
                </div>
                <div>
                    <x-label for="header_text" value="{{ __('Header text') }}" />
                    <x-textarea id="header_text" type="text" rows="2" class="block w-full mt-1"
                        wire:model="header_text" />
                    <x-input-error for="header_text" class="mt-1" />
                </div>
                <div>
                    <x-label for="about_text" value="{{ __('About text') }}" />
                    <x-textarea id="about_text" type="text" class="block w-full mt-1" wire:model="about_text" />
                    <x-input-error for="about_text" class="mt-1" />
                </div>
                <div>
                    <x-label for="footer_text" value="{{ __('Footer text') }}" />
                    <x-textarea id="footer_text" type="text" rows="2" class="block w-full mt-1"
                        wire:model="footer_text" />
                    <x-input-error for="footer_text" class="mt-1" />
                </div>
                <div>
                    <x-label for="contribute_text" value="{{ __('Contribute text') }}" />
                    <x-textarea id="contribute_text" type="text" class="block w-full mt-1"
                        wire:model="contribute_text" />
                    <x-input-error for="contribute_text" class="mt-1" />
                </div>
                <div>
                    <x-label for="mail_signature" value="{{ __('Mail Signature') }}" />
                    <x-textarea id="mail_signature" type="text" class="block w-full mt-1" rows="2"
                        wire:model="mail_signature" />
                    <x-input-error for="mail_signature" class="mt-1" />
                </div>
            </div>
            <div class="pt-4 border-t scroll-mt-4">
                <x-button>
                    {{ __('Save') }}
                </x-button>
            </div>
        </div>
    </form>
</div>
