<x-modal.content title="{{ $action }} Repository">
    <form wire:submit.prevent="save">
        <div class="space-y-4">
            <div>
                <x-jet-label for="url" value="{{ __('Repository Url') }}" />
                <div class="flex items-center space-x-2">
                    <x-jet-input id="url" type="text" class="mt-1 block w-full" wire:model.defer="repository.url" />
                    <x-jet-secondary-button type="button" wire:loading.remove="testRemote" wire:click="testRemote">Test</x-jet-secondary-button>
                </div>
                <x-jet-input-error for="repository.url" class="mt-2" />
            </div>
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
                <x-jet-label for="tags" value="{{ __('Tags') }}" />
                @livewire('tag-select', ['values' => $repository['tags']])
                <x-jet-input-error for="repository.tags" class="mt-2 w-full" />
            </div>
            {{--<div class="bg-orange-50 text-orange-500 text-sm p-4 border border-orange-500">
                ! All informations below will be updated with data from the API !
            </div>--}}
        </div>
        <x-slot name="footer">
            @if($cancelEvent != '')
                <x-jet-secondary-button wire:click.prevent="$emit('{{ $cancelEvent }}')">{{ __('Cancel') }}</x-jet-seconday-button>
            @endif
            <x-jet-button wire:click.prevent="save">{{ Str::headline($action) }}</x-jet-button>
        </x-slot>
    </form>
</x-modal.content>
