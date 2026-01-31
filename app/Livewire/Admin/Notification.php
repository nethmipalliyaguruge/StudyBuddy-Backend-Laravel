<?php

namespace App\Livewire\Admin;

use Livewire\Attributes\On;
use Livewire\Component;

class Notification extends Component
{
    public array $notifications = [];

    #[On('notify')]
    public function addNotification(string $type, string $message): void
    {
        $this->notifications[] = [
            'id' => uniqid(),
            'type' => $type,
            'message' => $message,
        ];
    }

    public function dismiss(string $id): void
    {
        $this->notifications = array_values(
            array_filter(
                $this->notifications,
                fn($n) => $n['id'] !== $id
            )
        );
    }

    public function render()
    {
        return view('livewire.admin.notification');
    }
}
