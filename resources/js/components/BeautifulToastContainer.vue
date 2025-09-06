<template>
    <div class="fixed top-4 right-4 z-50 space-y-3">
        <Transition
            v-for="toast in toasts"
            :key="toast.id"
            enter-active-class="transform ease-out duration-300 transition"
            enter-from-class="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
            enter-to-class="translate-y-0 opacity-100 sm:translate-x-0"
            leave-active-class="transition ease-in duration-100"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div
                class="pointer-events-auto w-full max-w-sm overflow-hidden rounded-2xl bg-white shadow-2xl ring-1 ring-black ring-opacity-5 border-l-4"
                :class="getBorderColor(toast.type)"
            >
                <div class="p-4">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <!-- Ícone com gradiente -->
                            <div 
                                :class="[
                                    'w-10 h-10 rounded-full flex items-center justify-center shadow-lg',
                                    getIconGradient(toast.type)
                                ]"
                            >
                                <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path :d="getIconPath(toast.type)" />
                                </svg>
                            </div>
                        </div>
                        <div class="ml-3 w-0 flex-1 pt-0.5">
                            <p class="text-sm font-semibold text-gray-900">
                                {{ toast.title }}
                            </p>
                            <p class="mt-1 text-sm text-gray-600 leading-relaxed">
                                {{ toast.message }}
                            </p>
                            <p class="mt-1 text-xs text-gray-400 flex items-center">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                {{ formatTime(toast.timestamp) }}
                            </p>
                        </div>
                        <div class="ml-4 flex flex-shrink-0">
                            <button
                                @click="removeToast(toast.id)"
                                class="inline-flex rounded-md bg-white text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors duration-200"
                            >
                                <span class="sr-only">Fechar</span>
                                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Barra de progresso para auto-fechamento -->
                <div class="h-1 bg-gray-200">
                    <div 
                        class="h-full transition-all duration-5000 ease-linear"
                        :class="getProgressColor(toast.type)"
                        :style="{ width: progressWidth + '%' }"
                    ></div>
                </div>
            </div>
        </Transition>
    </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'

const toasts = ref([])
const progressWidth = ref(100)

const addToast = (notification) => {
    const toast = {
        id: Date.now() + Math.random(),
        ...notification
    }
    toasts.value.push(toast)
    
    // Auto-remover após 5 segundos
    setTimeout(() => {
        removeToast(toast.id)
    }, 5000)
}

const removeToast = (toastId) => {
    toasts.value = toasts.value.filter(t => t.id !== toastId)
}

const getBorderColor = (type) => {
    const colors = {
        'task_assigned': 'border-green-500',
        'task_delegated': 'border-blue-500',
        'task_created': 'border-purple-500',
        'task_status_updated': 'border-yellow-500',
        'task_comment_added': 'border-indigo-500',
        'user_joined': 'border-green-500',
        'user_left': 'border-gray-500'
    }
    return colors[type] || 'border-blue-500'
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

const getProgressColor = (type) => {
    const colors = {
        'task_assigned': 'bg-green-500',
        'task_delegated': 'bg-blue-500',
        'task_created': 'bg-purple-500',
        'task_status_updated': 'bg-yellow-500',
        'task_comment_added': 'bg-indigo-500',
        'user_joined': 'bg-green-500',
        'user_left': 'bg-gray-500'
    }
    return colors[type] || 'bg-blue-500'
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

// Expor métodos para uso externo
defineExpose({
    addToast,
    removeToast
})
</script>

<style scoped>
/* Animações customizadas */
@keyframes progress-bar {
    from {
        width: 100%;
    }
    to {
        width: 0%;
    }
}

.transition-all {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}
</style>
