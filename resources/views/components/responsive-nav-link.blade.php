@props(['active'])

@php
$classes = $active
            ? 'dropdown-item active fw-semibold'
            : 'dropdown-item';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
