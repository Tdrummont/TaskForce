<template>
  <AuthenticatedLayout>
    <template #header>
      <div class="flex items-center justify-between">
        <div>
          <h2 class="text-2xl font-bold text-white">{{ t('search.placeholder') }}</h2>
          <p class="text-blue-100 text-sm mt-1">{{ resultsCount }} {{ t('tasks.found') }}</p>
        </div>
      </div>
    </template>

    <div class="max-w-7xl mx-auto">
      <!-- Search Bar -->
      <div class="mb-6">
        <div class="relative">
          <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
          </div>
          <input
            type="text"
            v-model="searchInput"
            @keydown.enter="performSearch"
            :placeholder="t('search.placeholder')"
            class="block w-full pl-10 pr-4 py-3 rounded-lg leading-5 bg-white/10 text-white placeholder-slate-300 border border-white/10 focus:outline-none focus:ring-2 focus:ring-[#7c3aed] focus:border-[#7c3aed] transition-all duration-200"
          />
        </div>
      </div>

      <!-- Tabs -->
      <div class="flex gap-4 mb-6 border-b border-white/10">
        <button
          v-for="tab in ['tasks', 'users', 'messages']"
          :key="tab"
          @click="activeTab = tab"
          :class="[
            'px-4 py-3 text-sm font-medium border-b-2 transition-colors',
            activeTab === tab
              ? 'text-blue-400 border-blue-400'
              : 'text-gray-400 border-transparent hover:text-gray-300'
          ]"
        >
          {{ getTabLabel(tab) }}
          <span v-if="getResultsCount(tab) > 0" class="ml-2 text-xs bg-blue-500/20 text-blue-300 px-2 py-0.5 rounded-full">
            {{ getResultsCount(tab) }}
          </span>
        </button>
      </div>

      <!-- Results Content -->
      <div class="bg-white/10 backdrop-blur border border-white/10 rounded-lg overflow-hidden">
        <!-- Tasks Tab -->
        <template v-if="activeTab === 'tasks'">
          <div v-if="results.tasks && results.tasks.data.length > 0" class="divide-y divide-white/10">
            <Link
              v-for="task in results.tasks.data"
              :key="task.id"
              :href="routeL('tasks.show', { id: task.id })"
              class="block p-4 hover:bg-white/5 transition-colors group"
            >
              <div class="flex items-start justify-between gap-4">
                <div class="flex-1 min-w-0">
                  <div class="flex items-center gap-3 mb-2">
                    <h3 class="text-lg font-semibold text-white group-hover:text-blue-300 truncate">
                      {{ task.title }}
                    </h3>
                    <span :class="getTaskStatusColor(task.status)" class="text-xs px-2 py-1 rounded-full flex-shrink-0">
                      {{ getTaskStatusLabel(task.status) }}
                    </span>
                  </div>
                  <p v-if="task.description" class="text-sm text-gray-300 truncate mb-2">
                    {{ task.description.substring(0, 150) }}{{ task.description.length > 150 ? '...' : '' }}
                  </p>
                  <div class="flex items-center gap-4 text-xs text-gray-400">
                    <div v-if="task.category" class="flex items-center gap-1">
                      <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M5 3a2 2 0 012-2h6a2 2 0 012 2v14a2 2 0 01-2 2H7a2 2 0 01-2-2V3z"></path>
                      </svg>
                      {{ task.category }}
                    </div>
                    <div class="flex items-center gap-1">
                      <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z"></path>
                      </svg>
                      {{ formatDate(task.created_at) }}
                    </div>
                  </div>
                </div>
                <div class="flex flex-col gap-2 items-end flex-shrink-0">
                  <span v-if="task.priority" :class="getPriorityColor(task.priority)" class="text-xs font-medium px-2 py-1 rounded">
                    {{ getPriorityLabel(task.priority) }}
                  </span>
                  <span v-if="task.assigned_to" class="text-xs text-gray-400">
                    {{ t('tasks.assigned_to') }}: {{ task.assigned_to.name }}
                  </span>
                </div>
              </div>
            </Link>
          </div>
          <div v-else class="p-8 text-center text-gray-400">
            {{ t('search.no_tasks') }}
          </div>

          <!-- Pagination -->
          <div v-if="results.tasks && results.tasks.links" class="p-4 border-t border-white/10">
            <div class="flex justify-center gap-2">
              <template v-for="link in results.tasks.links" :key="link.url || link.label">
                <button
                  v-if="link.url"
                  @click="goToPage(link.url)"
                  :class="[
                    'px-3 py-2 rounded text-sm transition-colors',
                    link.active
                      ? 'bg-blue-600 text-white'
                      : 'bg-white/10 text-gray-300 hover:bg-white/20'
                  ]"
                >
                  {{ link.label }}
                </button>
                <span v-else class="px-3 py-2 text-sm text-gray-500">
                  {{ link.label }}
                </span>
              </template>
            </div>
          </div>
        </template>

        <!-- Users Tab -->
        <template v-if="activeTab === 'users'">
          <div v-if="results.users && results.users.data.length > 0" class="divide-y divide-white/10">
            <div
              v-for="user in results.users.data"
              :key="user.id"
              class="p-4 hover:bg-white/5 transition-colors flex items-center justify-between"
            >
              <div class="flex items-center gap-4">
                <div class="w-10 h-10 bg-gradient-to-br from-blue-400 to-blue-600 rounded-full flex items-center justify-center text-white font-semibold">
                  {{ user.name.charAt(0).toUpperCase() }}
                </div>
                <div>
                  <h3 class="text-white font-semibold">{{ user.name }}</h3>
                  <p class="text-sm text-gray-400">{{ user.email }}</p>
                </div>
              </div>
              <div class="text-right">
                <p class="text-sm text-gray-400">
                  <span class="font-semibold text-white">{{ user.assigned_tasks_count }}</span>
                  {{ t('tasks.assigned_to') }}
                </p>
              </div>
            </div>
          </div>
          <div v-else class="p-8 text-center text-gray-400">
            {{ t('search.no_users') }}
          </div>
        </template>

        <!-- Messages Tab -->
        <template v-if="activeTab === 'messages'">
          <div v-if="results.messages && results.messages.data.length > 0" class="divide-y divide-white/10">
            <div
              v-for="message in results.messages.data"
              :key="message.id"
              class="p-4 hover:bg-white/5 transition-colors"
            >
              <Link :href="routeL('tasks.show', { id: message.task_id })" class="block group">
                <div class="flex items-start gap-3 mb-2">
                  <div class="w-8 h-8 bg-green-500/20 rounded-full flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
                    </svg>
                  </div>
                  <div class="flex-1">
                    <h3 class="text-white font-semibold group-hover:text-blue-300">
                      {{ message.task.title }}
                    </h3>
                    <p class="text-sm text-gray-300 mt-1">
                      {{ message.content.substring(0, 100) }}{{ message.content.length > 100 ? '...' : '' }}
                    </p>
                    <p class="text-xs text-gray-500 mt-2">
                      {{ formatDate(message.created_at) }}
                    </p>
                  </div>
                </div>
              </Link>
            </div>
          </div>
          <div v-else class="p-8 text-center text-gray-400">
            {{ t('search.no_messages') }}
          </div>
        </template>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { Link, usePage, router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { useLocale } from '@/Components/useLocale'

const { t, routeL, formatDate } = useLocale()
const page = usePage()

const activeTab = ref('tasks')
const searchInput = ref(page.props.query as string || '')

const results = ref(page.props.results as any || {
  tasks: { data: [], links: [] },
  users: { data: [], links: [] },
  messages: { data: [], links: [] }
})

const resultsCount = ref(page.props.totalCount as number || 0)

// Perform search
const performSearch = () => {
  if (searchInput.value.trim()) {
    router.get(routeL('search.results'), {
      q: searchInput.value,
      tab: activeTab.value
    })
  }
}

// Get tab label
const getTabLabel = (tab: string) => {
  const labels: Record<string, string> = {
    tasks: t('search.tab_tasks'),
    users: t('search.tab_users'),
    messages: t('search.tab_messages')
  }
  return labels[tab] || tab
}

// Get results count
const getResultsCount = (tab: string) => {
  return results.value[tab]?.data?.length || 0
}

// Get task status color
const getTaskStatusColor = (status: string) => {
  const colors: Record<string, string> = {
    pending: 'bg-yellow-500/20 text-yellow-300',
    in_progress: 'bg-blue-500/20 text-blue-300',
    completed: 'bg-green-500/20 text-green-300'
  }
  return colors[status] || 'bg-gray-500/20 text-gray-300'
}

// Get task status label
const getTaskStatusLabel = (status: string) => {
  const labels: Record<string, string> = {
    pending: t('status.pending'),
    in_progress: t('status.in_progress'),
    completed: t('status.completed')
  }
  return labels[status] || status
}

// Get priority color
const getPriorityColor = (priority: string) => {
  const colors: Record<string, string> = {
    low: 'bg-green-500/20 text-green-300',
    medium: 'bg-yellow-500/20 text-yellow-300',
    high: 'bg-red-500/20 text-red-300'
  }
  return colors[priority] || 'bg-gray-500/20 text-gray-300'
}

// Get priority label
const getPriorityLabel = (priority: string) => {
  const labels: Record<string, string> = {
    low: t('priority.low'),
    medium: t('priority.medium'),
    high: t('priority.high')
  }
  return labels[priority] || priority
}

// Go to page
const goToPage = (url: string) => {
  router.visit(url)
}

// Update results on page load
onMounted(() => {
  results.value = page.props.results as any
  resultsCount.value = page.props.totalCount as number
})
</script>
