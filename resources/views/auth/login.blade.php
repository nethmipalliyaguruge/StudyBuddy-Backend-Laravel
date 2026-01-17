<x-guest-layout>
    <div class="text-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Welcome Back</h1>
        <p class="text-gray-600 mt-1">Sign in to continue to StudyBuddy</p>
    </div>

    @if (session('status'))
        <x-alert type="success" class="mb-4">
            {{ session('status') }}
        </x-alert>
    @endif

    <x-validation-errors class="mb-4" />

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf

        <!-- Email -->
        <div>
            <label for="email" class="form-label">Email Address</label>
            <div class="relative">
                <input type="email"
                       id="email"
                       name="email"
                       value="{{ old('email') }}"
                       required
                       autofocus
                       autocomplete="username"
                       class="input pl-10"
                       placeholder="you@example.com">
                <i class="fas fa-envelope absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
            </div>
        </div>

        <!-- Password -->
        <div>
            <label for="password" class="form-label">Password</label>
            <x-password-input name="password" placeholder="Enter your password" required autocomplete="current-password" />
        </div>

        <!-- Remember Me & Forgot Password -->
        <div class="flex items-center justify-between">
            <label class="flex items-center">
                <input type="checkbox" name="remember" class="rounded border-gray-300 text-primary focus:ring-primary">
                <span class="ml-2 text-sm text-gray-600">Remember me</span>
            </label>

            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="text-sm text-primary hover:underline">
                    Forgot password?
                </a>
            @endif
        </div>

        <!-- Submit -->
        <button type="submit" class="btn-primary w-full btn-lg">
            <i class="fas fa-sign-in-alt mr-2"></i>Sign In
        </button>
    </form>

    <!-- Divider -->
    <div class="relative my-6">
        <div class="absolute inset-0 flex items-center">
            <div class="w-full border-t border-gray-200"></div>
        </div>
        <div class="relative flex justify-center text-sm">
            <span class="px-4 bg-white text-gray-500">Don't have an account?</span>
        </div>
    </div>

    <!-- Register Link -->
    <a href="{{ route('register') }}" class="btn-outline w-full">
        <i class="fas fa-user-plus mr-2"></i>Create Account
    </a>
</x-guest-layout>
