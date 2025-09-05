<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\TaskAttachmentController;
use App\Http\Controllers\TaskCommentController;
use App\Http\Controllers\Auth\SocialLoginController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Middleware\LocaleFromUrl;
use Illuminate\Http\Request;

// Redireciona raiz para /{locale} com base no idioma preferido do navegador
Route::get('/', function (Request $request) {
    // Detecta o idioma do navegador entre pt/en; se não achar, usa 'pt'
    $pref = $request->getPreferredLanguage(['pt', 'en']) ?: 'pt';
    // Se você QUISER forçar sempre PT, troque a linha acima por: $pref = 'pt';
    return redirect("/{$pref}");
});
// Redireciona acessos sem locale para auth no default
Route::redirect('/login', '/pt/login');
Route::redirect('/register', '/pt/register');
Route::redirect('/dashboard', '/pt/dashboard');
Route::redirect('/tasks', '/pt/tasks');
Route::redirect('/reports', '/pt/reports');

// Google Auth (FORA do grupo {locale} para evitar /pt/ no callback)
Route::get('/auth/google/redirect', [SocialLoginController::class, 'redirectToGoogle'])->name('google.redirect');
Route::get('/auth/google', [SocialLoginController::class, 'redirectToGoogle'])->name('login.google');
Route::get('/auth/google/callback', [SocialLoginController::class, 'handleGoogleCallback'])->name('google.callback');
Route::get('/auth/google/callback-page', fn () => Inertia::render('Auth/GoogleCallback'))
    ->name('google.callback.page');

// Agrupa tudo que é web sob /{locale} e garante LocaleFromUrl
Route::group([
    'prefix' => '{locale}',
    'where' => ['locale' => 'pt|en'],
    'middleware' => ['web'], // LocaleFromUrl já roda no stack web
], function () {

    Route::get('/', function () {
        return Inertia::render('Welcome', [
            'canLogin' => Route::has('login'),
            'canRegister' => Route::has('register'),
            'laravelVersion' => Application::VERSION,
            'phpVersion' => PHP_VERSION,
        ]);
    })->name('welcome');

    Route::get('/dashboard', [TaskController::class, 'dashboard'])
        ->middleware(['auth', 'verified'])
        ->name('dashboard');

    // Debug
    Route::get('/debug-broadcasting', fn () => view('debug-broadcasting'))
        ->middleware(['auth'])
        ->name('debug.broadcasting');


    // Área logada
    Route::middleware('auth')->group(function () {
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

        // Tarefas
        Route::get('/tasks', [TaskController::class, 'index'])->name('tasks.index');
        Route::get('/tasks/create', [TaskController::class, 'create'])->name('tasks.create');
        Route::post('/tasks', [TaskController::class, 'store'])->name('tasks.store');
        Route::get('/tasks/{task}/edit', [TaskController::class, 'edit'])->name('tasks.edit');
        Route::put('/tasks/{task}', [TaskController::class, 'update'])->name('tasks.update');
        Route::patch('/tasks/{task}/status', [TaskController::class, 'updateStatus'])->name('tasks.updateStatus');
        Route::delete('/tasks/{task}', [TaskController::class, 'destroy'])->name('tasks.destroy');
        Route::delete('/tasks', [TaskController::class, 'deleteAll'])->name('tasks.deleteAll');
        Route::post('/tasks/reorder', [TaskController::class, 'reorder'])->name('tasks.reorder');

        // Export/Backup
        Route::get('/tasks/export/csv', [TaskController::class, 'exportCsv'])->name('tasks.export.csv');
        Route::get('/tasks/backup', [TaskController::class, 'backup'])->name('tasks.backup');
        Route::post('/tasks/restore', [TaskController::class, 'restore'])->name('tasks.restore');
        
        // Categorias
        Route::get('/tasks/categories', [TaskController::class, 'getCategories'])->name('tasks.categories');

        // Anexos
        Route::get('/tasks/{task}/attachments', [TaskAttachmentController::class, 'index'])->name('tasks.attachments.index');
        Route::post('/tasks/{task}/attachments', [TaskAttachmentController::class, 'store'])->name('tasks.attachments.store');
        Route::get('/attachments/{attachment}/download', [TaskAttachmentController::class, 'download'])->name('tasks.attachments.download');
        Route::delete('/attachments/{attachment}', [TaskAttachmentController::class, 'destroy'])->name('tasks.attachments.destroy');

        // Comentários
        Route::get('/tasks/{task}/comments', [TaskCommentController::class, 'index'])->name('tasks.comments.index');
        Route::post('/tasks/{task}/comments', [TaskCommentController::class, 'store'])->name('tasks.comments.store');
        Route::put('/comments/{comment}', [TaskCommentController::class, 'update'])->name('tasks.comments.update');
        Route::delete('/comments/{comment}', [TaskCommentController::class, 'destroy'])->name('tasks.comments.destroy');
        Route::patch('/comments/{comment}/pin', [TaskCommentController::class, 'togglePin'])->name('tasks.comments.togglePin');

        // Relatórios
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/export/pdf', [ReportController::class, 'exportPdf'])->name('reports.export.pdf');
        Route::get('/reports/export/csv', [ReportController::class, 'exportCsv'])->name('reports.export.csv');

        // Notifications API (dentro do grupo {locale} para manter locale)
        Route::get('/api/notifications', [App\Http\Controllers\NotificationController::class, 'apiIndex'])->name('api.notifications.index');
        Route::get('/api/notifications/unread-count', [App\Http\Controllers\NotificationController::class, 'apiUnreadCount'])->name('api.notifications.unreadCount');
        Route::post('/api/notifications/{id}/mark-read', [App\Http\Controllers\NotificationController::class, 'apiMarkAsRead'])->name('api.notifications.markRead');
        Route::post('/api/notifications/mark-all-read', [App\Http\Controllers\NotificationController::class, 'apiMarkAllAsRead'])->name('api.notifications.markAllRead');
        Route::delete('/api/notifications/{id}', [App\Http\Controllers\NotificationController::class, 'apiDelete'])->name('api.notifications.delete');
        Route::delete('/api/notifications/clear-all', [App\Http\Controllers\NotificationController::class, 'apiClearAll'])->name('api.notifications.clearAll');
    });

    // Rota SPA (fallback) - captura rotas específicas que não existem
    // IMPORTANTE: Deve ser a ÚLTIMA rota para não interferir com as rotas da API
    Route::get('/{any}', function () {
        return Inertia::render('Welcome', [
            'canLogin' => Route::has('login'),
            'canRegister' => Route::has('register'),
            'laravelVersion' => Application::VERSION,
            'phpVersion' => PHP_VERSION,
        ]);
    })->where('any', '^(?!login|register|dashboard|tasks|reports|profile|forgot-password|reset-password|verify-email|confirm-password|debug-broadcasting|api).*$')->name('spa.fallback');

});

// Rotas de teste (antes da rota SPA)
Route::get('/test', function () {
    return 'Test route working';
});

Route::get('/test-callback', function (Request $request) {
    return response('Callback test working - Error: ' . $request->get('error', 'none'));
});

// Rota de teste para Google Auth sem Socialite
Route::get('/test-google', function () {
    return response('Google Auth test working');
});
