<?php

namespace App\Services;

use App\Models\User;
use App\Models\Task;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class NotificationService
{
    /**
     * Criar notificação para um usuário
     */
    public static function createNotification(User $user, string $title, string $message, string $type = 'info', array $data = []): void
    {
        try {
            DB::table('notifications')->insert([
                'id' => Str::uuid(),
                'type' => 'App\Notifications\TaskNotification',
                'notifiable_type' => User::class,
                'notifiable_id' => $user->id,
                'data' => json_encode([
                    'title' => $title,
                    'message' => $message,
                    'type' => $type,
                    'data' => $data
                ]),
                'created_at' => now(),
                'updated_at' => now()
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao criar notificação: ' . $e->getMessage());
        }
    }

    /**
     * Notificar quando tarefa é atribuída
     */
    public static function taskAssigned(Task $task, User $assignedTo): void
    {
        $title = 'Tarefa Atribuída';
        $message = "A tarefa '{$task->title}' foi atribuída para você";
        
        self::createNotification($assignedTo, $title, $message, 'success', [
            'task_id' => $task->id,
            'task_title' => $task->title,
            'assigned_by' => Auth::user()->name ?? 'Sistema'
        ]);
    }

    /**
     * Notificar quando tarefa é concluída
     */
    public static function taskCompleted(Task $task, User $user): void
    {
        $title = 'Tarefa Concluída';
        $message = "A tarefa '{$task->title}' foi marcada como concluída";
        
        self::createNotification($user, $title, $message, 'success', [
            'task_id' => $task->id,
            'task_title' => $task->title,
            'completed_by' => Auth::user()->name ?? 'Sistema'
        ]);
    }

    /**
     * Notificar quando status da tarefa muda
     */
    public static function taskStatusChanged(Task $task, User $user, string $oldStatus, string $newStatus): void
    {
        $title = 'Status Alterado';
        $message = "A tarefa '{$task->title}' mudou de '{$oldStatus}' para '{$newStatus}'";
        
        self::createNotification($user, $title, $message, 'info', [
            'task_id' => $task->id,
            'task_title' => $task->title,
            'old_status' => $oldStatus,
            'new_status' => $newStatus
        ]);
    }

    /**
     * Notificar quando prioridade da tarefa muda
     */
    public static function taskPriorityChanged(Task $task, User $user, string $oldPriority, string $newPriority): void
    {
        $title = 'Prioridade Alterada';
        $message = "A prioridade da tarefa '{$task->title}' mudou de '{$oldPriority}' para '{$newPriority}'";
        
        self::createNotification($user, $title, $message, 'warning', [
            'task_id' => $task->id,
            'task_title' => $task->title,
            'old_priority' => $oldPriority,
            'new_priority' => $newPriority
        ]);
    }

    /**
     * Notificar quando tarefa é criada
     */
    public static function taskCreated(Task $task, User $creator): void
    {
        $title = 'Nova Tarefa Criada';
        $message = "A tarefa '{$task->title}' foi criada com sucesso";
        
        self::createNotification($creator, $title, $message, 'success', [
            'task_id' => $task->id,
            'task_title' => $task->title
        ]);
    }

    /**
     * Notificar quando tarefa está vencendo
     */
    public static function taskDueSoon(Task $task, User $user): void
    {
        $title = 'Tarefa Vencendo';
        $message = "A tarefa '{$task->title}' vence em breve";
        
        self::createNotification($user, $title, $message, 'warning', [
            'task_id' => $task->id,
            'task_title' => $task->title,
            'due_date' => $task->due_date
        ]);
    }

    /**
     * Notificar quando tarefa venceu
     */
    public static function taskOverdue(Task $task, User $user): void
    {
        $title = 'Tarefa Vencida';
        $message = "A tarefa '{$task->title}' venceu";
        
        self::createNotification($user, $title, $message, 'error', [
            'task_id' => $task->id,
            'task_title' => $task->title,
            'due_date' => $task->due_date
        ]);
    }

    /**
     * Notificar quando tarefa é delegada
     */
    public static function taskDelegated(Task $task, User $delegatedTo, User $delegatedBy): void
    {
        $title = 'Tarefa Delegada';
        $message = "A tarefa '{$task->title}' foi delegada para você por {$delegatedBy->name}";
        
        self::createNotification($delegatedTo, $title, $message, 'info', [
            'task_id' => $task->id,
            'task_title' => $task->title,
            'delegated_by' => $delegatedBy->name,
            'delegated_by_id' => $delegatedBy->id,
            'type' => 'task_delegated'
        ]);
    }
} 