<template>
    <div class="relative">
        <!-- Botão de Notificações com Design Moderno -->
        <button
            @click="toggleNotifications"
            class="relative bg-white bg-opacity-20 hover:bg-opacity-30 text-white p-3 rounded-xl transition-all duration-300 backdrop-blur-sm hover:scale-105 hover:shadow-lg group"
        >
            <!-- Ícone de sino com animação -->
            <svg 
                class="w-5 h-5 transition-transform duration-300 group-hover:rotate-12" 
                fill="none" 
                stroke="currentColor" 
                viewBox="0 0 24 24"
            >
                <path 
                    stroke-linecap="round" 
                    stroke-linejoin="round" 
                    stroke-width="2" 
                    d="M15 17h5l-5 5v-5zM4.828 7l2.586 2.586a2 2 0 002.828 0L12.828 7H4.828zM4.828 17h8l-2.586-2.586a2 2 0 00-2.828 0L4.828 17z" 
                />
            </svg>
            
            <!-- Badge de notificações com animação -->
            <span 
                v-if="unreadCount > 0"
                class="absolute -top-1 -right-1 bg-gradient-to-r from-red-500 to-pink-500 text-white text-xs rounded-full h-6 w-6 flex items-center justify-center animate-bounce shadow-lg border-2 border-white"
            >
                {{ unreadCount > 99 ? '99+' : unreadCount }}
            </span>
            
            <!-- Efeito de brilho no hover -->
            <div class="absolute inset-0 rounded-xl bg-gradient-to-r from-blue-400 to-purple-500 opacity-0 group-hover:opacity-20 transition-opacity duration-300"></div>
        </button>

        <!-- Dropdown de Notificações com Design Moderno -->
        <Transition
            enter-active-class="transition ease-out duration-300"
            enter-from-class="opacity-0 scale-95 translate-y-2"
            enter-to-class="opacity-100 scale-100 translate-y-0"
            leave-active-class="transition ease-in duration-200"
            leave-from-class="opacity-100 scale-100 translate-y-0"
            leave-to-class="opacity-0 scale-95 translate-y-2"
        >
            <div
                v-if="showNotifications"
                class="absolute right-0 mt-3 w-96 bg-white rounded-2xl shadow-2xl ring-1 ring-black ring-opacity-5 z-50 overflow-hidden backdrop-blur-xl"
            >
                <!-- Header com gradiente -->
                <div class="bg-gradient-to-r from-blue-600 to-purple-600 px-6 py-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM4.828 7l2.586 2.586a2 2 0 002.828 0L12.828 7H4.828zM4.828 17h8l-2.586-2.586a2 2 0 00-2.828 0L4.828 17z" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-white">Notificações</h3>
                        </div>
                        <div class="flex items-center space-x-2">
                            <button
                                @click="markAllAsRead"
                                v-if="unreadCount > 0"
                                class="text-xs text-blue-100 hover:text-white transition-colors duration-200 bg-white bg-opacity-20 px-3 py-1 rounded-full"
                            >
                                Marcar todas
                            </button>
                            <button
                                @click="clearAll"
                                class="text-xs text-blue-100 hover:text-white transition-colors duration-200"
                            >
                                Limpar
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Lista de Notificações -->
                <div class="max-h-96 overflow-y-auto">
                    <div v-if="notifications.length === 0" class="px-6 py-12 text-center">
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM4.828 7l2.586 2.586a2 2 0 002.828 0L12.828 7H4.828zM4.828 17h8l-2.586-2.586a2 2 0 00-2.828 0L4.828 17z" />
                            </svg>
                        </div>
                        <p class="text-gray-500 font-medium">Nenhuma notificação</p>
                        <p class="text-gray-400 text-sm mt-1">Você está em dia! 🎉</p>
                    </div>
                    
                    <div v-else>
                        <div
                            v-for="notification in notifications"
                            :key="notification.id"
                            @click="markAsRead(notification)"
                            :class="[
                                'px-6 py-4 cursor-pointer transition-all duration-200 hover:bg-gray-50 border-l-4',
                                !notification.read_at ? 'bg-blue-50 border-blue-500' : 'border-transparent'
                            ]"
                        >
                            <div class="flex items-start space-x-4">
                                <!-- Ícone com gradiente -->
                                <div 
                                    :class="[
                                        'w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0 shadow-lg',
                                        getIconGradient(notification.type)
                                    ]"
                                >
                                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path :d="getIconPath(notification.type)" />
                                    </svg>
                                </div>
                                
                                <!-- Conteúdo -->
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1">
                                            <p class="text-sm font-semibold text-gray-900 mb-1">
                                                {{ notification.title }}
                                            </p>
                                            <p class="text-sm text-gray-600 leading-relaxed">
                                                {{ notification.message }}
                                            </p>
                                            <p class="text-xs text-gray-400 mt-2 flex items-center">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                {{ formatTime(notification.created_at) }}
                                            </p>
                                        </div>
                                        
                                        <!-- Indicador de não lida -->
                                        <div v-if="!notification.read_at" class="w-3 h-3 bg-blue-500 rounded-full flex-shrink-0 mt-1 animate-pulse"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                    <button
                        @click="showAllNotifications"
                        class="w-full text-center text-sm text-blue-600 hover:text-blue-800 font-medium transition-colors duration-200"
                    >
                        Ver todas as notificações
                    </button>
                </div>
            </div>
        </Transition>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'

const props = defineProps({
    notifications: {
        type: Array,
        default: () => []
    }
})

const emit = defineEmits(['mark-as-read', 'mark-all-read', 'clear-all', 'load-notifications'])

const showNotifications = ref(false)
const unreadCount = computed(() => {
    return props.notifications.filter(n => !n.read_at).length
})

const toggleNotifications = () => {
    console.log('🔔 BeautifulNotificationCenter toggleNotifications chamado!');
    showNotifications.value = !showNotifications.value;
    
    if (showNotifications.value) {
        console.log('✅ Painel aberto, emitindo evento para carregar notificações...');
        emit('load-notifications');
    } else {
        console.log('❌ Painel fechado');
    }
}

const markAsRead = (notification) => {
    if (!notification.read_at) {
        emit('mark-as-read', notification)
    }
}

const markAllAsRead = () => {
    emit('mark-all-read')
}

const clearAll = () => {
    emit('clear-all')
}

const showAllNotifications = () => {
    window.location.href = '/notifications'
}

const getIconGradient = (type) => {
    const gradients = {
        'task_assigned': 'bg-gradient-to-br from-green-400 to-green-600',
        'task_delegated': 'bg-gradient-to-br from-blue-400 to-blue-600',
        'task_created': 'bg-gradient-to-br from-purple-400 to-purple-600',
        'task_status_updated': 'bg-gradient-to-br from-yellow-400 to-orange-500',
        'task_comment_added': 'bg-gradient-to-br from-indigo-400 to-indigo-600',
        'user_joined': 'bg-gradient-to-br from-green-400 to-green-600',
        'user_left': 'bg-gradient-to-br from-gray-400 to-gray-600'
    }
    return gradients[type] || 'bg-gradient-to-br from-blue-400 to-blue-600'
}

const getIconPath = (type) => {
    const paths = {
        'task_assigned': 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
        'task_delegated': 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z',
        'task_created': 'M12 6v6m0 0v6m0-6h6m-6 0H6',
        'task_status_updated': 'M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2',
        'task_comment_added': 'M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z',
        'user_joined': 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z',
        'user_left': 'M13 7a4 4 0 11-8 0 4 4 0 018 0zM9 14a6 6 0 00-6 6v1h12v-1a6 6 0 00-6-6z'
    }
    return paths[type] || 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'
}

const formatTime = (timestamp) => {
    const now = new Date()
    const time = new Date(timestamp)
    const diff = now - time
    
    if (diff < 60000) {
        return 'Agora mesmo'
    } else if (diff < 3600000) {
        const minutes = Math.floor(diff / 60000)
        return `${minutes} min atrás`
    } else if (diff < 86400000) {
        const hours = Math.floor(diff / 3600000)
        return `${hours}h atrás`
    } else {
        return time.toLocaleDateString('pt-BR')
    }
}

// Fechar dropdown ao clicar fora
const handleClickOutside = (event) => {
    if (!event.target.closest('.relative')) {
        showNotifications.value = false
    }
}

onMounted(() => {
    document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside)
})
</script>

<style scoped>
/* Animações customizadas */
@keyframes bounce-subtle {
    0%, 100% {
        transform: translateY(0);
    }
    50% {
        transform: translateY(-2px);
    }
}

.animate-bounce {
    animation: bounce-subtle 2s ease-in-out infinite;
}

/* Efeito de glassmorphism */
.backdrop-blur-xl {
    backdrop-filter: blur(20px);
}

/* Transições suaves */
.transition-all {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}
</style>
