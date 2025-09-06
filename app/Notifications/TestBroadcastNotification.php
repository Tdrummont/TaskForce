<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class TestBroadcastNotification extends Notification implements ShouldBroadcast
{
    use Queueable;

    public function via($notifiable): array
    {
        // manda só por broadcast no teste
        return ['broadcast'];
    }

    // payload que chega no front (Echo .notification)
    public function toBroadcast($notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'title' => 'Hello WS',
            'time'  => now()->toIso8601String(),
            'message' => 'Teste de notificação WebSocket funcionando!',
        ]);
    }

    // tipo da notificação (útil pra front diferenciar)
    public function broadcastType(): string
    {
        return 'test.broadcast'; // aparece como notification.type
    }
}
