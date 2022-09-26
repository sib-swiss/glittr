<div>
    @if ($submitted)
    @else
        <x-jet-form-section submit="save">
            <x-slot name="title">
                {{ __('Submit a repository') }}
            </x-slot-name>

            <x-slot name="description">
                {{ __('Description') }}
            </x-slot>

            <x-slot name="form">
                <div class="col-span-6">
                    <x-jet-label for="url" value="{{ __('Repository url') }}" />
                    <x-jet-input id="url" type="text" class="mt-1 block w-full" placeholder="" wire:model.defer="url" autofocus />
                    <div class="text-sm font-light mt-1">
                        <strong>https://github.com/... </strong> for example
                    </div>
                    <x-jet-input-error for="url" class="mt-1" />
                </div>

                <div class="col-span-6">
                    <x-jet-label for="tags" value="{{ __('Proposed tags') }}" />
                    @livewire('tag-select')
                    <x-jet-input-error for="tags" class="mt-1" />
                </div>

                <div class="col-span-6">
                    <div class="text-sm border rounded p-4 mt-1 bg-blue-50 text-blue-500">
                        Your name and email will only be used to keep you informed about these submit process.
                    </div>
                </div>

                <div class="col-span-6 sm:col-span-3">
                    <x-jet-label for="name" value="{{ __('Name') }}" />
                    <x-jet-input id="name" type="text" placeholder="Your name" class="mt-1 block w-full" wire:model.defer="name" />
                    <x-jet-input-error for="name" class="mt-1" />
                </div>
                <div class="col-span-6 sm:col-span-3">
                    <x-jet-label for="email" value="{{ __('Email') }}" />
                    <x-jet-input id="email" type="email" class="mt-1 block w-full" placeholder="Your email" wire:model.defer="email" />
                    <x-jet-input-error for="email" class="mt-1" />
                </div>

                <div class="col-span-6">
                    <x-jet-label for="comment" value="{{ __('Comment') }}" />
                    <x-textarea id="comment" class="mt-1 block w-full" placeholder="Optional comment" wire:model.defer="comment" />
                    <x-jet-input-error for="comment" class="mt-1" />
                </div>
            </x-slot>

            <x-slot name="actions">
                <x-jet-button>
                    Submit
                </x-jet-button>
            </x-slot>
        </x-jet-form-section>
    @endif
</div>
