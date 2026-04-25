<div>
    @isset($jsPath)
        <script>{!! file_get_contents($jsPath) !!}</script>
    @endisset
    @isset($cssPath)
        <style>{!! file_get_contents($cssPath) !!}</style>
    @endisset

    <div
            x-data="LivewireUIModal()"
            x-on:close.stop="setShowPropertyTo(false)"
            x-on:keydown.escape.window="closeModalOnEscape()"
            _x-show="show"
            :style="show ? 'display:block' : 'display:none'"
            class="main-popup-wrap"
            x-bind:class="modalWidth"
    >
        <div class="popup-wrap">
            <div class="popup-close" x-on:click="closeModalOnClickAway()"></div>
            <div
                    x-show="show && showActiveComponent"
                    x-transition.scale
                    {{--x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" --}}
                    x-bind:class="modalWidth"
                    class="popup"
                    id="modal-container"
                    x-trap.noscroll.inert="show && showActiveComponent"
                    aria-modal="true"
            >
                <div class="closer" x-show="show" x-on:click="closeModalOnClickAway()">
                    <img src="/assets/images/closer.svg" alt="{{__('Закрити')}}">
                </div>

                @forelse($components as $id => $component)
                    <div x-show.immediate="activeComponent == '{{ $id }}'" x-ref="{{ $id }}" wire:key="com-lwmodal-{{ $id }}">
                        @livewire($component['name'], $component['arguments'], key($id))
                    </div>
                @empty
                @endforelse
            </div>
        </div>
    </div>

    <span wire:loading class="loadShow">
        <span class="spinners"></span>
    </span>

</div>
