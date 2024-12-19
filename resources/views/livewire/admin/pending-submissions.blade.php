<div>
    <h2 class="text-2xl font-bold tracking-tight">{{ __('Pending submissions' )}}</h2>
    @if (count($submissions) > 0)
        <x-table class="my-4">
            <thead>
                <x-table.header>{{ __('URL') }}</x-table.header>
                <x-table.header>{{ __('Proposed tags') }}</x-table.header>
                <x-table.header>{{ __('Submitted by') }}</x-table.header>
                <x-table.header>{{ __('Date') }}</x-table.header>
                <x-table.header></x-table.header>
            </thead>
            <tbody>
                @foreach($submissions as $submission)
                    <x-table.row class="group bg:white hover:bg-gray-50">
                        <x-table.cell class="">
                            <a href="{{ $submission->url }}" class="font-semibold underline" target="_blank">
                                {{ $submission->url }}
                            </a>
                            @if ($submission->repositoryExists())
                                <div class="text-sm font-semibold text-orange-600">
                                    {{ __('This repository already exists in the collection.') }}
                                </div>
                            @endif
                            @if ($submission->comment != '')
                                <div class="text-sm ">
                                    <span class="mt-1 text-xs font-semibold tracking-wide text-gray-500 uppercase">{{ __('Comment: ')}}</span>

                                    {!! nl2br($submission->comment) !!}
                                </div>
                            @endif
                        </x-table.cell>
                        <x-table.cell class="group-hover:bg-gray-50">
                            {{ $submission->tags->pluck('name')->implode(', ') }}
                        </x-table.cell>
                        <x-table.cell class="group-hover:bg-gray-50">
                            {{ $submission->name }}
                            <div class="text-sm text-gray-500">
                                {{ $submission->email }}
                            </div>
                            @if ($submission->apicuron_orcid && $submission->apicuron_submit)
                            <div class="flex flex-col mt-1 text-sm">
                                <div class="text-[#4D194D] font-bold flex items-center space-x-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-5 shrink-0">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm3.857-9.809a.75.75 0 0 0-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 1 0-1.06 1.061l2.5 2.5a.75.75 0 0 0 1.137-.089l4-5.5Z" clip-rule="evenodd" />
                                    </svg>
                                    <span>APICURON</span>
                                </div>
                                <div class="">ORCID id ({{ $submission->apicuron_orcid }})</div>
                            </div>
                            @endif
                        </x-table.cell>
                        <x-table.cell class="group-hover:bg-gray-50">
                            {{ $submission->created_at }}
                            @if ($submission->created_at)
                                <div class="text-sm text-gray-500">
                                    {{ $submission->created_at->diffForHumans() }}
                                </div>
                            @endif
                        </x-table.cell>
                        <x-table.cell class="group-hover:bg-gray-50">
                            <div class="flex items-center space-x-2">
                                <button
                                    class="flex items-center flex-1 p-4 text-sm text-green-600 uppercase bg-green-100 border border-green-600 hover:bg-green-600 hover:text-white"
                                    wire:click="acceptSubmission({{ $submission->id }})"
                                >
                                    <x-heroicon-o-hand-thumb-up class="w-6 h-6 mr-2" />
                                    <span class="font-bold">Accept</span>

                                </button>

                                <button
                                    class="flex items-center flex-1 p-4 text-sm text-red-600 uppercase bg-red-100 border border-red-600 hover:bg-red-600 hover:text-white"
                                    wire:click="declineSubmission({{ $submission->id }})"
                                >
                                    <x-heroicon-o-hand-thumb-down class="w-6 h-6 mr-2" />
                                    <span class="font-bold">Decline</span>

                                </button>
                            </div>
                        </x-table.cell>
                    </x-table.row>
                @endforeach
            </tbody>
        </x-table>
       {{ $submissions->links() }}

       <!-- Add Repository Form Modal -->
        <x-modal wire:model.live="showAccept" persisted="true" id="accept-{{time()}}">
            @if ($acceptingSubmissionId)
                @livewire('admin.repository-form', [null, 'addRepositoryCancel', $acceptingSubmissionId], key("submissionAccept-{$acceptingSubmissionId}"))
            @endif
        </x-modal>
        <!-- Edit Repository Form Modal -->
        <x-modal wire:model.live="showDecline" persisted="true" id="decline-{{time()}}">
            <x-modal.content title="Decline Submission">
                <form wire:submit="decline">
                    <div class="space-y-4">
                        <div>
                            <x-label for="declineComment" value="{{ __('Comment')}}" />
                            <x-textarea wire:model="declineComment" class="w-full" />
                        </div>
                    </div>
                    <x-slot name="footer">
                        <x-secondary-button wire:click.prevent="cancelDecline">{{ __('Cancel') }}</x-seconday-button>
                        <x-button wire:click.prevent="decline">Confirm decline</x-button>
                    </x-slot>
                </form>
            </x-modal.content>
        </x-modal>
    @else
        <div class="flex flex-col items-center p-4 mt-4 space-y-4 text-lg text-green-500 border border-green-500 rounded bg-green-50">
            <x-heroicon-o-hand-thumb-up class="w-8 h-8" />
            <div>{{ __('No pending submissions') }}</div>
        </div>
    @endif
</div>
