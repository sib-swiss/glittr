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
                <button
                    role="button"
                    class="p-4 text-sm font-semibold tracking-wider text-white uppercase transition border rounded-t-lg hover:bg-gray-800"
                    x-bind:class="{ 'bg-gray-900': show == 'apicuron', 'bg-gray-500': show != 'apicuron' }"
                    x-on:click.prevent="show = 'apicuron'"
                >
                    APICURON
                </button>
            </div>

            <div class="space-y-4" x-cloack x-show="show == 'apicuron'">
                <div>
                    <label for="apicuron_enabled" class="flex items-center">
                        <x-checkbox id="apicuron_enabled" name="apicuron_enabled" value="1" wire:model="apicuron_enabled" />
                        <span class="ml-2 text-sm text-gray-600">{{ __('Enable APICURON') }}</span>
                    </label>
                </div>
                <div>
                    <x-label for="apicuron_submission_activity_term" value="{{ __('Repository submission activity term id') }}" />
                    <x-input id="apicuron_submission_activity_term" type="text" class="block w-full mt-1" wire:model="apicuron_submission_activity_term" />
                    <x-input-error for="apicuron_submission_activity_term" class="mt-1" />
                    <div class="mt-1 text-sm text-gray-500">
                        the term id created in APICURON for the repository submission activity
                    </div>
                </div>
                <div>
                    <x-label for="apicuron_title" value="{{ __('APICURON title') }}" />
                    <x-input id="apicuron_title" type="text" class="block w-full mt-1" wire:model="apicuron_title" />
                    <x-input-error for="apicuron_title" class="mt-1" />
                </div>
                <div>
                    <x-label for="apicuron_introduction" value="{{ __('APICURON introduction text') }}" />
                    <x-textarea id="apicuron_introduction" type="text" class="block w-full mt-1" rows="2"
                        wire:model="apicuron_introduction" />
                    <x-input-error for="apicuron_introduction" class="mt-1" />
                </div>
                <div>
                    <x-label for="apicuron_login_btn" value="{{ __('APICURON login ORCID button text') }}" />
                    <x-input id="apicuron_login_btn" type="text" class="block w-full mt-1" wire:model="apicuron_login_btn" />
                    <x-input-error for="apicuron_login_btn" class="mt-1" />
                </div>
                <div>
                    <x-label for="apicuron_logged_warning" value="{{ __('APICURON logged in warning') }}" />
                    <x-textarea id="apicuron_logged_warning" type="text" class="block w-full mt-1" rows="2"
                        wire:model="apicuron_logged_warning" />
                    <x-input-error for="apicuron_logged_warning" class="mt-1" />
                </div>
                <div>
                    <x-label for="apicuron_logout_btn" value="{{ __('APICURON logout ORCID button text') }}" />
                    <x-input id="apicuron_logout_btn" type="text" class="block w-full mt-1" wire:model="apicuron_logout_btn" />
                    <x-input-error for="apicuron_logout_btn" class="mt-1" />
                </div>
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
