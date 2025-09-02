<script setup>
import { ref, watch, onMounted, onUnmounted } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import QuickTaskModal from '@/Components/QuickTaskModal.vue';
import RealTimeNotifications from '@/Components/RealTimeNotifications.vue';
import EmailNotificationSnackbar from '@/Components/EmailNotificationSnackbar.vue';
import LanguageSelector from '@/Components/LanguageSelector.vue';
import { Link, useForm, router, usePage } from '@inertiajs/vue3';

const props = defineProps({
    showingNavigation: {
        type: Boolean,
        default: false
    }
});

// Inertia page
const $page = usePage();

const showingNavigationDropdown = ref(false);
const showUserMenu = ref(false);
const searchQuery = ref('');
const showFabMenu = ref(false);
const showQuickTaskModal = ref(false);
const showNotifications = ref(false);
const notifications = ref([]);
const unreadCount = ref(0);

// Sincronizar com a prop externa
watch(() => props.showingNavigation, (newValue) => {
    showingNavigationDropdown.value = newValue;
});

const toggleSidebar = () => {
    showingNavigationDropdown.value = !showingNavigationDropdown.value;
};

const toggleUserMenu = () => {
    showUserMenu.value = !showUserMenu.value;
};

const logout = () => {
    useForm().post(route('logout'))
};

const performSearch = () => {
    if (searchQuery.value.trim()) {
        // Se estiver na página de tarefas, usar filtros locais
        if (route().current('tasks.*')) {
            // Emitir evento para a página de tarefas
            window.dispatchEvent(new CustomEvent('search-tasks', {
                detail: { query: searchQuery.value }
            }));
        } else {
            // Navegar para a página de tarefas com a pesquisa
            router.get(route('tasks.index'), { search: searchQuery.value });
        }
    }
};

const handleSearchKeydown = (event) => {
    if (event.key === 'Enter') {
        performSearch();
    }
};

// Funções do FAB (Floating Action Button)
const toggleFab = () => {
    console.log('Toggle FAB clicked');
    showFabMenu.value = !showFabMenu.value;
    console.log('showFabMenu:', showFabMenu.value);
};

const openNewTaskModal = () => {
    console.log('Opening new task modal');
    showFabMenu.value = false;
    console.log('Navigating to:', route('tasks.create'));
    router.get(route('tasks.create'));
};

const openQuickTaskModal = () => {
    console.log('Opening quick task modal');
    showFabMenu.value = false;
    showQuickTaskModal.value = true;
};

const closeQuickTaskModal = () => {
    showQuickTaskModal.value = false;
};

const goToTasks = () => {
    console.log('Going to tasks');
    showFabMenu.value = false;
    console.log('Navigating to:', route('tasks.index'));
    router.get(route('tasks.index'));
};

// Funções de notificações
const toggleNotifications = () => {
    console.log('🔔 toggleNotifications chamado!');
    console.log('📊 Estado atual:', { showNotifications: showNotifications.value, showUserMenu: showUserMenu.value });
    
    showNotifications.value = !showNotifications.value;
    showUserMenu.value = false; // Fechar menu do usuário se estiver aberto
    
    console.log('🔄 Estado após toggle:', { showNotifications: showNotifications.value, showUserMenu: showUserMenu.value });
    
    if (showNotifications.value) {
        console.log('✅ Dropdown aberto, carregando notificações...');
        loadNotifications();
    } else {
        console.log('❌ Dropdown fechado');
    }
};

const loadNotifications = async () => {
    console.log('🚀 loadNotifications INICIADA!');
    try {
        console.log('🔍 Carregando notificações...');
        console.log('👤 Usuário logado:', $page.props.auth.user);
        
        const response = await fetch('/api/notifications', {
            credentials: 'same-origin',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        
        console.log('📡 Response status:', response.status);
        console.log('📡 Response headers:', Object.fromEntries(response.headers.entries()));
        
        if (response.ok) {
            const data = await response.json();
            console.log('📊 Dados recebidos:', data);
            console.log('📊 Estrutura dos dados:', Object.keys(data));
            
            if (data.success && data.notifications) {
                notifications.value = data.notifications;
                console.log('✅ Notificações carregadas:', notifications.value.length);
                console.log('📝 Primeira notificação:', notifications.value[0]);
            } else {
                console.warn('⚠️ API retornou sucesso=false ou sem notificações:', data);
                notifications.value = [];
            }
        } else {
            console.error('❌ Erro na resposta:', response.status, response.statusText);
            const text = await response.text();
            console.error('📄 Conteúdo da resposta:', text);
        }
    } catch (error) {
        console.error('❌ Erro ao carregar notificações:', error);
        console.error('📋 Stack trace:', error.stack);
    }
};

const loadUnreadCount = async () => {
    try {
        console.log('🔍 Carregando contagem de não lidas...');
        const response = await fetch('/api/notifications/unread-count', {
            credentials: 'same-origin',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        });
        
        console.log('📡 Response status:', response.status);
        
        if (response.ok) {
            const data = await response.json();
            console.log('📊 Contagem recebida:', data);
            
            if (data.success) {
                unreadCount.value = data.count;
                console.log('✅ Contagem atualizada:', unreadCount.value);
            }
        } else {
            console.error('❌ Erro na resposta:', response.status, response.statusText);
        }
    } catch (error) {
        console.error('❌ Erro ao carregar contagem:', error);
    }
};

const markAsRead = async (notificationId) => {
    try {
        console.log('🔍 Marcando notificação como lida:', notificationId);
        const response = await fetch(`/api/notifications/${notificationId}/mark-read`, {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        });
        
        if (response.ok) {
            const notification = notifications.value.find(n => n.id === notificationId);
            if (notification) {
                notification.read_at = new Date().toISOString();
            }
            await loadUnreadCount();
            console.log('✅ Notificação marcada como lida');
        }
    } catch (error) {
        console.error('❌ Erro ao marcar como lido:', error);
    }
};

const markAllAsRead = async () => {
    try {
        console.log('🔍 Marcando todas como lidas...');
        const response = await fetch('/api/notifications/mark-all-read', {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        });
        
        if (response.ok) {
            notifications.value.forEach(notification => {
                notification.read_at = new Date().toISOString();
            });
            unreadCount.value = 0;
            console.log('✅ Todas as notificações marcadas como lidas');
        }
    } catch (error) {
        console.error('❌ Erro ao marcar todas como lidas:', error);
    }
};

const deleteNotification = async (notificationId) => {
    try {
        console.log('🔍 Deletando notificação:', notificationId);
        const response = await fetch(`/api/notifications/${notificationId}`, {
            method: 'DELETE',
            credentials: 'same-origin',
            headers: {
                'Accept': 'application/json'
            }
        });
        
        if (response.ok) {
            notifications.value = notifications.value.filter(n => n.id !== notificationId);
            await loadUnreadCount();
            console.log('✅ Notificação deletada');
        }
    } catch (error) {
        console.error('❌ Erro ao deletar notificação:', error);
    }
};

const getUnreadCount = () => {
    return unreadCount.value;
};

const getNotificationIcon = (type) => {
    const icons = {
        success: 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
        warning: 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z',
        error: 'M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
        info: 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'
    };
    return icons[type] || icons.info;
};

const getNotificationColor = (type) => {
    const colors = {
        success: 'text-green-500',
        warning: 'text-yellow-500',
        error: 'text-red-500',
        info: 'text-blue-500'
    };
    return colors[type] || colors.info;
};

// Fechar dropdowns quando clicar fora
const handleClickOutside = (event) => {
    if (showNotifications.value || showUserMenu.value) {
        const target = event.target;
        if (!target.closest('.notification-dropdown') && !target.closest('.user-menu-dropdown')) {
            showNotifications.value = false;
            showUserMenu.value = false;
        }
    }
};

onMounted(() => {
    document.addEventListener('click', handleClickOutside);
    
    console.log('🚀 Componente AuthenticatedLayout montado!');
    console.log('👤 Usuário logado:', $page.props.auth.user);
    
    // Carregar contagem de notificações não lidas
    console.log('🔍 Iniciando carregamento de notificações...');
    loadUnreadCount();
    
    // Atualizar contagem a cada 30 segundos
    setInterval(() => {
        console.log('⏰ Atualizando contagem de notificações...');
        loadUnreadCount();
    }, 30000);
});

onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside);
});
</script>

<template>
    <div class="min-h-screen bg-gray-100">
        <!-- Toolbar Principal Adaptada -->
        <div class="bg-white shadow-lg">
            <!-- Toolbar com Gradiente -->
            <div class="relative h-20 bg-gradient-to-r from-blue-600 to-purple-600">
                <!-- Background Pattern -->
                <div class="absolute inset-0 bg-black bg-opacity-10"></div>
                
                <!-- Toolbar Content -->
                <div class="relative flex items-center justify-between h-full px-6">
                    <!-- Left Side -->
                    <div class="flex items-center space-x-4">
                        <!-- Menu Button -->
                        <button @click="toggleSidebar" 
                                class="text-white hover:bg-white hover:bg-opacity-20 p-2 rounded-md transition-all duration-200">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                            </svg>
                        </button>

                        <!-- Logo e Título -->
                        <div class="flex items-center space-x-3">
                            <div class="text-white">
                                <h1 class="text-xl font-bold">TaskForce</h1>
                                <p class="text-blue-100 text-xs">Gerenciamento de Tarefas</p>
                            </div>
                        </div>
                    </div>

                    <!-- Center: Search Bar -->
                    <div class="flex-1 max-w-lg mx-8 hidden lg:block">
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-blue-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                            <input type="text" 
                                   placeholder="Pesquisar tarefas..." 
                                   class="block w-full pl-10 pr-12 py-2 border border-blue-300 rounded-md leading-5 bg-white bg-opacity-20 text-white placeholder-blue-200 focus:outline-none focus:bg-white focus:text-gray-900 focus:border-white transition-all duration-200 backdrop-blur-sm"
                                   v-model="searchQuery"
                                   @keydown="handleSearchKeydown">
                            <button @click="performSearch" 
                                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-blue-200 hover:text-white transition-colors">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Right Side -->
                    <div class="flex items-center space-x-3">
                        <!-- Navigation Links -->
                        <div class="hidden md:flex items-center space-x-2">
                            <Link :href="route('dashboard')" 
                                  class="text-white hover:bg-white hover:bg-opacity-20 px-3 py-2 rounded-md text-sm font-medium transition-all duration-200"
                                  :class="route().current('dashboard') ? 'bg-white bg-opacity-20' : ''">
                                Dashboard
                            </Link>
                            <Link :href="route('tasks.index')" 
                                  class="text-white hover:bg-white hover:bg-opacity-20 px-3 py-2 rounded-md text-sm font-medium transition-all duration-200"
                                  :class="route().current('tasks.*') ? 'bg-white bg-opacity-20' : ''">
                                Tarefas
                            </Link>
                            <Link :href="route('reports.index')" 
                                  class="text-white hover:bg-white hover:bg-opacity-20 px-3 py-2 rounded-md text-sm font-medium transition-all duration-200"
                                  :class="route().current('reports.*') ? 'bg-white bg-opacity-20' : ''">
                                Relatórios
                            </Link>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex items-center space-x-2">
                            <!-- Language Selector -->
                            <LanguageSelector />

                            <!-- Create Task Button -->
                            <button 
                                @click="router.get(route('tasks.create'))"
                                class="bg-white bg-opacity-20 hover:bg-opacity-30 text-white px-4 py-2 rounded-md text-sm font-medium transition-all duration-200 flex items-center space-x-2 backdrop-blur-sm"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                <span>Nova Tarefa</span>
                            </button>

                            <!-- Notifications -->
                            <div class="relative notification-dropdown">
                                <button @click="toggleNotifications" 
                                        class="bg-white bg-opacity-20 hover:bg-opacity-30 text-white p-2 rounded-md transition-all duration-200 backdrop-blur-sm relative">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM4.83 2.17a1 1 0 00-1.66 0L1 4v16a1 1 0 001 1h16a1 1 0 001-1V4l-2.17-1.83a1 1 0 00-1.66 0L4.83 2.17z"></path>
                                    </svg>
                                    <!-- Notification Badge -->
                                    <span v-if="getUnreadCount() > 0" 
                                          class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                                        {{ getUnreadCount() }}
                                    </span>
                                </button>

                                <!-- Notifications Dropdown -->
                                <div v-if="showNotifications" 
                                     class="absolute right-0 mt-2 w-80 bg-white rounded-md shadow-lg py-1 z-50 max-h-96 overflow-y-auto">
                                    <!-- Header -->
                                    <div class="px-4 py-2 border-b border-gray-200">
                                        <div class="flex items-center justify-between">
                                            <h3 class="text-sm font-semibold text-gray-900">Notificações</h3>
                                            <button @click="markAllAsRead" 
                                                    class="text-xs text-blue-600 hover:text-blue-800">
                                                Marcar todas como lidas
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Notifications List -->
                                    <div v-if="notifications.length > 0">
                                        <div v-for="notification in notifications" 
                                             :key="notification.id"
                                             @click="markAsRead(notification.id)"
                                             class="px-4 py-3 hover:bg-gray-50 cursor-pointer border-b border-gray-100 last:border-b-0"
                                             :class="{ 'bg-blue-50': !notification.read }">
                                            <div class="flex items-start space-x-3">
                                                <!-- Icon -->
                                                <div class="flex-shrink-0">
                                                    <svg class="w-5 h-5" 
                                                         :class="getNotificationColor(notification.type)"
                                                         fill="none" 
                                                         stroke="currentColor" 
                                                         viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" 
                                                              stroke-linejoin="round" 
                                                              stroke-width="2" 
                                                              :d="getNotificationIcon(notification.type)"></path>
                                                    </svg>
                                                </div>
                                                
                                                <!-- Content -->
                                                <div class="flex-1 min-w-0">
                                                    <div class="flex items-center justify-between">
                                                        <p class="text-sm font-medium text-gray-900" 
                                                           :class="{ 'font-semibold': !notification.read }">
                                                            {{ notification.title }}
                                                        </p>
                                                        <button @click.stop="deleteNotification(notification.id)"
                                                                class="text-gray-400 hover:text-red-500">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                            </svg>
                                                        </button>
                                                    </div>
                                                    <p class="text-sm text-gray-600 mt-1">{{ notification.message }}</p>
                                                    <p class="text-xs text-gray-400 mt-1">{{ notification.time }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Empty State -->
                                    <div v-else class="px-4 py-8 text-center">
                                        <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM4.83 2.17a1 1 0 00-1.66 0L1 4v16a1 1 0 001 1h16a1 1 0 001-1V4l-2.17-1.83a1 1 0 00-1.66 0L4.83 2.17z"></path>
                                        </svg>
                                        <p class="text-sm text-gray-500">Nenhuma notificação</p>
                                    </div>

                                    <!-- Footer -->
                                    <div class="px-4 py-2 border-t border-gray-200">
                                        <Link href="#" class="text-xs text-blue-600 hover:text-blue-800 block text-center">
                                            Ver todas as notificações
                                        </Link>
                                    </div>
                                </div>
                            </div>

                            <!-- User Menu -->
                            <div class="relative user-menu-dropdown">
                                <button @click="toggleUserMenu" 
                                        class="bg-white bg-opacity-20 hover:bg-opacity-30 text-white p-2 rounded-md transition-all duration-200 backdrop-blur-sm flex items-center space-x-2">
                                    <div class="w-8 h-8 bg-white bg-opacity-30 rounded-full flex items-center justify-center">
                                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                    </div>
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </button>

                                <!-- User Dropdown Menu -->
                                <div v-if="showUserMenu" 
                                     class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50">
                                    <Link :href="route('profile.edit')" 
                                          class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        Perfil
                                    </Link>
                                    <button @click="logout" 
                                            class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        Sair
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Info Bar -->
            <div class="px-6 py-2 bg-gray-50 border-t border-gray-200">
                <div class="flex items-center justify-between text-xs text-gray-600">
                    <div class="flex items-center space-x-4">
                        <span class="flex items-center space-x-1">
                            <svg class="w-3 h-3 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span>Sistema online</span>
                        </span>
                        <span class="flex items-center space-x-1">
                            <svg class="w-3 h-3 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span>{{ new Date().toLocaleString('pt-BR') }}</span>
                        </span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs font-medium">
                            Ativo
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mobile Navigation Menu -->
        <div v-if="showingNavigationDropdown" 
             class="md:hidden bg-white border-b border-gray-200">
            <div class="px-4 py-2 space-y-1">
                <Link :href="route('dashboard')" 
                      class="block px-3 py-2 text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50 rounded-md"
                      :class="route().current('dashboard') ? 'bg-gray-100 text-gray-900' : ''">
                    Dashboard
                </Link>
                <Link :href="route('tasks.index')" 
                      class="block px-3 py-2 text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50 rounded-md"
                      :class="route().current('tasks.*') ? 'bg-gray-100 text-gray-900' : ''">
                    Tarefas
                </Link>
                <Link :href="route('reports.index')" 
                      class="block px-3 py-2 text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50 rounded-md"
                      :class="route().current('reports.*') ? 'bg-gray-100 text-gray-900' : ''">
                    Relatórios
                </Link>
                <Link :href="route('profile.edit')" 
                      class="block px-3 py-2 text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50 rounded-md"
                      :class="route().current('profile.*') ? 'bg-gray-100 text-gray-900' : ''">
                    Perfil
                </Link>
            </div>
        </div>

        <!-- Page Content -->
        <main>
            <slot />
        </main>

        <!-- Botão Flutuante (FAB) para Adicionar Tarefas -->
        <div class="fixed bottom-6 right-6 z-[9999]">
            <!-- Overlay para fechar o menu -->
            <div 
                v-if="showFabMenu" 
                @click="toggleFab"
                class="fixed inset-0 bg-black bg-opacity-25 z-[9998]"
            ></div>

            <!-- Menu de Opções -->
            <div v-if="showFabMenu" class="absolute bottom-20 right-0 space-y-3 z-[9999]">
                <!-- Botão Nova Tarefa -->
                <div class="flex items-center">
                    <div class="bg-white rounded-lg shadow-lg px-4 py-2 mr-3 whitespace-nowrap">
                        <span class="text-sm font-medium text-gray-700">Nova Tarefa</span>
                    </div>
                    <button
                        @click="openNewTaskModal"
                        class="bg-green-500 hover:bg-green-600 text-white w-12 h-12 rounded-full shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-110 flex items-center justify-center z-[9999]"
                    >
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                    </button>
                </div>

                <!-- Botão Nova Tarefa Rápida -->
                <div class="flex items-center">
                    <div class="bg-white rounded-lg shadow-lg px-4 py-2 mr-3 whitespace-nowrap">
                        <span class="text-sm font-medium text-gray-700">Tarefa Rápida</span>
                    </div>
                    <button
                        @click="openQuickTaskModal"
                        class="bg-yellow-500 hover:bg-yellow-600 text-white w-12 h-12 rounded-full shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-110 flex items-center justify-center z-[9999]"
                    >
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </button>
                </div>

                <!-- Botão Ir para Tarefas -->
                <div class="flex items-center">
                    <div class="bg-white rounded-lg shadow-lg px-4 py-2 mr-3 whitespace-nowrap">
                        <span class="text-sm font-medium text-gray-700">Ver Tarefas</span>
                    </div>
                    <button
                        @click="goToTasks"
                        class="bg-purple-500 hover:bg-purple-600 text-white w-12 h-12 rounded-full shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-110 flex items-center justify-center z-[9999]"
                    >
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Botão Principal -->
            <button
                @click="toggleFab"
                class="bg-blue-600 hover:bg-blue-700 text-white w-16 h-16 rounded-full shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-110 flex items-center justify-center z-[9999] relative"
                :class="{ 'rotate-45': showFabMenu }"
            >
                <svg v-if="!showFabMenu" class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                <svg v-else class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <!-- Modal de Tarefa Rápida -->
        <QuickTaskModal 
            :show="showQuickTaskModal" 
            @close="closeQuickTaskModal" 
        />

        <!-- Componente de Notificações em Tempo Real -->
        <RealTimeNotifications 
            v-if="$page.props.auth.user"
            :user-id="$page.props.auth.user.id"
        />
        
        <!-- Snackbar de Notificação de Email -->
        <EmailNotificationSnackbar />
        
        <!-- Debug: Informações do usuário -->
        <div v-if="$page.props.auth.user" class="fixed bottom-4 left-4 bg-blue-100 p-2 rounded text-xs">
            Debug: User ID: {{ $page.props.auth.user.id }}
        </div>
    </div>
</template>
