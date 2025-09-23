<template>
    <AuthenticatedLayout>
      <template #header>
        <div class="flex flex-col">
          <h2 class="font-semibold text-2xl text-800 text-white leading-tight">
            BEM VINDO {{ firstName }} — AO SEU GERENCIADOR DE TAREFAS
          </h2>
          <p class="text-sm text-gray-500 mt-1">{{ t('dashboard.title') }}</p>
        </div>
      </template>
  
      <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
          <!-- KPIs -->
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Weekly Productivity -->
            <div class="bg-white/10 backdrop-blur border border-white/10 text-white overflow-hidden shadow-sm sm:rounded-lg">
              <div class="p-6">
                <div class="flex items-center">
                  <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-blue-600 rounded-md flex items-center justify-center">
                      <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                      </svg>
                    </div>
                  </div>
                  <div class="ml-4">
                    <p class="text-sm font-medium text-slate-300">{{ t('dashboard.week_productivity') }}</p>
                    <p class="text-2xl font-semibold text-white">{{ metrics.week_productivity }}%</p>
                  </div>
                </div>
              </div>
            </div>
  
            <!-- Productivity Streak -->
            <div class="bg-white/10 backdrop-blur border border-white/10 text-white overflow-hidden shadow-sm sm:rounded-lg">
              <div class="p-6">
                <div class="flex items-center">
                  <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-green-600 rounded-md flex items-center justify-center">
                      <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                      </svg>
                    </div>
                  </div>
                  <div class="ml-4">
                    <p class="text-sm font-medium text-slate-300">{{ t('dashboard.productivity_streak') }}</p>
                    <p class="text-2xl font-semibold text-white">{{ metrics.productivity_streak }}</p>
                  </div>
                </div>
              </div>
            </div>
  
            <!-- Overdue -->
            <div class="bg-white/10 backdrop-blur border border-white/10 text-white overflow-hidden shadow-sm sm:rounded-lg">
              <div class="p-6">
                <div class="flex items-center">
                  <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-red-600 rounded-md flex items-center justify-center">
                      <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                      </svg>
                    </div>
                  </div>
                  <div class="ml-4">
                    <p class="text-sm font-medium text-slate-300">{{ t('dashboard.overdue_tasks') }}</p>
                    <p class="text-2xl font-semibold text-red-300">{{ metrics.overdue_tasks }}</p>
                  </div>
                </div>
              </div>
            </div>
  
            <!-- Avg Completion Time -->
            <div class="bg-white/10 backdrop-blur border border-white/10 text-white overflow-hidden shadow-sm sm:rounded-lg">
              <div class="p-6">
                <div class="flex items-center">
                  <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-purple-600 rounded-md flex items-center justify-center">
                      <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                      </svg>
                    </div>
                  </div>
                  <div class="ml-4">
                    <p class="text-sm font-medium text-slate-300">{{ t('dashboard.avg_time') }}</p>
                    <p class="text-2xl font-semibold text-white">{{ metrics.avg_completion_time }}</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
  
          <!-- Goals -->
          <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <div class="bg-white/10 backdrop-blur border border-white/10 text-white overflow-hidden shadow-sm sm:rounded-lg">
              <div class="p-6">
                <h3 class="text-lg font-semibold mb-4">{{ t('dashboard.daily_goal') }}</h3>
                <div class="mb-4">
                  <div class="flex justify-between items-center mb-2">
                    <span class="text-sm font-medium text-slate-300">{{ t('dashboard.progress') }}</span>
                    <span class="text-sm font-medium text-slate-300">{{ metrics.daily_completed }}/{{ metrics.daily_goal }}</span>
                  </div>
                  <div class="w-full bg-white/10 rounded-full h-2">
                    <div class="bg-blue-500 h-2 rounded-full transition-all duration-300"
                         :style="{ width: Math.min(metrics.daily_progress, 100) + '%' }"></div>
                  </div>
                  <p class="text-sm text-slate-300 mt-2">{{ metrics.daily_progress }}{{ t('dashboard.of_daily_goal') }}</p>
                </div>
              </div>
            </div>
  
            <div class="bg-white/10 backdrop-blur border border-white/10 text-white overflow-hidden shadow-sm sm:rounded-lg">
              <div class="p-6">
                <h3 class="text-lg font-semibold mb-4">{{ t('dashboard.weekly_goal') }}</h3>
                <div class="mb-4">
                  <div class="flex justify-between items-center mb-2">
                    <span class="text-sm font-medium text-slate-300">{{ t('dashboard.progress') }}</span>
                    <span class="text-sm font-medium text-slate-300">{{ metrics.weekly_completed }}/{{ metrics.weekly_goal }}</span>
                  </div>
                  <div class="w-full bg-white/10 rounded-full h-2">
                    <div class="bg-green-500 h-2 rounded-full transition-all duration-300"
                         :style="{ width: Math.min(metrics.weekly_progress, 100) + '%' }"></div>
                  </div>
                  <p class="text-sm text-slate-300 mt-2">{{ metrics.weekly_progress }}{{ t('dashboard.of_weekly_goal') }}</p>
                </div>
              </div>
            </div>
          </div>
  
          <!-- Last 7 days -->
          <div class="bg-white/10 backdrop-blur border border-white/10 text-white overflow-hidden shadow-sm sm:rounded-lg mb-8">
            <div class="p-6">
              <h3 class="text-lg font-semibold mb-4">{{ t('dashboard.last_7_days') }}</h3>
              <div class="grid grid-cols-7 gap-4">
                <div v-for="day in last_7_days" :key="day.date" class="text-center">
                  <div class="text-xs text-slate-300 mb-2">{{ day.day }}</div>
                  <div class="text-sm font-medium mb-1">{{ formatDate(day.date) }}</div>
                  <div class="text-xs text-slate-300 mb-2">
                    {{ day.completed }}/{{ day.total }}
                  </div>
                  <div class="w-full bg-white/10 rounded-full h-2">
                    <div class="bg-blue-500 h-2 rounded-full transition-all duration-300"
                         :style="{ width: Math.min(day.productivity, 100) + '%' }"></div>
                  </div>
                  <div class="text-xs text-slate-300 mt-1">{{ day.productivity }}%</div>
                </div>
              </div>
            </div>
          </div>
  
          <!-- Overdue highlight -->
          <div v-if="overdue_tasks.length > 0" class="bg-red-950/30 border border-red-500/30 text-white rounded-lg mb-8">
            <div class="p-6">
              <h3 class="text-lg font-semibold text-red-300 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                {{ t('dashboard.overdue_highlight') }} ({{ overdue_tasks.length }})
              </h3>
              <div class="space-y-3">
                <div v-for="task in overdue_tasks" :key="task.id" class="bg-white/5 border border-red-500/30 rounded-lg p-4">
                  <div class="flex justify-between items-start">
                    <div>
                      <h4 class="font-medium text-white">{{ task.title }}</h4>
                      <p class="text-sm text-slate-300 mt-1">{{ task.description }}</p>
                      <div class="flex items-center gap-4 mt-2 text-sm">
                        <span class="text-red-300 font-medium">
                          {{ t('dashboard.due_on') }} {{ formatDate(task.due_date) }}
                        </span>
                        <span class="text-slate-300">
                          {{ getDaysOverdue(task.due_date) }} {{ t('dashboard.days_overdue_suffix') }}
                        </span>
                      </div>
                    </div>
                    <div class="flex gap-2">
                      <button @click="markAsCompleted(task.id)"
                              class="text-green-300 hover:text-green-200 text-sm font-medium">
                        {{ t('dashboard.mark_completed') }}
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
  
          <!-- Detailed stats -->
          <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            <!-- Status -->
            <div class="bg-white/10 backdrop-blur border border-white/10 text-white overflow-hidden shadow-sm sm:rounded-lg">
              <div class="p-6">
                <h3 class="text-lg font-semibold mb-4">{{ t('dashboard.status_breakdown') }}</h3>
                <div class="space-y-3">
                  <div v-for="status in status_stats" :key="status.status" class="flex justify-between items-center">
                    <span class="text-sm font-medium text-slate-300">{{ getStatusLabel(status.status) }}</span>
                    <span class="text-sm font-semibold" :class="getStatusColor(status.status)">{{ status.count }}</span>
                  </div>
                </div>
              </div>
            </div>
  
            <!-- Priority -->
            <div class="bg-white/10 backdrop-blur border border-white/10 text-white overflow-hidden shadow-sm sm:rounded-lg">
              <div class="p-6">
                <h3 class="text-lg font-semibold mb-4">{{ t('dashboard.by_priority') }}</h3>
                <div class="space-y-3">
                  <div v-for="priority in priority_stats" :key="priority.priority" class="flex justify-between items-center">
                    <span class="text-sm font-medium text-slate-300">{{ getPriorityLabel(priority.priority) }}</span>
                    <span class="text-sm font-semibold" :class="getPriorityColor(priority.priority)">{{ priority.count }}</span>
                  </div>
                </div>
              </div>
            </div>
  
            <!-- Top categories -->
            <div class="bg-white/10 backdrop-blur border border-white/10 text-white overflow-hidden shadow-sm sm:rounded-lg">
              <div class="p-6">
                <h3 class="text-lg font-semibold mb-4">{{ t('dashboard.top_categories') }}</h3>
                <div class="space-y-3">
                  <div v-for="category in category_productivity" :key="category.category" class="flex justify-between items-center">
                    <span class="text-sm font-medium text-slate-300">{{ category.category }}</span>
                    <span class="text-sm font-semibold text-green-300">
                      {{ category.completed_count }} {{ t('status.completed') }}
                    </span>
                  </div>
                </div>
              </div>
            </div>
          </div>
  
          <!-- Recent / Upcoming -->
          <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Recent -->
            <div class="bg-white/10 backdrop-blur border border-white/10 text-white overflow-hidden shadow-sm sm:rounded-lg">
              <div class="p-6">
                <h3 class="text-lg font-semibold mb-4">{{ t('dashboard.recent_tasks') }}</h3>
                <div v-if="recent_tasks.length === 0" class="text-slate-300 text-center py-4">
                  {{ t('dashboard.no_recent_tasks') }}
                </div>
                <div v-else class="space-y-3">
                  <div v-for="task in recent_tasks" :key="task.id" class="border border-white/10 rounded-lg p-3">
                    <h4 class="font-medium text-white">{{ task.title }}</h4>
                    <p class="text-sm text-slate-300 mt-1">{{ task.description }}</p>
                    <div class="flex items-center gap-2 mt-2">
                      <span :class="getStatusColor(task.status)" class="text-xs font-medium px-2 py-1 rounded">
                        {{ getStatusLabel(task.status) }}
                      </span>
                      <span class="text-xs text-slate-300">
                        {{ formatDate(task.created_at) }}
                      </span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
  
            <!-- Upcoming -->
            <div class="bg-white/10 backdrop-blur border border-white/10 text-white overflow-hidden shadow-sm sm:rounded-lg">
              <div class="p-6">
                <h3 class="text-lg font-semibold mb-4">{{ t('dashboard.upcoming_tasks') }}</h3>
                <div v-if="upcoming_tasks.length === 0" class="text-slate-300 text-center py-4">
                  {{ t('dashboard.no_upcoming_tasks') }}
                </div>
                <div v-else class="space-y-3">
                  <div v-for="task in upcoming_tasks" :key="task.id" class="border border-white/10 rounded-lg p-3">
                    <h4 class="font-medium text-white">{{ task.title }}</h4>
                    <p class="text-sm text-slate-300 mt-1">{{ task.description }}</p>
                    <div class="flex items-center gap-2 mt-2">
                      <span :class="getPriorityColor(task.priority)" class="text-xs font-medium px-2 py-1 rounded">
                        {{ getPriorityLabel(task.priority) }}
                      </span>
                      <span class="text-xs text-slate-300">
                        {{ t('dashboard.due_on') }} {{ formatDate(task.due_date) }}
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
  
  <script setup lang="ts">
  import { router, usePage } from '@inertiajs/vue3'
  import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
  import { useLocale } from '@/Components/useLocale'
  import { computed } from 'vue'
  
  const { t, formatDate, routeL } = useLocale()
  const page = usePage()
  const firstName = computed(() => {
    const name = (page.props as any)?.auth?.user?.name || ''
    return name.split(' ')[0] || name
  })
  
  const props = defineProps<{
    metrics: any,
    overdue_tasks: any[],
    last_7_days: any[],
    category_productivity: any[],
    priority_stats: any[],
    status_stats: any[],
    recent_tasks: any[],
    upcoming_tasks: any[],
  }>()
  
  const getDaysOverdue = (dueDate: string) => {
    const due = new Date(dueDate).getTime()
    const today = new Date().getTime()
    const diffTime = today - due
    return Math.max(0, Math.ceil(diffTime / (1000 * 60 * 60 * 24)))
  }
  
  const getStatusLabel = (status: string) => {
    const map: Record<string, string> = {
      pending: t('status.pending'),
      in_progress: t('status.in_progress'),
      completed: t('status.completed'),
    }
    return map[status] || status
  }
  
  const getStatusColor = (status: string) => {
    const colors: Record<string, string> = {
      pending: 'text-gray-600 bg-gray-100',
      in_progress: 'text-yellow-600 bg-yellow-100',
      completed: 'text-green-600 bg-green-100',
    }
    return colors[status] || 'text-gray-600 bg-gray-100'
  }
  
  const getPriorityLabel = (priority: string) => {
    const map: Record<string, string> = {
      low: t('priority.low'),
      medium: t('priority.medium'),
      high: t('priority.high'),
    }
    return map[priority] || priority
  }
  
  const getPriorityColor = (priority: string) => {
    const colors: Record<string, string> = {
      low: 'text-blue-600 bg-blue-100',
      medium: 'text-orange-600 bg-orange-100',
      high: 'text-red-600 bg-red-100',
    }
    return colors[priority] || 'text-gray-600 bg-gray-100'
  }
  
  const markAsCompleted = (taskId: number) => {
    if (confirm(t('dashboard.mark_completed') + '?')) {
      // ⚠️ Rotas Inertia precisam do { locale } agora:
      router.patch(routeL('tasks.updateStatus', { task: taskId }), {
        status: 'completed',
      })
    }
  }
  </script>
  