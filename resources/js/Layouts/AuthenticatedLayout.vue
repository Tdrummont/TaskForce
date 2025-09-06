<script setup lang="ts">
import { ref, watch, onMounted, onUnmounted } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import QuickTaskModal from '@/Components/QuickTaskModal.vue';
import HolidaySnackbar from '@/Components/HolidaySnackbar.vue';
import LanguageSelector from '@/Components/LanguageSelector.vue';
import OnlineUsersFAB from '../components/OnlineUsersFAB.vue';
import BeautifulNotificationCenter from '../components/BeautifulNotificationCenter.vue';
import ToastContainer from '../components/BeautifulToastContainer.vue';

import { Link, useForm, router, usePage } from '@inertiajs/vue3';
import { useLocale } from '@/Components/useLocale';

const props = defineProps({
    showingNavigation: {
        type: Boolean,
        default: false
    }
});

// Tipagem opcional dos slots para TS (suporta <template #header>)
const slots = defineSlots<{
    default?: () => any
    header?: () => any
}>()

// Inertia page
const $page = usePage();
const { routeL, t } = useLocale();

const showingNavigationDropdown = ref(false);
const showUserMenu = ref(false);
const searchQuery = ref('');
const showFabMenu = ref(false);
const showQuickTaskModal = ref(false);
const showNotifications = ref(false);
const notifications = ref<any[]>([]);
const unreadCount = ref(0);
const onlineUsersCount = ref(2);
const toastContainer = ref<any>(null);
const categories = ref([]);

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

const logout = async () => {
    try {
        // Desconectar WebSocketService antes do logout
        // @ts-ignore
        const module = await import('../services/WebSocketService.js');
        const WebSocketService = module.default;
        
        console.log('🔌 Desconectando WebSocketService...');
        WebSocketService.disconnect();
        console.log('✅ WebSocketService desconectado com sucesso');
    } catch (error) {
        console.error('❌ Erro ao desconectar WebSocketService:', error);
    } finally {
        // Executar logout mesmo se houver erro no WebSocket
        const form = useForm({})
        form.post(routeL('logout'))
    }
};

const performSearch = () => {
    if (searchQuery.value.trim()) {
        // Se estiver na página de tarefas, usar filtros locais
        if (window.location.pathname.includes('/tasks')) {
            // Emitir evento para a página de tarefas
            window.dispatchEvent(new CustomEvent('search-tasks', {
                detail: { query: searchQuery.value }
            }));
        } else {
            // Navegar para a página de tarefas com a pesquisa
            router.get(routeL('tasks.index'), { search: searchQuery.value });
        }
    }
};

const handleSearchKeydown = (event: KeyboardEvent) => {
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
    console.log('Navigating to:', routeL('tasks.create'));
    router.get(routeL('tasks.create'));
};

const openQuickTaskModal = async () => {
    console.log('Opening quick task modal');
    showFabMenu.value = false;
    await loadCategories();
    showQuickTaskModal.value = true;
};

const closeQuickTaskModal = () => {
    showQuickTaskModal.value = false;
};

const handleTaskCreated = () => {
    console.log('Tarefa criada com sucesso!');
    // Opcional: recarregar a página ou atualizar dados
    // router.reload();
};

const loadCategories = async () => {
    try {
        console.log('🔄 Carregando categorias...');
        console.log('📍 URL:', routeL('tasks.categories'));
        
        const response = await fetch(routeL('tasks.categories'), {
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin'
        });
        
        console.log('📡 Resposta recebida:', response.status, response.statusText);
        
        if (response.ok) {
            const data = await response.json();
            console.log('📊 Dados das categorias:', data);
            if (data.success) {
                categories.value = data.categories;
                console.log('✅ Categorias carregadas:', categories.value);
            }
        } else {
            console.error('❌ Erro ao carregar categorias:', response.status, response.statusText);
        }
    } catch (error) {
        console.error('❌ Erro ao carregar categorias:', error);
    }
};

const goToTasks = () => {
    console.log('Going to tasks');
    showFabMenu.value = false;
    console.log('Navigating to:', routeL('tasks.index'));
    router.get(routeL('tasks.index'));
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
        console.log('👤 Usuário logado:', ($page.props as any).auth.user);
        
        const url = routeL('api.notifications.index');
        console.log('🔗 URL da API:', url);
        
        const response = await fetch(url, {
            credentials: 'same-origin',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        
        console.log('📡 Response status:', response.status);
        console.log('📡 Response ok:', response.ok);
        console.log('📡 Response headers:', Object.fromEntries(response.headers.entries()));
        
        if (response.ok) {
            const data = await response.json();
            console.log('📊 Dados recebidos:', data);
            console.log('📊 Estrutura dos dados:', Object.keys(data));
            console.log('📊 data.success:', data.success);
            console.log('📊 data.notifications existe:', !!data.notifications);
            console.log('📊 data.notifications length:', data.notifications?.length);
            
            if (data.success && data.notifications) {
                notifications.value = data.notifications;
                console.log('✅ Notificações carregadas:', notifications.value.length);
                console.log('📝 Primeira notificação:', notifications.value[0]);
                console.log('📝 Todas as notificações:', notifications.value);
                console.log('🔍 notifications.value após carregamento:', notifications.value);
            } else {
                console.warn('⚠️ API retornou sucesso=false ou sem notificações:', data);
                console.warn('⚠️ data.success:', data.success);
                console.warn('⚠️ data.notifications:', data.notifications);
                notifications.value = [];
            }
        } else {
            console.error('❌ Erro na resposta:', response.status, response.statusText);
            const text = await response.text();
            console.error('📄 Conteúdo da resposta:', text);
            notifications.value = [];
        }
    } catch (error) {
        console.error('❌ Erro ao carregar notificações:', error);
        console.error('📋 Stack trace:', (error as any).stack);
    }
};

const loadUnreadCount = async () => {
    try {
        console.log('🔍 Carregando contagem de não lidas...');
        const response = await fetch(routeL('api.notifications.unreadCount'), {
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

const markAsRead = async (notificationId: any) => {
    try {
        console.log('🔍 Marcando notificação como lida:', notificationId);
        const response = await fetch(routeL('api.notifications.markRead', { id: notificationId }), {
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
        const response = await fetch(routeL('api.notifications.markAllRead'), {
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

const deleteNotification = async (notificationId: any) => {
    try {
        console.log('🔍 Deletando notificação:', notificationId);
        const response = await fetch(routeL('api.notifications.delete', { id: notificationId }), {
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

const clearAllNotifications = async () => {
    try {
        console.log('🔍 Limpando todas as notificações...');
        notifications.value = [];
        unreadCount.value = 0;
        console.log('✅ Todas as notificações limpas');
    } catch (error) {
        console.error('❌ Erro ao limpar notificações:', error);
    }
};

const getUnreadCount = () => {
    return unreadCount.value;
};

const getNotificationIcon = (type: any) => {
    const icons = {
        success: 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
        warning: 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z',
        error: 'M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
        info: 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'
    };
    return icons[type as keyof typeof icons] || icons.info;
};

const getNotificationColor = (type: any) => {
    const colors = {
        success: 'text-green-500',
        warning: 'text-yellow-500',
        error: 'text-red-500',
        info: 'text-blue-500'
    };
    return colors[type as keyof typeof colors] || colors.info;
};

// Função para mostrar toast de notificação
const showToast = (message: any, type = 'info') => {
    // Criar elemento de toast
    const toast = document.createElement('div');
    toast.className = `fixed top-4 right-4 z-50 px-6 py-3 rounded-lg shadow-lg text-white ${
        type === 'success' ? 'bg-green-500' : 
        type === 'error' ? 'bg-red-500' : 
        type === 'warning' ? 'bg-yellow-500' : 
        'bg-blue-500'
    }`;
    toast.textContent = message;
    
    // Adicionar ao DOM
    document.body.appendChild(toast);
    
    // Remover após 3 segundos
    setTimeout(() => {
        if (toast.parentElement) {
            toast.remove();
        }
    }, 3000);
};

// Fechar dropdowns quando clicar fora
const handleClickOutside = (event: any) => {
    if (showNotifications.value || showUserMenu.value) {
        const target = event.target;
        if (!target.closest('.notification-dropdown') && 
            !target.closest('.user-menu-dropdown')) {
            showNotifications.value = false;
            showUserMenu.value = false;
        }
    }
};

onMounted(() => {
    document.addEventListener('click', handleClickOutside);
    
    // Listener para desconectar WebSocketService ao fechar aba/navegar
    window.addEventListener('beforeunload', () => {
        // @ts-ignore
        import('../services/WebSocketService.js').then((module) => {
            const WebSocketService = module.default;
            console.log('🔌 Desconectando WebSocketService antes de sair...');
            WebSocketService.disconnect();
        }).catch((error) => {
            console.error('❌ Erro ao desconectar WebSocketService no beforeunload:', error);
        });
    });
    
    console.log('🚀 Componente AuthenticatedLayout montado!');
    console.log('🎯 FAB de Usuários Online sendo renderizado...');
    console.log('👥 onlineUsersCount:', onlineUsersCount.value);
    console.log('👤 Usuário logado:', ($page.props as any).auth.user);
    
    // Carregar contagem de notificações não lidas
    console.log('🔍 Iniciando carregamento de notificações...');
    loadUnreadCount();
    
    // Configurar WebSocket usando o serviço melhorado
    if (($page.props as any).auth.user) {
        // @ts-ignore
        import('../services/WebSocketService.js').then((module: any) => {
            const WebSocketService = module.default;
        
        // Inicializar serviço WebSocket
        WebSocketService.init(($page.props as any).auth.user);
        
        // Configurar listeners para usuários online
        WebSocketService.on('users_online', (users: any) => {
            console.log('👥 Usuários online atualizados no layout:', users);
            onlineUsersCount.value = users ? users.length : 0;
        });
        
        WebSocketService.on('user_joined', (user: any) => {
            console.log('👋 Usuário entrou, atualizando contador:', user);
            onlineUsersCount.value++;
        });
        
        WebSocketService.on('user_left', (user: any) => {
            console.log('👋 Usuário saiu, atualizando contador:', user);
            onlineUsersCount.value = Math.max(0, onlineUsersCount.value - 1);
        });

        // Configurar listeners para notificações
        WebSocketService.on('task_assigned', (notification: any) => {
            const newNotification = {
                id: Date.now(),
                type: 'task_assigned',
                title: 'Nova Tarefa Atribuída',
                message: notification.message || `A tarefa "${notification.data?.task?.title || 'Nova tarefa'}" foi atribuída para você`,
                data: notification.data,
                read_at: null,
                created_at: notification.timestamp || new Date().toISOString()
            };
            notifications.value.unshift(newNotification);
            unreadCount.value++;
            
            // Mostrar toast
            if (toastContainer.value) {
                toastContainer.value.addToast(newNotification);
            }
        });
        
        WebSocketService.on('task_delegated', (notification: any) => {
            console.log('🔔 Notificação task_delegated recebida no AuthenticatedLayout:', notification);
            console.log('🔔 Dados da notificação:', JSON.stringify(notification, null, 2));
            
            const newNotification = {
                id: Date.now(),
                type: 'task_delegated',
                title: 'Tarefa Delegada',
                message: notification.message || `A tarefa "${notification.data?.task?.title || 'Nova tarefa'}" foi delegada para você por ${notification.data?.delegated_by?.name || 'um usuário'}`,
                data: notification.data,
                read_at: null,
                created_at: notification.timestamp || new Date().toISOString()
            };
            
            console.log('🔔 Criando nova notificação:', newNotification);
            notifications.value.unshift(newNotification);
            unreadCount.value++;
            console.log('🔔 Notificação adicionada. Total:', notifications.value.length);
            
            // Mostrar toast
            if (toastContainer.value) {
                console.log('🔔 Mostrando toast...');
                toastContainer.value.addToast(newNotification);
            } else {
                console.warn('⚠️ toastContainer não está disponível');
            }
        });
        
        WebSocketService.on('task_created', (notification: any) => {
            const newNotification = {
                id: Date.now(),
                type: 'task_created',
                title: 'Nova Tarefa Criada',
                message: notification.message || `A tarefa "${notification.data?.task?.title || 'Nova tarefa'}" foi criada`,
                data: notification.data,
                read_at: null,
                created_at: notification.timestamp || new Date().toISOString()
            };
            notifications.value.unshift(newNotification);
            unreadCount.value++;
            
            // Mostrar toast
            if (toastContainer.value) {
                toastContainer.value.addToast(newNotification);
            }
        });
        
        WebSocketService.on('task_status_updated', (notification: any) => {
            const newNotification = {
                id: Date.now(),
                type: 'task_status_updated',
                title: 'Status da Tarefa Atualizado',
                message: notification.message || `O status da tarefa "${notification.data?.task?.title || 'Nova tarefa'}" foi alterado de "${notification.data?.old_status || 'pendente'}" para "${notification.data?.new_status || 'em progresso'}"`,
                data: notification.data,
                read_at: null,
                created_at: notification.timestamp || new Date().toISOString()
            };
            notifications.value.unshift(newNotification);
            unreadCount.value++;
            
            // Mostrar toast
            if (toastContainer.value) {
                toastContainer.value.addToast(newNotification);
            }
        });
        
        WebSocketService.on('task_comment_added', (notification: any) => {
            const newNotification = {
                id: Date.now(),
                type: 'task_comment_added',
                title: 'Novo Comentário',
                message: notification.message || `${notification.data?.commented_by?.name || 'Um usuário'} comentou na tarefa "${notification.data?.task?.title || 'Nova tarefa'}"`,
                data: notification.data,
                read_at: null,
                created_at: notification.timestamp || new Date().toISOString()
            };
            notifications.value.unshift(newNotification);
            unreadCount.value++;
            
            // Mostrar toast
            if (toastContainer.value) {
                toastContainer.value.addToast(newNotification);
            }
        });
        
        // Listener para usuários online
        WebSocketService.on('users_online', (users: any) => {
            console.log('👥 Usuários online atualizados:', users);
            onlineUsersCount.value = users.length;
        });
        
        WebSocketService.on('user_joined', (user: any) => {
            console.log('👋 Usuário entrou:', user);
            onlineUsersCount.value++;
            if (toastContainer.value) {
                toastContainer.value.addToast({
                    type: 'user_joined',
                    title: 'Usuário Online',
                    message: `${user.name || 'Um usuário'} está online`,
                    timestamp: new Date().toISOString()
                });
            }
        });
        
        WebSocketService.on('user_left', (user: any) => {
            console.log('👋 Usuário saiu:', user);
            onlineUsersCount.value = Math.max(0, onlineUsersCount.value - 1);
        });

        // Listener para notificações do Laravel
        WebSocketService.on('laravel_notification', (notification: any) => {
            console.log('🔔 Notificação Laravel recebida no AuthenticatedLayout:', notification);
            
            const newNotification = {
                id: Date.now(),
                type: notification.type || 'notification',
                title: notification.title || 'Nova Notificação',
                message: notification.message || 'Você tem uma nova notificação',
                data: notification.data,
                read_at: null,
                created_at: notification.timestamp || new Date().toISOString()
            };
            
            console.log('🔔 Criando nova notificação Laravel:', newNotification);
            notifications.value.unshift(newNotification);
            unreadCount.value++;
            console.log('🔔 Notificação Laravel adicionada. Total:', notifications.value.length);
            
            // Mostrar toast
            if (toastContainer.value) {
                console.log('🔔 Mostrando toast Laravel...');
                toastContainer.value.addToast(newNotification);
            } else {
                console.warn('⚠️ toastContainer não está disponível para notificação Laravel');
            }
        });
        });
    }
    
    // Atualizar contagem a cada 30 segundos
    setInterval(() => {
        console.log('⏰ Atualizando contagem de notificações...');
        loadUnreadCount();
    }, 30000);
});

onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside);
    
    // Desconectar WebSocketService ao sair do componente
    // @ts-ignore
    import('../services/WebSocketService.js').then((module) => {
        const WebSocketService = module.default;
        console.log('🔌 Desconectando WebSocketService no unmount...');
        WebSocketService.disconnect();
    }).catch((error) => {
        console.error('❌ Erro ao desconectar WebSocketService no unmount:', error);
    });
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
                                <p class="text-blue-100 text-xs">{{ t('navbar.subtitle') }}</p>
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
                                   :placeholder="t('navbar.search_ph')" 
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
                            <Link :href="routeL('dashboard')" 
                                  class="text-white hover:bg-white hover:bg-opacity-20 px-3 py-2 rounded-md text-sm font-medium transition-all duration-200"
                                  :class="$page.url.startsWith('/dashboard') ? 'bg-white bg-opacity-20' : ''">
                                Dashboard
                            </Link>
                            <Link :href="routeL('tasks.index')" 
                                  class="text-white hover:bg-white hover:bg-opacity-20 px-3 py-2 rounded-md text-sm font-medium transition-all duration-200"
                                  :class="$page.url.startsWith('/tasks') ? 'bg-white bg-opacity-20' : ''">
                                {{ t('navbar.tasks') }}
                            </Link>
                            <Link :href="routeL('reports.index')" 
                                  class="text-white hover:bg-white hover:bg-opacity-20 px-3 py-2 rounded-md text-sm font-medium transition-all duration-200"
                                  :class="$page.url.startsWith('/reports') ? 'bg-white bg-opacity-20' : ''">
                                {{ t('navbar.reports') }}
                            </Link>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex items-center space-x-2">
                            <!-- Language Selector -->
                            <LanguageSelector />

                            <!-- Create Task Button -->
                            <button 
                                @click="router.get(routeL('tasks.create'))"
                                class="bg-white bg-opacity-20 hover:bg-opacity-30 text-white px-4 py-2 rounded-md text-sm font-medium transition-all duration-200 flex items-center space-x-2 backdrop-blur-sm"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                <span>{{ t('navbar.new_task') }}</span>
                            </button>


                            <!-- Centro de Notificações com Design Moderno -->
                            <BeautifulNotificationCenter 
                                :notifications="notifications"
                                @mark-as-read="markAsRead"
                                @mark-all-read="markAllAsRead"
                                @clear-all="clearAllNotifications"
                                @load-notifications="loadNotifications"
                            />

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
                                    <Link :href="routeL('profile.edit')" 
                                          class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        {{ t('navbar.profile') }}
                                    </Link>
                                    <button @click="logout" 
                                            class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        {{ t('navbar.logout') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Mobile Navigation Menu -->
        <div v-if="showingNavigationDropdown" 
             class="md:hidden bg-white border-b border-gray-200">
            <div class="px-4 py-2 space-y-1">
                <Link :href="routeL('dashboard')" 
                      class="block px-3 py-2 text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50 rounded-md"
                      :class="$page.url.startsWith('/dashboard') ? 'bg-gray-100 text-gray-900' : ''">
                    Dashboard
                </Link>
                <Link :href="routeL('tasks.index')" 
                      class="block px-3 py-2 text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50 rounded-md"
                      :class="$page.url.startsWith('/tasks') ? 'bg-gray-100 text-gray-900' : ''">
                    Tarefas
                </Link>
                <Link :href="routeL('reports.index')" 
                      class="block px-3 py-2 text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50 rounded-md"
                      :class="$page.url.startsWith('/reports') ? 'bg-gray-100 text-gray-900' : ''">
                    Relatórios
                </Link>
                <Link :href="routeL('profile.edit')" 
                      class="block px-3 py-2 text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50 rounded-md"
                      :class="$page.url.startsWith('/profile') ? 'bg-gray-100 text-gray-900' : ''">
                    Perfil
                </Link>
            </div>
        </div>

        <!-- Page Content -->

        <main>
            <slot />
        </main>

        <!-- FAB de Usuários Online (Canto Inferior Esquerdo) -->
        <OnlineUsersFAB 
            :online-users-count="onlineUsersCount"
        />

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
                        <span class="text-sm font-medium text-gray-700">{{ t('fab.new_task') }}</span>
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
                        <span class="text-sm font-medium text-gray-700">{{ t('fab.quick_task') }}</span>
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
                        <span class="text-sm font-medium text-gray-700">{{ t('fab.view_tasks') }}</span>
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
            :categories="categories"
            :user-state="'SP'"
            @close="closeQuickTaskModal"
            @created="handleTaskCreated"
        />

        <!-- Snackbar de Feriados -->
        <HolidaySnackbar />

        <!-- Toast Container para Notificações -->
        <ToastContainer ref="toastContainer" />


    </div>
</template>
