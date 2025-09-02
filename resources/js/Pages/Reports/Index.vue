<template>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Relatórios e Estatísticas
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Toolbar Adaptada -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="relative h-32 bg-gradient-to-r from-blue-600 to-purple-600 rounded-t-lg">
                        <!-- Background Pattern -->
                        <div class="absolute inset-0 bg-black bg-opacity-20"></div>
                        
                        <!-- Toolbar Content -->
                        <div class="relative flex items-center justify-between h-full px-6">
                            <!-- Left Side -->
                            <div class="flex items-center space-x-4">
                                <!-- Title -->
                                <div class="text-white">
                                    <h1 class="text-2xl font-bold">Relatórios</h1>
                                    <p class="text-blue-100 text-sm">Análise de dados e estatísticas</p>
                                </div>
                            </div>

                            <!-- Right Side -->
                            <div class="flex items-center space-x-3">

                                <!-- Refresh Button -->
                                <button
                                    @click="refreshData"
                                    class="bg-white bg-opacity-20 hover:bg-opacity-30 text-white p-2 rounded-md transition-all duration-200 backdrop-blur-sm"
                                    :class="{ 'animate-spin': isRefreshing }"
                                >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                    </svg>
                                </button>


                            </div>
                        </div>
                    </div>

                    <!-- Toolbar Info Bar -->
                    <div class="px-6 py-3 bg-gray-50 border-t border-gray-200">
                        <div class="flex items-center justify-between text-sm text-gray-600">
                            <div class="flex items-center space-x-4">
                                <span class="flex items-center space-x-1">
                                    <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span>Dados atualizados em tempo real</span>
                                </span>
                                <span class="flex items-center space-x-1">
                                    <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span>Última atualização: {{ lastUpdate }}</span>
                                </span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs font-medium">
                                    {{ stats.total_tasks }} tarefas
                                </span>
                                <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-medium">
                                    {{ stats.productivity_rate }}% produtividade
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Estatísticas Gerais -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold mb-4">Estatísticas Gerais</h3>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <div class="text-center p-4 bg-blue-50 rounded-lg">
                                <div class="text-2xl font-bold text-blue-600">{{ stats.total_tasks }}</div>
                                <div class="text-sm text-gray-600">Total de Tarefas</div>
                            </div>
                            <div class="text-center p-4 bg-green-50 rounded-lg">
                                <div class="text-2xl font-bold text-green-600">{{ stats.completed_tasks }}</div>
                                <div class="text-sm text-gray-600">Concluídas</div>
                            </div>
                            <div class="text-center p-4 bg-yellow-50 rounded-lg">
                                <div class="text-2xl font-bold text-yellow-600">{{ stats.productivity_rate }}%</div>
                                <div class="text-sm text-gray-600">Taxa de Produtividade</div>
                            </div>
                            <div class="text-center p-4 bg-purple-50 rounded-lg">
                                <div class="text-2xl font-bold text-purple-600">{{ stats.avg_completion_time }}d</div>
                                <div class="text-sm text-gray-600">Tempo Médio</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Gráficos -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                    <!-- Produtividade ao longo do tempo -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold mb-4">Produtividade (Últimos 30 dias)</h3>
                            <div class="h-64">
                                <canvas ref="productivityChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Tarefas por categoria -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold mb-4">Tarefas por Status</h3>
                            <div class="h-64">
                                <canvas ref="statusChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tempo de conclusão -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold mb-4">Tempo de Conclusão</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <canvas ref="completionTimeChart"></canvas>
                            </div>
                            <div class="space-y-4">
                                <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                                    <span class="font-medium">Tempo Médio:</span>
                                    <span class="text-blue-600 font-bold">{{ completionTimeData.average }} dias</span>
                                </div>
                                <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                                    <span class="font-medium">Tempo Mediano:</span>
                                    <span class="text-green-600 font-bold">{{ completionTimeData.median }} dias</span>
                                </div>
                                <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                                    <span class="font-medium">Mais Rápida:</span>
                                    <span class="text-purple-600 font-bold">{{ completionTimeData.fastest }} dias</span>
                                </div>
                                <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                                    <span class="font-medium">Mais Lenta:</span>
                                    <span class="text-red-600 font-bold">{{ completionTimeData.slowest }} dias</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Relatórios Semanais e Mensais -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                    <!-- Relatório Semanal -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold mb-4">Relatório Semanal</h3>
                            <div class="space-y-4">
                                <div class="flex justify-between items-center">
                                    <span class="font-medium">Período:</span>
                                    <span class="text-gray-600">{{ weeklyReport.period }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="font-medium">Total de Tarefas:</span>
                                    <span class="text-blue-600 font-bold">{{ weeklyReport.total_tasks }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="font-medium">Concluídas:</span>
                                    <span class="text-green-600 font-bold">{{ weeklyReport.completed_tasks }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="font-medium">Taxa de Conclusão:</span>
                                    <span class="text-purple-600 font-bold">{{ weeklyReport.completion_rate }}%</span>
                                </div>
                            </div>
                            
                            <!-- Atividade diária -->
                            <div class="mt-4">
                                <h4 class="font-medium mb-2">Atividade Diária</h4>
                                <div class="space-y-2">
                                    <div v-for="day in weeklyReport.daily_activity" :key="day.day" class="flex justify-between items-center text-sm">
                                        <span class="font-medium">{{ day.day }}</span>
                                        <div class="flex gap-4">
                                            <span class="text-blue-600">{{ day.created }} criadas</span>
                                            <span class="text-green-600">{{ day.completed }} concluídas</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Relatório Mensal -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold mb-4">Relatório Mensal</h3>
                            <div class="space-y-4">
                                <div class="flex justify-between items-center">
                                    <span class="font-medium">Período:</span>
                                    <span class="text-gray-600">{{ monthlyReport.period }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="font-medium">Total de Tarefas:</span>
                                    <span class="text-blue-600 font-bold">{{ monthlyReport.total_tasks }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="font-medium">Concluídas:</span>
                                    <span class="text-green-600 font-bold">{{ monthlyReport.completed_tasks }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="font-medium">Taxa de Conclusão:</span>
                                    <span class="text-purple-600 font-bold">{{ monthlyReport.completion_rate }}%</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="font-medium">Crescimento:</span>
                                    <span :class="monthlyReport.growth_from_last_month >= 0 ? 'text-green-600' : 'text-red-600'" class="font-bold">
                                        {{ monthlyReport.growth_from_last_month >= 0 ? '+' : '' }}{{ monthlyReport.growth_from_last_month }}%
                                    </span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="font-medium">Média por Dia:</span>
                                    <span class="text-orange-600 font-bold">{{ monthlyReport.avg_tasks_per_day }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Histórico de Atividades -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold mb-4">Histórico de Atividades</h3>
                        
                        <div v-if="recentActivities.length === 0" class="text-gray-500 text-center py-8">
                            Nenhuma atividade registrada.
                        </div>

                        <div v-else class="space-y-4">
                            <div
                                v-for="activity in recentActivities"
                                :key="activity.id"
                                class="flex items-start gap-4 p-4 border rounded-lg hover:bg-gray-50 transition"
                            >
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 rounded-full flex items-center justify-center" :class="getActivityBgColor(activity.action)">
                                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path v-if="activity.action_icon === 'plus-circle'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                            <path v-else-if="activity.action_icon === 'edit'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            <path v-else-if="activity.action_icon === 'trash'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            <path v-else-if="activity.action_icon === 'refresh'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                            <path v-else-if="activity.action_icon === 'flag'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-4a4 4 0 014-4h6a4 4 0 014 4v4"></path>
                                            <path v-else-if="activity.action_icon === 'user-plus'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                                            <path v-else-if="activity.action_icon === 'check-circle'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                </div>
                                
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="font-medium" :class="activity.action_color">{{ activity.action_label }}</span>
                                        <span v-if="activity.task_title" class="text-gray-600">- {{ activity.task_title }}</span>
                                    </div>
                                    <p class="text-sm text-gray-600">{{ activity.description }}</p>
                                    <p class="text-xs text-gray-500 mt-1">{{ activity.time_ago }}</p>
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
import { ref, onMounted, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Chart from 'chart.js/auto';

const props = defineProps({
    stats: Object,
    productivityData: Array,
    tasksByCategory: Object,
    completionTimeData: Object,
    weeklyReport: Object,
    monthlyReport: Object,
    recentActivities: Array
});

// Referências para os gráficos
const productivityChart = ref(null);
const statusChart = ref(null);
const completionTimeChart = ref(null);

// Estado para refresh
const isRefreshing = ref(false);

// Computed para última atualização
const lastUpdate = computed(() => {
    return new Date().toLocaleString('pt-BR');
});



// Função para refresh dos dados
const refreshData = () => {
    isRefreshing.value = true;
    
    // Simular refresh
    setTimeout(() => {
        router.reload();
        isRefreshing.value = false;
    }, 1000);
};

// Função para obter cor de fundo da atividade
const getActivityBgColor = (action) => {
    const colors = {
        'created': 'bg-green-500',
        'updated': 'bg-blue-500',
        'deleted': 'bg-red-500',
        'status_changed': 'bg-yellow-500',
        'priority_changed': 'bg-purple-500',
        'assigned': 'bg-indigo-500',
        'completed': 'bg-green-500'
    };
    return colors[action] || 'bg-gray-500';
};

// Inicializar gráficos
onMounted(() => {
    // Gráfico de produtividade
    if (productivityChart.value) {
        new Chart(productivityChart.value, {
            type: 'line',
            data: {
                labels: props.productivityData.map(item => item.date),
                datasets: [
                    {
                        label: 'Tarefas Criadas',
                        data: props.productivityData.map(item => item.created),
                        borderColor: 'rgb(59, 130, 246)',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        tension: 0.1
                    },
                    {
                        label: 'Tarefas Concluídas',
                        data: props.productivityData.map(item => item.completed),
                        borderColor: 'rgb(34, 197, 94)',
                        backgroundColor: 'rgba(34, 197, 94, 0.1)',
                        tension: 0.1
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }

    // Gráfico de status
    if (statusChart.value) {
        new Chart(statusChart.value, {
            type: 'doughnut',
            data: {
                labels: props.tasksByCategory.by_status.map(item => item.category),
                datasets: [{
                    data: props.tasksByCategory.by_status.map(item => item.count),
                    backgroundColor: props.tasksByCategory.by_status.map(item => item.color),
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                    }
                }
            }
        });
    }

    // Gráfico de tempo de conclusão
    if (completionTimeChart.value) {
        const ranges = Object.keys(props.completionTimeData.ranges);
        const values = Object.values(props.completionTimeData.ranges);
        
        new Chart(completionTimeChart.value, {
            type: 'bar',
            data: {
                labels: ranges.map(range => `${range} dias`),
                datasets: [{
                    label: 'Quantidade de Tarefas',
                    data: values,
                    backgroundColor: [
                        'rgba(34, 197, 94, 0.8)',
                        'rgba(59, 130, 246, 0.8)',
                        'rgba(245, 158, 11, 0.8)',
                        'rgba(239, 68, 68, 0.8)',
                        'rgba(107, 114, 128, 0.8)'
                    ],
                    borderColor: [
                        'rgb(34, 197, 94)',
                        'rgb(59, 130, 246)',
                        'rgb(245, 158, 11)',
                        'rgb(239, 68, 68)',
                        'rgb(107, 114, 128)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }
});
</script> 