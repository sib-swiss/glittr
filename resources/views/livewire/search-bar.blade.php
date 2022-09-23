<div x-data @keydown.slash.window.prevent="$refs.input.focus()" class="flex-1">
    <!-- ("/" === e.key || "p" === e.key && e.metaKey || "k" === e.key && e.metaKey || "k" === e.key && e.ctrlKey) -->
    <input class="w-full border-gray-400 rounded-full" type="text" placeholder="{{ __('Global search ("/" to focus)') }}" x-ref="input" wire:model.debounce.500ms="search" />
</div>
