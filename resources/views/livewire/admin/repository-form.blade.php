<x-modal.content title="{{ $action }} Repository">
    <form wire:submit="save">
        <div class="space-y-4">
            <div>
                <x-label for="url" value="{{ __('Repository Url') }}" />
                <div class="flex items-center space-x-2">
                    <x-input id="url" type="text" class="block w-full mt-1" wire:model.live="repository.url" />
                    <x-secondary-button type="button" wire:loading.remove="testRemote" wire:click="testRemote">Test</x-secondary-button>
                </div>
                <x-input-error for="repository.url" class="mt-2" />
            </div>
            @if ($existingWarning)
            <div class="p-2 text-sm font-bold text-orange-500 bg-orange-100 border border-orange-500 rounded">
                This repository already exists in the collection.
            </div>
            @endif
            <div wire:loading.block wire:target="testRemote" class="block p-2 text-sm text-blue-500 border border-blue-500 bg-blue-50">
                Testing remote status...
            </div>
            @if ($showTests)
            <div wire:loading.remove="testRemote">
                <div class="text-sm p-2 border rounded {{ $tests['class'] }}">
                    <div class="flex items-center">
                        <div class="flex-1 p-2"><strong>REPO:</strong> {{ $tests['repo'] ? 'ok':'no' }}</div>
                        <div class="flex-1 p-2"><strong>AUTHOR:</strong> {{ $tests['author'] ? 'ok':'no' }}</div>
                    </div>
                    <ul class="pl-8 list-disc">
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
                <x-input-error for="repository.tags" class="w-full mt-2" />
            </div>
            {{--<div class="p-4 text-sm text-orange-500 border border-orange-500 bg-orange-50">
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
                <x-button type="submit" wire:click.prevent="save">{{ Str::headline($action) }}</x-button>
            @endif
        </x-slot>
    </form>
</x-modal.content>
