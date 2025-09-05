<template>
  <AuthenticatedLayout>
    <template #header>
      <div class="flex items-center justify-between">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
          {{ $t('tasks.view_task') }}
        </h2>
        <div class="flex space-x-2">
          <Link
            :href="route('tasks.edit', { locale: $page.props.locale, task: task.id })"
            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
          >
            {{ $t('tasks.edit') }}
          </Link>
          <Link
            :href="route('tasks.index', { locale: $page.props.locale })"
            class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded"
          >
            {{ $t('tasks.back_to_list') }}
          </Link>
        </div>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 text-gray-900 dark:text-gray-100">
            <!-- Header da Tarefa -->
            <div class="mb-6">
              <h1 class="text-3xl font-bold mb-2">{{ task.title }}</h1>
              <div class="flex items-center space-x-4 text-sm text-gray-600 dark:text-gray-400">
                <span class="flex items-center">
                  <span class="w-2 h-2 rounded-full mr-2" :class="getStatusColor(task.status)"></span>
                  {{ $t(`tasks.status.${task.status}`) }}
                </span>
                <span class="flex items-center">
                  <span class="w-2 h-2 rounded-full mr-2" :class="getPriorityColor(task.priority)"></span>
                  {{ $t(`tasks.priority.${task.priority}`) }}
                </span>
                <span v-if="task.category" class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded">
                  {{ $t(`categories.${task.category}`) }}
                </span>
              </div>
            </div>

            <!-- Informações da Tarefa -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
              <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                <h3 class="font-semibold mb-3">{{ $t('tasks.task_information') }}</h3>
                <div class="space-y-2 text-sm">
                  <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-gray-400">{{ $t('tasks.created_by') }}:</span>
                    <span>{{ task.created_by_user?.name || 'N/A' }}</span>
                  </div>
                  <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-gray-400">{{ $t('tasks.assigned_to') }}:</span>
                    <span>{{ task.assigned_to_user?.name || $t('tasks.unassigned') }}</span>
                  </div>
                  <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-gray-400">{{ $t('tasks.created_at') }}:</span>
                    <span>{{ formatDate(task.created_at) }}</span>
                  </div>
                  <div class="flex justify-between" v-if="task.due_date">
                    <span class="text-gray-600 dark:text-gray-400">{{ $t('tasks.due_date') }}:</span>
                    <span>{{ formatDate(task.due_date) }}</span>
                  </div>
                </div>
              </div>

              <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                <h3 class="font-semibold mb-3">{{ $t('tasks.progress') }}</h3>
                <div class="space-y-2 text-sm">
                  <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-gray-400">{{ $t('tasks.completion_percentage') }}:</span>
                    <span>{{ task.completion_percentage || 0 }}%</span>
                  </div>
                  <div class="w-full bg-gray-200 rounded-full h-2">
                    <div 
                      class="bg-blue-600 h-2 rounded-full transition-all duration-300" 
                      :style="{ width: (task.completion_percentage || 0) + '%' }"
                    ></div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Descrição -->
            <div class="mb-6" v-if="task.description">
              <h3 class="font-semibold mb-3">{{ $t('tasks.description') }}</h3>
              <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                <p class="whitespace-pre-wrap">{{ task.description }}</p>
              </div>
            </div>

            <!-- Tags -->
            <div class="mb-6" v-if="task.tags && task.tags.length > 0">
              <h3 class="font-semibold mb-3">{{ $t('tasks.tags') }}</h3>
              <div class="flex flex-wrap gap-2">
                <span 
                  v-for="tag in task.tags" 
                  :key="tag"
                  class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded"
                >
                  {{ tag }}
                </span>
              </div>
            </div>

            <!-- Subtarefas -->
            <div class="mb-6" v-if="task.subtasks && task.subtasks.length > 0">
              <h3 class="font-semibold mb-3">{{ $t('tasks.subtasks') }}</h3>
              <div class="space-y-2">
                <div 
                  v-for="subtask in task.subtasks" 
                  :key="subtask.id"
                  class="bg-gray-50 dark:bg-gray-700 p-3 rounded-lg flex items-center justify-between"
                >
                  <div class="flex items-center">
                    <input 
                      type="checkbox" 
                      :checked="subtask.status === 'completed'"
                      class="mr-3"
                      disabled
                    >
                    <span :class="{ 'line-through text-gray-500': subtask.status === 'completed' }">
                      {{ subtask.title }}
                    </span>
                  </div>
                  <span class="text-xs px-2 py-1 rounded" :class="getStatusBadgeClass(subtask.status)">
                    {{ $t(`tasks.status.${subtask.status}`) }}
                  </span>
                </div>
              </div>
            </div>

            <!-- Comentários -->
            <div class="mb-6" v-if="task.comments && task.comments.length > 0">
              <h3 class="font-semibold mb-3">{{ $t('tasks.comments') }}</h3>
              <div class="space-y-4">
                <div 
                  v-for="comment in task.comments" 
                  :key="comment.id"
                  class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg"
                >
                  <div class="flex justify-between items-start mb-2">
                    <span class="font-medium">{{ comment.user?.name }}</span>
                    <span class="text-xs text-gray-500">{{ formatDate(comment.created_at) }}</span>
                  </div>
                  <p class="text-sm whitespace-pre-wrap">{{ comment.content }}</p>
                </div>
              </div>
            </div>

            <!-- Anexos -->
            <div class="mb-6" v-if="task.attachments && task.attachments.length > 0">
              <h3 class="font-semibold mb-3">{{ $t('tasks.attachments') }}</h3>
              <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <div 
                  v-for="attachment in task.attachments" 
                  :key="attachment.id"
                  class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg"
                >
                  <div class="flex items-center">
                    <div class="flex-shrink-0">
                      <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                      </svg>
                    </div>
                    <div class="ml-3 flex-1 min-w-0">
                      <p class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">
                        {{ attachment.original_name }}
                      </p>
                      <p class="text-xs text-gray-500">
                        {{ formatFileSize(attachment.file_size) }}
                      </p>
                    </div>
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
import { Link } from '@inertiajs/vue3'
import { useLocale } from '@/Components/useLocale'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

const { t, formatDate } = useLocale()

const props = defineProps({
  task: Object
})

const getStatusColor = (status) => {
  const colors = {
    pending: 'bg-yellow-500',
    in_progress: 'bg-blue-500',
    completed: 'bg-green-500',
    cancelled: 'bg-red-500'
  }
  return colors[status] || 'bg-gray-500'
}

const getPriorityColor = (priority) => {
  const colors = {
    low: 'bg-green-500',
    medium: 'bg-yellow-500',
    high: 'bg-red-500'
  }
  return colors[priority] || 'bg-gray-500'
}

const getStatusBadgeClass = (status) => {
  const classes = {
    pending: 'bg-yellow-100 text-yellow-800',
    in_progress: 'bg-blue-100 text-blue-800',
    completed: 'bg-green-100 text-green-800',
    cancelled: 'bg-red-100 text-red-800'
  }
  return classes[status] || 'bg-gray-100 text-gray-800'
}


const formatFileSize = (bytes) => {
  if (bytes === 0) return '0 Bytes'
  const k = 1024
  const sizes = ['Bytes', 'KB', 'MB', 'GB']
  const i = Math.floor(Math.log(bytes) / Math.log(k))
  return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i]
}
</script>
