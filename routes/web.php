<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\TaskAttachmentController;
use App\Http\Controllers\TaskCommentController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\Auth\SocialLoginController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', [TaskController::class, 'dashboard'])->middleware(['auth', 'verified'])->name('dashboard');

// Rota de debug (apenas para desenvolvimento)
Route::get('/debug-broadcasting', function () {
    return view('debug-broadcasting');
})->middleware(['auth'])->name('debug.broadcasting');

// Rotas de notificações (web)
Route::middleware('auth')->group(function () {
    // Rotas web para notificações
    Route::get('/notifications', [App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
    Route::patch('/notifications/{id}/read', [App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');
    Route::delete('/notifications/clear-all', [App\Http\Controllers\NotificationController::class, 'clearAll'])->name('notifications.clearAll');
    
    // Rotas API para notificações
    Route::get('/api/notifications', [App\Http\Controllers\NotificationController::class, 'apiIndex'])->name('api.notifications.index');
    Route::post('/api/notifications/{id}/mark-read', [App\Http\Controllers\NotificationController::class, 'apiMarkAsRead'])->name('api.notifications.markRead');
    Route::post('/api/notifications/mark-all-read', [App\Http\Controllers\NotificationController::class, 'apiMarkAllAsRead'])->name('api.notifications.markAllRead');
    Route::delete('/api/notifications/{id}', [App\Http\Controllers\NotificationController::class, 'apiDelete'])->name('api.notifications.delete');
    Route::delete('/api/notifications/clear-all', [App\Http\Controllers\NotificationController::class, 'apiClearAll'])->name('api.notifications.clearAll');
    Route::get('/api/notifications/unread-count', [App\Http\Controllers\NotificationController::class, 'apiUnreadCount'])->name('api.notifications.unreadCount');
});

// Rotas de autenticação Google
Route::get('/auth/google', [SocialLoginController::class, 'redirectToGoogle'])->name('login.google');
Route::get('/auth/google/callback', [SocialLoginController::class, 'handleGoogleCallback'])->name('google.callback');

// Página de callback do Google (frontend)
Route::get('/auth/google/callback-page', function () {
    return Inertia::render('Auth/GoogleCallback');
})->name('google.callback.page');



Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Rotas de Tarefas
    Route::get('/tasks', [TaskController::class, 'index'])->name('tasks.index');
    Route::get('/tasks/create', [TaskController::class, 'create'])->name('tasks.create');
    Route::post('/tasks', [TaskController::class, 'store'])->name('tasks.store');
    Route::put('/tasks/{task}', [TaskController::class, 'update'])->name('tasks.update');
    Route::patch('/tasks/{task}/status', [TaskController::class, 'updateStatus'])->name('tasks.updateStatus');
    Route::delete('/tasks/{task}', [TaskController::class, 'destroy'])->name('tasks.destroy');
    Route::delete('/tasks', [TaskController::class, 'deleteAll'])->name('tasks.deleteAll');
    Route::post('/tasks/reorder', [TaskController::class, 'reorder'])->name('tasks.reorder');

    // Rotas de Exportação e Backup
    Route::get('/tasks/export/csv', [TaskController::class, 'exportCsv'])->name('tasks.export.csv');
    Route::get('/tasks/backup', [TaskController::class, 'backup'])->name('tasks.backup');
    Route::post('/tasks/restore', [TaskController::class, 'restore'])->name('tasks.restore');

    // Rotas de Anexos
    Route::get('/tasks/{task}/attachments', [TaskAttachmentController::class, 'index'])->name('tasks.attachments.index');
    Route::post('/tasks/{task}/attachments', [TaskAttachmentController::class, 'store'])->name('tasks.attachments.store');
    Route::get('/attachments/{attachment}/download', [TaskAttachmentController::class, 'download'])->name('tasks.attachments.download');
    Route::delete('/attachments/{attachment}', [TaskAttachmentController::class, 'destroy'])->name('tasks.attachments.destroy');

    // Rotas de Comentários
    Route::get('/tasks/{task}/comments', [TaskCommentController::class, 'index'])->name('tasks.comments.index');
    Route::post('/tasks/{task}/comments', [TaskCommentController::class, 'store'])->name('tasks.comments.store');
    Route::put('/comments/{comment}', [TaskCommentController::class, 'update'])->name('tasks.comments.update');
    Route::delete('/comments/{comment}', [TaskCommentController::class, 'destroy'])->name('tasks.comments.destroy');
    Route::patch('/comments/{comment}/pin', [TaskCommentController::class, 'togglePin'])->name('tasks.comments.togglePin');

    // Rotas de Relatórios
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export/pdf', [ReportController::class, 'exportPdf'])->name('reports.export.pdf');
    Route::get('/reports/export/csv', [ReportController::class, 'exportCsv'])->name('reports.export.csv');
});

require __DIR__.'/auth.php';
