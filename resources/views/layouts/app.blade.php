<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @livewireStyles
</head>

<body class="bg-light">
    <div>
        <livewire:layout.navigation />

        @if (isset($header))
        <header class="bg-white shadow-sm mb-4 border-bottom">
            <div class="container py-4">
                {{ $header }}
            </div>
        </header>
        @endif

        <main class="container py-4">
            @yield('content')
        </main>
    </div>

    @livewireScripts

    <!-- Bootstrap Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- SweetAlert event listeners -->
    <script>
        window.addEventListener('swal:alert', event => {
            const detail = event.detail || {};
            Swal.fire({
                icon: detail.type || 'success',
                title: detail.title || 'Berhasil!',
                text: detail.message || '',
                timer: detail.timer || 2000,
                showConfirmButton: detail.showConfirmButton ?? false,
            });
        });

        if (window.Livewire && typeof Livewire.on === 'function') {
            Livewire.on('swal:alert', detail => {
                detail = detail || {};
                Swal.fire({
                    icon: detail.type || 'success',
                    title: detail.title || 'Berhasil!',
                    text: detail.message || '',
                    timer: detail.timer || 2000,
                    showConfirmButton: detail.showConfirmButton ?? false,
                });
            });
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    @stack('scripts')
</body>
</html>
