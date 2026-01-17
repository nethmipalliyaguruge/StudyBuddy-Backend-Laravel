<x-guest-layout>
    <div class="text-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Create Account</h1>
        <p class="text-gray-600 mt-1">Join StudyBuddy and start learning</p>
    </div>

    <x-validation-errors class="mb-4" />

    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf

        <!-- Name -->
        <div>
            <label for="name" class="form-label">Full Name</label>
            <div class="relative">
                <input type="text"
                       id="name"
                       name="name"
                       value="{{ old('name') }}"
                       required
                       autofocus
                       autocomplete="name"
                       class="input pl-10"
                       placeholder="John Doe">
                <i class="fas fa-user absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
            </div>
        </div>

        <!-- Email -->
        <div>
            <label for="email" class="form-label">Email Address</label>
            <div class="relative">
                <input type="email"
                       id="email"
                       name="email"
                       value="{{ old('email') }}"
                       required
                       autocomplete="username"
                       class="input pl-10"
                       placeholder="you@example.com">
                <i class="fas fa-envelope absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
            </div>
        </div>

        <!-- Phone -->
        <div>
            <label for="phone" class="form-label">Phone Number</label>
            <div class="relative">
                <input type="text"
                       id="phone"
                       name="phone"
                       value="{{ old('phone') }}"
                       required
                       autocomplete="tel"
                       class="input pl-10"
                       placeholder="+94 77 123 4567">
                <i class="fas fa-phone absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
            </div>
        </div>

        <!-- Password -->
        <div>
            <label for="password" class="form-label">Password</label>
            <x-password-input name="password" placeholder="Create a strong password" required autocomplete="new-password" />
        </div>

        <!-- Confirm Password -->
        <div>
            <label for="password_confirmation" class="form-label">Confirm Password</label>
            <x-password-input name="password_confirmation" id="password_confirmation" placeholder="Confirm your password" required autocomplete="new-password" />
        </div>

        @if (Laravel\Jetstream\Jetstream::hasTermsAndPrivacyPolicyFeature())
            <div>
                <label class="flex items-start">
                    <input type="checkbox" name="terms" class="rounded border-gray-300 text-primary focus:ring-primary mt-1" required>
                    <span class="ml-2 text-sm text-gray-600">
                        I agree to the
                        <a href="{{ route('terms.show') }}" target="_blank" class="text-primary hover:underline">Terms of Service</a>
                        and
                        <a href="{{ route('policy.show') }}" target="_blank" class="text-primary hover:underline">Privacy Policy</a>
                    </span>
                </label>
            </div>
        @endif

        <!-- Submit -->
        <button type="submit" class="btn-primary w-full btn-lg">
            <i class="fas fa-user-plus mr-2"></i>Create Account
        </button>
    </form>

    <!-- Divider -->
    <div class="relative my-6">
        <div class="absolute inset-0 flex items-center">
            <div class="w-full border-t border-gray-200"></div>
        </div>
        <div class="relative flex justify-center text-sm">
            <span class="px-4 bg-white text-gray-500">Already have an account?</span>
        </div>
    </div>

    <!-- Login Link -->
    <a href="{{ route('login') }}" class="btn-outline w-full">
        <i class="fas fa-sign-in-alt mr-2"></i>Sign In
    </a>
</x-guest-layout>
