<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Pencatatan Uang App</title>
</head>
<body class="bg-dark text-white d-flex align-items-center justify-content-center min-vh-100">

<div class="container text-center p-4">

    <!-- Logo -->
    <div class="mb-4">
        <img 
            src="https://img.freepik.com/vektor-premium/logo-uang-tanda-logo-dolar_1174662-223.jpg?semt=ais_hybrid&w=740&q=80" 
            alt="App Logo"
            class="rounded-circle border shadow bg-white"
            style="width:150px; height:150px; object-fit:contain;"
        >
    </div>

    <!-- Title -->
    <h1 class="fw-bold mb-2 text-danger">
        Welcome to Pencatatan Uang App
    </h1>

    <!-- Description -->
    <p class="text-light opacity-75 fs-5 mx-auto" style="max-width: 400px;">
        A simple Laravel application to record your income and expenses easily.
    </p>

    <!-- Buttons -->
    @if (Route::has('login'))
        <div class="mt-4 d-flex justify-content-center gap-3">

            @auth
                <a href="{{ url('/dashboard') }}" class="btn btn-danger px-4 fw-semibold shadow">
                    Go to Dashboard
                </a>
            @else
                <a href="{{ route('login') }}" class="btn btn-danger px-4 fw-semibold shadow">
                    Login
                </a>

                @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="btn btn-light px-4 fw-semibold shadow">
                        Register
                    </a>
                @endif
            @endauth

        </div>
    @endif

    <!-- Footer -->
    <footer class="mt-5 text-secondary small">
        Laravel v{{ Illuminate\Foundation\Application::VERSION }} (PHP v{{ PHP_VERSION }})
    </footer>
</div>

</body>
</html>
