<div
    x-data="{
        show: false,
        handleScroll() {
            pos = document.documentElement.scrollTop || document.body.scrollTop
            this.show = pos > 200
        }
    }"
    x-transition
    x-show="show"
    x-init="handleScroll()"
    x-cloak
    @scroll.window.throttle.100ms="handleScroll"
    {!! $attributes->merge(['class' => 'fixed z-20 bottom-0 right-0 p-2']) !!}
>
    <x-jet-button class="py-3 px-3" type="button" @click="window.scrollTo({top: 0, behavior: 'smooth'});" title="{{ __('Scroll to top') }}" >
        <x-heroicon-o-chevron-up class="w-5 h-5" />
    </x-jet-button>
</div>
