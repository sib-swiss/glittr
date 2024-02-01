<div>
    @if ($submitted)
        <div class="text-center my-8 lg:my-12">
            <h1 class="font-bold text-xl lg:text-2xl tracking-tighter text-primary">{{ __('Thank you!') }}</h1>
            <div class="prose prose-lg mx-auto">{{ __('Your submission was successfully sent.')}}</div>
        </div>
    @else
        <x-form-section submit="save">
            <x-slot name="title">
                {{ __('Submit a repository') }}
            </x-slot-name>

            <x-slot name="description">
                <div class="prose prose-sm">
                    <p>
                        First of all, great that you're considering to contribute. Anything that you can contribute is highly appreciated!
                    </p>
                    <p>
                        With this form you can request you can submit a new repository to be added to the collection or updates information for a repository.
                    </p>
                    <p>
                        If you just want to send us a general question or remark, feel free to <a href="mailto:info@glittr.org">contact us by email</a>.
                    </p>
                </div>
            </x-slot>

            <x-slot name="form">
                <div class="col-span-6">
                    <div class="flex items-center">
                        <x-label for="url" value="{{ __('Repository url') }}" /> <span class="text-red-500 ml-1">*</span>
                    </div>
                    <x-input id="url" type="text" class="mt-1 block w-full" placeholder="https://github.com/..." wire:model.live="url" autofocus />
                    <div class="text-sm font-light mt-1">
                        <strong>github.com</strong> or <strong>gitlab.com</strong> repository url
                    </div>
                    <x-input-error for="url" class="mt-1" />
                </div>

                @if ($existingWarning)
                <div class="col-span-6 text-sm border rounded p-4 mt-1 bg-orange-50 border-orange-500 text-orange-500">
                    A repository with this url already exists in the collection.<br />
                    If you want to update information regarding this entry, please leave a comment.
                </div>
                @endif

                <div class="col-span-6">
                    <x-label for="tags" value="{{ __('Proposed topics') }}" />
                    @livewire('tag-select')
                    <x-input-error for="tags" class="mt-1" />
                </div>

                <div class="col-span-6">
                    <div class="text-sm border rounded p-4 mt-1 bg-blue-50 border-blue-500 text-blue-500">
                        {{ __('Your name and email will only be used to keep you informed about these submit process.') }}
                    </div>
                </div>

                <div class="col-span-6 sm:col-span-3">
                    <div class="flex items-center">
                        <x-label for="name" value="{{ __('Name') }}" /> <span class="text-red-500 ml-1">*</span>
                    </div>
                    <x-input id="name" type="text" placeholder="Your name" class="mt-1 block w-full" wire:model="name" />
                    <x-input-error for="name" class="mt-1" />
                </div>
                <div class="col-span-6 sm:col-span-3">
                    <div class="flex items-center">
                        <x-label for="email" value="{{ __('Email') }}" /> <span class="text-red-500 ml-1">*</span>
                    </div>
                    <x-input id="email" type="email" class="mt-1 block w-full" placeholder="Your email" wire:model="email" />
                    <x-input-error for="email" class="mt-1" />
                </div>

                <div class="col-span-6">
                    <x-label for="comment" value="{{ __('Comment') }}" />
                    <x-textarea id="comment" class="mt-1 block w-full" placeholder="Optional comment" wire:model="comment" />
                    <x-input-error for="comment" class="mt-1" />
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
