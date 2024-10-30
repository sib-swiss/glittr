<div>
    @if ($submitted)
        <div class="my-8 text-center lg:my-12">
            <h1 class="text-xl font-bold tracking-tighter lg:text-2xl text-primary">{{ __('Thank you!') }}</h1>
            <div class="space-y-4">
                <div class="mx-auto prose prose-lg">{{ __('Your submission was successfully sent.')}}</div>
                @if (session('orcid'))
                    <div class="p-4 mx-auto prose rounded-lg bg-gray-50">
                        {!! $apicuron_logged_warning !!}
                        <p><x-secondary-button role="button" wire:click.prevent="orcidLogout">{{ $apicuron_logout_btn }}</x-secondary-button></p>
                    </div>
                @endif
                <x-button role="button" wire:click="resetForm" class="mt-4">{{ __('Submit another repository') }}</x-button>
            </div>
        </div>
    @else
        <x-form-section submit="save">
            <x-slot name="title">
                {{ __('Submit a repository') }}
            </x-slot-name>

            <x-slot name="description">
                <div class="prose-sm prose">
                    {!! $text !!}
                </div>
            </x-slot>

            <x-slot name="form">
                <div class="col-span-6">
                    <div class="flex items-center">
                        <x-label for="url" value="{{ __('Repository url') }}" /> <span class="ml-1 text-red-500">*</span>
                    </div>
                    <x-input id="url" type="text" class="block w-full mt-1" placeholder="https://github.com/..." wire:model.live="url" autofocus />
                    <div class="mt-1 text-sm font-light">
                        <strong>github.com</strong> or <strong>gitlab.com</strong> repository url
                    </div>
                    <x-input-error for="url" class="mt-1" />
                </div>

                @if ($existingWarning)
                <div class="col-span-6 p-4 mt-1 text-sm text-orange-500 border border-orange-500 rounded bg-orange-50">
                    A repository with this url already exists in the collection.<br />
                    If you want to update information regarding this entry, please leave a comment.
                </div>
                @endif

                <div class="col-span-6">
                    <x-label for="tags" value="{{ __('Proposed topics') }}" />
                    @livewire('tag-select', ['values' => $tags])
                    <x-input-error for="tags" class="mt-1" />
                </div>

                <div class="col-span-6 bg-gradient-to-r from-[#4D194D] to-[#9A031E] p-4 md:p-6 rounded-lg my-4">
                    <div class="prose-sm prose text-white max-w-none prose-invert">
                        <h3>{{ $apicuron_title }}</h3>
                        {!! $apicuron_introduction !!}
                    </div>
                    @if (session('orcid'))
                        <div class="pt-2 mt-2 prose-sm prose border-t max-w-none border-white/20 prose-invert">
                            <div class="flex flex-wrap space-x-2 item-center">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="text-green-500 size-6">
                                    <path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12Zm13.36-1.814a.75.75 0 1 0-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 0 0-1.06 1.06l2.25 2.25a.75.75 0 0 0 1.14-.094l3.75-5.25Z" clip-rule="evenodd" />
                                </svg>
                                <div>Logged in as: {{ session('orcid.name') }} ({{ session('orcid.id') }})</div>
                                <div>
                                    <button role="button" wire:click.prevent="orcidLogout" class="font-bold text-white underline">Logout</button></div>
                            </div>
                        </div>
                    @else
                        <button role="button" wire:click.prevent="orcidLogin" class="w-full p-2 mt-4 bg-white hover:bg-white/90 transition rounded-lg text-[#4D194D]">
                            {{ $apicuron_login_btn }}
                        </button>
                    @endif
                </div>

                <div class="col-span-6">
                    <div class="p-4 mt-1 text-sm text-blue-500 border border-blue-500 rounded bg-blue-50">
                        {{ __('Your name and email will only be used to keep you informed about these submit process.') }}
                    </div>
                </div>
                <div class="col-span-6 sm:col-span-3">
                    <div class="flex items-center">
                        <x-label for="name" value="{{ __('Name') }}" /> <span class="ml-1 text-red-500">*</span>
                    </div>
                    <x-input id="name" type="text" placeholder="Your name" class="block w-full mt-1" wire:model="name" />
                    <x-input-error for="name" class="mt-1" />
                </div>
                <div class="col-span-6 sm:col-span-3">
                    <div class="flex items-center">
                        <x-label for="email" value="{{ __('Email') }}" /> <span class="ml-1 text-red-500">*</span>
                    </div>
                    <x-input id="email" type="email" class="block w-full mt-1" placeholder="Your email" wire:model="email" />
                    <x-input-error for="email" class="mt-1" />
                </div>

                <div class="col-span-6">
                    <x-label for="comment" value="{{ __('Comment') }}" />
                    <x-textarea id="comment" class="block w-full mt-1" placeholder="Optional comment" wire:model="comment" />
                    <x-input-error for="comment" class="mt-1" />
                </div>
                <div class="block col-span-6 mt-1 text-sm font-medium text-gray-700">
                    By submitting a repository, you hereby acknowledge and confirm that you have read the <a href="{{ route('terms-of-use') }}" target="_blank" class="inline-flex items-center mr-1 space-x-1 text-sm text-blue-500 underline hover:text-blue-700"><span>Glittr Terms of Use</span><x-heroicon-m-arrow-top-right-on-square class="w-3 h-3" /></a> and that you agree to comply with them.
                </div>
            </x-slot>

            <x-slot name="actions">
                <x-button>
                    {{ __('Submit') }}
                </x-button>
            </x-slot>
        </x-form-section>
    @endif
</div>
