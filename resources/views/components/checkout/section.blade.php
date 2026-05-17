@props(['number', 'title', 'locked' => false])

<div class="card p-6">
    <div class="flex items-center gap-3 mb-5">
        <span class="w-7 h-7 rounded-full flex items-center justify-center text-sm font-bold flex-shrink-0
            {{ $locked ? 'bg-gray-100 text-gray-400' : 'bg-brand text-white' }}">
            {{ $number }}
        </span>
        <h2 class="font-bold text-lg {{ $locked ? 'text-ink-muted' : '' }}">{{ $title }}</h2>
        @if($locked)
            <svg class="ml-auto w-4 h-4 text-gray-300 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
            </svg>
        @endif
    </div>
    {{ $slot }}
</div>
