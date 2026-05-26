<div>
    <x-header title="Contributors" />
    <x-admin.container>
        <div class="flex gap-4 my-4">
            <x-input placeholder="Search by username, name or ORCID" class="flex-1 p-4" wire:model.live.debounce.500ms="search" />
            <x-select wire:model.live="repositoryFilter" class="flex-1">
                <option value="">All repositories</option>
                @foreach($repositories as $repository)
                    <option value="{{ $repository->id }}">{{ $repository->name }}</option>
                @endforeach
            </x-select>
            <x-select wire:model.live="sortBy">
                <optgroup label="{{ __('Username') }}">
                    <option value="username_asc">{{ __('Username (A → Z)') }}</option>
                    <option value="username_desc">{{ __('Username (Z → A)') }}</option>
                </optgroup>
                <optgroup label="{{ __('Full name') }}">
                    <option value="full_name_asc">{{ __('Full name (A → Z)') }}</option>
                    <option value="full_name_desc">{{ __('Full name (Z → A)') }}</option>
                </optgroup>
                <optgroup label="{{ __('ORCID') }}">
                    <option value="orcid_asc">{{ __('ORCID (A → Z)') }}</option>
                    <option value="orcid_desc">{{ __('ORCID (Z → A)') }}</option>
                </optgroup>
                <optgroup label="{{ __('ORCID retrieved') }}">
                    <option value="orcid_fetched_at_asc">{{ __('ORCID retrieved (oldest first)') }}</option>
                    <option value="orcid_fetched_at_desc">{{ __('ORCID retrieved (newest first)') }}</option>
                </optgroup>
                <optgroup label="{{ __('Repositories') }}">
                    <option value="repositories_desc">{{ __('Repositories (most first)') }}</option>
                    <option value="repositories_asc">{{ __('Repositories (fewest first)') }}</option>
                </optgroup>
                <optgroup label="{{ __('Bot') }}">
                    <option value="bot_desc">{{ __('Bot (flagged first)') }}</option>
                    <option value="bot_asc">{{ __('Bot (unflagged first)') }}</option>
                </optgroup>
            </x-select>
        </div>

        @if(count($selectedIds) > 0)
        <div class="flex items-center gap-3 px-4 py-3 mb-4 bg-blue-50 border border-blue-200 rounded-md">
            <span class="text-sm text-blue-800 font-medium">{{ count($selectedIds) }} selected</span>
            <x-secondary-button wire:click="bulkFlagAsBots" wire:loading.attr="disabled" class="text-xs">
                <x-heroicon-m-no-symbol class="w-4 h-4 mr-1" />
                {{ __('Flag as bot') }}
            </x-secondary-button>
            <x-secondary-button wire:click="bulkUnflagAsBots" wire:loading.attr="disabled" class="text-xs">
                <x-heroicon-m-user class="w-4 h-4 mr-1" />
                {{ __('Unflag as bot') }}
            </x-secondary-button>
            <x-secondary-button wire:click="bulkFetchInfo" wire:loading.attr="disabled" class="text-xs">
                <x-heroicon-m-arrow-down-tray class="w-4 h-4 mr-1" />
                {{ __('Fetch info') }}
            </x-secondary-button>
            <button wire:click="$set('selectAll', false)" class="ml-auto text-sm text-blue-600 underline">{{ __('Clear selection') }}</button>
        </div>
        @endif

        <x-table class="mb-4">
            <thead>
                <x-table.header class="w-8">
                    <input type="checkbox" wire:model.live="selectAll" class="rounded border-gray-300" />
                </x-table.header>
                <x-table.header>{{ __('Username') }}</x-table.header>
                <x-table.header>{{ __('Full name') }}</x-table.header>
                <x-table.header>{{ __('ORCID') }}</x-table.header>
                <x-table.header>{{ __('ORCID retrieved') }}</x-table.header>
                <x-table.header>{{ __('Repositories') }}</x-table.header>
                <x-table.header title="{{ __('GitHub App = detected automatically via URL pattern. Manual = flagged by an admin.') }}">
                    {{ __('Bot') }}
                </x-table.header>
                <x-table.header></x-table.header>
            </thead>
            <tbody>
                @foreach($contributors as $contributor)
                @php
                    $isGithubApp = str_starts_with((string) $contributor->profile_url, 'https://github.com/apps/');
                @endphp
                <x-table.row class="{{ ($contributor->is_bot || $isGithubApp) ? 'bg-orange-50 hover:bg-orange-100' : 'bg-white hover:bg-gray-50' }}">
                    <x-table.cell>
                        <input type="checkbox" wire:model.live="selectedIds" value="{{ $contributor->id }}" class="rounded border-gray-300" />
                    </x-table.cell>
                    <x-table.cell>
                        <a href="{{ $contributor->profile_url }}" target="_blank" class="underline text-sm">{{ $contributor->username }}</a>
                    </x-table.cell>
                    <x-table.cell class="text-sm">{{ $contributor->full_name ?: '-' }}</x-table.cell>
                    <x-table.cell class="text-sm">
                        @if($contributor->orcid)
                            <a href="https://orcid.org/{{ $contributor->orcid }}" target="_blank" class="underline text-green-700">{{ $contributor->orcid }}</a>
                        @else
                            -
                        @endif
                    </x-table.cell>
                    <x-table.cell class="text-xs text-gray-500 whitespace-nowrap">
                        @if($contributor->orcid_fetched_at)
                            {{ $contributor->orcid_fetched_at->format('Y-m-d H:i') }}
                        @else
                            -
                        @endif
                    </x-table.cell>
                    <x-table.cell class="text-sm">{{ $contributor->repositories_count }}</x-table.cell>
                    <x-table.cell>
                        <div class="flex flex-col gap-1">
                            @if($isGithubApp)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800" title="{{ __('Automatically detected: profile URL matches github.com/apps/') }}">GitHub App</span>
                            @endif
                            @if($contributor->is_bot)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-orange-100 text-orange-800" title="{{ __('Manually flagged by an admin') }}">Manual</span>
                            @endif
                            @if(! $isGithubApp && ! $contributor->is_bot)
                                -
                            @endif
                        </div>
                    </x-table.cell>
                    <x-table.cell width="100">
                        <div class="flex items-center gap-1">
                            @if($contributor->is_bot)
                                <x-secondary-button title="{{ __('Unflag as bot') }}" wire:click="toggleBot({{ $contributor->id }})">
                                    <x-heroicon-m-user class="w-4 h-4" />
                                </x-secondary-button>
                            @else
                                <x-secondary-button title="{{ __('Flag as bot') }}" wire:click="toggleBot({{ $contributor->id }})">
                                    <x-heroicon-m-no-symbol class="w-4 h-4" />
                                </x-secondary-button>
                            @endif
                            <x-secondary-button title="{{ __('Fetch info') }}" wire:click="fetchInfo({{ $contributor->id }})" wire:loading.attr="disabled" wire:target="fetchInfo({{ $contributor->id }})">
                                <x-heroicon-m-arrow-down-tray class="w-4 h-4" />
                            </x-secondary-button>
                        </div>
                    </x-table.cell>
                </x-table.row>
                @endforeach
            </tbody>
        </x-table>
        {{ $contributors->links() }}
    </x-admin.container>
</div>
