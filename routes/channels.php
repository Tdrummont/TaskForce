<?php

use Illuminate\Support\Facades\Broadcast;

// Registra a rota de autenticação de broadcasting
Broadcast::routes(['middleware' => ['web', 'auth']]);

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can access the channel.
|
*/

// Canal privado para usuários específicos
Broadcast::channel('user.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Canal privado para tarefas específicas
Broadcast::channel('task.{id}', function ($user, $id) {
    // Verificar se o usuário tem acesso à tarefa
    $task = \App\Models\Task::find($id);
    return $task && ($task->user_id === $user->id || $user->hasRole('admin'));
});

// Canal privado para notificações do usuário
Broadcast::channel('notifications.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Canal privado para dashboard do usuário
Broadcast::channel('dashboard.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
}); 