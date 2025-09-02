<template>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Dashboard - Métricas e KPIs
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- KPIs Principais -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <!-- Produtividade da Semana -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-blue-500 rounded-md flex items-center justify-center">
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-500">Produtividade Semanal</p>
                                    <p class="text-2xl font-semibold text-gray-900">{{ metrics.week_productivity }}%</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Streak de Produtividade -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-green-500 rounded-md flex items-center justify-center">
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-500">Streak de Produtividade</p>
                                    <p class="text-2xl font-semibold text-gray-900">{{ metrics.productivity_streak }} dias</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tarefas em Atraso -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-red-500 rounded-md flex items-center justify-center">
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-500">Tarefas em Atraso</p>
                                    <p class="text-2xl font-semibold text-red-600">{{ metrics.overdue_tasks }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tempo Médio de Conclusão -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-purple-500 rounded-md flex items-center justify-center">
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-500">Tempo Médio</p>
                                    <p class="text-2xl font-semibold text-gray-900">{{ metrics.avg_completion_time }} dias</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Metas vs Realizado -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                    <!-- Meta Diária -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold mb-4">Meta Diária vs Realizado</h3>
                            <div class="mb-4">
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-sm font-medium text-gray-700">Progresso</span>
                                    <span class="text-sm font-medium text-gray-700">{{ metrics.daily_completed }}/{{ metrics.daily_goal }}</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div 
                                        class="bg-blue-600 h-2 rounded-full transition-all duration-300"
                                        :style="{ width: Math.min(metrics.daily_progress, 100) + '%' }"
                                    ></div>
                                </div>
                                <p class="text-sm text-gray-600 mt-2">{{ metrics.daily_progress }}% da meta diária</p>
                            </div>
                        </div>
                    </div>

                    <!-- Meta Semanal -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold mb-4">Meta Semanal vs Realizado</h3>
                            <div class="mb-4">
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-sm font-medium text-gray-700">Progresso</span>
                                    <span class="text-sm font-medium text-gray-700">{{ metrics.weekly_completed }}/{{ metrics.weekly_goal }}</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div 
                                        class="bg-green-600 h-2 rounded-full transition-all duration-300"
                                        :style="{ width: Math.min(metrics.weekly_progress, 100) + '%' }"
                                    ></div>
                                </div>
                                <p class="text-sm text-gray-600 mt-2">{{ metrics.weekly_progress }}% da meta semanal</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Produtividade dos Últimos 7 Dias -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-8">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold mb-4">Produtividade dos Últimos 7 Dias</h3>
                        <div class="grid grid-cols-7 gap-4">
                            <div 
                                v-for="day in last_7_days" 
                                :key="day.date"
                                class="text-center"
                            >
                                <div class="text-xs text-gray-500 mb-2">{{ day.day }}</div>
                                <div class="text-sm font-medium mb-1">{{ day.date }}</div>
                                <div class="text-xs text-gray-600 mb-2">
                                    {{ day.completed }}/{{ day.total }}
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div 
                                        class="bg-blue-500 h-2 rounded-full transition-all duration-300"
                                        :style="{ width: Math.min(day.productivity, 100) + '%' }"
                                    ></div>
                                </div>
                                <div class="text-xs text-gray-500 mt-1">{{ day.productivity }}%</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tarefas em Atraso (Destaque) -->
                <div v-if="overdue_tasks.length > 0" class="bg-red-50 border border-red-200 rounded-lg mb-8">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-red-800 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Tarefas em Atraso ({{ overdue_tasks.length }})
                        </h3>
                        <div class="space-y-3">
                            <div 
                                v-for="task in overdue_tasks" 
                                :key="task.id"
                                class="bg-white border border-red-200 rounded-lg p-4"
                            >
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h4 class="font-medium text-gray-900">{{ task.title }}</h4>
                                        <p class="text-sm text-gray-600 mt-1">{{ task.description }}</p>
                                        <div class="flex items-center gap-4 mt-2 text-sm">
                                            <span class="text-red-600 font-medium">
                                                Vencida em {{ formatDate(task.due_date) }}
                                            </span>
                                            <span class="text-gray-500">
                                                {{ getDaysOverdue(task.due_date) }} dias de atraso
                                            </span>
                                        </div>
                                    </div>
                                    <div class="flex gap-2">
                                        <button 
                                            @click="markAsCompleted(task.id)"
                                            class="text-green-600 hover:text-green-800 text-sm font-medium"
                                        >
                                            Marcar como Concluída
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Estatísticas Detalhadas -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                    <!-- Status das Tarefas -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold mb-4">Status das Tarefas</h3>
                            <div class="space-y-3">
                                <div 
                                    v-for="status in status_stats" 
                                    :key="status.status"
                                    class="flex justify-between items-center"
                                >
                                    <span class="text-sm font-medium text-gray-700">
                                        {{ getStatusLabel(status.status) }}
                                    </span>
                                    <span class="text-sm font-semibold" :class="getStatusColor(status.status)">
                                        {{ status.count }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Prioridades -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold mb-4">Por Prioridade</h3>
                            <div class="space-y-3">
                                <div 
                                    v-for="priority in priority_stats" 
                                    :key="priority.priority"
                                    class="flex justify-between items-center"
                                >
                                    <span class="text-sm font-medium text-gray-700">
                                        {{ getPriorityLabel(priority.priority) }}
                                    </span>
                                    <span class="text-sm font-semibold" :class="getPriorityColor(priority.priority)">
                                        {{ priority.count }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Categorias Mais Produtivas -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold mb-4">Categorias Mais Produtivas</h3>
                            <div class="space-y-3">
                                <div 
                                    v-for="category in category_productivity" 
                                    :key="category.category"
                                    class="flex justify-between items-center"
                                >
                                    <span class="text-sm font-medium text-gray-700">
                                        {{ category.category }}
                                    </span>
                                    <span class="text-sm font-semibold text-green-600">
                                        {{ category.completed_count }} concluídas
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tarefas Recentes e Próximas -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Tarefas Recentes -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold mb-4">Tarefas Recentes</h3>
                            <div v-if="recent_tasks.length === 0" class="text-gray-500 text-center py-4">
                                Nenhuma tarefa recente.
                            </div>
                            <div v-else class="space-y-3">
                                <div 
                                    v-for="task in recent_tasks" 
                                    :key="task.id"
                                    class="border rounded-lg p-3"
                                >
                                    <h4 class="font-medium text-gray-900">{{ task.title }}</h4>
                                    <p class="text-sm text-gray-600 mt-1">{{ task.description }}</p>
                                    <div class="flex items-center gap-2 mt-2">
                                        <span :class="getStatusColor(task.status)" class="text-xs font-medium px-2 py-1 rounded">
                                            {{ getStatusLabel(task.status) }}
                                        </span>
                                        <span class="text-xs text-gray-500">
                                            {{ formatDate(task.created_at) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Próximas Tarefas -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold mb-4">Próximas Tarefas</h3>
                            <div v-if="upcoming_tasks.length === 0" class="text-gray-500 text-center py-4">
                                Nenhuma tarefa próxima.
                            </div>
                            <div v-else class="space-y-3">
                                <div 
                                    v-for="task in upcoming_tasks" 
                                    :key="task.id"
                                    class="border rounded-lg p-3"
                                >
                                    <h4 class="font-medium text-gray-900">{{ task.title }}</h4>
                                    <p class="text-sm text-gray-600 mt-1">{{ task.description }}</p>
                                    <div class="flex items-center gap-2 mt-2">
                                        <span :class="getPriorityColor(task.priority)" class="text-xs font-medium px-2 py-1 rounded">
                                            {{ getPriorityLabel(task.priority) }}
                                        </span>
                                        <span class="text-xs text-gray-500">
                                            Vence em {{ formatDate(task.due_date) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    metrics: Object,
    overdue_tasks: Array,
    last_7_days: Array,
    category_productivity: Array,
    priority_stats: Array,
    status_stats: Array,
    recent_tasks: Array,
    upcoming_tasks: Array
});

// Funções auxiliares
const formatDate = (date) => {
    return new Date(date).toLocaleDateString('pt-BR');
};

const getDaysOverdue = (dueDate) => {
    const due = new Date(dueDate);
    const today = new Date();
    const diffTime = today - due;
    return Math.ceil(diffTime / (1000 * 60 * 60 * 24));
};

const getStatusLabel = (status) => {
    const labels = {
        pending: 'Pendente',
        in_progress: 'Em Progresso',
        completed: 'Concluída'
    };
    return labels[status] || status;
};

const getStatusColor = (status) => {
    const colors = {
        pending: 'text-gray-600 bg-gray-100',
        in_progress: 'text-yellow-600 bg-yellow-100',
        completed: 'text-green-600 bg-green-100'
    };
    return colors[status] || 'text-gray-600 bg-gray-100';
};

const getPriorityLabel = (priority) => {
    const labels = {
        low: 'Baixa',
        medium: 'Média',
        high: 'Alta'
    };
    return labels[priority] || priority;
};

const getPriorityColor = (priority) => {
    const colors = {
        low: 'text-blue-600 bg-blue-100',
        medium: 'text-orange-600 bg-orange-100',
        high: 'text-red-600 bg-red-100'
    };
    return colors[priority] || 'text-gray-600 bg-gray-100';
};

const markAsCompleted = (taskId) => {
    if (confirm('Marcar esta tarefa como concluída?')) {
        router.patch(route('tasks.updateStatus', taskId), {
            status: 'completed'
        });
    }
};
</script>
