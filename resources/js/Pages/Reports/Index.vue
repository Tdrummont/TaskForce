<template>
    <AuthenticatedLayout>
      <template #header>
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          {{ t('reports.title') }}
        </h2>
      </template>
  
      <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
          <!-- Toolbar -->
          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="relative h-32 bg-gradient-to-r from-blue-600 to-purple-600 rounded-t-lg">
              <div class="absolute inset-0 bg-black bg-opacity-20"></div>
  
              <div class="relative flex items-center justify-between h-full px-6">
                <div class="text-white">
                  <h1 class="text-2xl font-bold">{{ t('reports.header') }}</h1>
                  <p class="text-blue-100 text-sm">{{ t('reports.subtitle') }}</p>
                </div>
  
                <div class="flex items-center space-x-3">
                  <button
                    @click="refreshData"
                    class="bg-white bg-opacity-20 hover:bg-opacity-30 text-white p-2 rounded-md transition-all duration-200 backdrop-blur-sm"
                    :class="{ 'animate-spin': isRefreshing }"
                    :title="t('common.refresh')"
                  >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                  </button>
                </div>
              </div>
            </div>
  
            <div class="px-6 py-3 bg-gray-50 border-t border-gray-200">
              <div class="flex items-center justify-between text-sm text-gray-600">
                <div class="flex items-center space-x-4">
                  <span class="flex items-center space-x-1">
                    <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>{{ t('reports.realtime') }}</span>
                  </span>
                  <span class="flex items-center space-x-1">
                    <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>{{ t('reports.last_update') }}: {{ lastUpdate }}</span>
                  </span>
                </div>
                <div class="flex items-center space-x-2">
                  <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs font-medium">
                    {{ stats.total_tasks }} {{ t('reports.tasks') }}
                  </span>
                  <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-medium">
                    {{ stats.productivity_rate }}% {{ t('reports.productivity') }}
                  </span>
                </div>
              </div>
            </div>
          </div>
  
          <!-- Estatísticas -->
          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6">
              <h3 class="text-lg font-semibold mb-4">{{ t('reports.overview') }}</h3>
              <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="text-center p-4 bg-blue-50 rounded-lg">
                  <div class="text-2xl font-bold text-blue-600">{{ stats.total_tasks }}</div>
                  <div class="text-sm text-gray-600">{{ t('reports.total_tasks') }}</div>
                </div>
                <div class="text-center p-4 bg-green-50 rounded-lg">
                  <div class="text-2xl font-bold text-green-600">{{ stats.completed_tasks }}</div>
                  <div class="text-sm text-gray-600">{{ t('reports.completed') }}</div>
                </div>
                <div class="text-center p-4 bg-yellow-50 rounded-lg">
                  <div class="text-2xl font-bold text-yellow-600">{{ stats.productivity_rate }}%</div>
                  <div class="text-sm text-gray-600">{{ t('reports.productivity_rate') }}</div>
                </div>
                <div class="text-center p-4 bg-purple-50 rounded-lg">
                  <div class="text-2xl font-bold text-purple-600">{{ stats.avg_completion_time }}d</div>
                  <div class="text-sm text-gray-600">{{ t('reports.avg_time') }}</div>
                </div>
              </div>
            </div>
          </div>
  
          <!-- Gráficos -->
          <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
              <div class="p-6">
                <h3 class="text-lg font-semibold mb-4">{{ t('reports.productivity_30d') }}</h3>
                <div class="h-64">
                  <canvas ref="productivityChart"></canvas>
                </div>
              </div>
            </div>
  
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
              <div class="p-6">
                <h3 class="text-lg font-semibold mb-4">{{ t('reports.by_status') }}</h3>
                <div class="h-64">
                  <canvas ref="statusChart"></canvas>
                </div>
              </div>
            </div>
          </div>
  
          <!-- Tempo de conclusão -->
          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6">
              <h3 class="text-lg font-semibold mb-4">{{ t('reports.completion_time') }}</h3>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <canvas ref="completionTimeChart"></canvas>
                </div>
                <div class="space-y-4">
                  <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                    <span class="font-medium">{{ t('reports.avg') }}</span>
                    <span class="text-blue-600 font-bold">{{ completionTimeData.average }} {{ t('common.days') }}</span>
                  </div>
                  <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                    <span class="font-medium">{{ t('reports.median') }}</span>
                    <span class="text-green-600 font-bold">{{ completionTimeData.median }} {{ t('common.days') }}</span>
                  </div>
                  <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                    <span class="font-medium">{{ t('reports.fastest') }}</span>
                    <span class="text-purple-600 font-bold">{{ completionTimeData.fastest }} {{ t('common.days') }}</span>
                  </div>
                  <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                    <span class="font-medium">{{ t('reports.slowest') }}</span>
                    <span class="text-red-600 font-bold">{{ completionTimeData.slowest }} {{ t('common.days') }}</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
  
          <!-- Semanal / Mensal -->
          <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
              <div class="p-6">
                <h3 class="text-lg font-semibold mb-4">{{ t('reports.weekly') }}</h3>
                <div class="space-y-4">
                  <div class="flex justify-between items-center">
                    <span class="font-medium">{{ t('reports.period') }}</span>
                    <span class="text-gray-600">{{ weeklyReport.period }}</span>
                  </div>
                  <div class="flex justify-between items-center">
                    <span class="font-medium">{{ t('reports.total_tasks') }}</span>
                    <span class="text-blue-600 font-bold">{{ weeklyReport.total_tasks }}</span>
                  </div>
                  <div class="flex justify-between items-center">
                    <span class="font-medium">{{ t('reports.completed') }}</span>
                    <span class="text-green-600 font-bold">{{ weeklyReport.completed_tasks }}</span>
                  </div>
                  <div class="flex justify-between items-center">
                    <span class="font-medium">{{ t('reports.completion_rate') }}</span>
                    <span class="text-purple-600 font-bold">{{ weeklyReport.completion_rate }}%</span>
                  </div>
                </div>
  
                <div class="mt-4">
                  <h4 class="font-medium mb-2">{{ t('reports.daily_activity') }}</h4>
                  <div class="space-y-2">
                    <div v-for="day in weeklyReport.daily_activity" :key="day.day" class="flex justify-between items-center text-sm">
                      <span class="font-medium">{{ day.day }}</span>
                      <div class="flex gap-4">
                        <span class="text-blue-600">{{ day.created }} {{ t('reports.created') }}</span>
                        <span class="text-green-600">{{ day.completed }} {{ t('reports.completed') }}</span>
                      </div>
                    </div>
                  </div>
                </div>
  
              </div>
            </div>
  
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
              <div class="p-6">
                <h3 class="text-lg font-semibold mb-4">{{ t('reports.monthly') }}</h3>
                <div class="space-y-4">
                  <div class="flex justify-between items-center">
                    <span class="font-medium">{{ t('reports.period') }}</span>
                    <span class="text-gray-600">{{ monthlyReport.period }}</span>
                  </div>
                  <div class="flex justify-between items-center">
                    <span class="font-medium">{{ t('reports.total_tasks') }}</span>
                    <span class="text-blue-600 font-bold">{{ monthlyReport.total_tasks }}</span>
                  </div>
                  <div class="flex justify-between items-center">
                    <span class="font-medium">{{ t('reports.completed') }}</span>
                    <span class="text-green-600 font-bold">{{ monthlyReport.completed_tasks }}</span>
                  </div>
                  <div class="flex justify-between items-center">
                    <span class="font-medium">{{ t('reports.completion_rate') }}</span>
                    <span class="text-purple-600 font-bold">{{ monthlyReport.completion_rate }}%</span>
                  </div>
                  <div class="flex justify-between items-center">
                    <span class="font-medium">{{ t('reports.growth') }}</span>
                    <span :class="monthlyReport.growth_from_last_month >= 0 ? 'text-green-600' : 'text-red-600'" class="font-bold">
                      {{ monthlyReport.growth_from_last_month >= 0 ? '+' : '' }}{{ monthlyReport.growth_from_last_month }}%
                    </span>
                  </div>
                  <div class="flex justify-between items-center">
                    <span class="font-medium">{{ t('reports.avg_per_day') }}</span>
                    <span class="text-orange-600 font-bold">{{ monthlyReport.avg_tasks_per_day }}</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
  
          <!-- Atividades -->
          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
              <h3 class="text-lg font-semibold mb-4">{{ t('reports.activity_history') }}</h3>
  
              <div v-if="recentActivities.length === 0" class="text-gray-500 text-center py-8">
                {{ t('reports.no_activity') }}
              </div>
  
              <div v-else class="space-y-4">
                <div v-for="activity in recentActivities" :key="activity.id" class="flex items-start gap-4 p-4 border rounded-lg hover:bg-gray-50 transition">
                  <div class="flex-shrink-0">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center" :class="getActivityBgColor(activity.action)">
                      <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path v-if="activity.action_icon === 'plus-circle'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        <path v-else-if="activity.action_icon === 'edit'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        <path v-else-if="activity.action_icon === 'trash'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        <path v-else-if="activity.action_icon === 'refresh'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        <path v-else-if="activity.action_icon === 'flag'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 21v-4a4 4 0 014-4h6a4 4 0 014 4v4" />
                        <path v-else-if="activity.action_icon === 'user-plus'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                        <path v-else-if="activity.action_icon === 'check-circle'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
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
  import { ref, onMounted, computed } from 'vue'
  import { router } from '@inertiajs/vue3'
  import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
  import Chart from 'chart.js/auto'
  import { useLocale } from '@/Components/useLocale'
  
  const { t, formatDate /*, routeL*/ } = useLocale()
  
  const props = defineProps({
    stats: Object,
    productivityData: Array,
    tasksByCategory: Object,
    completionTimeData: Object,
    weeklyReport: Object,
    monthlyReport: Object,
    recentActivities: Array
  })
  
  const productivityChart = ref(null)
  const statusChart = ref(null)
  const completionTimeChart = ref(null)
  
  const isRefreshing = ref(false)
  const lastUpdate = computed(() => formatDate(new Date()))
  
  const refreshData = () => {
    isRefreshing.value = true
    setTimeout(() => {
      router.reload()
      isRefreshing.value = false
    }, 800)
  }
  
  const getActivityBgColor = (action) => {
    const map = {
      created: 'bg-green-500',
      updated: 'bg-blue-500',
      deleted: 'bg-red-500',
      status_changed: 'bg-yellow-500',
      priority_changed: 'bg-purple-500',
      assigned: 'bg-indigo-500',
      completed: 'bg-green-500'
    }
    return map[action] || 'bg-gray-500'
  }
  
  onMounted(() => {
    if (productivityChart.value) {
      new Chart(productivityChart.value, {
        type: 'line',
        data: {
          labels: props.productivityData.map(i => i.date),
          datasets: [
            {
              label: t('reports.created'),
              data: props.productivityData.map(i => i.created),
              borderColor: 'rgb(59, 130, 246)',
              backgroundColor: 'rgba(59, 130, 246, 0.1)',
              tension: 0.1
            },
            {
              label: t('reports.completed'),
              data: props.productivityData.map(i => i.completed),
              borderColor: 'rgb(34, 197, 94)',
              backgroundColor: 'rgba(34, 197, 94, 0.1)',
              tension: 0.1
            }
          ]
        },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'top' } }, scales: { y: { beginAtZero: true } } }
      })
    }
  
    if (statusChart.value) {
      new Chart(statusChart.value, {
        type: 'doughnut',
        data: {
          labels: props.tasksByCategory.by_status.map(i => i.category),
          datasets: [{
            data: props.tasksByCategory.by_status.map(i => i.count),
            backgroundColor: props.tasksByCategory.by_status.map(i => i.color),
            borderWidth: 2,
            borderColor: '#fff'
          }]
        },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } } }
      })
    }
  
    if (completionTimeChart.value) {
      const ranges = Object.keys(props.completionTimeData.ranges)
      const values = Object.values(props.completionTimeData.ranges)
      new Chart(completionTimeChart.value, {
        type: 'bar',
        data: {
          labels: ranges.map(r => `${r} ${t('common.days')}`),
          datasets: [{
            label: t('reports.tasks_count'),
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
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
      })
    }
  })
  </script>
  