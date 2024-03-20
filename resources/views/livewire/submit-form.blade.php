<div>
    @if ($submitted)
        <div class="my-8 text-center lg:my-12">
            <h1 class="text-xl font-bold tracking-tighter lg:text-2xl text-primary">{{ __('Thank you!') }}</h1>
            <div class="mx-auto prose prose-lg">{{ __('Your submission was successfully sent.')}}</div>
        </div>
    @else
        <x-form-section submit="save">
            <x-slot name="title">
                {{ __('Submit a repository') }}
            </x-slot-name>

            <x-slot name="description">
                <div class="prose-sm prose">
                    <p>
                        First of all, great that you're considering to contribute. Anything that you can contribute is highly appreciated!
                    </p>
                    <p>
                        With this form you can request you can submit a new repository to be added to the collection or updates information for a repository.
                    </p>
                    <p>
                        If you just want to send us a general question or remark, feel free to <a href="mailto:{{ collect(config('glittr.notification_emails'))->first() }}">contact us by email</a>.
                    </p>
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
                    @livewire('tag-select')
                    <x-input-error for="tags" class="mt-1" />
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
