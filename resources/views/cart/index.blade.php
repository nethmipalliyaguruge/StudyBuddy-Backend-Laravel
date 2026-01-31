<x-app-layout>
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="section-title">Shopping Cart</h1>
            <p class="section-subtitle">Review your items before checkout</p>
        </div>

        @php
            $cart = session('cart', []);
            // Ensure cart is a flat array of integers (defensive against corrupted session data)
            $cart = collect($cart)->flatten()->filter(fn($id) => is_numeric($id))->map(fn($id) => (int) $id)->unique()->values()->toArray();
            $cartNotes = !empty($cart) ? \App\Models\Note::whereIn('id', $cart)->with(['user', 'module'])->get() : collect();
            $total = $cartNotes->sum('price');
        @endphp

        @if($cartNotes->count() > 0)
            <div class="grid lg:grid-cols-3 gap-8">
                <!-- Cart Items -->
                <div class="lg:col-span-2 space-y-4">
                    @foreach($cartNotes as $note)
                        <div class="card p-4">
                            <div class="flex gap-4">
                                <!-- Preview -->
                                <div class="w-20 h-20 flex-shrink-0">
                                    @if($note->getMedia('previews')->first())
                                        <img src="{{ $note->getMedia('previews')->first()->getUrl() }}"
                                             alt="{{ $note->title }}"
                                             class="w-full h-full object-cover rounded-lg">
                                    @else
                                        <div class="w-full h-full bg-gray-100 rounded-lg flex items-center justify-center">
                                            <i class="fas fa-file-alt text-gray-300"></i>
                                        </div>
                                    @endif
                                </div>

                                <!-- Details -->
                                <div class="flex-grow min-w-0">
                                    <h3 class="font-semibold text-gray-900 truncate">{{ $note->title }}</h3>
                                    <p class="text-sm text-gray-500">by {{ $note->user->name ?? 'Unknown' }}</p>
                                    @if($note->module)
                                        <p class="text-xs text-gray-400 mt-1">{{ $note->module->title }}</p>
                                    @endif
                                </div>

                                <!-- Price & Remove -->
                                <div class="flex flex-col items-end justify-between">
                                    <span class="font-bold text-primary">LKR {{ number_format($note->price, 2) }}</span>

                                    <form method="POST" action="{{ route('cart.remove', $note) }}">
                                        @csrf
                                        <button type="submit" class="text-sm text-red-600 hover:text-red-700">
                                            <i class="fas fa-trash mr-1"></i>Remove
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Order Summary -->
                <div class="lg:col-span-1">
                    <div class="card p-6 sticky top-24">
                        <h3 class="font-semibold text-gray-900 mb-4">Order Summary</h3>

                        <div class="space-y-3 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Items ({{ $cartNotes->count() }})</span>
                                <span>LKR {{ number_format($total, 2) }}</span>
                            </div>
                            <div class="border-t pt-3">
                                <div class="flex justify-between font-semibold text-lg">
                                    <span>Total</span>
                                    <span class="text-primary">LKR {{ number_format($total, 2) }}</span>
                                </div>
                            </div>
                        </div>

                        <form method="POST" action="{{ route('stripe.checkout') }}" class="mt-6">
                            @csrf
                            <button type="submit" class="btn-primary w-full btn-lg">
                                <i class="fab fa-stripe mr-2"></i>Pay with Stripe
                            </button>
                        </form>

                        <a href="{{ route('materials.index') }}" class="btn-secondary w-full mt-3">
                            <i class="fas fa-arrow-left mr-2"></i>Continue Shopping
                        </a>

{{--                        <!-- Test Mode Instructions -->--}}
{{--                        @if(str_starts_with(config('services.stripe.key'), 'pk_test'))--}}
{{--                            <div class="mt-4 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">--}}
{{--                                <p class="text-xs font-semibold text-yellow-800 mb-1">--}}
{{--                                    <i class="fas fa-flask mr-1"></i>Test Mode--}}
{{--                                </p>--}}
{{--                                <p class="text-xs text-yellow-700">--}}
{{--                                    Use card: <code class="bg-yellow-100 px-1 rounded">4242 4242 4242 4242</code><br>--}}
{{--                                    Any future expiry, any 3-digit CVC--}}
{{--                                </p>--}}
{{--                            </div>--}}
{{--                        @endif--}}
                    </div>
                </div>
            </div>
        @else
            <!-- Empty Cart -->
            <div class="card p-12 text-center">
                <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-shopping-cart text-3xl text-gray-400"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Your cart is empty</h3>
                <p class="text-gray-600 mb-6">Browse our study materials and add items to your cart!</p>
                <a href="{{ route('materials.index') }}" class="btn-primary">
                    <i class="fas fa-book mr-2"></i>Browse Materials
                </a>
            </div>
        @endif
    </div>
</x-app-layout>
