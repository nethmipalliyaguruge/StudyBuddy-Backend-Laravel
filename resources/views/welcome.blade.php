<x-layouts.landing>
    <x-slot name="title">StudyBuddy - Share and Discover Study Materials</x-slot>

    <!-- Hero Section -->
    <section class="relative overflow-hidden bg-gradient-to-br from-primary/5 via-white to-secondary">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 lg:py-32">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                <!-- Text Content -->
                <div class="space-y-8">
                    <div class="inline-flex items-center gap-2 px-4 py-2 bg-primary/10 rounded-full text-primary text-sm font-medium">
                        <i class="fas fa-graduation-cap"></i>
                        <span>Your Learning Journey Starts Here</span>
                    </div>

                    <h1 class="text-4xl lg:text-5xl xl:text-6xl font-bold text-gray-900 leading-tight">
                        Share Knowledge,
                        <span class="text-primary">Empower Learning</span>
                    </h1>

                    <p class="text-lg text-gray-600 max-w-lg">
                        Connect with fellow students, share your study materials, and discover quality notes
                        from peers across various subjects and levels.
                    </p>

                    <div class="flex flex-col sm:flex-row gap-4">
                        <a href="{{ route('materials.index') }}" class="btn-primary btn-lg">
                            <i class="fas fa-book mr-2"></i>Browse Materials
                        </a>
                        @guest
                            <a href="{{ route('register') }}" class="btn-outline btn-lg">
                                <i class="fas fa-user-plus mr-2"></i>Get Started
                            </a>
                        @else
                            <a href="{{ route('notes.create') }}" class="btn-outline btn-lg">
                                <i class="fas fa-upload mr-2"></i>Upload Notes
                            </a>
                        @endguest
                    </div>

                    <!-- Stats -->
                    <div class="flex gap-8 pt-4">
                        <div>
                            <div class="text-3xl font-bold text-gray-900">500+</div>
                            <div class="text-sm text-gray-500">Study Materials</div>
                        </div>
                        <div>
                            <div class="text-3xl font-bold text-gray-900">1000+</div>
                            <div class="text-sm text-gray-500">Happy Students</div>
                        </div>
                        <div>
                            <div class="text-3xl font-bold text-gray-900">50+</div>
                            <div class="text-sm text-gray-500">Modules</div>
                        </div>
                    </div>
                </div>

                <!-- Illustration -->
                <div class="hidden lg:block relative">
                    <div class="relative w-full h-96">
                        <!-- Decorative Cards -->
                        <div class="absolute top-0 right-0 w-64 h-80 bg-white rounded-2xl shadow-xl p-6 transform rotate-3">
                            <div class="w-full h-32 bg-gradient-to-br from-primary/20 to-primary/10 rounded-lg mb-4"></div>
                            <div class="h-4 bg-gray-200 rounded w-3/4 mb-2"></div>
                            <div class="h-4 bg-gray-100 rounded w-1/2 mb-4"></div>
                            <div class="flex gap-2">
                                <span class="badge-success">PDF</span>
                                <span class="badge-info">15 pages</span>
                            </div>
                        </div>
                        <div class="absolute top-20 left-0 w-56 h-72 bg-white rounded-2xl shadow-lg p-5 transform -rotate-6">
                            <div class="w-full h-28 bg-gradient-to-br from-accent/20 to-accent/10 rounded-lg mb-3"></div>
                            <div class="h-3 bg-gray-200 rounded w-2/3 mb-2"></div>
                            <div class="h-3 bg-gray-100 rounded w-1/2"></div>
                        </div>
                        <div class="absolute bottom-0 right-10 w-48 h-60 bg-primary rounded-2xl shadow-lg p-4 transform rotate-6 text-white">
                            <i class="fas fa-book-reader text-4xl opacity-50 mb-4"></i>
                            <div class="text-xl font-bold">Learn</div>
                            <div class="text-sm opacity-75">Quality materials for success</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Wave Decoration -->
        <div class="absolute bottom-0 left-0 right-0">
            <svg viewBox="0 0 1440 120" class="w-full h-auto fill-current text-white">
                <path d="M0,64L80,69.3C160,75,320,85,480,80C640,75,800,53,960,48C1120,43,1280,53,1360,58.7L1440,64L1440,120L1360,120C1280,120,1120,120,960,120C800,120,640,120,480,120C320,120,160,120,80,120L0,120Z"></path>
            </svg>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl font-bold text-gray-900 mb-4">Why Choose StudyBuddy?</h2>
                <p class="text-gray-600 max-w-2xl mx-auto">
                    We provide a platform that makes sharing and discovering study materials easy, secure, and rewarding.
                </p>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="text-center p-6">
                    <div class="w-16 h-16 bg-primary/10 rounded-2xl flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-upload text-2xl text-primary"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-3">Easy Upload</h3>
                    <p class="text-gray-600">
                        Share your notes in any format. Simply upload, add details, and start helping fellow students.
                    </p>
                </div>

                <!-- Feature 2 -->
                <div class="text-center p-6">
                    <div class="w-16 h-16 bg-primary/10 rounded-2xl flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-shield-alt text-2xl text-primary"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-3">Secure Platform</h3>
                    <p class="text-gray-600">
                        Your materials are protected. Only verified purchases can access the full content.
                    </p>
                </div>

                <!-- Feature 3 -->
                <div class="text-center p-6">
                    <div class="w-16 h-16 bg-primary/10 rounded-2xl flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-coins text-2xl text-primary"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-3">Earn Rewards</h3>
                    <p class="text-gray-600">
                        Get rewarded when students purchase your notes. Turn your hard work into earnings.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works -->
    <section class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl font-bold text-gray-900 mb-4">How It Works</h2>
                <p class="text-gray-600 max-w-2xl mx-auto">
                    Getting started with StudyBuddy is simple. Follow these easy steps.
                </p>
            </div>

            <div class="grid md:grid-cols-4 gap-8">
                <!-- Step 1 -->
                <div class="relative">
                    <div class="text-center">
                        <div class="w-12 h-12 bg-primary text-white rounded-full flex items-center justify-center mx-auto mb-4 text-xl font-bold">
                            1
                        </div>
                        <h3 class="font-semibold text-gray-900 mb-2">Create Account</h3>
                        <p class="text-sm text-gray-600">Sign up for free and join our community</p>
                    </div>
                    <div class="hidden md:block absolute top-6 left-1/2 w-full h-0.5 bg-primary/20"></div>
                </div>

                <!-- Step 2 -->
                <div class="relative">
                    <div class="text-center">
                        <div class="w-12 h-12 bg-primary text-white rounded-full flex items-center justify-center mx-auto mb-4 text-xl font-bold">
                            2
                        </div>
                        <h3 class="font-semibold text-gray-900 mb-2">Browse Materials</h3>
                        <p class="text-sm text-gray-600">Explore notes by school, level, or module</p>
                    </div>
                    <div class="hidden md:block absolute top-6 left-1/2 w-full h-0.5 bg-primary/20"></div>
                </div>

                <!-- Step 3 -->
                <div class="relative">
                    <div class="text-center">
                        <div class="w-12 h-12 bg-primary text-white rounded-full flex items-center justify-center mx-auto mb-4 text-xl font-bold">
                            3
                        </div>
                        <h3 class="font-semibold text-gray-900 mb-2">Purchase or Upload</h3>
                        <p class="text-sm text-gray-600">Buy notes you need or share your own</p>
                    </div>
                    <div class="hidden md:block absolute top-6 left-1/2 w-full h-0.5 bg-primary/20"></div>
                </div>

                <!-- Step 4 -->
                <div class="text-center">
                    <div class="w-12 h-12 bg-primary text-white rounded-full flex items-center justify-center mx-auto mb-4 text-xl font-bold">
                        4
                    </div>
                    <h3 class="font-semibold text-gray-900 mb-2">Learn & Earn</h3>
                    <p class="text-sm text-gray-600">Access materials or earn from sales</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20 bg-primary">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-3xl font-bold text-white mb-4">Ready to Start Learning?</h2>
            <p class="text-primary-foreground/80 mb-8 max-w-2xl mx-auto">
                Join thousands of students who are already sharing and discovering quality study materials on StudyBuddy.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('materials.index') }}" class="btn bg-white text-primary hover:bg-gray-100 btn-lg">
                    <i class="fas fa-search mr-2"></i>Explore Materials
                </a>
                @guest
                    <a href="{{ route('register') }}" class="btn border-2 border-white text-white hover:bg-white/10 btn-lg">
                        <i class="fas fa-user-plus mr-2"></i>Join Now - It's Free
                    </a>
                @endguest
            </div>
        </div>
    </section>
</x-layouts.landing>
