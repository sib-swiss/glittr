<div>
    @if ($contributors->isEmpty())
        <div class="flex flex-col items-center justify-center py-12 text-center text-gray-400">
            <svg class="w-10 h-10 mb-3 opacity-40" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            <p class="text-sm font-medium">{{ __('No contributors synced yet.') }}</p>
        </div>
    @else
        <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-5 gap-3">
            @foreach ($contributors as $contributor)
                <div wire:key="contributor-{{ $contributor->id }}" class="group flex flex-col items-center text-center p-4 rounded-lg bg-white border border-gray-100 hover:border-gray-300 hover:shadow-sm transition-all duration-150">

                    {{-- Avatar --}}
                    <a href="{{ $contributor->profile_url }}" target="_blank" rel="noopener" class="block mb-2.5 flex-shrink-0">
                        @if ($contributor->avatar_url)
                            <img
                                src="{{ $contributor->avatar_url }}"
                                alt="{{ $contributor->username }}"
                                class="w-20 h-20 rounded-full ring-2 ring-transparent group-hover:ring-glittr-blue transition-all duration-150"
                                loading="lazy"
                                decoding="async"
                            />
                        @else
                            <div class="w-20 h-20 rounded-full bg-gray-200 flex items-center justify-center text-xl font-bold text-gray-500 ring-2 ring-transparent group-hover:ring-glittr-blue transition-all">
                                {{ strtoupper(substr($contributor->username, 0, 1)) }}
                            </div>
                        @endif
                    </a>

                    {{-- Username --}}
                    <a href="{{ $contributor->profile_url }}" target="_blank" rel="noopener"
                       class="text-sm font-semibold text-gray-800 hover:text-glittr-blue transition-colors leading-tight truncate w-full"
                       title="{{ $contributor->username }}">
                        {{ $contributor->username }}
                    </a>

                    {{-- Full name --}}
                    @if ($contributor->full_name && $contributor->full_name !== $contributor->username)
                        <div class="text-xs text-gray-500 leading-tight truncate w-full mt-0.5" title="{{ $contributor->full_name }}">
                            {{ $contributor->full_name }}
                        </div>
                    @endif

                    {{-- Contribution count --}}
                    <div class="mt-2 inline-flex items-center gap-1 text-xs font-medium text-gray-500 bg-gray-50 rounded px-1.5 py-0.5">
                        <svg class="w-3 h-3 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                        </svg>
                        <span>{{ number_format($contributor->pivot->contributions) }} {{ __('contributions') }}</span>
                    </div>

                    {{-- ORCID badge --}}
                    @if ($contributor->orcid)
                        <a href="https://orcid.org/{{ $contributor->orcid }}" target="_blank" rel="noopener"
                           class="mt-1.5 inline-flex items-center gap-1 text-xs text-[#A6CE39] hover:text-[#8aac2a] font-semibold transition-colors"
                           title="ORCID: {{ $contributor->orcid }}">
                            <svg class="w-3.5 h-3.5 flex-shrink-0" viewBox="0 0 32 32" fill="currentColor">
                                <path d="M16 0c-8.839 0-16 7.161-16 16s7.161 16 16 16c8.839 0 16-7.161 16-16s-7.161-16-16-16zM9.823 5.839c0.704 0 1.265 0.573 1.265 1.26 0 0.688-0.561 1.265-1.265 1.265-0.692-0.004-1.26-0.567-1.26-1.265 0-0.697 0.563-1.26 1.26-1.26zM8.864 9.885h1.923v13.391h-1.923zM13.615 9.885h5.197c4.948 0 7.125 3.541 7.125 6.703 0 3.439-2.687 6.699-7.099 6.699h-5.224zM15.536 11.625v9.927h3.063c4.365 0 5.365-3.312 5.365-4.964 0-2.687-1.713-4.963-5.464-4.963z"/>
                            </svg>
                            <span>ORCID</span>
                        </a>
                    @endif
                </div>
            @endforeach
        </div>

        @if ($contributors->hasPages())
            <div class="mt-6">
                {{ $contributors->links(data: ['scrollTo' => '#contributors-section']) }}
            </div>
        @endif
    @endif
</div>
