<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? config('app.name', 'StudyBuddy') }}</title>

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
    <x-banner />

    <div class="min-h-screen flex flex-col">
        <!-- Navigation -->
        <nav x-data="{ mobileOpen: false, profileOpen: false }" class="bg-white shadow-sm sticky top-0 z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <!-- Logo & Main Nav -->
                    <div class="flex items-center">
                        <!-- Logo -->
                        <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
                            <div class="w-10 h-10 bg-primary rounded-lg flex items-center justify-center">
                                <i class="fas fa-book-open text-white"></i>
                            </div>
                            <span class="text-xl font-bold text-gray-900 hidden sm:block">StudyBuddy</span>
                        </a>

                        <!-- Desktop Nav Links -->
                        <div class="hidden md:flex items-center ml-10 space-x-6">
                            <a href="{{ route('dashboard') }}"
                               class="{{ request()->routeIs('dashboard') ? 'nav-link-active' : 'nav-link' }}">
                                <i class="fas fa-th-large mr-1"></i>Dashboard
                            </a>
                            <a href="{{ route('materials.index') }}"
                               class="{{ request()->routeIs('materials.*') ? 'nav-link-active' : 'nav-link' }}">
                                <i class="fas fa-book mr-1"></i>Materials
                            </a>
                            <a href="{{ route('notes.mine') }}"
                               class="{{ request()->routeIs('notes.*') ? 'nav-link-active' : 'nav-link' }}">
                                <i class="fas fa-file-alt mr-1"></i>My Notes
                            </a>
                            <a href="{{ route('purchases.index') }}"
                               class="{{ request()->routeIs('purchases.*') ? 'nav-link-active' : 'nav-link' }}">
                                <i class="fas fa-shopping-bag mr-1"></i>Purchases
                            </a>
                            @if(auth()->user()->role === 'admin')
                                <a href="{{ route('admin.dashboard') }}"
                                   class="{{ request()->routeIs('admin.*') ? 'nav-link-active' : 'nav-link' }}">
                                    <i class="fas fa-cog mr-1"></i>Admin
                                </a>
                            @endif
                        </div>
                    </div>

                    <!-- Right Side -->
                    <div class="flex items-center gap-4">
                        <!-- Cart -->
                        @php
                            $cartCount = session('cart') ? count(session('cart')) : 0;
                        @endphp
                        <a href="{{ route('cart.index') }}" class="relative p-2 text-gray-600 hover:text-primary transition-colors">
                            <i class="fas fa-shopping-cart text-xl"></i>
                            @if($cartCount > 0)
                                <span class="absolute -top-1 -right-1 w-5 h-5 bg-primary text-white text-xs rounded-full flex items-center justify-center">
                                    {{ $cartCount }}
                                </span>
                            @endif
                        </a>

                        <!-- Profile Dropdown (Desktop) -->
                        <div class="hidden md:block relative" @click.away="profileOpen = false">
                            <button @click="profileOpen = !profileOpen"
                                    class="flex items-center gap-2 p-2 rounded-lg hover:bg-gray-100 transition-colors">
                                @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                                    <img class="w-8 h-8 rounded-full object-cover"
                                         src="{{ Auth::user()->profile_photo_url }}"
                                         alt="{{ Auth::user()->name }}">
                                @else
                                    <div class="w-8 h-8 bg-primary/10 rounded-full flex items-center justify-center">
                                        <i class="fas fa-user text-primary text-sm"></i>
                                    </div>
                                @endif
                                <span class="text-sm font-medium text-gray-700">{{ Auth::user()->name }}</span>
                                <i class="fas fa-chevron-down text-xs text-gray-400"></i>
                            </button>

                            <!-- Dropdown Menu -->
                            <div x-show="profileOpen"
                                 x-transition:enter="transition ease-out duration-100"
                                 x-transition:enter-start="transform opacity-0 scale-95"
                                 x-transition:enter-end="transform opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-75"
                                 x-transition:leave-start="transform opacity-100 scale-100"
                                 x-transition:leave-end="transform opacity-0 scale-95"
                                 class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-gray-100 py-2"
                                 x-cloak>
                                <a href="{{ route('profile.show') }}"
                                   class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                    <i class="fas fa-user-cog mr-2 w-4"></i>Profile Settings
                                </a>
                                <div class="border-t border-gray-100 my-1"></div>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit"
                                            class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                        <i class="fas fa-sign-out-alt mr-2 w-4"></i>Logout
                                    </button>
                                </form>
                            </div>
                        </div>

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
                    <!-- User Info -->
                    <div class="flex items-center gap-3 px-4 py-3 bg-gray-50 rounded-lg mb-3">
                        @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                            <img class="w-10 h-10 rounded-full object-cover"
                                 src="{{ Auth::user()->profile_photo_url }}"
                                 alt="{{ Auth::user()->name }}">
                        @else
                            <div class="w-10 h-10 bg-primary/10 rounded-full flex items-center justify-center">
                                <i class="fas fa-user text-primary"></i>
                            </div>
                        @endif
                        <div>
                            <div class="font-medium text-gray-900">{{ Auth::user()->name }}</div>
                            <div class="text-sm text-gray-500">{{ Auth::user()->email }}</div>
                        </div>
                    </div>

                    <!-- Nav Links -->
                    <a href="{{ route('dashboard') }}"
                       class="block px-4 py-2 rounded-lg {{ request()->routeIs('dashboard') ? 'bg-primary/10 text-primary' : 'text-gray-700 hover:bg-gray-100' }}">
                        <i class="fas fa-th-large mr-2 w-5"></i>Dashboard
                    </a>
                    <a href="{{ route('materials.index') }}"
                       class="block px-4 py-2 rounded-lg {{ request()->routeIs('materials.*') ? 'bg-primary/10 text-primary' : 'text-gray-700 hover:bg-gray-100' }}">
                        <i class="fas fa-book mr-2 w-5"></i>Study Materials
                    </a>
                    <a href="{{ route('notes.mine') }}"
                       class="block px-4 py-2 rounded-lg {{ request()->routeIs('notes.*') ? 'bg-primary/10 text-primary' : 'text-gray-700 hover:bg-gray-100' }}">
                        <i class="fas fa-file-alt mr-2 w-5"></i>My Notes
                    </a>
                    <a href="{{ route('purchases.index') }}"
                       class="block px-4 py-2 rounded-lg {{ request()->routeIs('purchases.*') ? 'bg-primary/10 text-primary' : 'text-gray-700 hover:bg-gray-100' }}">
                        <i class="fas fa-shopping-bag mr-2 w-5"></i>My Purchases
                    </a>
                    <a href="{{ route('cart.index') }}"
                       class="block px-4 py-2 rounded-lg {{ request()->routeIs('cart.*') ? 'bg-primary/10 text-primary' : 'text-gray-700 hover:bg-gray-100' }}">
                        <i class="fas fa-shopping-cart mr-2 w-5"></i>Cart
                        @if($cartCount > 0)
                            <span class="ml-2 badge-primary">{{ $cartCount }}</span>
                        @endif
                    </a>

                    @if(auth()->user()->role === 'admin')
                        <a href="{{ route('admin.dashboard') }}"
                           class="block px-4 py-2 rounded-lg {{ request()->routeIs('admin.*') ? 'bg-primary/10 text-primary' : 'text-gray-700 hover:bg-gray-100' }}">
                            <i class="fas fa-cog mr-2 w-5"></i>Admin Dashboard
                        </a>
                    @endif

                    <div class="border-t my-2 pt-2">
                        <a href="{{ route('profile.show') }}"
                           class="block px-4 py-2 rounded-lg text-gray-700 hover:bg-gray-100">
                            <i class="fas fa-user-cog mr-2 w-5"></i>Profile Settings
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                    class="w-full text-left px-4 py-2 rounded-lg text-red-600 hover:bg-red-50">
                                <i class="fas fa-sign-out-alt mr-2 w-5"></i>Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Page Heading -->
        @if (isset($header))
            <header class="bg-white shadow-sm">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endif

        <!-- Flash Messages -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4 space-y-2">
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

        <!-- Page Content -->
        <main class="flex-grow">
            {{ $slot }}
        </main>

        <!-- Footer -->
        <x-footer />
    </div>

    @stack('modals')

    @livewireScripts
</body>
</html>
