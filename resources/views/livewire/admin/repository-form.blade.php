<x-modal.content title="{{ $action }} Repository">
    <form wire:submit="save">
        <div class="space-y-4">
            <div>
                <x-label for="url" value="{{ __('Repository Url') }}" />
                <div class="flex items-center space-x-2">
                    <x-input id="url" type="text" class="mt-1 block w-full" wire:model.live="repository.url" />
                    <x-secondary-button type="button" wire:loading.remove="testRemote" wire:click="testRemote">Test</x-secondary-button>
                </div>
                <x-input-error for="repository.url" class="mt-2" />
            </div>
            @if ($existingWarning)
            <div class="text-sm font-bold p-2 border rounded bg-orange-100 border-orange-500 text-orange-500">
                This repository already exists in the collection.
            </div>
            @endif
            <div wire:loading.block wire:target="testRemote" class="block border text-sm bg-blue-50  border-blue-500 text-blue-500 p-2">
                Testing remote status...
            </div>
            @if ($showTests)
            <div wire:loading.remove="testRemote">
                <div class="text-sm p-2 border rounded {{ $tests['class'] }}">
                    <div class="flex items-center">
                        <div class="flex-1 p-2"><strong>REPO:</strong> {{ $tests['repo'] ? 'ok':'no' }}</div>
                        <div class="flex-1 p-2"><strong>AUTHOR:</strong> {{ $tests['author'] ? 'ok':'no' }}</div>
                    </div>
                    <ul class="list-disc pl-8">
                        @foreach($tests['errors'] as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
            @endif
            <div class="flex flex-wrap items-center space-x-1">
                <x-label for="tags" value="{{ __('Tags') }}" />
                @livewire('tag-select', ['values' => $repository['tags']])
                <x-input-error for="repository.tags" class="mt-2 w-full" />
            </div>
            {{--<div class="bg-orange-50 text-orange-500 text-sm p-4 border border-orange-500">
                ! All informations below will be updated with data from the API !
            </div>--}}

            @if ($submissionId)
                <div>
                    <x-label for="submissionMessage" value="{{ __('Optional comment to save with submission')}}" />
                    <x-textarea class="w-full" id="submissionMessage" wire:model.live="submissionMessage" />
                </div>
            @endif
        </div>
        <x-slot name="footer">
            @if($cancelEvent != '')
                <x-secondary-button wire:click.prevent="$dispatch('{{ $cancelEvent }}')">{{ __('Cancel') }}</x-seconday-button>
            @endif
            @if (!$existingWarning)
                <x-button wire:click.prevent="save">{{ Str::headline($action) }}</x-button>
            @endif
        </x-slot>
    </form>
</x-modal.content>
