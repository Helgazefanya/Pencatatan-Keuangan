@props(['active'])

@php
$classes = $active
            ? 'dropdown-item active fw-bold'
            : 'dropdown-item';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
