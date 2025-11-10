@props(['name','show' => false,'maxWidth' => 'lg'])

<div
    x-data="{ show: @js($show) }"
    x-init="$watch('show', value => {
        if (value) document.body.classList.add('modal-open');
        else document.body.classList.remove('modal-open');
    })"
    x-on:open-modal.window="$event.detail == '{{ $name }}' ? show = true : null"
    x-on:close-modal.window="$event.detail == '{{ $name }}' ? show = false : null"
    x-show="show"
    class="modal fade show d-block"
    style="display: {{ $show ? 'block' : 'none' }};"
>

    <div class="modal-backdrop fade show" x-show="show"></div>

    <div class="modal-dialog modal-{{ $maxWidth }} modal-dialog-centered">
        <div class="modal-content shadow">
            {{ $slot }}
        </div>
    </div>

</div>
