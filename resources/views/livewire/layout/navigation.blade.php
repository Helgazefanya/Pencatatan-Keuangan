<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
    <div class="container">

        <!-- Logo -->
        <a class="navbar-brand fw-bold" href="{{ route('dashboard') }}">
            {{ config('app.name', 'Laravel') }}
        </a>

        <!-- Toggle -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Menu -->
        <div class="collapse navbar-collapse" id="mainNavbar">
            <!-- Left Nav -->
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        Dashboard
                    </x-nav-link>
                </li>

                <li class="nav-item">
                    <x-nav-link :href="route('profile.show')" :active="request()->routeIs('profile.show')">
                        Profile
                    </x-nav-link>
                </li>
            </ul>

            <!-- Right Nav -->
            <ul class="navbar-nav ms-auto">

                @auth
                    <!-- User Dropdown -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle fw-semibold" href="#" role="button"
                           data-bs-toggle="dropdown">
                            {{ Auth::user()->name }}
                        </a>

                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <x-responsive-nav-link :href="route('profile.show')" :active="request()->routeIs('profile.show')">
                                    Profile
                                </x-responsive-nav-link>
                            </li>

                            <li><hr class="dropdown-divider"></li>

                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button class="dropdown-item fw-semibold"
                                            onclick="event.preventDefault(); this.closest('form').submit();">
                                        Logout
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                @endauth

                @guest
                    <li class="nav-item">
                        <a class="btn btn-outline-light btn-sm me-2" href="{{ route('login') }}">Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-danger btn-sm" href="{{ route('register') }}">Register</a>
                    </li>
                @endguest

            </ul>
        </div>
    </div>
</nav>
