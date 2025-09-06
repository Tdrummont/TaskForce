<template>
    <div class="relative">
        <!-- Botão de Notificações -->
        <button
            @click="toggleNotifications"
            class="relative p-2 text-gray-600 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 rounded-md"
        >
            <!-- Ícone de sino -->
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM4.828 7l2.586 2.586a2 2 0 002.828 0L12.828 7H4.828zM4.828 17h8l-2.586-2.586a2 2 0 00-2.828 0L4.828 17z" />
            </svg>
            
            <!-- Badge de notificações não lidas -->
            <span 
                v-if="unreadCount > 0"
                class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center animate-pulse"
            >
                {{ unreadCount > 99 ? '99+' : unreadCount }}
            </span>
        </button>

        <!-- Dropdown de Notificações -->
        <Transition
            enter-active-class="transition ease-out duration-200"
            enter-from-class="opacity-0 scale-95"
            enter-to-class="opacity-100 scale-100"
            leave-active-class="transition ease-in duration-75"
            leave-from-class="opacity-100 scale-100"
            leave-to-class="opacity-0 scale-95"
        >
            <div
                v-if="showNotifications"
                class="absolute right-0 mt-2 w-80 bg-white rounded-md shadow-lg ring-1 ring-black ring-opacity-5 z-50"
            >
                <!-- Header -->
                <div class="px-4 py-3 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-medium text-gray-900">Notificações</h3>
                        <div class="flex items-center space-x-2">
                            <button
                                @click="markAllAsRead"
                                v-if="unreadCount > 0"
                                class="text-sm text-indigo-600 hover:text-indigo-900"
                            >
                                Marcar todas como lidas
                            </button>
                            <button
                                @click="clearAll"
                                class="text-sm text-gray-500 hover:text-gray-700"
                            >
                                Limpar
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Lista de Notificações -->
                <div class="max-h-96 overflow-y-auto">
                    <div v-if="notifications.length === 0" class="px-4 py-8 text-center text-gray-500">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM4.828 7l2.586 2.586a2 2 0 002.828 0L12.828 7H4.828zM4.828 17h8l-2.586-2.586a2 2 0 00-2.828 0L4.828 17z" />
                        </svg>
                        <p class="mt-2">Nenhuma notificação</p>
                    </div>
                    
                    <div v-else>
                        <div
                            v-for="notification in notifications"
                            :key="notification.id"
                            @click="markAsRead(notification)"
                            :class="[
                                'px-4 py-3 border-b border-gray-100 cursor-pointer hover:bg-gray-50 transition-colors',
                                !notification.read_at ? 'bg-blue-50 border-l-4 border-l-blue-500' : ''
                            ]"
                        >
                            <div class="flex items-start space-x-3">
                                <!-- Ícone -->
                                <div 
                                    :class="[
                                        'w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0',
                                        getIconClass(notification.type)
                                    ]"
                                >
                                    <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path :d="getIconPath(notification.type)" />
                                    </svg>
                                </div>
                                
                                <!-- Conteúdo -->
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900">
                                        {{ notification.title }}
                                    </p>
                                    <p class="text-sm text-gray-600 mt-1">
                                        {{ notification.message }}
                                    </p>
                                    <p class="text-xs text-gray-400 mt-1">
                                        {{ formatTime(notification.created_at) }}
                                    </p>
                                </div>
                                
                                <!-- Indicador de não lida -->
                                <div v-if="!notification.read_at" class="w-2 h-2 bg-blue-500 rounded-full flex-shrink-0"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="px-4 py-3 border-t border-gray-200">
                    <button
                        @click="showAllNotifications"
                        class="w-full text-center text-sm text-indigo-600 hover:text-indigo-900"
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

const emit = defineEmits(['mark-as-read', 'mark-all-read', 'clear-all'])

const showNotifications = ref(false)

const unreadCount = computed(() => {
    return props.notifications.filter(n => !n.read_at).length
})

const toggleNotifications = () => {
    showNotifications.value = !showNotifications.value
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
    // Redirecionar para página de notificações
    window.location.href = '/notifications'
}

const getIconClass = (type) => {
    const classes = {
        'task_assigned': 'bg-green-500',
        'task_delegated': 'bg-blue-500',
        'task_created': 'bg-purple-500',
        'task_status_updated': 'bg-yellow-500',
        'task_comment_added': 'bg-indigo-500',
        'user_joined': 'bg-green-500',
        'user_left': 'bg-gray-500'
    }
    return classes[type] || 'bg-blue-500'
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
