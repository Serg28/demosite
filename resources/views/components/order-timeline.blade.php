@props(['statuses', 'currentPriority' => 0, 'orderStatus' => null])
@php
    $isTerminal = $orderStatus && ! $orderStatus->is_in_timeline;
@endphp
@if($statuses->count() > 0)
    <div class="relative pl-5 py-2 space-y-4">
        <div class="absolute left-[9px] top-3 bottom-3 w-0.5 {{ $isTerminal ? 'bg-gray-200' : 'bg-gray-200' }}"></div>
        @foreach($statuses as $ts)
            @php
                $isDone   = ! $isTerminal && $ts->priority < $currentPriority;
                $isActive = ! $isTerminal && $ts->priority === $currentPriority;
            @endphp
            <div class="flex items-start gap-3 relative">
                <div class="timeline-dot mt-0.5 {{ $isDone ? 'done' : ($isActive ? 'active' : '') }}"></div>
                <p class="text-sm leading-tight {{ ($isDone || $isActive) ? 'text-ink font-semibold' : 'text-ink-muted' }}">
                    {{ $ts->t('title') }}
                </p>
            </div>
        @endforeach

        {{-- Термінальний стан (скасовано / повернення) --}}
        @if($isTerminal)
            <div class="flex items-start gap-3 relative">
                <div class="w-3 h-3 rounded-full mt-0.5 flex-shrink-0 bg-red-100 border-2 border-red-400 flex items-center justify-center">
                </div>
                <p class="text-sm font-semibold text-red-600 leading-tight">
                    {{ $orderStatus->t('title') }}
                </p>
            </div>
        @endif
    </div>
@endif
