<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'StudyBuddy') }}</title>

    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
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
<body class="font-sans antialiased">
    <div class="min-h-screen flex flex-col justify-center items-center bg-gradient-to-br from-primary/5 via-white to-secondary py-12 px-4">
        <!-- Logo -->
        <a href="{{ route('home') }}" class="flex items-center gap-2 mb-8">
            <div class="w-12 h-12 bg-primary rounded-xl flex items-center justify-center shadow-lg">
                <i class="fas fa-book-open text-white text-xl"></i>
            </div>
            <span class="text-2xl font-bold text-gray-900">StudyBuddy</span>
        </a>

        <!-- Card -->
        <div class="w-full max-w-md">
            <div class="card p-8 shadow-xl">
                {{ $slot }}
            </div>
        </div>

        <!-- Back to home -->
        <a href="{{ route('home') }}" class="mt-8 text-sm text-gray-600 hover:text-primary transition-colors">
            <i class="fas fa-arrow-left mr-2"></i>Back to home
        </a>
    </div>

    @livewireScripts
</body>
</html>
