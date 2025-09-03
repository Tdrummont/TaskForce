<?php

namespace App\Http\Controllers;

use App\Events\TaskAssigned;
use App\Events\TaskDelegated;
use App\Notifications\TaskAssignedNotification;
use App\Notifications\TaskDelegatedNotification;
use App\Notifications\TaskCreatedNotification;
use App\Models\Task;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Carbon\Carbon;

class TaskController extends Controller
{
    public function index($locale, Request $request)
    {
        $query = Task::query()
            ->where('created_by', Auth::id())
            ->with(['assignedTo', 'subtasks', 'attachments', 'comments'])
            ->orderBy('order')
            ->orderBy('created_at', 'desc');

        // Filtros avançados
        if ($request->filled('status')) {
            $query->byStatus($request->status);
        }

        if ($request->filled('priority')) {
            $query->byPriority($request->priority);
        }

        if ($request->filled('category')) {
            $query->byCategory($request->category);
        }

        if ($request->filled('tags')) {
            $tags = explode(',', $request->tags);
            $query->byTags($tags);
        }

        if ($request->filled('assignee')) {
            $query->byAssignee($request->assignee);
        }

        if ($request->filled('date_from') && $request->filled('date_to')) {
            $query->byDateRange($request->date_from, $request->date_to);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereJsonContains('tags', $search);
            });
        }

        // Filtrar por tipo de tarefa
        if ($request->filled('task_type')) {
            if ($request->task_type === 'parent') {
                $query->parentTasks();
            } elseif ($request->task_type === 'subtask') {
                $query->subtasks();
            }
        }

        $tasks = $query->get();

        // Estatísticas
        $stats = [
            'total_tasks' => Task::where('created_by', Auth::id())->count(),
            'pending_tasks' => Task::where('created_by', Auth::id())->byStatus('pending')->count(),
            'in_progress_tasks' => Task::where('created_by', Auth::id())->byStatus('in_progress')->count(),
            'completed_tasks' => Task::where('created_by', Auth::id())->byStatus('completed')->count(),
            'overdue_tasks' => Task::where('created_by', Auth::id())->overdue()->count(),
            'due_today_tasks' => Task::where('created_by', Auth::id())->dueToday()->count(),
            'due_soon_tasks' => Task::where('created_by', Auth::id())->dueSoon()->count(),
        ];

        // Dados para filtros
        $categories = Task::where('created_by', Auth::id())
            ->whereNotNull('category')
            ->distinct()
            ->pluck('category');

        $allTags = Task::where('created_by', Auth::id())
            ->whereNotNull('tags')
            ->get()
            ->pluck('tags')
            ->flatten()
            ->unique()
            ->values();

        $users = User::all();

        return Inertia::render('Tasks/Index', [
            'tasks' => $tasks,
            'stats' => $stats,
            'filters' => [
                'categories' => $categories,
                'tags' => $allTags,
                'users' => $users,
                'current' => $request->only(['status', 'priority', 'category', 'tags', 'assignee', 'date_from', 'date_to', 'search', 'task_type'])
            ]
        ]);
    }

    public function create($locale)
    {
        $users = User::all();
        $parentTasks = Task::where('created_by', Auth::id())
            ->whereNull('parent_task_id')
            ->get();

        // Categorias predefinidas (chaves de tradução)
        $predefinedCategories = [
            'development',
            'design',
            'marketing',
            'sales',
            'support',
            'administrative',
            'financial',
            'human_resources',
            'operations',
            'quality',
            'research',
            'training',
            'maintenance',
            'infrastructure',
            'security'
        ];

        // Categorias existentes no banco
        $existingCategories = Task::where('created_by', Auth::id())
            ->whereNotNull('category')
            ->distinct()
            ->pluck('category')
            ->toArray();

        // Combinar categorias predefinidas com existentes
        $allCategories = array_unique(array_merge($predefinedCategories, $existingCategories));
        sort($allCategories);

        return Inertia::render('Tasks/Create', [
            'users' => $users,
            'parentTasks' => $parentTasks,
            'categories' => $allCategories
        ]);
    }

    public function store($locale, Request $request)
    {
        Log::info('Task creation attempt', [
            'user_id' => Auth::id(),
            'request_data' => $request->all()
        ]);

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255|min:3',
            'description' => 'nullable|string|max:1000',
            'due_date' => 'nullable|date',
            'start_date' => 'nullable|date',
            'status' => ['required', Rule::in(['pending', 'in_progress', 'completed'])],
            'priority' => ['required', Rule::in(['low', 'medium', 'high'])],
            'category' => 'nullable|string|max:100',
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:50',
            'estimated_hours' => 'nullable|integer|min:0',
            'assigned_to' => 'nullable|exists:users,id',
            'parent_task_id' => 'nullable|exists:tasks,id'
        ], [
            'title.required' => 'O título é obrigatório.',
            'title.min' => 'O título deve ter pelo menos 3 caracteres.',
            'status.in' => 'O status deve ser: pendente, em progresso ou concluída.',
            'priority.in' => 'A prioridade deve ser: baixa, média ou alta.'
        ]);

        if ($validator->fails()) {
            Log::error('Validation failed', ['errors' => $validator->errors()]);
            return back()->withErrors($validator)->withInput();
        }

        try {
            $taskData = [
                'title' => $request->title,
                'description' => $request->description,
                'due_date' => $request->due_date,
                'start_date' => $request->start_date,
                'status' => $request->status,
                'priority' => $request->priority,
                'category' => $request->category,
                'tags' => $request->tags,
                'estimated_hours' => $request->estimated_hours,
                'assigned_to' => $request->assigned_to,
                'parent_task_id' => $request->parent_task_id,
                'order' => $this->getNextOrder($request->parent_task_id),
                'created_by' => Auth::id()
            ];

            Log::info('Creating task with data', $taskData);

            $task = Task::create($taskData);

            Log::info('Task created successfully', ['task_id' => $task->id]);

            // Criar notificação para o criador da tarefa
            try {
                // Notificação no banco de dados
                NotificationService::taskCreated($task, Auth::user());
                Log::info('🔔 Notificação de tarefa criada salva no banco', ['task_id' => $task->id]);
                
                // Enviar email de notificação para o criador
                Auth::user()->notify(new TaskCreatedNotification($task, Auth::user()));
                Log::info('📧 Email de notificação de tarefa criada enviado', ['task_id' => $task->id]);
                
                // Adicionar mensagem de sucesso para o snackbar
                session()->flash('email_sent', [
                    'type' => 'success',
                    'title' => 'Tarefa Criada!',
                    'message' => "Tarefa '{$task->title}' criada com sucesso e notificação enviada para seu email"
                ]);
                
            } catch (\Exception $e) {
                Log::error('❌ Erro ao criar notificação de tarefa criada', [
                    'task_id' => $task->id,
                    'error' => $e->getMessage()
                ]);
                
                // Adicionar mensagem de erro para o snackbar
                session()->flash('email_error', [
                    'type' => 'error',
                    'title' => 'Erro na Notificação',
                    'message' => 'Tarefa criada, mas não foi possível enviar a notificação por email'
                ]);
            }

            // Se a tarefa foi atribuída a alguém, disparar evento e enviar email
            if ($request->assigned_to && $request->assigned_to !== Auth::id()) {
                Log::info('🔔 Tarefa atribuída detectada', [
                    'task_id' => $task->id,
                    'assigned_to' => $request->assigned_to,
                    'current_user' => Auth::id()
                ]);
                
                $assignedTo = User::find($request->assigned_to);
                if ($assignedTo) {
                    Log::info('👤 Usuário destinatário encontrado', [
                        'user_id' => $assignedTo->id,
                        'user_email' => $assignedTo->email,
                        'user_name' => $assignedTo->name
                    ]);
                    
                    // Verificar se é uma delegação (usuário diferente do criador)
                    $isDelegation = $request->assigned_to !== $task->created_by;
                    
                    if ($isDelegation) {
                        Log::info('🔄 Delegação de tarefa detectada', [
                            'task_id' => $task->id,
                            'delegated_by' => Auth::id(),
                            'delegated_to' => $assignedTo->id,
                            'original_creator' => $task->created_by
                        ]);
                        
                        // Disparar evento para WebSocket (delegação)
                        try {
                            event(new TaskDelegated($task, Auth::user(), $assignedTo));
                            Log::info('📡 Evento TaskDelegated disparado com sucesso', [
                                'event_class' => TaskDelegated::class,
                                'channel' => 'user.' . $assignedTo->id
                            ]);
                        } catch (\Exception $e) {
                            Log::error('❌ Erro ao disparar evento TaskDelegated', [
                                'task_id' => $task->id,
                                'error' => $e->getMessage(),
                                'trace' => $e->getTraceAsString()
                            ]);
                        }
                        
                        // Enviar notificação por email (delegação)
                        try {
                            $assignedTo->notify(new TaskDelegatedNotification($task, Auth::user(), $assignedTo));
                            Log::info('📧 Notificação de tarefa delegada enviada com sucesso', [
                                'task_id' => $task->id,
                                'assigned_to' => $assignedTo->email,
                                'notification_class' => TaskDelegatedNotification::class
                            ]);
                            
                            // Adicionar mensagem de sucesso para o snackbar
                            session()->flash('email_sent', [
                                'type' => 'success',
                                'title' => 'Tarefa Delegada!',
                                'message' => "Tarefa delegada para {$assignedTo->name} ({$assignedTo->email})"
                            ]);
                            
                        } catch (\Exception $e) {
                            Log::error('❌ Erro ao enviar notificação de tarefa delegada', [
                                'task_id' => $task->id,
                                'error' => $e->getMessage(),
                                'trace' => $e->getTraceAsString()
                            ]);
                            
                            // Adicionar mensagem de erro para o snackbar
                            session()->flash('email_error', [
                                'type' => 'error',
                                'title' => 'Erro na Delegação',
                                'message' => 'Não foi possível enviar a notificação de delegação'
                            ]);
                        }

                                            // Notificação já foi criada via Laravel Notifications
                    Log::info('🔔 Notificação de tarefa delegada criada via Laravel Notifications', [
                        'task_id' => $task->id,
                        'delegated_to' => $assignedTo->id
                    ]);
                    } else {
                        // Tarefa atribuída ao criador (não é delegação)
                        Log::info('ℹ️ Tarefa atribuída ao criador (não é delegação)', [
                            'task_id' => $task->id,
                            'assigned_to' => $assignedTo->id,
                            'created_by' => $task->created_by
                        ]);
                        
                        // Disparar evento para WebSocket (atribuição normal)
                        try {
                            event(new TaskAssigned($task, Auth::user(), $assignedTo));
                            Log::info('📡 Evento TaskAssigned disparado com sucesso', [
                                'event_class' => TaskAssigned::class,
                                'channel' => 'user.' . $assignedTo->id
                            ]);
                        } catch (\Exception $e) {
                            Log::error('❌ Erro ao disparar evento TaskAssigned', [
                                'task_id' => $task->id,
                                'error' => $e->getMessage(),
                                'trace' => $e->getTraceAsString()
                            ]);
                        }
                        
                        // Enviar notificação por email (atribuição normal)
                        try {
                            $assignedTo->notify(new TaskAssignedNotification($task, Auth::user(), $assignedTo));
                            Log::info('📧 Notificação de tarefa atribuída enviada com sucesso', [
                                'task_id' => $task->id,
                                'assigned_to' => $assignedTo->email,
                                'notification_class' => TaskAssignedNotification::class
                            ]);
                            
                            // Adicionar mensagem de sucesso para o snackbar
                            session()->flash('email_sent', [
                                'type' => 'success',
                                'title' => 'Notificação Enviada!',
                                'message' => "Notificação enviada para {$assignedTo->name} ({$assignedTo->email})"
                            ]);
                            
                        } catch (\Exception $e) {
                            Log::error('❌ Erro ao enviar notificação de tarefa atribuída', [
                                'task_id' => $task->id,
                                'error' => $e->getMessage(),
                                'trace' => $e->getTraceAsString()
                            ]);
                            
                            // Adicionar mensagem de erro para o snackbar
                            session()->flash('email_error', [
                                'type' => 'error',
                                'title' => 'Erro na Notificação',
                                'message' => 'Não foi possível enviar a notificação'
                            ]);
                        }

                        // Criar notificação no sistema para o usuário atribuído
                        try {
                            NotificationService::taskAssigned($task, $assignedTo);
                            Log::info('🔔 Notificação de tarefa atribuída criada no sistema', [
                                'task_id' => $task->id,
                                'assigned_to' => $assignedTo->id
                            ]);
                        } catch (\Exception $e) {
                            Log::error('❌ Erro ao criar notificação de tarefa atribuída no sistema', [
                                'task_id' => $task->id,
                                'error' => $e->getMessage()
                            ]);
                        }
                    }
                } else {
                    Log::warning('⚠️ Usuário destinatário não encontrado', [
                        'assigned_to_id' => $request->assigned_to
                    ]);
                }
            } else {
                Log::info('ℹ️ Tarefa não atribuída a outro usuário', [
                    'task_id' => $task->id,
                    'assigned_to' => $request->assigned_to,
                    'current_user' => Auth::id()
                ]);
            }

            return redirect()->route('tasks.index', ['locale' => $locale])->with('success', 'Tarefa criada com sucesso!');
        } catch (\Exception $e) {
            Log::error('Error creating task', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->withErrors(['error' => 'Erro ao criar tarefa: ' . $e->getMessage()])->withInput();
        }
    }

    public function edit($locale, Task $task)
    {
        if (!$task->canEdit(Auth::user())) {
            return back()->with('error', 'Você não tem permissão para editar esta tarefa.');
        }

        $users = User::all();
        $parentTasks = Task::where('created_by', Auth::id())
            ->whereNull('parent_task_id')
            ->where('id', '!=', $task->id)
            ->get();

        // Categorias predefinidas (chaves de tradução)
        $predefinedCategories = [
            'development',
            'design',
            'marketing',
            'sales',
            'support',
            'administrative',
            'financial',
            'human_resources',
            'operations',
            'quality',
            'research',
            'training',
            'maintenance',
            'infrastructure',
            'security'
        ];

        // Categorias existentes no banco
        $existingCategories = Task::where('created_by', Auth::id())
            ->whereNotNull('category')
            ->distinct()
            ->pluck('category')
            ->toArray();

        // Combinar categorias predefinidas com existentes
        $allCategories = array_unique(array_merge($predefinedCategories, $existingCategories));
        sort($allCategories);

        return Inertia::render('Tasks/Edit', [
            'task' => $task,
            'users' => $users,
            'parentTasks' => $parentTasks,
            'categories' => $allCategories
        ]);
    }

    public function update(Request $request, $locale, Task $task)
    {
        if (!$task->canEdit(Auth::user())) {
            return back()->with('error', 'Você não tem permissão para editar esta tarefa.');
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255|min:3',
            'description' => 'nullable|string|max:1000',
            'due_date' => 'nullable|date',
            'start_date' => 'nullable|date|before_or_equal:due_date',
            'status' => ['required', Rule::in(['pending', 'in_progress', 'completed'])],
            'priority' => ['required', Rule::in(['low', 'medium', 'high'])],
            'category' => 'nullable|string|max:100',
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:50',
            'estimated_hours' => 'nullable|integer|min:0',
            'actual_hours' => 'nullable|integer|min:0',
            'assigned_to' => 'nullable|exists:users,id',
            'parent_task_id' => 'nullable|exists:tasks,id'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $oldAssignedTo = $task->assigned_to;
        $oldStatus = $task->status;
        $oldPriority = $task->priority;
        
        $task->update($request->all());
        
        // Notificar mudanças de status
        if ($request->status !== $oldStatus) {
            try {
                $userToNotify = $task->assignedTo ?: Auth::user();
                NotificationService::taskStatusChanged($task, $userToNotify, $oldStatus, $request->status);
                Log::info('🔔 Notificação de mudança de status criada', [
                    'task_id' => $task->id,
                    'old_status' => $oldStatus,
                    'new_status' => $request->status
                ]);
            } catch (\Exception $e) {
                Log::error('❌ Erro ao criar notificação de mudança de status', [
                    'task_id' => $task->id,
                    'error' => $e->getMessage()
                ]);
            }
        }
        
        // Notificar mudanças de prioridade
        if ($request->priority !== $oldPriority) {
            try {
                $userToNotify = $task->assignedTo ?: Auth::user();
                NotificationService::taskPriorityChanged($task, $userToNotify, $oldPriority, $request->priority);
                Log::info('🔔 Notificação de mudança de prioridade criada', [
                    'task_id' => $task->id,
                    'old_priority' => $oldPriority,
                    'new_priority' => $request->priority
                ]);
            } catch (\Exception $e) {
                Log::error('❌ Erro ao criar notificação de mudança de prioridade', [
                    'task_id' => $task->id,
                    'error' => $e->getMessage()
                ]);
            }
        }

        // Se a tarefa foi reatribuída a um usuário diferente, disparar evento e enviar email
        if ($request->assigned_to && 
            $request->assigned_to !== $oldAssignedTo && 
            $request->assigned_to !== Auth::id()) {
            
            $assignedTo = User::find($request->assigned_to);
            if ($assignedTo) {
                // Verificar se é uma delegação (usuário diferente do criador)
                $isDelegation = $request->assigned_to !== $task->created_by;
                
                if ($isDelegation) {
                    Log::info('🔄 Delegação de tarefa detectada na edição', [
                        'task_id' => $task->id,
                        'delegated_by' => Auth::id(),
                        'delegated_to' => $assignedTo->id,
                        'original_creator' => $task->created_by
                    ]);
                    
                    // Disparar evento para WebSocket (delegação)
                    event(new TaskDelegated($task, Auth::user(), $assignedTo));
                    
                    // Enviar notificação por email (delegação)
                    try {
                        $assignedTo->notify(new TaskDelegatedNotification($task, Auth::user(), $assignedTo));
                        Log::info('📧 Notificação de tarefa delegada enviada na edição', [
                            'task_id' => $task->id,
                            'assigned_to' => $assignedTo->email
                        ]);
                        
                        // Adicionar mensagem de sucesso para o snackbar
                        session()->flash('email_sent', [
                            'type' => 'success',
                            'title' => 'Tarefa Delegada!',
                            'message' => "Tarefa delegada para {$assignedTo->name} ({$assignedTo->email})"
                        ]);
                        
                    } catch (\Exception $e) {
                        Log::error('❌ Erro ao enviar notificação de tarefa delegada na edição', [
                            'task_id' => $task->id,
                            'error' => $e->getMessage()
                        ]);
                        
                        // Adicionar mensagem de erro para o snackbar
                        session()->flash('email_error', [
                            'type' => 'error',
                            'title' => 'Erro na Delegação',
                            'message' => 'Não foi possível enviar a notificação de delegação'
                        ]);
                    }

                    // Notificação já foi criada via Laravel Notifications
                    Log::info('🔔 Notificação de tarefa delegada criada via Laravel Notifications (edição)', [
                        'task_id' => $task->id,
                        'delegated_to' => $assignedTo->id
                    ]);
                } else {
                    // Tarefa reatribuída ao criador (não é delegação)
                    Log::info('ℹ️ Tarefa reatribuída ao criador (não é delegação)', [
                        'task_id' => $task->id,
                        'assigned_to' => $assignedTo->id,
                        'created_by' => $task->created_by
                    ]);
                    
                    // Disparar evento para WebSocket (atribuição normal)
                    event(new TaskAssigned($task, Auth::user(), $assignedTo));
                    
                    // Enviar notificação por email (atribuição normal)
                    try {
                        $assignedTo->notify(new TaskAssignedNotification($task, Auth::user(), $assignedTo));
                        Log::info('📧 Notificação de tarefa reatribuída enviada', [
                            'task_id' => $task->id,
                            'assigned_to' => $assignedTo->email
                        ]);
                        
                        // Adicionar mensagem de sucesso para o snackbar
                        session()->flash('email_sent', [
                            'type' => 'success',
                            'title' => 'Notificação Enviada!',
                            'message' => "Notificação enviada para {$assignedTo->name} ({$assignedTo->email})"
                        ]);
                        
                    } catch (\Exception $e) {
                        Log::error('❌ Erro ao enviar notificação de tarefa reatribuída', [
                            'task_id' => $task->id,
                            'error' => $e->getMessage()
                        ]);
                        
                        // Adicionar mensagem de erro para o snackbar
                        session()->flash('email_error', [
                            'type' => 'error',
                            'title' => 'Erro na Notificação',
                            'message' => 'Não foi possível enviar a notificação'
                        ]);
                    }

                    // Criar notificação no sistema para o usuário reatribuído
                    try {
                        NotificationService::taskAssigned($task, $assignedTo);
                        Log::info('🔔 Notificação de tarefa reatribuída criada no sistema', [
                            'task_id' => $task->id,
                            'assigned_to' => $assignedTo->id
                        ]);
                    } catch (\Exception $e) {
                        Log::error('❌ Erro ao criar notificação de tarefa reatribuída no sistema', [
                            'task_id' => $task->id,
                            'error' => $e->getMessage()
                        ]);
                    }
                }
            }
        }

        return redirect()->route('tasks.index', ['locale' => $locale])->with('success', 'Tarefa atualizada com sucesso!');
    }

    public function updateStatus(Request $request, $locale, Task $task)
    {
        try {
            // Log de debug
            Log::info('🔍 updateStatus chamado', [
                'task_id' => $task->id,
                'request_method' => $request->method(),
                'request_headers' => $request->headers->all(),
                'request_content_type' => $request->header('Content-Type'),
                'request_accept' => $request->header('Accept'),
                'expects_json' => $request->expectsJson(),
                'is_ajax' => $request->ajax(),
                'user_id' => Auth::id()
            ]);

            if (!$task->canEdit(Auth::user())) {
                Log::warning('❌ Usuário sem permissão para editar tarefa', [
                    'task_id' => $task->id,
                    'user_id' => Auth::id()
                ]);
                
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Você não tem permissão para alterar o status desta tarefa.'
                    ], 403);
                }
                return back()->with('error', 'Você não tem permissão para alterar o status desta tarefa.');
            }

            $validator = Validator::make($request->all(), [
                'status' => ['required', Rule::in(['pending', 'in_progress', 'completed'])],
            ], [
                'status.in' => 'O status deve ser: pendente, em progresso ou concluída.'
            ]);

            if ($validator->fails()) {
                Log::warning('❌ Validação falhou', [
                    'task_id' => $task->id,
                    'errors' => $validator->errors()->toArray()
                ]);
                
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Dados inválidos',
                        'errors' => $validator->errors()
                    ], 422);
                }
                return back()->withErrors($validator)->withInput();
            }

            $oldStatus = $task->status;
            $task->update(['status' => $request->status]);

            // Log da atividade
            Log::info('✅ Status da tarefa atualizado com sucesso', [
                'task_id' => $task->id,
                'task_title' => $task->title,
                'old_status' => $oldStatus,
                'new_status' => $request->status,
                'user_id' => Auth::id()
            ]);

            // Para requisições AJAX, retornar JSON
            if ($request->expectsJson()) {
                Log::info('📤 Retornando resposta JSON para requisição AJAX');
                return response()->json([
                    'success' => true,
                    'message' => 'Status atualizado com sucesso!',
                    'task' => [
                        'id' => $task->id,
                        'title' => $task->title,
                        'status' => $task->status,
                        'old_status' => $oldStatus
                    ]
                ]);
            }

            // Para requisições normais, redirecionar com mensagem de sucesso
            Log::info('📤 Retornando redirecionamento para requisição normal');
            return redirect()->route('tasks.index', ['locale' => $locale])->with('success', 'Status da tarefa atualizado com sucesso!');

        } catch (\Exception $e) {
            Log::error('❌ Erro ao atualizar status da tarefa', [
                'task_id' => $task->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id()
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro interno ao atualizar status da tarefa.'
                ], 500);
            }

            return back()->with('error', 'Erro interno ao atualizar status da tarefa.');
        }
    }

    public function reorder($locale, Request $request)
    {
        $request->validate([
            'tasks' => 'required|array',
            'tasks.*.id' => 'required|exists:tasks,id',
            'tasks.*.order' => 'required|integer|min:0'
        ]);

        foreach ($request->tasks as $taskData) {
            $task = Task::find($taskData['id']);
            if ($task && $task->canEdit(Auth::user())) {
                $task->update(['order' => $taskData['order']]);
            }
        }

        // Se for uma requisição AJAX, retornar JSON
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Ordem das tarefas atualizada!'
            ]);
        }

        return redirect()->route('tasks.index', ['locale' => $locale])->with('success', 'Ordem das tarefas atualizada!');
    }

    public function destroy($locale, Task $task)
    {
        if (!$task->canDelete(Auth::user())) {
            return back()->with('error', 'Você não tem permissão para excluir esta tarefa.');
        }

        $task->delete();

        return redirect()->route('tasks.index', ['locale' => $locale])->with('success', 'Tarefa excluída com sucesso!');
    }

    public function deleteAll($locale)
    {
        // Verificar se o usuário tem permissão para excluir todas as tarefas
        $user = Auth::user();
        
        // Excluir todas as tarefas do usuário
        $deletedCount = Task::where('created_by', $user->id)->delete();

        return redirect()->route('tasks.index', ['locale' => $locale])->with('success', "Todas as {$deletedCount} tarefas foram excluídas com sucesso!");
    }

    public function exportCsv($locale, Request $request)
    {
        $query = Task::where('created_by', Auth::id());

        // Aplicar filtros se fornecidos
        if ($request->filled('status')) {
            $query->byStatus($request->status);
        }
        if ($request->filled('priority')) {
            $query->byPriority($request->priority);
        }
        if ($request->filled('date_from') && $request->filled('date_to')) {
            $query->byDateRange($request->date_from, $request->date_to);
        }

        $tasks = $query->with(['assignedTo'])->get();

        $filename = 'tasks_' . date('Y-m-d_H-i-s') . '.csv';
        $filepath = storage_path('app/public/exports/' . $filename);

        // Criar diretório se não existir
        if (!file_exists(dirname($filepath))) {
            mkdir(dirname($filepath), 0755, true);
        }

        $file = fopen($filepath, 'w');

        // Cabeçalho
        fputcsv($file, [
            'ID', 'Título', 'Descrição', 'Status', 'Prioridade', 'Data de Vencimento',
            'Data de Início', 'Categoria', 'Tags', 'Tempo Estimado (min)', 'Tempo Real (min)',
            'Responsável', 'Criado em', 'Atualizado em'
        ]);

        // Dados
        foreach ($tasks as $task) {
            fputcsv($file, [
                $task->id,
                $task->title,
                $task->description,
                $task->status,
                $task->priority,
                $task->due_date ? $task->due_date->format('d/m/Y') : '',
                $task->start_date ? $task->start_date->format('d/m/Y') : '',
                $task->category,
                $task->tags ? implode(', ', $task->tags) : '',
                $task->estimated_hours,
                $task->actual_hours,
                $task->assignedTo ? $task->assignedTo->name : '',
                $task->created_at->format('d/m/Y H:i:s'),
                $task->updated_at->format('d/m/Y H:i:s')
            ]);
        }

        fclose($file);

        return response()->download($filepath, $filename, [
            'Content-Type' => 'text/csv',
        ])->deleteFileAfterSend();
    }

    public function backup($locale, Request $request)
    {
        $query = Task::where('created_by', Auth::id());
        
        if ($request->filled('include_subtasks')) {
            $query->with(['subtasks', 'attachments', 'comments']);
        }

        $tasks = $query->get();

        $backupData = [
            'exported_at' => now()->toISOString(),
            'user_id' => Auth::id(),
            'tasks_count' => $tasks->count(),
            'tasks' => $tasks->toArray()
        ];

        $filename = 'tasks_backup_' . date('Y-m-d_H-i-s') . '.json';
        $filepath = storage_path('app/public/backups/' . $filename);

        // Criar diretório se não existir
        if (!file_exists(dirname($filepath))) {
            mkdir(dirname($filepath), 0755, true);
        }

        file_put_contents($filepath, json_encode($backupData, JSON_PRETTY_PRINT));

        return response()->download($filepath, $filename, [
            'Content-Type' => 'application/json',
        ])->deleteFileAfterSend();
    }

    public function restore($locale, Request $request)
    {
        $request->validate([
            'backup_file' => 'required|file|mimes:json|max:10240'
        ]);

        $file = $request->file('backup_file');
        $content = file_get_contents($file->getPathname());
        $backupData = json_decode($content, true);

        if (!$backupData || !isset($backupData['tasks'])) {
            return response()->json([
                'success' => false,
                'message' => 'Arquivo de backup inválido.'
            ], 400);
        }

        // Por segurança, não restaurar automaticamente
        // Apenas retornar informações sobre o backup
        return response()->json([
            'success' => true,
            'message' => 'Backup analisado com sucesso!',
            'backup_info' => [
                'exported_at' => $backupData['exported_at'] ?? 'N/A',
                'tasks_count' => count($backupData['tasks']),
                'user_id' => $backupData['user_id'] ?? 'N/A'
            ]
        ]);
    }

    public function dashboard($locale)
    {
        $userId = Auth::id();
        $now = Carbon::now();
        $startOfWeek = $now->copy()->startOfWeek();
        $endOfWeek = $now->copy()->endOfWeek();
        $startOfMonth = $now->copy()->startOfMonth();
        $endOfMonth = $now->copy()->endOfMonth();

        // Métricas básicas
        $totalTasks = Task::where('created_by', $userId)->count();
        $completedTasks = Task::where('created_by', $userId)->where('status', 'completed')->count();
        $pendingTasks = Task::where('created_by', $userId)->where('status', 'pending')->count();
        $inProgressTasks = Task::where('created_by', $userId)->where('status', 'in_progress')->count();
        $overdueTasks = Task::where('created_by', $userId)->overdue()->count();

        // Produtividade da semana
        $weekTasks = Task::where('created_by', $userId)
            ->whereBetween('created_at', [$startOfWeek, $endOfWeek])
            ->get();
        
        $weekCompletedTasks = $weekTasks->where('status', 'completed')->count();
        $weekProductivity = $weekTasks->count() > 0 ? round(($weekCompletedTasks / $weekTasks->count()) * 100, 1) : 0;

        // Produtividade do mês
        $monthTasks = Task::where('created_by', $userId)
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->get();
        
        $monthCompletedTasks = $monthTasks->where('status', 'completed')->count();
        $monthProductivity = $monthTasks->count() > 0 ? round(($monthCompletedTasks / $monthTasks->count()) * 100, 1) : 0;

        // Tarefas em atraso
        $overdueTasksList = Task::where('created_by', $userId)
            ->overdue()
            ->with('assignedTo')
            ->orderBy('due_date')
            ->get();

        // Tempo médio de conclusão
        $completedTasksWithTime = Task::where('created_by', $userId)
            ->where('status', 'completed')
            ->whereNotNull('created_at')
            ->whereNotNull('updated_at')
            ->get();

        $avgCompletionTime = 0;
        if ($completedTasksWithTime->count() > 0) {
            $totalDays = 0;
            foreach ($completedTasksWithTime as $task) {
                $created = Carbon::parse($task->created_at);
                $completed = Carbon::parse($task->updated_at);
                $totalDays += $created->diffInDays($completed);
            }
            $avgCompletionTime = round($totalDays / $completedTasksWithTime->count(), 1);
        }

        // Streak de produtividade (dias consecutivos atingindo metas)
        $productivityStreak = $this->calculateProductivityStreak($userId);

        // Meta diária vs realizado
        $dailyGoal = 3; // Meta padrão de 3 tarefas por dia
        $todayCompleted = Task::where('created_by', $userId)
            ->where('status', 'completed')
            ->whereDate('updated_at', $now->toDateString())
            ->count();
        
        $dailyProgress = round(($todayCompleted / $dailyGoal) * 100, 1);
        $dailyProgress = min($dailyProgress, 100); // Máximo 100%

        // Meta semanal vs realizado
        $weeklyGoal = 15; // Meta padrão de 15 tarefas por semana
        $weekCompleted = Task::where('created_by', $userId)
            ->where('status', 'completed')
            ->whereBetween('updated_at', [$startOfWeek, $endOfWeek])
            ->count();
        
        $weeklyProgress = round(($weekCompleted / $weeklyGoal) * 100, 1);
        $weeklyProgress = min($weeklyProgress, 100); // Máximo 100%

        // Produtividade dos últimos 7 dias
        $last7Days = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = $now->copy()->subDays($i);
            $dayTasks = Task::where('created_by', $userId)
                ->whereDate('created_at', $date->toDateString())
                ->count();
            $dayCompleted = Task::where('created_by', $userId)
                ->where('status', 'completed')
                ->whereDate('updated_at', $date->toDateString())
                ->count();
            
            $last7Days[] = [
                'date' => $date->format('d/m'),
                'day' => $date->format('D'),
                'total' => $dayTasks,
                'completed' => $dayCompleted,
                'productivity' => $dayTasks > 0 ? round(($dayCompleted / $dayTasks) * 100, 1) : 0
            ];
        }

        // Categorias mais produtivas
        $categoryProductivity = Task::where('created_by', $userId)
            ->whereNotNull('category')
            ->where('status', 'completed')
            ->selectRaw('category, COUNT(*) as completed_count')
            ->groupBy('category')
            ->orderBy('completed_count', 'desc')
            ->limit(5)
            ->get();

        // Prioridades e status
        $priorityStats = Task::where('created_by', $userId)
            ->selectRaw('priority, COUNT(*) as count')
            ->groupBy('priority')
            ->get();

        $statusStats = Task::where('created_by', $userId)
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get();

        // Tarefas recentes
        $recentTasks = Task::where('created_by', $userId)
            ->with('assignedTo')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Próximas tarefas a vencer
        $upcomingTasks = Task::where('created_by', $userId)
            ->where('status', '!=', 'completed')
            ->where('due_date', '>=', $now->toDateString())
            ->where('due_date', '<=', $now->copy()->addDays(7)->toDateString())
            ->with('assignedTo')
            ->orderBy('due_date')
            ->limit(5)
            ->get();

        return Inertia::render('Dashboard', [
            'metrics' => [
                'total_tasks' => $totalTasks,
                'completed_tasks' => $completedTasks,
                'pending_tasks' => $pendingTasks,
                'in_progress_tasks' => $inProgressTasks,
                'overdue_tasks' => $overdueTasks,
                'week_productivity' => $weekProductivity,
                'month_productivity' => $monthProductivity,
                'avg_completion_time' => $avgCompletionTime,
                'productivity_streak' => $productivityStreak,
                'daily_goal' => $dailyGoal,
                'daily_completed' => $todayCompleted,
                'daily_progress' => $dailyProgress,
                'weekly_goal' => $weeklyGoal,
                'weekly_completed' => $weekCompleted,
                'weekly_progress' => $weeklyProgress,
            ],
            'overdue_tasks' => $overdueTasksList,
            'last_7_days' => $last7Days,
            'category_productivity' => $categoryProductivity,
            'priority_stats' => $priorityStats,
            'status_stats' => $statusStats,
            'recent_tasks' => $recentTasks,
            'upcoming_tasks' => $upcomingTasks,
        ]);
    }

    private function calculateProductivityStreak($userId)
    {
        $streak = 0;
        $currentDate = Carbon::now();
        $dailyGoal = 3; // Meta diária de 3 tarefas

        // Verificar os últimos 30 dias para encontrar o streak
        for ($i = 0; $i < 30; $i++) {
            $date = $currentDate->copy()->subDays($i);
            
            $dayCompleted = Task::where('created_by', $userId)
                ->where('status', 'completed')
                ->whereDate('updated_at', $date->toDateString())
                ->count();

            if ($dayCompleted >= $dailyGoal) {
                $streak++;
            } else {
                break; // Streak quebrado
            }
        }

        return $streak;
    }

    private function getNextOrder($parentTaskId = null)
    {
        $maxOrder = Task::where('created_by', Auth::id())
            ->where('parent_task_id', $parentTaskId)
            ->max('order');
        
        return ($maxOrder ?? 0) + 1;
    }

    // ========================================
    // MÉTODOS DA API
    // ========================================

    /**
     * API: Listar tarefas
     */
    public function apiIndex(Request $request)
    {
        $query = Task::query()
            ->where('created_by', Auth::id())
            ->with(['assignedTo', 'subtasks', 'attachments', 'comments'])
            ->orderBy('order')
            ->orderBy('created_at', 'desc');

        // Aplicar filtros
        if ($request->filled('status')) {
            $query->byStatus($request->status);
        }

        if ($request->filled('priority')) {
            $query->byPriority($request->priority);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $tasks = $query->get();

        return response()->json([
            'success' => true,
            'tasks' => $tasks
        ]);
    }

    /**
     * API: Criar tarefa
     */
    public function apiStore(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
            'priority' => 'nullable|in:low,medium,high',
            'status' => 'nullable|in:pending,in_progress,completed',
            'category' => 'nullable|string|max:100',
            'tags' => 'nullable|array',
            'assigned_to' => 'nullable|exists:users,id',
            'parent_task_id' => 'nullable|exists:tasks,id',
        ]);

        $validated['created_by'] = Auth::id();
        $validated['order'] = $this->getNextOrder($validated['parent_task_id'] ?? null);

        $task = Task::create($validated);

        // Notificar usuário designado
        if ($task->assigned_to && $task->assigned_to !== Auth::id()) {
            event(new TaskAssigned($task));
        }

        return response()->json([
            'success' => true,
            'message' => 'Tarefa criada com sucesso!',
            'task' => $task->load(['assignedTo', 'subtasks', 'attachments', 'comments'])
        ], 201);
    }

    /**
     * API: Mostrar tarefa
     */
    public function apiShow(Task $task)
    {
        if ($task->created_by !== Auth::id()) {
            return response()->json(['error' => 'Não autorizado'], 403);
        }

        return response()->json([
            'success' => true,
            'task' => $task->load(['assignedTo', 'subtasks', 'attachments', 'comments'])
        ]);
    }

    /**
     * API: Atualizar tarefa
     */
    public function apiUpdate(Request $request, Task $task)
    {
        if ($task->created_by !== Auth::id()) {
            return response()->json(['error' => 'Não autorizado'], 403);
        }

        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
            'priority' => 'nullable|in:low,medium,high',
            'status' => 'nullable|in:pending,in_progress,completed',
            'category' => 'nullable|string|max:100',
            'tags' => 'nullable|array',
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        $task->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Tarefa atualizada com sucesso!',
            'task' => $task->load(['assignedTo', 'subtasks', 'attachments', 'comments'])
        ]);
    }

    /**
     * API: Atualizar status da tarefa
     */
    public function apiUpdateStatus(Request $request, Task $task)
    {
        if ($task->created_by !== Auth::id()) {
            return response()->json(['error' => 'Não autorizado'], 403);
        }

        $validated = $request->validate([
            'status' => 'required|in:pending,in_progress,completed',
        ]);

        $task->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Status da tarefa atualizado com sucesso!',
            'task' => $task
        ]);
    }

    /**
     * API: Excluir tarefa
     */
    public function apiDestroy(Task $task)
    {
        if ($task->created_by !== Auth::id()) {
            return response()->json(['error' => 'Não autorizado'], 403);
        }

        $task->delete();

        return response()->json([
            'success' => true,
            'message' => 'Tarefa excluída com sucesso!'
        ]);
    }

    /**
     * API: Dashboard
     */
    public function apiDashboard()
    {
        $userId = Auth::id();
        $now = Carbon::now();

        // Estatísticas básicas
        $totalTasks = Task::where('created_by', $userId)->count();
        $completedTasks = Task::where('created_by', $userId)->byStatus('completed')->count();
        $pendingTasks = Task::where('created_by', $userId)->byStatus('pending')->count();
        $inProgressTasks = Task::where('created_by', $userId)->byStatus('in_progress')->count();
        $overdueTasks = Task::where('created_by', $userId)->overdue()->count();

        // Produtividade da semana
        $weekStart = $now->copy()->startOfWeek();
        $weekEnd = $now->copy()->endOfWeek();
        $weekCompleted = Task::where('created_by', $userId)
            ->byStatus('completed')
            ->whereBetween('updated_at', [$weekStart, $weekEnd])
            ->count();

        // Tarefas recentes
        $recentTasks = Task::where('created_by', $userId)
            ->with('assignedTo')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return response()->json([
            'success' => true,
            'metrics' => [
                'total_tasks' => $totalTasks,
                'completed_tasks' => $completedTasks,
                'pending_tasks' => $pendingTasks,
                'in_progress_tasks' => $inProgressTasks,
                'overdue_tasks' => $overdueTasks,
                'week_completed' => $weekCompleted,
            ],
            'recent_tasks' => $recentTasks,
        ]);
    }

    /**
     * Obter categorias disponíveis
     */
    public function getCategories($locale)
    {
        // Categorias predefinidas (chaves de tradução)
        $predefinedCategories = [
            'development',
            'design',
            'marketing',
            'sales',
            'support',
            'administrative',
            'financial',
            'human_resources',
            'operations',
            'quality',
            'research',
            'training',
            'maintenance',
            'infrastructure',
            'security'
        ];

        // Categorias existentes no banco
        $existingCategories = Task::where('created_by', Auth::id())
            ->whereNotNull('category')
            ->distinct()
            ->pluck('category')
            ->toArray();

        // Combinar categorias predefinidas com existentes
        $allCategories = array_unique(array_merge($predefinedCategories, $existingCategories));
        sort($allCategories);

        return response()->json([
            'success' => true,
            'categories' => $allCategories
        ]);
    }
}
