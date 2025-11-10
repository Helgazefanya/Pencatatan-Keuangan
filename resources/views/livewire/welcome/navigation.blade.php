<nav class="d-flex justify-content-end align-items-center">
    @auth
        <a
            href="{{ url('/dashboard') }}"
            class="btn btn-outline-primary me-2"
        >
            Dashboard
        </a>
    @else
        <a
            href="{{ route('login') }}"
            class="btn btn-outline-secondary me-2"
        >
            Log In
        </a>

        @if (Route::has('register'))
            <a
                href="{{ route('register') }}"
                class="btn btn-primary"
            >
                Register
            </a>
        @endif
    @endauth
</nav>
