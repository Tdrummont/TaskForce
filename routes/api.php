<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\TaskAttachmentController;
use App\Http\Controllers\TaskCommentController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Rotas públicas da API
Route::post('/login', [App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'apiLogin']);
Route::post('/register', [App\Http\Controllers\Auth\RegisteredUserController::class, 'apiStore']);

// Rota de teste pública
Route::get('/test', function () {
    return response()->json([
        'message' => 'API funcionando!',
        'timestamp' => now(),
        'status' => 'success'
    ]);
});

// Rotas protegidas da API (requerem JWT)
Route::middleware('auth:api')->group(function () {
    // Usuário autenticado
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    
    // Logout
    Route::post('/logout', [App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'apiDestroy']);
    
    // Refresh token
    Route::post('/refresh', [App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'refresh']);
    
    // Perfil
    Route::get('/profile', [ProfileController::class, 'apiShow']);
    Route::put('/profile', [ProfileController::class, 'apiUpdate']);
    
    // Tarefas
    Route::get('/tasks', [TaskController::class, 'apiIndex']);
    Route::post('/tasks', [TaskController::class, 'apiStore']);
    Route::get('/tasks/{task}', [TaskController::class, 'apiShow']);
    Route::put('/tasks/{task}', [TaskController::class, 'apiUpdate']);
    Route::patch('/tasks/{task}/status', [TaskController::class, 'apiUpdateStatus']);
    Route::delete('/tasks/{task}', [TaskController::class, 'apiDestroy']);
    
    // Dashboard
    Route::get('/dashboard', [TaskController::class, 'apiDashboard']);
    
    // Relatórios
    Route::get('/reports', [ReportController::class, 'apiIndex']);
    
    // Anexos
    Route::get('/tasks/{task}/attachments', [TaskAttachmentController::class, 'apiIndex']);
    Route::post('/tasks/{task}/attachments', [TaskAttachmentController::class, 'apiStore']);
    Route::delete('/attachments/{attachment}', [TaskAttachmentController::class, 'apiDestroy']);
    
    // Comentários
    Route::get('/tasks/{task}/comments', [TaskCommentController::class, 'apiIndex']);
    Route::post('/tasks/{task}/comments', [TaskCommentController::class, 'apiStore']);
    Route::put('/comments/{comment}', [TaskCommentController::class, 'apiUpdate']);
    Route::delete('/comments/{comment}', [TaskCommentController::class, 'apiDestroy']);
    
    // Notificações
    Route::get('/notifications', [App\Http\Controllers\NotificationController::class, 'apiIndex']);
    Route::post('/notifications/{id}/mark-read', [App\Http\Controllers\NotificationController::class, 'apiMarkAsRead']);
    Route::post('/notifications/mark-all-read', [App\Http\Controllers\NotificationController::class, 'apiMarkAllAsRead']);
    Route::delete('/notifications/{id}', [App\Http\Controllers\NotificationController::class, 'apiDelete']);
    Route::delete('/notifications/clear-all', [App\Http\Controllers\NotificationController::class, 'apiClearAll']);
    Route::get('/notifications/unread-count', [App\Http\Controllers\NotificationController::class, 'apiUnreadCount']);
}); 