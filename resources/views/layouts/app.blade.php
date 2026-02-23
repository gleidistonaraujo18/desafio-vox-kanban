<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name'))</title>

    @vite('resources/css/app.css')

    @stack('styles')
</head>

<body class="bg-light">
    <div id="app">
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm py-3">
            <div class="container-fluid">
                <a class="navbar-brand fw-bold d-flex align-items-center gap-2" href="{{ url('/') }}">
                    <i class="bi bi-kanban-fill"></i>
                    {{ config('app.name', 'Kanban') }}
                </a>

                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse justify-content-end" id="navbarContent">
                    @auth
                        <ul class="navbar-nav align-items-center gap-3">
                            <li class="nav-item text-white fw-semibold">
                                <i class="bi bi-person-circle me-1"></i>
                                {{ auth()->user()->name }}
                            </li>

                            <li class="nav-item">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-light btn-sm px-3">
                                        <i class="bi bi-box-arrow-right me-1"></i>
                                        Logout
                                    </button>
                                </form>
                            </li>
                        </ul>
                    @endauth

                    @guest
                        <ul class="navbar-nav align-items-center gap-2">
                            <li class="nav-item">
                                <a class="btn btn-outline-light btn-sm" href="{{ route('index') }}">
                                    <i class="bi bi-box-arrow-in-right me-1"></i> Entrar
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="btn btn-light btn-sm" href="{{ route('register') }}">
                                    <i class="bi bi-person-plus me-1"></i> Cadastrar
                                </a>
                            </li>
                        </ul>
                    @endguest
                </div>
            </div>
        </nav>

        <main class="container-fluid py-4">
            @yield('content')
        </main>
    </div>
    {{-- Load Vite JS bundle before page scripts so jQuery ($) is available --}}
    <!-- jQuery (global) to ensure $ is defined for inline scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    @vite('resources/js/app.js')

    @stack('scripts')
</body>

</html>
