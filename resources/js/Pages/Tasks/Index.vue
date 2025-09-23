<template>
    <AuthenticatedLayout>
      <template #header>
        <h2 class="font-semibold text-xl text-00 text-white leading-tight">
          {{ t('tasks.my_tasks') }}
        </h2>
      </template>
  
      <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
          <!-- Toolbar -->
          <div class="bg-white/10 backdrop-blur border border-white/10 text-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6">
              <div class="flex flex-wrap gap-4 items-center justify-between">
                <div class="flex gap-4">
                  <button
                    @click="backup"
                    class="bg-gradient-to-r from-[#1d4ed8] via-[#7c3aed] to-[#9333ea] text-white px-4 py-2 rounded-md hover:from-[#2563eb] hover:via-[#8b5cf6] hover:to-[#a855f7] transition flex items-center gap-2"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                    </svg>
                    {{ t('tasks.backup') }}
                  </button>
  
                  <button
                    @click="showRestoreModal = true"
                    class="bg-white/10 border border-white/10 text-white px-4 py-2 rounded-md hover:bg-white/20 transition flex items-center gap-2"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    {{ t('tasks.restore') }}
                  </button>
  
                  <button
                    @click="showDeleteAllModal = true"
                    class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700 transition flex items-center gap-2"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    {{ t('tasks.delete_all') }}
                  </button>
                </div>
  

              </div>
            </div>
          </div>
  
          <!-- Search & Filters -->
          <div class="bg-white/10 backdrop-blur border border-white/10 text-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6">
              <div class="flex flex-wrap gap-4 items-center">
                <!-- Search -->
                <div class="flex-1 min-w-64">
                  <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                      <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                      </svg>
                    </div>
                    <input
                      type="text"
                      v-model="searchQuery"
                      :placeholder="t('tasks.search_placeholder')"
                      class="block w-full pl-10 pr-4 py-2 rounded-md leading-5 bg-white/10 text-white placeholder-slate-300 border border-white/10 focus:outline-none focus:ring-2 focus:ring-[#7c3aed] focus:border-[#7c3aed] transition-all duration-200">
                  </div>
                </div>
  
                <!-- Filters -->
                <div class="flex gap-3">
                  <select v-model="statusFilter"
                    class="px-3 py-2 border border-white/10 rounded-md bg-white/10 text-white focus:outline-none focus:ring-2 focus:ring-[#7c3aed] focus:border-[#7c3aed]">
                    <option value="">{{ t('filters.all_status') }}</option>
                    <option value="pending">{{ t('status.pending') }}</option>
                    <option value="in_progress">{{ t('status.in_progress') }}</option>
                    <option value="completed">{{ t('status.completed') }}</option>
                  </select>
  
                  <select v-model="priorityFilter"
                    class="px-3 py-2 border border-white/10 rounded-md bg-white/10 text-white focus:outline-none focus:ring-2 focus:ring-[#7c3aed] focus:border-[#7c3aed]">
                    <option value="">{{ t('filters.all_priorities') }}</option>
                    <option value="low">{{ t('priority.low') }}</option>
                    <option value="medium">{{ t('priority.medium') }}</option>
                    <option value="high">{{ t('priority.high') }}</option>
                  </select>
  
                  <select v-model="categoryFilter"
                    class="px-3 py-2 border border-white/10 rounded-md bg-white/10 text-white focus:outline-none focus:ring-2 focus:ring-[#7c3aed] focus:border-[#7c3aed]">
                    <option value="">{{ t('filters.all_categories') }}</option>
                    <option value="Work">{{ t('categories.work') }}</option>
                    <option value="Personal">{{ t('categories.personal') }}</option>
                    <option value="Study">{{ t('categories.study') }}</option>
                    <option value="Health">{{ t('categories.health') }}</option>
                    <option value="Leisure">{{ t('categories.leisure') }}</option>
                  </select>
  
                  <button @click="clearFilters"
                    class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    {{ t('filters.clear') }}
                  </button>
                </div>
              </div>
  
              <!-- Search result badge -->
              <div v-if="searchQuery || statusFilter || priorityFilter || categoryFilter"
                class="mt-4 p-3 bg-white/5 border border-white/10 rounded-md text-white">
                <div class="flex items-center justify-between">
                  <div class="text-sm text-blue-800">
                    <span class="font-medium">{{ filteredTasks.length }}</span>
                    {{ t('tasks.found') }}
                    <span v-if="searchQuery" class="ml-2">
                      {{ t('tasks.for_query') }} "<strong>{{ searchQuery }}</strong>"
                    </span>
                  </div>
                  <button @click="clearFilters" class="text-indigo-300 hover:text-indigo-200 text-sm font-medium">
                    {{ t('filters.clear_filters') }}
                  </button>
                </div>
              </div>
            </div>
          </div>
  
          <!-- Kanban -->
          <div class="bg-white/10 backdrop-blur border border-white/10 text-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6">
              <h3 class="text-lg font-semibold mb-4">{{ t('tasks.organize') }}</h3>
              <p class="text-sm text-slate-300 mb-6">{{ t('tasks.drag_help') }}</p>
  
              <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Pending -->
                <div class="bg-white/5 border border-white/10 rounded-xl p-4 kanban-column">
                  <div class="flex items-center justify-between mb-4">
                    <h4 class="text-lg font-semibold text-gray-800 flex items-center">
                      <div class="w-3 h-3 bg-yellow-500 rounded-full mr-2"></div>
                      {{ t('status.pending') }}
                      <span class="ml-2 bg-yellow-100 text-yellow-800 text-xs font-medium px-2 py-1 rounded-full">
                        {{ getTasksByStatus('pending').length }}
                      </span>
                    </h4>
                    <button @click="showCreateModal = true"
                      class="text-gray-400 hover:text-gray-600 p-1 rounded-full hover:bg-gray-200">
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                      </svg>
                    </button>
                  </div>
  
                  <draggable 
                    v-model="pendingTasks" 
                    group="tasks" 
                    class="space-y-3 min-h-[200px]" 
                    item-key="id"
                    :animation="200"
                    :force-fallback="true"
                    :fallback-class="'drag-fallback'"
                    :ghost-class="'drag-ghost'"
                    :chosen-class="'drag-chosen'"
                    :drag-class="'drag-dragging'"
                    @start="onDragStart"
                    @end="onDragEnd"
                  >
                    <template #item="{ element: task }">
                      <div class="bg-white p-4 rounded-lg shadow-sm border-l-4 border-yellow-400 hover:shadow-md transition-shadow cursor-move task-card" :data-task-id="task.id">
                        <div class="flex items-start justify-between mb-2">
                      <h5 class="font-semibold text-white text-sm">{{ task.title }}</h5>
                          <div class="flex items-center space-x-1">
                            <span class="text-xs px-2 py-1 rounded-full" :class="getPriorityClass(task.priority)">
                              {{ getPriorityLabel(task.priority) }}
                            </span>
                          </div>
                        </div>
  
                        <p v-if="task.description" class="text-slate-300 text-xs mb-3 line-clamp-2">
                          {{ task.description }}
                        </p>
  
                        <div class="flex items-center justify-between text-xs text-slate-300">
                          <div class="flex items-center space-x-2">
                            <span v-if="task.due_date" class="flex items-center">
                              <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                              </svg>
                              {{ formatDate(task.due_date) }}
                            </span>
                          </div>
  
                          <div class="flex items-center space-x-2">
                            <button @click="editTask(task)" class="text-blue-600 hover:text-blue-800 p-1">
                              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                              </svg>
                            </button>
                            <button @click="deleteTask(task.id)" class="text-red-600 hover:text-red-800 p-1">
                              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                              </svg>
                            </button>
                          </div>
                        </div>
                      </div>
                    </template>
                  </draggable>
                </div>
  
                <!-- In progress -->
                <div class="bg-white/5 border border-white/10 rounded-xl p-4 kanban-column">
                  <div class="flex items-center justify-between mb-4">
                    <h4 class="text-lg font-semibold text-gray-800 flex items-center">
                      <div class="w-3 h-3 bg-orange-500 rounded-full mr-2"></div>
                      {{ t('status.in_progress') }}
                      <span class="ml-2 bg-orange-100 text-orange-800 text-xs font-medium px-2 py-1 rounded-full">
                        {{ getTasksByStatus('in_progress').length }}
                      </span>
                    </h4>
                    <button @click="showCreateModal = true"
                      class="text-gray-400 hover:text-gray-600 p-1 rounded-full hover:bg-gray-200">
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                      </svg>
                    </button>
                  </div>
  
                  <draggable 
                    v-model="inProgressTasks" 
                    group="tasks" 
                    class="space-y-3 min-h-[200px]" 
                    item-key="id"
                    :animation="200"
                    :force-fallback="true"
                    :fallback-class="'drag-fallback'"
                    :ghost-class="'drag-ghost'"
                    :chosen-class="'drag-chosen'"
                    :drag-class="'drag-dragging'"
                    @start="onDragStart"
                    @end="onDragEnd"
                  >
                    <template #item="{ element: task }">
                      <div class="bg-white p-4 rounded-lg shadow-sm border-l-4 border-orange-400 hover:shadow-md transition-shadow cursor-move task-card" :data-task-id="task.id">
                        <div class="flex items-start justify-between mb-2">
                      <h5 class="font-semibold text-white text-sm">{{ task.title }}</h5>
                          <div class="flex items-center space-x-1">
                            <span class="text-xs px-2 py-1 rounded-full" :class="getPriorityClass(task.priority)">
                              {{ getPriorityLabel(task.priority) }}
                            </span>
                          </div>
                        </div>
  
                        <p v-if="task.description" class="text-slate-300 text-xs mb-3 line-clamp-2">
                          {{ task.description }}
                        </p>
  
                        <div class="flex items-center justify-between text-xs text-slate-300">
                          <div class="flex items-center space-x-2">
                            <span v-if="task.due_date" class="flex items-center">
                              <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                              </svg>
                              {{ formatDate(task.due_date) }}
                            </span>
                          </div>
  
                          <div class="flex items-center space-x-2">
                            <button @click="editTask(task)" class="text-blue-600 hover:text-blue-800 p-1">
                              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                              </svg>
                            </button>
                            <button @click="deleteTask(task.id)" class="text-red-600 hover:text-red-800 p-1">
                              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                              </svg>
                            </button>
                          </div>
                        </div>
                      </div>
                    </template>
                  </draggable>
                </div>
  
                <!-- Completed -->
                <div class="bg-white/5 border border-white/10 rounded-xl p-4 kanban-column">
                  <div class="flex items-center justify-between mb-4">
                    <h4 class="text-lg font-semibold text-gray-800 flex items-center">
                      <div class="w-3 h-3 bg-green-500 rounded-full mr-2"></div>
                      {{ t('status.completed') }}
                      <span class="ml-2 bg-green-100 text-green-800 text-xs font-medium px-2 py-1 rounded-full">
                        {{ getTasksByStatus('completed').length }}
                      </span>
                    </h4>
                    <button @click="showCreateModal = true"
                      class="text-gray-400 hover:text-gray-600 p-1 rounded-full hover:bg-gray-200">
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                      </svg>
                    </button>
                  </div>
  
                  <draggable 
                    v-model="completedTasks" 
                    group="tasks" 
                    class="space-y-3 min-h-[200px]" 
                    item-key="id"
                    :animation="200"
                    :force-fallback="true"
                    :fallback-class="'drag-fallback'"
                    :ghost-class="'drag-ghost'"
                    :chosen-class="'drag-chosen'"
                    :drag-class="'drag-dragging'"
                    @start="onDragStart"
                    @end="onDragEnd"
                  >
                    <template #item="{ element: task }">
                      <div class="bg-white p-4 rounded-lg shadow-sm border-l-4 border-green-400 hover:shadow-md transition-shadow cursor-move task-card" :data-task-id="task.id">
                        <div class="flex items-start justify-between mb-2">
                      <h5 class="font-semibold text-white text-sm">{{ task.title }}</h5>
                          <div class="flex items-center space-x-1">
                            <span class="text-xs px-2 py-1 rounded-full" :class="getPriorityClass(task.priority)">
                              {{ getPriorityLabel(task.priority) }}
                            </span>
                          </div>
                        </div>
  
                        <p v-if="task.description" class="text-slate-300 text-xs mb-3 line-clamp-2">
                          {{ task.description }}
                        </p>
  
                        <div class="flex items-center justify-between text-xs text-slate-300">
                          <div class="flex items-center space-x-2">
                            <span v-if="task.due_date" class="flex items-center">
                              <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                              </svg>
                              {{ formatDate(task.due_date) }}
                            </span>
                          </div>
  
                          <div class="flex items-center space-x-2">
                            <button @click="editTask(task)" class="text-blue-600 hover:text-blue-800 p-1">
                              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                              </svg>
                            </button>
                            <button @click="deleteTask(task.id)" class="text-red-600 hover:text-red-800 p-1">
                              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                              </svg>
                            </button>
                          </div>
                        </div>
                      </div>
                    </template>
                  </draggable>
                </div>
              </div>
            </div>
          </div>
  
          <!-- Task list -->
          <div class="bg-white/10 backdrop-blur border border-white/10 text-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
              <h3 class="text-lg font-semibold mb-4">{{ t('tasks.list_title') }}</h3>
  
              <div v-if="tasks.length === 0" class="text-slate-300 text-center py-8">
                {{ t('tasks.none_found') }}
              </div>
  
              <div v-else class="space-y-4">
                <div
                  v-for="task in tasks"
                  :key="task.id"
                  class="border border-white/10 rounded-lg p-4 hover:shadow-md transition bg-white/5 text-white"
                  :class="{
                    'border-green-300 bg-green-50': task.status === 'completed',
                    'border-yellow-300 bg-yellow-50': task.status === 'in_progress',
                    'border-gray-300': task.status === 'pending',
                    'border-red-300 bg-red-50': isOverdue(task),
                    'border-orange-300 bg-orange-50': isDueToday(task)
                  }"
                >
                  <div class="flex justify-between items-start">
                    <div class="flex-1">
                      <h4 class="font-semibold text-lg">{{ task.title }}</h4>
                      <p class="text-slate-300 mt-1">{{ task.description }}</p>
  
                      <div class="flex gap-4 mt-3 text-sm">
                        <span class="flex items-center gap-1">
                          <span class="font-medium">{{ t('tasks.status') }}:</span>
                          <span :class="getStatusClass(task.status)">{{ getStatusLabel(task.status) }}</span>
                        </span>
  
                        <span class="flex items-center gap-1">
                          <span class="font-medium">{{ t('tasks.priority') }}:</span>
                          <span :class="getPriorityClass(task.priority)">{{ getPriorityLabel(task.priority) }}</span>
                        </span>
  
                        <span v-if="task.due_date" class="flex items-center gap-1">
                          <span class="font-medium">{{ t('tasks.due') }}:</span>
                          <span :class="getDueStatusColor(task)">
                            {{ formatDate(task.due_date) }}
                            <span v-if="getDueStatusLabel(task) !== t('due.normal')" class="ml-1">
                              ({{ getDueStatusLabel(task) }})
                            </span>
                          </span>
                        </span>
                      </div>
  
                      <div class="mt-2 text-sm text-slate-300">
                        {{ t('tasks.created_by') }}: {{ task.creator?.name || t('tasks.you') }}
                        <span v-if="task.assignee">
                          | {{ t('tasks.assigned_to') }}: {{ task.assignee.name }}
                        </span>
                      </div>
                    </div>
  
                    <div class="flex gap-2">
                      <button @click="editTask(task)" class="text-blue-600 hover:text-blue-800">
                        {{ t('tasks.edit') }}
                      </button>
                      <button @click="deleteTask(task.id)" class="text-red-600 hover:text-red-800">
                        {{ t('tasks.delete') }}
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
  
          <!-- Restore Modal -->
          <Modal :show="showRestoreModal" @close="showRestoreModal = false">
            <div class="p-6">
              <h3 class="text-lg font-semibold mb-4">{{ t('tasks.restore_backup') }}</h3>
              <form @submit.prevent="restoreBackup">
                <div class="mb-4">
                  <label class="block text-sm font-medium text-gray-700 mb-2">
                    {{ t('tasks.select_backup_file') }}
                  </label>
                  <input
                    ref="backupFileInput"
                    type="file"
                    accept=".json"
                    class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                    required
                  />
                </div>
                <div class="flex justify-end gap-2">
                  <button type="button" @click="showRestoreModal = false" class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600 transition">
                    {{ t('common.cancel') }}
                  </button>
                  <button type="submit" class="bg-purple-600 text-white px-4 py-2 rounded-md hover:bg-purple-700 transition">
                    {{ t('tasks.restore') }}
                  </button>
                </div>
              </form>
            </div>
          </Modal>
  
          <!-- Delete All Modal -->
          <Modal :show="showDeleteAllModal" @close="showDeleteAllModal = false">
            <div class="p-6">
              <div class="flex items-center mb-4">
                <div class="flex-shrink-0">
                  <svg class="h-8 w-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                  </svg>
                </div>
                <div class="ml-3">
                  <h3 class="text-lg font-semibold text-gray-900">{{ t('tasks.delete_all') }}</h3>
                  <p class="text-sm text-gray-500">{{ t('tasks.cannot_undo') }}</p>
                </div>
              </div>
  
              <div class="mb-6">
                <div class="bg-red-50 border border-red-200 rounded-md p-4">
                  <div class="flex">
                    <div class="flex-shrink-0">
                      <svg class="h-5 w-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                      </svg>
                    </div>
                    <div class="ml-3">
                      <h4 class="text-sm font-medium text-red-800">{{ t('common.attention') }}</h4>
                      <div class="mt-2 text-sm text-red-700">
                        <ul class="list-disc pl-5 space-y-1">
                          <li>{{ t('tasks.delete_all_warn1') }}</li>
                          <li>{{ t('tasks.delete_all_warn2') }}</li>
                          <li>{{ t('tasks.delete_all_warn3') }}</li>
                          <li>{{ t('tasks.delete_all_warn4') }}</li>
                        </ul>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
  
              <div class="flex justify-end gap-3">
                <button type="button" @click="showDeleteAllModal = false"
                  class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600 transition flex items-center gap-2">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M6 18L18 6M6 6l12 12" />
                  </svg>
                  {{ t('common.cancel') }}
                </button>
                <button type="button" @click="deleteAllTasks"
                  class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700 transition flex items-center gap-2">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                  </svg>
                  {{ t('tasks.delete_all') }}
                </button>
              </div>
            </div>
          </Modal>
        </div>
      </div>
    </AuthenticatedLayout>
  </template>
  
  <script setup>
  import { ref, computed, onMounted, onUnmounted } from 'vue'
  import { useForm, router } from '@inertiajs/vue3'
  import { useLocale } from '@/Components/useLocale'
  import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
  import Modal from '@/Components/Modal.vue'
  import draggable from 'vuedraggable'
  
  const { t, formatDate, routeL } = useLocale()
  
  const props = defineProps({
    tasks: Array,
    stats: Object
  })
  
  const showCreateModal = ref(false)
  const editingTask = ref(null)
  const showRestoreModal = ref(false)
  const showDeleteAllModal = ref(false)
  
  const searchQuery = ref('')
  const statusFilter = ref('')
  const priorityFilter = ref('')
  const categoryFilter = ref('')
  
  const filteredTasks = computed(() => {
    let tasks = props.tasks || []
    if (searchQuery.value.trim()) {
      const q = searchQuery.value.toLowerCase()
      tasks = tasks.filter(task =>
        task.title?.toLowerCase().includes(q) ||
        task.description?.toLowerCase().includes(q) ||
        task.tags?.toLowerCase().includes(q)
      )
    }
    if (statusFilter.value) tasks = tasks.filter(t => t.status === statusFilter.value)
    if (priorityFilter.value) tasks = tasks.filter(t => t.priority === priorityFilter.value)
    if (categoryFilter.value) tasks = tasks.filter(t => t.category === categoryFilter.value)
    return tasks
  })
  
  const form = useForm({
    title: '',
    description: '',
    due_date: '',
    status: 'pending',
    priority: 'medium',
    assigned_to: null
  })
  
  // Kanban columns (with D&D setters calling API)
  const pendingTasks = computed({
    get: () => filteredTasks.value.filter(t => t.status === 'pending'),
    set: (value) => {
      value.filter(t => t.status !== 'pending').forEach(t => updateTaskStatus(t.id, 'pending'))
    }
  })
  
  const inProgressTasks = computed({
    get: () => filteredTasks.value.filter(t => t.status === 'in_progress'),
    set: (value) => {
      value.filter(t => t.status !== 'in_progress').forEach(t => updateTaskStatus(t.id, 'in_progress'))
    }
  })
  
  const completedTasks = computed({
    get: () => filteredTasks.value.filter(t => t.status === 'completed'),
    set: (value) => {
      value.filter(t => t.status !== 'completed').forEach(t => updateTaskStatus(t.id, 'completed'))
    }
  })
  
  const clearFilters = () => {
    searchQuery.value = ''
    statusFilter.value = ''
    priorityFilter.value = ''
    categoryFilter.value = ''
  }
  
  const deleteAllTasks = () => {
    if (confirm(t('tasks.confirm_delete_all'))) {
      router.delete(routeL('tasks.deleteAll'), {
        onSuccess: () => { showDeleteAllModal.value = false },
        onError: (e) => {
          console.error(e)
          alert(t('toast.error'))
        }
      })
    }
  }
  
  const handleSearchFromLayout = (event) => {
    searchQuery.value = event.detail.query
  }
  
  const getStatusClass = (status) => {
    const classes = {
      pending: 'text-gray-600',
      in_progress: 'text-yellow-600',
      completed: 'text-green-600'
    }
    return classes[status] || 'text-gray-600'
  }

  const getStatusLabel = (status) => {
    const labels = {
      pending: t('status.pending'),
      in_progress: t('status.in_progress'),
      completed: t('status.completed')
    }
    return labels[status] || status
  }

  const getPriorityClass = (priority) => {
    const classes = {
      low: 'bg-green-100 text-green-800',
      medium: 'bg-yellow-100 text-yellow-800',
      high: 'bg-red-100 text-red-800'
    }
    return classes[priority] || 'bg-gray-100 text-gray-800'
  }

  const getPriorityLabel = (priority) => {
    const labels = {
      low: t('priority.low'),
      medium: t('priority.medium'),
      high: t('priority.high')
    }
    return labels[priority] || priority
  }

  const getTasksByStatus = (status) => filteredTasks.value.filter(t => t.status === status)
  
  const isOverdue = (task) => task.due_date && task.status !== 'completed' && (new Date(task.due_date) < new Date())
  const isDueToday = (task) => {
    if (!task.due_date || task.status === 'completed') return false
    return new Date(task.due_date).toDateString() === new Date().toDateString()
  }
  const isDueSoon = (task) => {
    if (!task.due_date || task.status === 'completed') return false
    const d = new Date(task.due_date).getTime() - Date.now()
    const days = Math.ceil(d / 86400000)
    return days > 0 && days <= 3
  }
  
  const getDueStatusColor = (task) => {
    if (task.status === 'completed') return 'text-green-600'
    if (isOverdue(task)) return 'text-red-600'
    if (isDueToday(task)) return 'text-orange-600'
    if (isDueSoon(task)) return 'text-yellow-600'
    return 'text-gray-600'
  }
  
  const getDueStatusLabel = (task) => {
    if (task.status === 'completed') return t('due.completed')
    if (isOverdue(task)) return t('due.overdue')
    if (isDueToday(task)) return t('due.today')
    if (isDueSoon(task)) return t('due.soon')
    return t('due.normal')
  }
  
  const backup = () => {
    window.location.href = routeL('tasks.backup')
  }
  
  const restoreBackup = () => {
    const file = backupFileInput.value?.files?.[0]
    if (!file) return
  
    const formData = new FormData()
    formData.append('backup_file', file)
  
    fetch(routeL('tasks.restore'), {
      method: 'POST',
      body: formData,
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
      }
    })
      .then(r => r.json())
      .then(data => {
        if (data.success) {
          showRestoreModal.value = false
          window.location.reload()
        } else {
          alert(t('toast.error') + ': ' + (data.message || ''))
        }
      })
      .catch(err => {
        console.error(err)
        alert(t('toast.error'))
      })
  }
  

  
  onMounted(() => {
    window.addEventListener('search-tasks', handleSearchFromLayout)
  })
  onUnmounted(() => {
    window.removeEventListener('search-tasks', handleSearchFromLayout)
  })
  
  // Drag & Drop functions
  const onDragStart = (evt) => {
    evt.item.classList.add('drag-start')
    document.body.classList.add('dragging')
  }
  
  const onDragEnd = (evt) => {
    evt.item.classList.remove('drag-start')
    document.body.classList.remove('dragging')
    
    // Update task status if moved to different column
    const taskId = evt.item.querySelector('.task-card').getAttribute('data-task-id')
    const newStatus = getStatusFromColumn(evt.to)
    
    if (newStatus && taskId) {
      updateTaskStatus(taskId, newStatus)
    }
  }
  
  const getStatusFromColumn = (column) => {
    if (column.closest('.kanban-column').querySelector('.bg-yellow-500')) {
      return 'pending'
    } else if (column.closest('.kanban-column').querySelector('.bg-orange-500')) {
      return 'in_progress'
    } else if (column.closest('.kanban-column').querySelector('.bg-green-500')) {
      return 'completed'
    }
    return null
  }

  const editTask = (task) => {
    router.get(routeL('tasks.edit', { task: task.id }))
  }
  
  const deleteTask = (id) => {
    if (confirm(t('tasks.confirm_delete'))) {
      router.delete(routeL('tasks.destroy', { task: id }), {
        onError: () => alert(t('toast.error'))
      })
    }
  }
  
  const updateTaskStatus = (taskId, newStatus) => {
    fetch(routeL('tasks.updateStatus', { task: taskId }), {
      method: 'PATCH',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        'Accept': 'application/json'
      },
      body: JSON.stringify({ status: newStatus })
    })
      .then(r => r.json())
      .then(data => {
        if (data.success) window.location.reload()
        else alert(t('toast.error') + ': ' + (data.message || ''))
      })
      .catch(() => alert(t('toast.error')))
  }
  
  // Refs
  const backupFileInput = ref(null)
  </script>
  
  <style scoped>
  .line-clamp-2 {line-clamp:2; -webkit-line-clamp:2; box-orient:vertical; -webkit-box-orient:vertical; display:-webkit-box; overflow:hidden;}
  
  /* Enhanced Drag & Drop Styles */
  .drag-ghost {
    opacity: 0.4;
    background: #f3f4f6;
    border: 2px dashed #6b7280;
    transform: rotate(2deg);
    transition: all 0.3s cubic-bezier(0.23, 1, 0.32, 1);
  }
  
  .drag-chosen {
    background: #f0f9ff;
    border: 2px solid #3b82f6;
    transform: scale(1.02);
    box-shadow: 0 8px 25px rgba(59, 130, 246, 0.15);
    transition: all 0.3s cubic-bezier(0.23, 1, 0.32, 1);
  }
  
  .drag-dragging {
    opacity: 0.8;
    transform: rotate(5deg) scale(1.05);
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
    z-index: 1000;
    transition: all 0.3s cubic-bezier(0.23, 1, 0.32, 1);
  }
  
  .drag-fallback {
    opacity: 0.6;
    background: #e5e7eb;
    border: 2px dashed #9ca3af;
    transform: rotate(3deg);
  }
  
  .kanban-column {
    transition: all 0.3s cubic-bezier(0.23, 1, 0.32, 1);
    position: relative;
  }
  
  .kanban-column.drag-over {
    background: #f0f9ff;
    border: 2px dashed #3b82f6;
    transform: scale(1.02);
  }
  
  .task-card {
    transition: all 0.3s cubic-bezier(0.23, 1, 0.32, 1);
    user-select: none;
    cursor: grab;
  }
  
  .task-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
  }
  
  .task-card.drag-start {
    cursor: grabbing;
    transform: scale(1.02);
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
  }
  
  .task-card:active {
    cursor: grabbing;
  }
  
  /* Body dragging state */
  body.dragging {
    cursor: grabbing;
  }
  
  body.dragging * {
    cursor: grabbing !important;
  }
  
  /* Smooth animations for all drag states */
  .drag-ghost,
  .drag-chosen,
  .drag-dragging,
  .drag-fallback {
    transition: all 0.3s cubic-bezier(0.23, 1, 0.32, 1);
  }
  
  /* Enhanced visual feedback */
  .kanban-column:hover {
    background: rgba(249, 250, 251, 0.8);
  }
  
  .task-card:hover .task-actions {
    opacity: 1;
  }
  
  .task-actions {
    opacity: 0.7;
    transition: opacity 0.2s ease;
  }
  </style>
  