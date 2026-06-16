@php
    $date = $date ?? null;
    $format = $format ?? 'M d, Y H:i';
@endphp

@if($date)
    {{ $date->format($format) }}
@else
    <span class="text-muted">Not set</span>
@endif