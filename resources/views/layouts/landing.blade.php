<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? config('app.name', 'StudyBuddy') }}</title>

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Styles -->
    @livewireStyles
</head>
<body class="font-sans antialiased bg-gray-50">
    <!-- Navigation -->
    <nav x-data="{ mobileOpen: false }" class="bg-white shadow-sm sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <!-- Logo & Main Nav -->
                <div class="flex items-center">
                    <!-- Logo -->
                    <a href="{{ route('home') }}" class="flex items-center gap-2">
                        <div class="w-10 h-10 bg-primary rounded-lg flex items-center justify-center">
                            <i class="fas fa-book-open text-white"></i>
                        </div>
                        <span class="text-xl font-bold text-gray-900 hidden sm:block">StudyBuddy</span>
                    </a>

                    <!-- Desktop Nav Links -->
                    <div class="hidden md:flex items-center ml-10 space-x-8">
                        <a href="{{ route('home') }}"
                           class="{{ request()->routeIs('home') ? 'nav-link-active' : 'nav-link' }}">
                            Home
                        </a>
                        <a href="{{ route('materials.index') }}"
                           class="{{ request()->routeIs('materials.*') ? 'nav-link-active' : 'nav-link' }}">
                            Study Materials
                        </a>
                    </div>
                </div>

                <!-- Right Side -->
                <div class="flex items-center gap-4">
                    @auth
                        <a href="{{ route('dashboard') }}" class="btn-secondary hidden sm:inline-flex">
                            <i class="fas fa-th-large mr-2"></i>Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="nav-link hidden sm:block">Login</a>
                        <a href="{{ route('register') }}" class="btn-primary hidden sm:inline-flex">
                            Get Started
                        </a>
                    @endauth

                    <!-- Mobile Menu Button -->
                    <button @click="mobileOpen = !mobileOpen"
                            class="md:hidden p-2 rounded-lg text-gray-600 hover:bg-gray-100">
                        <i class="fas" :class="mobileOpen ? 'fa-times' : 'fa-bars'"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div x-show="mobileOpen"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-1"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-1"
             class="md:hidden border-t"
             x-cloak>
            <div class="px-4 py-4 space-y-2">
                <a href="{{ route('home') }}"
                   class="block px-4 py-2 rounded-lg {{ request()->routeIs('home') ? 'bg-primary/10 text-primary' : 'text-gray-700 hover:bg-gray-100' }}">
                    <i class="fas fa-home mr-2 w-5"></i>Home
                </a>
                <a href="{{ route('materials.index') }}"
                   class="block px-4 py-2 rounded-lg {{ request()->routeIs('materials.*') ? 'bg-primary/10 text-primary' : 'text-gray-700 hover:bg-gray-100' }}">
                    <i class="fas fa-book mr-2 w-5"></i>Study Materials
                </a>

                <div class="border-t my-2 pt-2">
                    @auth
                        <a href="{{ route('dashboard') }}"
                           class="block px-4 py-2 rounded-lg text-gray-700 hover:bg-gray-100">
                            <i class="fas fa-th-large mr-2 w-5"></i>Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}"
                           class="block px-4 py-2 rounded-lg text-gray-700 hover:bg-gray-100">
                            <i class="fas fa-sign-in-alt mr-2 w-5"></i>Login
                        </a>
                        <a href="{{ route('register') }}"
                           class="block px-4 py-2 rounded-lg bg-primary text-white mt-2">
                            <i class="fas fa-user-plus mr-2 w-5"></i>Get Started
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Flash Messages -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
        @if(session('success'))
            <x-alert type="success">{{ session('success') }}</x-alert>
        @endif

        @if(session('error'))
            <x-alert type="error">{{ session('error') }}</x-alert>
        @endif

        @if ($errors->any())
            <x-alert type="error">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </x-alert>
        @endif
    </div>

    <!-- Main Content -->
    <main class="min-h-screen">
        {{ $slot }}
    </main>

    <!-- Footer -->
    <x-footer />

    @livewireScripts
</body>
</html>
