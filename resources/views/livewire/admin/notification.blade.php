<div class="fixed top-4 right-4 z-50 space-y-2">
    @foreach($notifications as $notification)
        <div
            wire:key="notification-{{ $notification['id'] }}"
            x-data="{ show: true }"
            x-show="show"
            x-init="setTimeout(() => { show = false; $wire.dismiss('{{ $notification['id'] }}') }, 5000)"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 transform translate-x-4"
            x-transition:enter-end="opacity-100 transform translate-x-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 transform translate-x-0"
            x-transition:leave-end="opacity-0 transform translate-x-4"
            class="flex items-center gap-3 px-4 py-3 rounded-lg shadow-lg min-w-72 {{ $notification['type'] === 'success' ? 'bg-green-50 border border-green-200' : 'bg-red-50 border border-red-200' }}"
        >
            <div class="flex-shrink-0">
                @if($notification['type'] === 'success')
                    <i class="fas fa-check-circle text-green-500"></i>
                @else
                    <i class="fas fa-exclamation-circle text-red-500"></i>
                @endif
            </div>
            <p class="flex-1 text-sm {{ $notification['type'] === 'success' ? 'text-green-800' : 'text-red-800' }}">
                {{ $notification['message'] }}
            </p>
            <button
                type="button"
                @click="show = false; $wire.dismiss('{{ $notification['id'] }}')"
                class="flex-shrink-0 {{ $notification['type'] === 'success' ? 'text-green-400 hover:text-green-600' : 'text-red-400 hover:text-red-600' }}"
            >
                <i class="fas fa-times"></i>
            </button>
        </div>
    @endforeach
</div>
