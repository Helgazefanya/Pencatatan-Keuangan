@props(['value'])

<label {{ $attributes->merge(['class' => 'form-label fw-medium small text-dark']) }}>
    {{ $value ?? $slot }}
</label>
