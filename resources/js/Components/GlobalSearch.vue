<template>
  <div class="relative">
    <!-- Search Input -->
    <div class="relative" @click.outside="showResults = false">
      <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
        <svg class="h-5 w-5 text-blue-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
        </svg>
      </div>
      <input 
        type="text"
        v-model="searchQuery"
        :placeholder="t('search.placeholder')"
        @input="handleSearch"
        @keydown.enter="performSearch"
        @focus="showResults = true"
        class="block w-full pl-10 pr-4 py-2 border border-blue-300 rounded-md leading-5 bg-white bg-opacity-20 text-white placeholder-blue-200 focus:outline-none focus:bg-white focus:text-gray-900 focus:border-white transition-all duration-200 backdrop-blur-sm"
      />
      <button 
        @click="performSearch"
        class="absolute inset-y-0 right-0 pr-3 flex items-center text-blue-200 hover:text-white transition-colors"
      >
        <kbd class="hidden sm:inline text-xs text-blue-100 px-2 py-1">Enter</kbd>
      </button>
    </div>

    <!-- Search Results Dropdown -->
    <transition
      enter-active-class="transition ease-out duration-100"
      enter-from-class="transform opacity-0 scale-95"
      enter-to-class="transform opacity-100 scale-100"
      leave-active-class="transition ease-in duration-75"
      leave-from-class="transform opacity-100 scale-100"
      leave-to-class="transform opacity-0 scale-95"
    >
      <div 
        v-if="showResults && (searchQuery || recentSearches.length > 0)"
        class="absolute z-50 w-full mt-2 bg-white border border-gray-200 rounded-lg shadow-xl overflow-hidden"
      >
        <!-- Tabs -->
        <div class="flex border-b border-gray-200 bg-gray-50">
          <button
            v-for="tab in tabs"
            :key="tab"
            @click="activeTab = tab"
            :class="[
              'flex-1 px-4 py-3 text-sm font-medium border-b-2 transition-colors',
              activeTab === tab
                ? 'text-blue-600 border-blue-600 bg-white'
                : 'text-gray-600 border-transparent hover:text-gray-900'
            ]"
          >
            {{ getTabLabel(tab) }}
            <span 
              v-if="getResultsCount(tab) > 0"
              class="ml-2 text-xs bg-blue-100 text-blue-800 px-2 py-0.5 rounded-full"
            >
              {{ getResultsCount(tab) }}
            </span>
          </button>
        </div>

        <!-- Results Content -->
        <div class="max-h-96 overflow-y-auto">
          <!-- Recent Searches -->
          <div v-if="!searchQuery && recentSearches.length > 0" class="p-3">
            <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2 px-2">
              {{ t('search.recent') }}
            </div>
            <button
              v-for="search in recentSearches"
              :key="search"
              @click="searchQuery = search; handleSearch()"
              class="w-full text-left px-3 py-2 hover:bg-gray-100 rounded-md text-sm text-gray-700 flex items-center gap-2 transition-colors"
            >
              <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
              {{ search }}
            </button>
            <div v-if="recentSearches.length > 0" class="mt-2 pt-2 border-t border-gray-200">
              <button
                @click="clearRecentSearches"
                class="w-full text-left px-3 py-2 hover:bg-gray-100 rounded-md text-xs text-gray-600 transition-colors"
              >
                {{ t('search.clear_recent') }}
              </button>
            </div>
          </div>

          <!-- Loading State -->
          <div v-else-if="loading" class="p-8 text-center">
            <div class="inline-block">
              <svg class="animate-spin h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
            </div>
          </div>

          <!-- Results -->
          <div v-else class="p-3">
            <!-- Tasks Tab -->
            <template v-if="activeTab === 'tasks'">
              <div v-if="results.tasks.length > 0" class="space-y-2">
                <Link
                  v-for="task in results.tasks"
                  :key="task.id"
                  :href="routeL('tasks.show', { id: task.id })"
                  class="block px-3 py-2 hover:bg-gray-100 rounded-md transition-colors group"
                >
                  <div class="flex items-start justify-between">
                    <div class="flex-1">
                      <div class="flex items-center gap-2">
                        <span class="text-sm font-medium text-gray-900">{{ task.title }}</span>
                        <span 
                          :class="[
                            'text-xs px-2 py-0.5 rounded-full',
                            getTaskStatusColor(task.status)
                          ]"
                        >
                          {{ getTaskStatusLabel(task.status) }}
                        </span>
                      </div>
                      <div class="text-xs text-gray-500 mt-1">
                        {{ task.description?.substring(0, 100) }}{{ task.description?.length > 100 ? '...' : '' }}
                      </div>
                      <div class="text-xs text-gray-400 mt-1 flex items-center gap-2">
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                          <path d="M5 3a2 2 0 012-2h6a2 2 0 012 2v14a2 2 0 01-2 2H7a2 2 0 01-2-2V3z"></path>
                        </svg>
                        {{ task.category || 'Sem categoria' }}
                      </div>
                    </div>
                    <span v-if="task.priority" :class="getPriorityColor(task.priority)" class="text-xs font-medium px-2 py-0.5 rounded">
                      {{ task.priority }}
                    </span>
                  </div>
                </Link>
              </div>
              <div v-else class="text-center py-6 text-gray-500 text-sm">
                {{ t('search.no_tasks') }}
              </div>
            </template>

            <!-- Users Tab -->
            <template v-if="activeTab === 'users'">
              <div v-if="results.users.length > 0" class="space-y-2">
                <div
                  v-for="user in results.users"
                  :key="user.id"
                  class="px-3 py-2 hover:bg-gray-100 rounded-md transition-colors cursor-pointer group"
                  @click="handleUserClick(user)"
                >
                  <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-gradient-to-br from-blue-400 to-blue-600 rounded-full flex items-center justify-center text-white text-xs font-semibold">
                      {{ user.name.charAt(0).toUpperCase() }}
                    </div>
                    <div class="flex-1 min-w-0">
                      <div class="text-sm font-medium text-gray-900">{{ user.name }}</div>
                      <div class="text-xs text-gray-500 truncate">{{ user.email }}</div>
                    </div>
                  </div>
                </div>
              </div>
              <div v-else class="text-center py-6 text-gray-500 text-sm">
                {{ t('search.no_users') }}
              </div>
            </template>

            <!-- Messages Tab -->
            <template v-if="activeTab === 'messages'">
              <div v-if="results.messages.length > 0" class="space-y-2">
                <div
                  v-for="message in results.messages"
                  :key="message.id"
                  class="px-3 py-2 hover:bg-gray-100 rounded-md transition-colors"
                >
                  <div class="flex items-start gap-2">
                    <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                      <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
                      </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                      <div class="text-sm font-medium text-gray-900">{{ message.title }}</div>
                      <div class="text-xs text-gray-500 truncate">{{ message.content }}</div>
                      <div class="text-xs text-gray-400 mt-1">{{ formatDate(message.created_at) }}</div>
                    </div>
                  </div>
                </div>
              </div>
              <div v-else class="text-center py-6 text-gray-500 text-sm">
                {{ t('search.no_messages') }}
              </div>
            </template>
          </div>
        </div>

        <!-- Footer -->
        <div v-if="searchQuery" class="border-t border-gray-200 bg-gray-50 px-4 py-2 text-right">
          <Link 
            :href="routeL('search.results', { q: searchQuery })"
            class="text-sm text-blue-600 hover:text-blue-700 font-medium"
          >
            {{ t('search.view_all') }} →
          </Link>
        </div>
      </div>
    </transition>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'
import { Link } from '@inertiajs/vue3'
import { useLocale } from './useLocale'

const { t, routeL } = useLocale()

const searchQuery = ref('')
const showResults = ref(false)
const loading = ref(false)
const activeTab = ref<'tasks' | 'users' | 'messages'>('tasks')
let debounceTimer: ReturnType<typeof setTimeout> | null = null

const tabs = ['tasks', 'users', 'messages'] as const

const results = ref({
  tasks: [],
  users: [],
  messages: []
})

const recentSearches = ref<string[]>([])

// Load recent searches from localStorage
const loadRecentSearches = () => {
  const saved = localStorage.getItem('yggdra_recent_searches')
  if (saved) {
    try {
      recentSearches.value = JSON.parse(saved).slice(0, 5)
    } catch {
      recentSearches.value = []
    }
  }
}

// Save to recent searches
const saveToRecentSearches = (query: string) => {
  if (query.trim().length === 0) return
  
  const searches = [query, ...recentSearches.value.filter(s => s !== query)].slice(0, 5)
  recentSearches.value = searches
  localStorage.setItem('yggdra_recent_searches', JSON.stringify(searches))
}

// Clear recent searches
const clearRecentSearches = () => {
  recentSearches.value = []
  localStorage.removeItem('yggdra_recent_searches')
}

// Handle search input with debouncing
const handleSearch = async () => {
  // Clear previous timer
  if (debounceTimer) {
    clearTimeout(debounceTimer)
  }

  if (searchQuery.value.trim().length === 0) {
    results.value = { tasks: [], users: [], messages: [] }
    return
  }

  // Set new timer
  debounceTimer = setTimeout(async () => {
    loading.value = true
    try {
      const url = new URL(routeL('api.search.global'), window.location.origin)
      url.searchParams.set('q', searchQuery.value)

      const response = await fetch(url.toString(), {
        method: 'GET',
        headers: {
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
          'Content-Type': 'application/json'
        },
        credentials: 'same-origin'
      })

      if (response.ok) {
        const data = await response.json()
        console.log('Search results:', data)
        results.value = data
        activeTab.value = 'tasks'
      } else if (response.status === 401) {
        console.warn('Not authenticated')
        results.value = { tasks: [], users: [], messages: [] }
      } else {
        console.error('Search error:', response.statusText)
      }
    } catch (error) {
      console.error('Search error:', error)
    } finally {
      loading.value = false
    }
  }, 300) // 300ms debounce
}

// Perform full search
const performSearch = () => {
  if (searchQuery.value.trim()) {
    saveToRecentSearches(searchQuery.value)
    window.location.href = routeL('search.results', { q: searchQuery.value })
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
  return results.value[tab as keyof typeof results.value]?.length || 0
}

// Get task status color
const getTaskStatusColor = (status: string) => {
  const colors: Record<string, string> = {
    pending: 'bg-yellow-100 text-yellow-800',
    in_progress: 'bg-blue-100 text-blue-800',
    completed: 'bg-green-100 text-green-800'
  }
  return colors[status] || 'bg-gray-100 text-gray-800'
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
    low: 'bg-green-100 text-green-800',
    medium: 'bg-yellow-100 text-yellow-800',
    high: 'bg-red-100 text-red-800'
  }
  return colors[priority] || 'bg-gray-100 text-gray-800'
}

// Format date
const formatDate = (date: string | Date) => {
  return new Intl.DateTimeFormat('pt-BR', {
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  }).format(new Date(date))
}

// Handle user click
const handleUserClick = (user: any) => {
  // Navigate to user profile or task assignment
  console.log('User selected:', user)
  saveToRecentSearches(user.name)
}

// Initialize
loadRecentSearches()
</script>

<style scoped>
/* Smooth transitions */
:deep(.transition) {
  transition: all 0.15s ease;
}
</style>
