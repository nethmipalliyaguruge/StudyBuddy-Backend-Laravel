<footer class="bg-gray-900 text-gray-300 mt-auto">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            <!-- Brand -->
            <div class="col-span-1 md:col-span-1">
                <a href="{{ route('home') }}" class="flex items-center gap-2 mb-4">
                    <div class="w-10 h-10 bg-primary rounded-lg flex items-center justify-center">
                        <i class="fas fa-book-open text-white text-lg"></i>
                    </div>
                    <span class="text-xl font-bold text-white">StudyBuddy</span>
                </a>
                <p class="text-sm text-gray-400">
                    Your trusted platform for sharing and discovering quality study materials.
                </p>
            </div>

            <!-- Quick Links -->
            <div>
                <h3 class="text-white font-semibold mb-4">Quick Links</h3>
                <ul class="space-y-2 text-sm">
                    <li><a href="{{ route('home') }}" class="hover:text-primary transition-colors">Home</a></li>
                    <li><a href="{{ route('materials.index') }}" class="hover:text-primary transition-colors">Study Materials</a></li>
                    @auth
                        <li><a href="{{ route('dashboard') }}" class="hover:text-primary transition-colors">Dashboard</a></li>
                        <li><a href="{{ route('notes.mine') }}" class="hover:text-primary transition-colors">My Notes</a></li>
                    @else
                        <li><a href="{{ route('login') }}" class="hover:text-primary transition-colors">Login</a></li>
                        <li><a href="{{ route('register') }}" class="hover:text-primary transition-colors">Register</a></li>
                    @endauth
                </ul>
            </div>

            <!-- Support -->
            <div>
                <h3 class="text-white font-semibold mb-4">Support</h3>
                <ul class="space-y-2 text-sm">
                    <li><a href="#" class="hover:text-primary transition-colors">Help Center</a></li>
                    <li><a href="#" class="hover:text-primary transition-colors">FAQs</a></li>
                    <li><a href="#" class="hover:text-primary transition-colors">Contact Us</a></li>
                    <li><a href="#" class="hover:text-primary transition-colors">Report an Issue</a></li>
                </ul>
            </div>

            <!-- Legal -->
            <div>
                <h3 class="text-white font-semibold mb-4">Legal</h3>
                <ul class="space-y-2 text-sm">
                    <li><a href="#" class="hover:text-primary transition-colors">Terms of Service</a></li>
                    <li><a href="#" class="hover:text-primary transition-colors">Privacy Policy</a></li>
                    <li><a href="#" class="hover:text-primary transition-colors">Copyright Policy</a></li>
                    <li><a href="#" class="hover:text-primary transition-colors">Refund Policy</a></li>
                </ul>
            </div>
        </div>

        <!-- Bottom Bar -->
        <div class="border-t border-gray-800 mt-8 pt-8 flex flex-col md:flex-row justify-between items-center gap-4">
            <p class="text-sm text-gray-400">
                &copy; {{ date('Y') }} StudyBuddy. All rights reserved.
            </p>
            <div class="flex gap-4">
                <a href="#" class="text-gray-400 hover:text-primary transition-colors">
                    <i class="fab fa-facebook-f"></i>
                </a>
                <a href="#" class="text-gray-400 hover:text-primary transition-colors">
                    <i class="fab fa-twitter"></i>
                </a>
                <a href="#" class="text-gray-400 hover:text-primary transition-colors">
                    <i class="fab fa-instagram"></i>
                </a>
                <a href="#" class="text-gray-400 hover:text-primary transition-colors">
                    <i class="fab fa-linkedin-in"></i>
                </a>
            </div>
        </div>
    </div>
</footer>
