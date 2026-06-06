@php
    $types = [
        'success' => 'success',
        'error'   => 'error',
        'warning' => 'warning',
        'info'    => 'info',
    ];
@endphp
@foreach($types as $session => $type)
    @if(session($session))
        <x-alert :type="$type" :dismissible="true">{{ session($session) }}</x-alert>
    @endif
@endforeach
