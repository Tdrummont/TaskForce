<template>
    <Transition
        enter-active-class="transform ease-out duration-300 transition"
        enter-from-class="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
        enter-to-class="translate-y-0 opacity-100 sm:translate-x-0"
        leave-active-class="transition ease-in duration-100"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
    >
        <div
            v-if="show"
            class="pointer-events-auto w-full max-w-sm overflow-hidden rounded-lg bg-white shadow-lg ring-1 ring-black ring-opacity-5"
        >
            <div class="p-4">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <!-- Ícone baseado no tipo de notificação -->
                        <div 
                            :class="[
                                'w-8 h-8 rounded-full flex items-center justify-center',
                                getIconClass()
                            ]"
                        >
                            <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path :d="getIconPath()" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-3 w-0 flex-1 pt-0.5">
                        <p class="text-sm font-medium text-gray-900">
                            {{ notification.title }}
                        </p>
                        <p class="mt-1 text-sm text-gray-500">
                            {{ notification.message }}
                        </p>
                        <p class="mt-1 text-xs text-gray-400">
                            {{ formatTime(notification.timestamp) }}
                        </p>
                    </div>
                    <div class="ml-4 flex flex-shrink-0">
                        <button
                            @click="close"
                            class="inline-flex rounded-md bg-white text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                        >
                            <span class="sr-only">Fechar</span>
                            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </Transition>
</template>

<script setup>
import { ref, onMounted } from 'vue'

const props = defineProps({
    notification: {
        type: Object,
        required: true
    },
    duration: {
        type: Number,
        default: 5000
    }
})

const emit = defineEmits(['close'])

const show = ref(false)
let timeoutId = null

onMounted(() => {
    show.value = true
    
    // Auto-fechar após a duração especificada
    if (props.duration > 0) {
        timeoutId = setTimeout(() => {
            close()
        }, props.duration)
    }
})

const close = () => {
    show.value = false
    if (timeoutId) {
        clearTimeout(timeoutId)
    }
    setTimeout(() => {
        emit('close')
    }, 300) // Aguardar a animação de saída
}

const getIconClass = () => {
    const type = props.notification.type || 'info'
    const classes = {
        'task_assigned': 'bg-green-500',
        'task_delegated': 'bg-blue-500',
        'task_created': 'bg-purple-500',
        'task_status_updated': 'bg-yellow-500',
        'task_comment_added': 'bg-indigo-500',
        'user_joined': 'bg-green-500',
        'user_left': 'bg-gray-500',
        'info': 'bg-blue-500',
        'success': 'bg-green-500',
        'warning': 'bg-yellow-500',
        'error': 'bg-red-500'
    }
    return classes[type] || classes['info']
}

const getIconPath = () => {
    const type = props.notification.type || 'info'
    const paths = {
        'task_assigned': 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
        'task_delegated': 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z',
        'task_created': 'M12 6v6m0 0v6m0-6h6m-6 0H6',
        'task_status_updated': 'M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2',
        'task_comment_added': 'M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z',
        'user_joined': 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z',
        'user_left': 'M13 7a4 4 0 11-8 0 4 4 0 018 0zM9 14a6 6 0 00-6 6v1h12v-1a6 6 0 00-6-6z',
        'info': 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
        'success': 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
        'warning': 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z',
        'error': 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z'
    }
    return paths[type] || paths['info']
}

const formatTime = (timestamp) => {
    const now = new Date()
    const time = new Date(timestamp)
    const diff = now - time
    
    if (diff < 60000) { // Menos de 1 minuto
        return 'Agora mesmo'
    } else if (diff < 3600000) { // Menos de 1 hora
        const minutes = Math.floor(diff / 60000)
        return `${minutes} min atrás`
    } else if (diff < 86400000) { // Menos de 1 dia
        const hours = Math.floor(diff / 3600000)
        return `${hours}h atrás`
    } else {
        return time.toLocaleDateString('pt-BR')
    }
}
</script>
