<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    public function index()
    {
        $userId = Auth::user()->id;
        
        // Estatísticas gerais
        $stats = $this->getGeneralStats($userId);
        
        // Dados para gráficos
        $productivityData = $this->getProductivityData($userId);
        $tasksByCategory = $this->getTasksByCategory($userId);
        $completionTimeData = $this->getCompletionTimeData($userId);
        
        // Relatórios semanais/mensais
        $weeklyReport = $this->getWeeklyReport($userId);
        $monthlyReport = $this->getMonthlyReport($userId);
        
        // Histórico de atividades
        $recentActivities = $this->getRecentActivities($userId);

        return Inertia::render('Reports/Index', [
            'stats' => $stats,
            'productivityData' => $productivityData,
            'tasksByCategory' => $tasksByCategory,
            'completionTimeData' => $completionTimeData,
            'weeklyReport' => $weeklyReport,
            'monthlyReport' => $monthlyReport,
            'recentActivities' => $recentActivities
        ]);
    }

    /**
     * Estatísticas gerais
     */
    private function getGeneralStats($userId)
    {
        $tasks = Task::where('created_by', $userId)
            ->orWhere('assigned_to', $userId);

        $totalTasks = $tasks->count();
        $completedTasks = (clone $tasks)->where('status', 'completed')->count();
        $pendingTasks = (clone $tasks)->where('status', 'pending')->count();
        $inProgressTasks = (clone $tasks)->where('status', 'in_progress')->count();

        // Tempo médio para conclusão
        $avgCompletionTime = Task::where('created_by', $userId)
            ->where('status', 'completed')
            ->whereNotNull('created_at')
            ->whereNotNull('updated_at')
            ->get()
            ->avg(function ($task) {
                return $task->created_at->diffInDays($task->updated_at);
            });

        // Taxa de produtividade (tarefas completadas / total)
        $productivityRate = $totalTasks > 0 ? ($completedTasks / $totalTasks) * 100 : 0;

        // Tarefas vencidas
        $overdueTasks = (clone $tasks)->where('due_date', '<', now())
            ->where('status', '!=', 'completed')
            ->count();

        return [
            'total_tasks' => $totalTasks,
            'completed_tasks' => $completedTasks,
            'pending_tasks' => $pendingTasks,
            'in_progress_tasks' => $inProgressTasks,
            'overdue_tasks' => $overdueTasks,
            'avg_completion_time' => round($avgCompletionTime, 1),
            'productivity_rate' => round($productivityRate, 1),
            'completion_percentage' => round($productivityRate, 1)
        ];
    }

    /**
     * Dados de produtividade ao longo do tempo
     */
    private function getProductivityData($userId)
    {
        $days = 30; // Últimos 30 dias
        $data = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            
            $tasksCreated = Task::where('created_by', $userId)
                ->whereDate('created_at', $date)
                ->count();

            $tasksCompleted = Task::where('created_by', $userId)
                ->where('status', 'completed')
                ->whereDate('updated_at', $date)
                ->count();

            $data[] = [
                'date' => $date->format('d/m'),
                'created' => $tasksCreated,
                'completed' => $tasksCompleted,
                'productivity' => $tasksCreated > 0 ? ($tasksCompleted / $tasksCreated) * 100 : 0
            ];
        }

        return $data;
    }

    /**
     * Tarefas por categoria (status e prioridade)
     */
    private function getTasksByCategory($userId)
    {
        $tasks = Task::where('created_by', $userId)
            ->orWhere('assigned_to', $userId);

        // Por status
        $byStatus = (clone $tasks)
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get()
            ->map(function ($item) {
                return [
                    'category' => $this->getStatusLabel($item->status),
                    'count' => $item->count,
                    'color' => $this->getStatusColor($item->status)
                ];
            });

        // Por prioridade
        $byPriority = (clone $tasks)
            ->select('priority', DB::raw('count(*) as count'))
            ->groupBy('priority')
            ->get()
            ->map(function ($item) {
                return [
                    'category' => $this->getPriorityLabel($item->priority),
                    'count' => $item->count,
                    'color' => $this->getPriorityColor($item->priority)
                ];
            });

        return [
            'by_status' => $byStatus,
            'by_priority' => $byPriority
        ];
    }

    /**
     * Dados de tempo de conclusão
     */
    private function getCompletionTimeData($userId)
    {
        $completedTasks = Task::where('created_by', $userId)
            ->where('status', 'completed')
            ->whereNotNull('created_at')
            ->whereNotNull('updated_at')
            ->get();

        $completionTimes = $completedTasks->map(function ($task) {
            return $task->created_at->diffInDays($task->updated_at);
        });

        $ranges = [
            '0-1' => 0,
            '2-3' => 0,
            '4-7' => 0,
            '8-14' => 0,
            '15+' => 0
        ];

        foreach ($completionTimes as $time) {
            if ($time <= 1) $ranges['0-1']++;
            elseif ($time <= 3) $ranges['2-3']++;
            elseif ($time <= 7) $ranges['4-7']++;
            elseif ($time <= 14) $ranges['8-14']++;
            else $ranges['15+']++;
        }

        return [
            'ranges' => $ranges,
            'average' => $completionTimes->avg(),
            'median' => $completionTimes->median(),
            'fastest' => $completionTimes->min(),
            'slowest' => $completionTimes->max()
        ];
    }

    /**
     * Relatório semanal
     */
    private function getWeeklyReport($userId)
    {
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();

        $tasks = Task::where('created_by', $userId)
            ->orWhere('assigned_to', $userId)
            ->whereBetween('created_at', [$startOfWeek, $endOfWeek]);

        $completedTasks = (clone $tasks)->where('status', 'completed')->count();
        $totalTasks = $tasks->count();

        // Atividades por dia da semana
        $dailyActivity = [];
        for ($i = 0; $i < 7; $i++) {
            $date = $startOfWeek->copy()->addDays($i);
            
            $dailyActivity[] = [
                'day' => $date->format('D'),
                'created' => Task::where('created_by', $userId)
                    ->whereDate('created_at', $date)
                    ->count(),
                'completed' => Task::where('created_by', $userId)
                    ->where('status', 'completed')
                    ->whereDate('updated_at', $date)
                    ->count()
            ];
        }

        return [
            'period' => $startOfWeek->format('d/m') . ' - ' . $endOfWeek->format('d/m'),
            'total_tasks' => $totalTasks,
            'completed_tasks' => $completedTasks,
            'completion_rate' => $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100, 1) : 0,
            'daily_activity' => $dailyActivity
        ];
    }

    /**
     * Relatório mensal
     */
    private function getMonthlyReport($userId)
    {
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        $tasks = Task::where('created_by', $userId)
            ->orWhere('assigned_to', $userId)
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth]);

        $completedTasks = (clone $tasks)->where('status', 'completed')->count();
        $totalTasks = $tasks->count();

        // Comparação com mês anterior
        $lastMonthStart = Carbon::now()->subMonth()->startOfMonth();
        $lastMonthEnd = Carbon::now()->subMonth()->endOfMonth();

        $lastMonthTasks = Task::where('created_by', $userId)
            ->orWhere('assigned_to', $userId)
            ->whereBetween('created_at', [$lastMonthStart, $lastMonthEnd])
            ->count();

        $growth = $lastMonthTasks > 0 ? (($totalTasks - $lastMonthTasks) / $lastMonthTasks) * 100 : 0;

        return [
            'period' => $startOfMonth->format('M/Y'),
            'total_tasks' => $totalTasks,
            'completed_tasks' => $completedTasks,
            'completion_rate' => $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100, 1) : 0,
            'growth_from_last_month' => round($growth, 1),
            'avg_tasks_per_day' => round($totalTasks / Carbon::now()->daysInMonth, 1)
        ];
    }

    /**
     * Histórico de atividades recentes
     */
    private function getRecentActivities($userId)
    {
        return ActivityLog::where('user_id', $userId)
            ->with(['task', 'user'])
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get()
            ->map(function ($activity) {
                return [
                    'id' => $activity->id,
                    'action' => $activity->action,
                    'action_label' => $activity->getActionLabel(),
                    'action_icon' => $activity->getActionIcon(),
                    'action_color' => $activity->getActionColor(),
                    'description' => $activity->getFormattedDescription(),
                    'task_title' => $activity->task ? $activity->task->title : null,
                    'created_at' => $activity->created_at->format('d/m/Y H:i'),
                    'time_ago' => $activity->created_at->diffForHumans()
                ];
            });
    }

    /**
     * Exportar relatório em PDF
     */
    public function exportPdf(Request $request)
    {
        $userId = Auth::user()->id;
        $period = $request->get('period', 'month'); // week, month, year
        
        $data = [
            'user' => Auth::user(),
            'period' => $period,
            'stats' => $this->getGeneralStats($userId),
            'productivity_data' => $this->getProductivityData($userId),
            'tasks_by_category' => $this->getTasksByCategory($userId),
            'completion_time_data' => $this->getCompletionTimeData($userId)
        ];

        // Aqui você implementaria a geração do PDF
        // Por enquanto, retornamos um JSON
        return response()->json($data);
    }

    /**
     * Exportar relatório em CSV
     */
    public function exportCsv(Request $request)
    {
        $userId = Auth::user()->id;
        $period = $request->get('period', 'month');
        
        $filename = "relatorio_{$period}_" . Auth::user()->id . "_" . date('Y-m-d_H-i-s') . '.csv';
        $filepath = storage_path('app/exports/' . $filename);

        if (!file_exists(dirname($filepath))) {
            mkdir(dirname($filepath), 0755, true);
        }

        $file = fopen($filepath, 'w');

        // Cabeçalho
        fputcsv($file, [
            'Período', 'Total de Tarefas', 'Tarefas Concluídas', 'Taxa de Conclusão (%)',
            'Tempo Médio de Conclusão (dias)', 'Tarefas Pendentes', 'Tarefas em Progresso'
        ]);

        // Dados
        $stats = $this->getGeneralStats($userId);
        fputcsv($file, [
            $period,
            $stats['total_tasks'],
            $stats['completed_tasks'],
            $stats['productivity_rate'],
            $stats['avg_completion_time'],
            $stats['pending_tasks'],
            $stats['in_progress_tasks']
        ]);

        fclose($file);

        return response()->download($filepath, $filename)->deleteFileAfterSend();
    }

    // Métodos auxiliares
    private function getStatusLabel($status)
    {
        return match($status) {
            'pending' => 'Pendente',
            'in_progress' => 'Em Progresso',
            'completed' => 'Concluída',
            default => $status
        };
    }

    private function getStatusColor($status)
    {
        return match($status) {
            'pending' => '#6B7280',
            'in_progress' => '#F59E0B',
            'completed' => '#10B981',
            default => '#6B7280'
        };
    }

    private function getPriorityLabel($priority)
    {
        return match($priority) {
            'low' => 'Baixa',
            'medium' => 'Média',
            'high' => 'Alta',
            default => $priority
        };
    }

    private function getPriorityColor($priority)
    {
        return match($priority) {
            'low' => '#3B82F6',
            'medium' => '#F59E0B',
            'high' => '#EF4444',
            default => '#6B7280'
        };
    }
} 