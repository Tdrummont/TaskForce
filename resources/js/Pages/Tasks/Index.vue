<template>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Minhas Tarefas
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Barra de ferramentas -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <div class="flex flex-wrap gap-4 items-center justify-between">
                            <div class="flex gap-4">

                                
                                <button
                                    @click="backup"
                                    class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition flex items-center gap-2"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                                    </svg>
                                    Backup
                                </button>

                                <button
                                    @click="showRestoreModal = true"
                                    class="bg-purple-600 text-white px-4 py-2 rounded-md hover:bg-purple-700 transition flex items-center gap-2"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                    </svg>
                                    Restaurar
                                </button>

                                <button
                                    @click="showDeleteAllModal = true"
                                    class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700 transition flex items-center gap-2"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                    Apagar Todas
                                </button>
                            </div>

                            <div class="text-sm text-gray-600">
                                <p>Atalhos: <kbd class="px-2 py-1 bg-gray-200 rounded">Ctrl+N</kbd> Nova tarefa, 
                                <kbd class="px-2 py-1 bg-gray-200 rounded">Ctrl+S</kbd> Salvar, 
                                <kbd class="px-2 py-1 bg-gray-200 rounded">Esc</kbd> Cancelar</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Barra de Pesquisa e Filtros -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <div class="flex flex-wrap gap-4 items-center">
                            <!-- Barra de Pesquisa -->
                            <div class="flex-1 min-w-64">
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                        </svg>
                                    </div>
                                    <input type="text" 
                                           v-model="searchQuery"
                                           placeholder="Pesquisar tarefas por título, descrição ou tags..." 
                                           class="block w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md leading-5 bg-white text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                                </div>
                            </div>

                            <!-- Filtros -->
                            <div class="flex gap-3">
                                <!-- Filtro de Status -->
                                <select v-model="statusFilter" 
                                        class="px-3 py-2 border border-gray-300 rounded-md bg-white text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Todos os Status</option>
                                    <option value="pending">Pendentes</option>
                                    <option value="in_progress">Em Progresso</option>
                                    <option value="completed">Concluídas</option>
                                </select>

                                <!-- Filtro de Prioridade -->
                                <select v-model="priorityFilter" 
                                        class="px-3 py-2 border border-gray-300 rounded-md bg-white text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Todas as Prioridades</option>
                                    <option value="low">Baixa</option>
                                    <option value="medium">Média</option>
                                    <option value="high">Alta</option>
                                </select>

                                <!-- Filtro de Categoria -->
                                <select v-model="categoryFilter" 
                                        class="px-3 py-2 border border-gray-300 rounded-md bg-white text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Todas as Categorias</option>
                                    <option value="Trabalho">Trabalho</option>
                                    <option value="Pessoal">Pessoal</option>
                                    <option value="Estudo">Estudo</option>
                                    <option value="Saúde">Saúde</option>
                                    <option value="Lazer">Lazer</option>
                                </select>

                                <!-- Botão Limpar Filtros -->
                                <button @click="clearFilters" 
                                        class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 transition flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                    Limpar
                                </button>
                            </div>
                        </div>

                        <!-- Resultados da Pesquisa -->
                        <div v-if="searchQuery || statusFilter || priorityFilter || categoryFilter" 
                             class="mt-4 p-3 bg-blue-50 border border-blue-200 rounded-md">
                            <div class="flex items-center justify-between">
                                <div class="text-sm text-blue-800">
                                    <span class="font-medium">{{ filteredTasks.length }}</span> tarefa(s) encontrada(s)
                                    <span v-if="searchQuery" class="ml-2">para "<strong>{{ searchQuery }}</strong>"</span>
                                </div>
                                <button @click="clearFilters" 
                                        class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                    Limpar filtros
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sistema Kanban com Drag & Drop -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold mb-4">Organizar Tarefas</h3>
                        <p class="text-sm text-gray-600 mb-6">Arraste as tarefas entre as colunas para organizá-las facilmente</p>
                        
                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                            <!-- Coluna: Pendentes -->
                            <div class="bg-gray-50 rounded-xl p-4 kanban-column">
                                <div class="flex items-center justify-between mb-4">
                                    <h4 class="text-lg font-semibold text-gray-800 flex items-center">
                                        <div class="w-3 h-3 bg-yellow-500 rounded-full mr-2"></div>
                                        Pendentes
                                        <span class="ml-2 bg-yellow-100 text-yellow-800 text-xs font-medium px-2 py-1 rounded-full">
                                            {{ getTasksByStatus('pending').length }}
                                        </span>
                                    </h4>
                                    <button @click="showCreateModal = true" 
                                            class="text-gray-400 hover:text-gray-600 p-1 rounded-full hover:bg-gray-200">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                        </svg>
                                    </button>
                                </div>
                                
                                <draggable 
                                    v-model="pendingTasks" 
                                    group="tasks"
                                    class="space-y-3 min-h-[200px]"
                                    item-key="id">
                                    <template #item="{ element: task }">
                                        <div class="bg-white p-4 rounded-lg shadow-sm border-l-4 border-yellow-400 hover:shadow-md transition-shadow cursor-move task-card">
                                            <div class="flex items-start justify-between mb-2">
                                                <h5 class="font-semibold text-gray-800 text-sm">{{ task.title }}</h5>
                                                <div class="flex items-center space-x-1">
                                                    <span class="text-xs px-2 py-1 rounded-full" 
                                                          :class="getPriorityClass(task.priority)">
                                                        {{ getPriorityLabel(task.priority) }}
                                                    </span>
                                                </div>
                                            </div>
                                            
                                            <p v-if="task.description" class="text-gray-600 text-xs mb-3 line-clamp-2">
                                                {{ task.description }}
                                            </p>
                                            
                                            <div class="flex items-center justify-between text-xs text-gray-500">
                                                <div class="flex items-center space-x-2">
                                                    <span v-if="task.due_date" class="flex items-center">
                                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                        </svg>
                                                        {{ formatDate(task.due_date) }}
                                                    </span>
                                                </div>
                                                
                                                <div class="flex items-center space-x-2">
                                                    <button @click="editTask(task)" 
                                                            class="text-blue-600 hover:text-blue-800 p-1">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                        </svg>
                                                    </button>
                                                    <button @click="deleteTask(task.id)" 
                                                            class="text-red-600 hover:text-red-800 p-1">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                        </svg>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                </draggable>
                            </div>

                            <!-- Coluna: Em Progresso -->
                            <div class="bg-gray-50 rounded-xl p-4 kanban-column">
                                <div class="flex items-center justify-between mb-4">
                                    <h4 class="text-lg font-semibold text-gray-800 flex items-center">
                                        <div class="w-3 h-3 bg-orange-500 rounded-full mr-2"></div>
                                        Em Progresso
                                        <span class="ml-2 bg-orange-100 text-orange-800 text-xs font-medium px-2 py-1 rounded-full">
                                            {{ getTasksByStatus('in_progress').length }}
                                        </span>
                                    </h4>
                                    <button @click="showCreateModal = true" 
                                            class="text-gray-400 hover:text-gray-600 p-1 rounded-full hover:bg-gray-200">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                        </svg>
                                    </button>
                                </div>
                                
                                <draggable 
                                    v-model="inProgressTasks" 
                                    group="tasks"
                                    class="space-y-3 min-h-[200px]"
                                    item-key="id">
                                    <template #item="{ element: task }">
                                        <div class="bg-white p-4 rounded-lg shadow-sm border-l-4 border-orange-400 hover:shadow-md transition-shadow cursor-move task-card">
                                            <div class="flex items-start justify-between mb-2">
                                                <h5 class="font-semibold text-gray-800 text-sm">{{ task.title }}</h5>
                                                <div class="flex items-center space-x-1">
                                                    <span class="text-xs px-2 py-1 rounded-full" 
                                                          :class="getPriorityClass(task.priority)">
                                                        {{ getPriorityLabel(task.priority) }}
                                                    </span>
                                                </div>
                                            </div>
                                            
                                            <p v-if="task.description" class="text-gray-600 text-xs mb-3 line-clamp-2">
                                                {{ task.description }}
                                            </p>
                                            
                                            <div class="flex items-center justify-between text-xs text-gray-500">
                                                <div class="flex items-center space-x-2">
                                                    <span v-if="task.due_date" class="flex items-center">
                                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                        </svg>
                                                        {{ formatDate(task.due_date) }}
                                                    </span>
                                                </div>
                                                
                                                <div class="flex items-center space-x-2">
                                                    <button @click="editTask(task)" 
                                                            class="text-blue-600 hover:text-blue-800 p-1">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                        </svg>
                                                    </button>
                                                    <button @click="deleteTask(task.id)" 
                                                            class="text-red-600 hover:text-red-800 p-1">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                        </svg>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                </draggable>
                            </div>

                            <!-- Coluna: Concluídas -->
                            <div class="bg-gray-50 rounded-xl p-4 kanban-column">
                                <div class="flex items-center justify-between mb-4">
                                    <h4 class="text-lg font-semibold text-gray-800 flex items-center">
                                        <div class="w-3 h-3 bg-green-500 rounded-full mr-2"></div>
                                        Concluídas
                                        <span class="ml-2 bg-green-100 text-green-800 text-xs font-medium px-2 py-1 rounded-full">
                                            {{ getTasksByStatus('completed').length }}
                                        </span>
                                    </h4>
                                    <button @click="showCreateModal = true" 
                                            class="text-gray-400 hover:text-gray-600 p-1 rounded-full hover:bg-gray-200">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                        </svg>
                                    </button>
                                </div>
                                
                                <draggable 
                                    v-model="completedTasks" 
                                    group="tasks"
                                    class="space-y-3 min-h-[200px]"
                                    item-key="id">
                                    <template #item="{ element: task }">
                                        <div class="bg-white p-4 rounded-lg shadow-sm border-l-4 border-green-400 hover:shadow-md transition-shadow cursor-move task-card">
                                            <div class="flex items-start justify-between mb-2">
                                                <h5 class="font-semibold text-gray-800 text-sm">{{ task.title }}</h5>
                                                <div class="flex items-center space-x-1">
                                                    <span class="text-xs px-2 py-1 rounded-full" 
                                                          :class="getPriorityClass(task.priority)">
                                                        {{ getPriorityLabel(task.priority) }}
                                                    </span>
                                                </div>
                                            </div>
                                            
                                            <p v-if="task.description" class="text-gray-600 text-xs mb-3 line-clamp-2">
                                                {{ task.description }}
                                            </p>
                                            
                                            <div class="flex items-center justify-between text-xs text-gray-500">
                                                <div class="flex items-center space-x-2">
                                                    <span v-if="task.due_date" class="flex items-center">
                                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                        </svg>
                                                        {{ formatDate(task.due_date) }}
                                                    </span>
                                                </div>
                                                
                                                <div class="flex items-center space-x-2">
                                                    <button @click="editTask(task)" 
                                                            class="text-blue-600 hover:text-blue-800 p-1">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                        </svg>
                                                    </button>
                                                    <button @click="deleteTask(task.id)" 
                                                            class="text-red-600 hover:text-red-800 p-1">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
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

                <!-- Lista de Tarefas -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold mb-4">Lista de Tarefas</h3>

                        <div v-if="tasks.length === 0" class="text-gray-500 text-center py-8">
                            Nenhuma tarefa encontrada.
                        </div>

                        <div v-else class="space-y-4">
                            <div
                                v-for="task in tasks"
                                :key="task.id"
                                class="border rounded-lg p-4 hover:shadow-md transition"
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
                                        <p class="text-gray-600 mt-1">{{ task.description }}</p>

                                        <div class="flex gap-4 mt-3 text-sm">
                                            <span class="flex items-center gap-1">
                                                <span class="font-medium">Status:</span>
                                                <span :class="getStatusClass(task.status)">
                                                    {{ getStatusLabel(task.status) }}
                                                </span>
                                            </span>

                                            <span class="flex items-center gap-1">
                                                <span class="font-medium">Prioridade:</span>
                                                <span :class="getPriorityClass(task.priority)">
                                                    {{ getPriorityLabel(task.priority) }}
                                                </span>
                                            </span>

                                            <span v-if="task.due_date" class="flex items-center gap-1">
                                                <span class="font-medium">Vencimento:</span>
                                                <span :class="getDueStatusColor(task)">
                                                    {{ formatDate(task.due_date) }}
                                                    <span v-if="getDueStatusLabel(task) !== 'Normal'" class="ml-1">
                                                        ({{ getDueStatusLabel(task) }})
                                                    </span>
                                                </span>
                                            </span>
                                        </div>

                                        <div class="mt-2 text-sm text-gray-500">
                                            Criado por: {{ task.creator?.name || 'Você' }}
                                            <span v-if="task.assignee">
                                                | Atribuído a: {{ task.assignee.name }}
                                            </span>
                                        </div>
                                    </div>

                                    <div class="flex gap-2">
                                        <button
                                            @click="editTask(task)"
                                            class="text-blue-600 hover:text-blue-800"
                                        >
                                            Editar
                                        </button>
                                        <button
                                            @click="deleteTask(task.id)"
                                            class="text-red-600 hover:text-red-800"
                                        >
                                            Excluir
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal de Restauração -->
        <Modal :show="showRestoreModal" @close="showRestoreModal = false">
            <div class="p-6">
                <h3 class="text-lg font-semibold mb-4">Restaurar Backup</h3>
                <form @submit.prevent="restoreBackup">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Selecione o arquivo de backup (.json)
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
                        <button
                            type="button"
                            @click="showRestoreModal = false"
                            class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600 transition"
                        >
                            Cancelar
                        </button>
                        <button
                            type="submit"
                            class="bg-purple-600 text-white px-4 py-2 rounded-md hover:bg-purple-700 transition"
                        >
                            Restaurar
                        </button>
                    </div>
                </form>
            </div>
        </Modal>

        <!-- Modal de Confirmação para Excluir Todas as Tarefas -->
        <Modal :show="showDeleteAllModal" @close="showDeleteAllModal = false">
            <div class="p-6">
                <div class="flex items-center mb-4">
                    <div class="flex-shrink-0">
                        <svg class="h-8 w-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-lg font-semibold text-gray-900">Excluir Todas as Tarefas</h3>
                        <p class="text-sm text-gray-500">Esta ação não pode ser desfeita</p>
                    </div>
                </div>
                
                <div class="mb-6">
                    <div class="bg-red-50 border border-red-200 rounded-md p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h4 class="text-sm font-medium text-red-800">Atenção!</h4>
                                <div class="mt-2 text-sm text-red-700">
                                    <ul class="list-disc pl-5 space-y-1">
                                        <li>Todas as tarefas serão excluídas permanentemente</li>
                                        <li>Esta ação não pode ser desfeita</li>
                                        <li>Os logs de atividade serão mantidos</li>
                                        <li>Recomendamos fazer um backup antes</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-3">
                    <button
                        type="button"
                        @click="showDeleteAllModal = false"
                        class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600 transition flex items-center gap-2"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        Cancelar
                    </button>
                    <button
                        type="button"
                        @click="deleteAllTasks"
                        class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700 transition flex items-center gap-2"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                        Excluir Todas
                    </button>
                </div>
            </div>
        </Modal>


    </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Modal from '@/Components/Modal.vue';
import draggable from 'vuedraggable';

const props = defineProps({
    tasks: Array,
    stats: Object
});

const showCreateModal = ref(false);
const editingTask = ref(null);
const showRestoreModal = ref(false);
const showDeleteAllModal = ref(false);

// Variáveis de pesquisa e filtros
const searchQuery = ref('');
const statusFilter = ref('');
const priorityFilter = ref('');
const categoryFilter = ref('');

// Computed property para tarefas filtradas
const filteredTasks = computed(() => {
    let tasks = props.tasks;

    // Filtro de pesquisa por texto
    if (searchQuery.value.trim()) {
        const query = searchQuery.value.toLowerCase();
        tasks = tasks.filter(task => 
            task.title.toLowerCase().includes(query) ||
            (task.description && task.description.toLowerCase().includes(query)) ||
            (task.tags && task.tags.toLowerCase().includes(query))
        );
    }

    // Filtro de status
    if (statusFilter.value) {
        tasks = tasks.filter(task => task.status === statusFilter.value);
    }

    // Filtro de prioridade
    if (priorityFilter.value) {
        tasks = tasks.filter(task => task.priority === priorityFilter.value);
    }

    // Filtro de categoria
    if (categoryFilter.value) {
        tasks = tasks.filter(task => task.category === categoryFilter.value);
    }

    return tasks;
});

const form = useForm({
    title: '',
    description: '',
    due_date: '',
    status: 'pending',
    priority: 'medium',
    assigned_to: null
});

// Computed properties para as colunas Kanban
const pendingTasks = computed({
    get: () => filteredTasks.value.filter(task => task.status === 'pending'),
    set: (value) => {
        console.log('🔄 Computed set - pendingTasks:', value);
        console.log('📊 Tarefas atuais pendentes:', filteredTasks.value.filter(task => task.status === 'pending'));
        
        // Encontrar tarefas que mudaram de status
        const currentPending = filteredTasks.value.filter(task => task.status === 'pending');
        const newPending = value.filter(task => task.status !== 'pending');
        
        console.log('🆕 Novas tarefas pendentes:', newPending);
        
        // Atualizar apenas as que mudaram
        newPending.forEach(task => {
            console.log('🚀 Movendo tarefa para pendente:', task.id, task.title);
            updateTaskStatus(task.id, 'pending');
        });
    }
});

const inProgressTasks = computed({
    get: () => filteredTasks.value.filter(task => task.status === 'in_progress'),
    set: (value) => {
        console.log('🔄 Computed set - inProgressTasks:', value);
        
        const currentInProgress = filteredTasks.value.filter(task => task.status === 'in_progress');
        const newInProgress = value.filter(task => task.status !== 'in_progress');
        
        console.log('🆕 Novas tarefas em progresso:', newInProgress);
        
        newInProgress.forEach(task => {
            console.log('🚀 Movendo tarefa para em progresso:', task.id, task.title);
            updateTaskStatus(task.id, 'in_progress');
        });
    }
});

const completedTasks = computed({
    get: () => filteredTasks.value.filter(task => task.status === 'completed'),
    set: (value) => {
        console.log('🔄 Computed set - completedTasks:', value);
        
        const currentCompleted = filteredTasks.value.filter(task => task.status === 'completed');
        const newCompleted = value.filter(task => task.status !== 'completed');
        
        console.log('🆕 Novas tarefas concluídas:', newCompleted);
        
        newCompleted.forEach(task => {
            console.log('🚀 Movendo tarefa para concluída:', task.id, task.title);
            updateTaskStatus(task.id, 'completed');
        });
    }
});

// Funções de pesquisa e filtros
const clearFilters = () => {
    searchQuery.value = '';
    statusFilter.value = '';
    priorityFilter.value = '';
    categoryFilter.value = '';
};

// Função para excluir todas as tarefas
const deleteAllTasks = () => {
    if (confirm('⚠️ ATENÇÃO: Esta ação irá excluir TODAS as tarefas permanentemente!\n\nEsta ação não pode ser desfeita. Tem certeza que deseja continuar?')) {
        router.delete(route('tasks.deleteAll'), {
            onSuccess: () => {
                showDeleteAllModal.value = false;
                // A página será recarregada automaticamente pelo Inertia
            },
            onError: (errors) => {
                console.error('Erro ao excluir todas as tarefas:', errors);
                alert('Erro ao excluir todas as tarefas. Tente novamente.');
            }
        });
    }
};

// Função para lidar com pesquisa do layout
const handleSearchFromLayout = (event) => {
    searchQuery.value = event.detail.query;
};

// Funções auxiliares
const getTasksByStatus = (status) => {
    return filteredTasks.value.filter(task => task.status === status);
};

// Computed properties para tarefas por prazo
const overdueTasks = computed(() => 
    props.tasks.filter(task => isOverdue(task))
);

const dueTodayTasks = computed(() => 
    props.tasks.filter(task => isDueToday(task))
);

const dueSoonTasks = computed(() => 
    props.tasks.filter(task => isDueSoon(task))
);

const today = computed(() => {
    const date = new Date();
    return date.toISOString().split('T')[0];
});

// Funções para verificar status de prazo
const isOverdue = (task) => {
    if (!task.due_date || task.status === 'completed') return false;
    return new Date(task.due_date) < new Date();
};

const isDueToday = (task) => {
    if (!task.due_date || task.status === 'completed') return false;
    const today = new Date().toDateString();
    return new Date(task.due_date).toDateString() === today;
};

const isDueSoon = (task) => {
    if (!task.due_date || task.status === 'completed') return false;
    const dueDate = new Date(task.due_date);
    const today = new Date();
    const diffTime = dueDate - today;
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
    return diffDays > 0 && diffDays <= 3;
};

const getDueStatusColor = (task) => {
    if (task.status === 'completed') return 'text-green-600';
    if (isOverdue(task)) return 'text-red-600';
    if (isDueToday(task)) return 'text-orange-600';
    if (isDueSoon(task)) return 'text-yellow-600';
    return 'text-gray-600';
};

const getDueStatusLabel = (task) => {
    if (task.status === 'completed') return 'Concluída';
    if (isOverdue(task)) return 'Vencida';
    if (isDueToday(task)) return 'Vence hoje';
    if (isDueSoon(task)) return 'Vence em breve';
    return 'Normal';
};

// Funções de backup

const backup = () => {
    window.location.href = route('tasks.backup');
};

const restoreBackup = () => {
    const file = backupFileInput.value.files[0];
    if (!file) return;

    const formData = new FormData();
    formData.append('backup_file', file);

    fetch(route('tasks.restore'), {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showRestoreModal.value = false;
            window.location.reload();
        } else {
            alert('Erro ao restaurar backup: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        alert('Erro ao restaurar backup');
    });
};

// Atalhos de teclado
const handleKeydown = (event) => {
    // Ctrl+N para nova tarefa
    if (event.ctrlKey && event.key === 'n') {
        event.preventDefault();
        showCreateModal.value = true;
    }
    
    // Ctrl+S para salvar
    if (event.ctrlKey && event.key === 's') {
        event.preventDefault();
        const submitButton = document.querySelector('button[type="submit"]');
        if (submitButton && !submitButton.disabled) {
            submitButton.click();
        }
    }
    
    // Esc para cancelar/limpar
    if (event.key === 'Escape') {
        if (showCreateModal.value) {
            closeModal();
        } else {
            resetForm();
        }
    }
};

// Event listeners
onMounted(() => {
    // Event listener para pesquisa do layout
    window.addEventListener('search-tasks', handleSearchFromLayout);
    
    // Keyboard shortcuts
    document.addEventListener('keydown', handleKeydown);
    
    // Cleanup function
    return () => {
        document.removeEventListener('keydown', handleKeydown);
    };
});

onUnmounted(() => {
    // Remover event listener de pesquisa
    window.removeEventListener('search-tasks', handleSearchFromLayout);
});

const editTask = (task) => {
    // Redirecionar para a página de edição
    router.get(route('tasks.edit', task.id));
};

const deleteTask = (id) => {
    if (confirm('Tem certeza que deseja excluir esta tarefa?')) {
        router.delete(route('tasks.destroy', id), {
            onSuccess: () => {
                // Tarefa excluída com sucesso
            },
            onError: (errors) => {
                console.error('Erro ao excluir tarefa:', errors);
                alert('Erro ao excluir tarefa. Tente novamente.');
            }
        });
    }
};

// Funções de drag & drop
const updateTaskStatus = (taskId, newStatus) => {
    console.log('🚀 Atualizando tarefa', taskId, 'para status', newStatus);
    
    // Usar fetch direto para evitar problemas com Inertia
    fetch(route('tasks.updateStatus', taskId), {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            status: newStatus
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            console.log('✅ Status atualizado com sucesso:', data);
            // Recarregar a página para atualizar os dados
            window.location.reload();
        } else {
            console.error('❌ Erro ao atualizar status:', data.message);
            alert('Erro ao atualizar status da tarefa: ' + data.message);
        }
    })
    .catch(error => {
        console.error('❌ Erro na requisição:', error);
        alert('Erro ao atualizar status da tarefa. Tente novamente.');
    });
};

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('pt-BR');
};

const getStatusLabel = (status) => {
    const labels = {
        pending: 'Pendente',
        in_progress: 'Em Progresso',
        completed: 'Concluída'
    };
    return labels[status] || status;
};

const getStatusClass = (status) => {
    const classes = {
        pending: 'text-gray-600',
        in_progress: 'text-yellow-600',
        completed: 'text-green-600'
    };
    return classes[status] || '';
};

const getPriorityLabel = (priority) => {
    const labels = {
        low: 'Baixa',
        medium: 'Média',
        high: 'Alta'
    };
    return labels[priority] || priority;
};

const getPriorityClass = (priority) => {
    const classes = {
        low: 'bg-green-100 text-green-800',
        medium: 'bg-yellow-100 text-yellow-800',
        high: 'bg-red-100 text-red-800'
    };
    return classes[priority] || 'bg-gray-100 text-gray-800';
};
</script>

<style scoped>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Estilos para o drag and drop */
.sortable-ghost {
    opacity: 0.5;
    background: #c8ebfb;
    border: 2px dashed #3b82f6;
    transform: rotate(2deg);
}

.sortable-drag {
    opacity: 0.8;
    transform: rotate(5deg);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
    z-index: 1000;
}

.sortable-chosen {
    background: #f0f9ff;
    border: 2px solid #3b82f6;
}

/* Estilos para as colunas durante o drag */
.kanban-column {
    transition: all 0.3s ease;
}

.kanban-column.drag-over {
    background: #f0f9ff;
    border: 2px dashed #3b82f6;
}

/* Estilos para os cards */
.task-card {
    transition: all 0.2s ease;
    user-select: none;
}

.task-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.task-card.dragging {
    cursor: grabbing;
}
</style>
