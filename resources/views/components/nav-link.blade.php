@props(['active'])

@php
$classes = $active
            ? 'nav-link active fw-bold'
            : 'nav-link';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
