<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class NotificationController extends Controller
{
    /**
     * Listar notificações do usuário autenticado
     */
    public function apiIndex(Request $request): JsonResponse
    {
        $user = $request->user();
        
        // Buscar notificações do usuário
        $notifications = DB::table('notifications')
            ->where('notifiable_type', User::class)
            ->where('notifiable_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get()
            ->map(function ($notification) {
                $data = json_decode($notification->data, true);
                return [
                    'id' => $notification->id,
                    'title' => $data['title'] ?? 'Notificação',
                    'message' => $data['message'] ?? 'Nova notificação',
                    'type' => $data['type'] ?? 'info',
                    'created_at' => $notification->created_at,
                    'read_at' => $notification->read_at,
                    'data' => $data
                ];
            });

        return response()->json([
            'success' => true,
            'notifications' => $notifications
        ]);
    }

    /**
     * Marcar notificação como lida
     */
    public function apiMarkAsRead(Request $request, $id): JsonResponse
    {
        $user = $request->user();
        
        $updated = DB::table('notifications')
            ->where('id', $id)
            ->where('notifiable_type', User::class)
            ->where('notifiable_id', $user->id)
            ->update(['read_at' => now()]);

        if ($updated) {
            return response()->json([
                'success' => true,
                'message' => 'Notificação marcada como lida'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Notificação não encontrada'
        ], 404);
    }

    /**
     * Marcar todas as notificações como lidas
     */
    public function apiMarkAllAsRead(Request $request): JsonResponse
    {
        $user = $request->user();
        
        DB::table('notifications')
            ->where('notifiable_type', User::class)
            ->where('notifiable_id', $user->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json([
            'success' => true,
            'message' => 'Todas as notificações foram marcadas como lidas'
        ]);
    }

    /**
     * Limpar todas as notificações
     */
    public function apiClearAll(Request $request): JsonResponse
    {
        $user = $request->user();
        
        DB::table('notifications')
            ->where('notifiable_type', User::class)
            ->where('notifiable_id', $user->id)
            ->delete();

        return response()->json([
            'success' => true,
            'message' => 'Todas as notificações foram removidas'
        ]);
    }

    /**
     * Deletar uma notificação específica
     */
    public function apiDelete(Request $request, $id): JsonResponse
    {
        $user = $request->user();
        
        $deleted = DB::table('notifications')
            ->where('id', $id)
            ->where('notifiable_type', User::class)
            ->where('notifiable_id', $user->id)
            ->delete();

        if ($deleted) {
            return response()->json([
                'success' => true,
                'message' => 'Notificação removida'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Notificação não encontrada'
        ], 404);
    }

    /**
     * Obter contagem de notificações não lidas
     */
    public function apiUnreadCount(Request $request): JsonResponse
    {
        $user = $request->user();
        
        $count = DB::table('notifications')
            ->where('notifiable_type', User::class)
            ->where('notifiable_id', $user->id)
            ->whereNull('read_at')
            ->count();

        return response()->json([
            'success' => true,
            'count' => $count
        ]);
    }

    /**
     * Listar notificações do usuário autenticado (rota web)
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        
        // Buscar notificações do usuário
        $notifications = DB::table('notifications')
            ->where('notifiable_type', User::class)
            ->where('notifiable_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get()
            ->map(function ($notification) {
                $data = json_decode($notification->data, true);
                return [
                    'id' => $notification->id,
                    'title' => $data['title'] ?? 'Notificação',
                    'message' => $data['message'] ?? 'Nova notificação',
                    'type' => $data['type'] ?? 'info',
                    'created_at' => $notification->created_at,
                    'read_at' => $notification->read_at,
                    'data' => $data
                ];
            });

        return response()->json([
            'success' => true,
            'notifications' => $notifications
        ]);
    }

    /**
     * Marcar notificação como lida (rota web)
     */
    public function markAsRead(Request $request, $id): JsonResponse
    {
        $user = $request->user();
        
        $updated = DB::table('notifications')
            ->where('id', $id)
            ->where('notifiable_type', User::class)
            ->where('notifiable_id', $user->id)
            ->update(['read_at' => now()]);

        if ($updated) {
            return response()->json([
                'success' => true,
                'message' => 'Notificação marcada como lida'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Notificação não encontrada'
        ], 404);
    }

    /**
     * Limpar todas as notificações (rota web)
     */
    public function clearAll(Request $request): JsonResponse
    {
        $user = $request->user();
        
        DB::table('notifications')
            ->where('notifiable_type', User::class)
            ->where('notifiable_id', $user->id)
            ->delete();

        return response()->json([
            'success' => true,
            'message' => 'Todas as notificações foram removidas'
        ]);
    }
} 