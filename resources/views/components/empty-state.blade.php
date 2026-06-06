@props(['emoji' => '📭', 'title', 'description' => null, 'actionUrl' => null, 'actionLabel' => null])
<div class="card p-12 text-center">
    <div class="text-5xl mb-3">{{ $emoji }}</div>
    <p class="font-medium text-ink-muted mb-1">{{ $title }}</p>
    @if($description)
        <p class="text-sm text-ink-muted mb-4">{{ $description }}</p>
    @endif
    @if($actionUrl && $actionLabel)
        <a href="{{ $actionUrl }}" class="btn btn-p btn-sm">{{ $actionLabel }}</a>
    @elseif($slot->isNotEmpty())
        <div class="mt-4">{{ $slot }}</div>
    @endif
</div>
