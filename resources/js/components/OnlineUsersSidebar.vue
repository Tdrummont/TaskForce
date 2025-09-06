<template>
    <div class="fixed left-0 top-0 h-full w-full sm:w-80 bg-white shadow-2xl transform transition-transform duration-300 ease-in-out z-40 safe-area-inset-left"
         :class="{ 'translate-x-0': isOpen, '-translate-x-full': !isOpen }"
         :style="{ 
           paddingTop: 'env(safe-area-inset-top)',
           paddingBottom: 'env(safe-area-inset-bottom)',
           paddingLeft: 'env(safe-area-inset-left)',
           paddingRight: 'env(safe-area-inset-right)'
         }">
        
        <!-- Header do Sidebar -->
        <div class="bg-gradient-to-r from-blue-600 to-purple-600 p-4 sm:p-6 text-white ios-header">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 sm:w-10 sm:h-10 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-lg sm:text-xl font-bold">Usuários Online</h2>
                        <p class="text-blue-100 text-xs sm:text-sm">{{ onlineUsers.length }} conectado(s)</p>
                    </div>
                </div>
                <button @click="$emit('close')" 
                        class="text-white hover:bg-white hover:bg-opacity-20 p-2 rounded-full transition-colors touch-manipulation ios-button"
                        style="min-width: 44px; min-height: 44px;">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Status de Conexão -->
        <div class="p-3 sm:p-4 border-b border-gray-200">
            <div class="flex items-center space-x-2 sm:space-x-3">
                <div class="relative">
                    <div class="w-2.5 h-2.5 sm:w-3 sm:h-3 bg-green-500 rounded-full animate-pulse"></div>
                    <div class="absolute inset-0 w-2.5 h-2.5 sm:w-3 sm:h-3 bg-green-500 rounded-full animate-ping opacity-75"></div>
                </div>
                <span class="text-xs sm:text-sm font-medium text-gray-700">Conectado</span>
                <span class="text-xs text-gray-500 hidden sm:inline">• WebSocket ativo</span>
            </div>
        </div>

        <!-- Lista de Usuários Online -->
        <div class="flex-1 overflow-y-auto p-3 sm:p-4">
            <div class="space-y-2 sm:space-y-3">
                <div v-for="user in onlineUsers" :key="user.id" 
                     class="group flex items-center space-x-2 sm:space-x-3 p-2 sm:p-3 rounded-xl hover:bg-gray-50 transition-all duration-200 cursor-pointer touch-manipulation ios-list-item"
                     style="min-height: 60px;">
                    
                    <!-- Avatar do Usuário -->
                    <div class="relative flex-shrink-0">
                        <div class="w-10 h-10 sm:w-12 sm:h-12 bg-gradient-to-br from-blue-400 to-purple-500 rounded-full flex items-center justify-center text-white font-bold text-sm sm:text-lg shadow-lg">
                            {{ getInitials(user.name) }}
                        </div>
                        <!-- Indicador Online -->
                        <div class="absolute -bottom-0.5 -right-0.5 sm:-bottom-1 sm:-right-1 w-3 h-3 sm:w-4 sm:h-4 bg-green-500 border-2 border-white rounded-full"></div>
                    </div>

                    <!-- Informações do Usuário -->
                    <div class="flex-1 min-w-0">
                        <h3 class="text-xs sm:text-sm font-semibold text-gray-900 truncate">{{ user.name }}</h3>
                        <p class="text-xs text-gray-500 truncate hidden sm:block">{{ user.email }}</p>
                        <div class="flex items-center space-x-1 mt-0.5 sm:mt-1">
                            <div class="w-1.5 h-1.5 sm:w-2 sm:h-2 bg-green-500 rounded-full animate-pulse"></div>
                            <span class="text-xs text-green-600 font-medium">Online</span>
                        </div>
                    </div>

                    <!-- Menu de Ações (apenas em desktop) -->
                    <div class="opacity-0 group-hover:opacity-100 transition-opacity hidden sm:block">
                        <button class="text-gray-400 hover:text-gray-600 p-1 rounded-full hover:bg-gray-100">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Estado Vazio -->
                <div v-if="onlineUsers.length === 0" class="text-center py-6 sm:py-8">
                    <div class="w-12 h-12 sm:w-16 sm:h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3 sm:mb-4">
                        <svg class="w-6 h-6 sm:w-8 sm:h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <h3 class="text-xs sm:text-sm font-medium text-gray-900 mb-1">Nenhum usuário online</h3>
                    <p class="text-xs text-gray-500">Aguarde outros usuários se conectarem</p>
                </div>
            </div>
        </div>

        <!-- Footer com Estatísticas -->
        <div class="p-3 sm:p-4 border-t border-gray-200 bg-gray-50">
            <div class="grid grid-cols-2 gap-3 sm:gap-4">
                <div class="text-center">
                    <div class="text-base sm:text-lg font-bold text-gray-900">{{ onlineUsers.length }}</div>
                    <div class="text-xs text-gray-500">Online</div>
                </div>
                <div class="text-center">
                    <div class="text-base sm:text-lg font-bold text-gray-900">{{ totalUsers }}</div>
                    <div class="text-xs text-gray-500">Total</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Overlay para fechar o sidebar -->
    <div v-if="isOpen" 
         @click="$emit('close')"
         class="fixed inset-0 bg-black bg-opacity-50 z-30 transition-opacity duration-300"></div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'

const props = defineProps({
    isOpen: {
        type: Boolean,
        default: false
    }
})

const emit = defineEmits(['close'])

// Variáveis para gestos de swipe
let startX = 0
let startY = 0
let isDragging = false

// Função para detectar gestos de swipe
const handleTouchStart = (e) => {
    startX = e.touches[0].clientX
    startY = e.touches[0].clientY
    isDragging = true
    
    // Prevenir scroll durante o swipe
    e.preventDefault()
}

const handleTouchMove = (e) => {
    if (!isDragging) return
    
    const currentX = e.touches[0].clientX
    const currentY = e.touches[0].clientY
    const diffX = startX - currentX
    const diffY = startY - currentY
    
    // Detectar swipe horizontal
    if (Math.abs(diffX) > Math.abs(diffY)) {
        // Prevenir scroll vertical durante swipe horizontal
        e.preventDefault()
        
        // Swipe para a esquerda - fechar sidebar
        if (diffX > 80) { // Aumentar sensibilidade para iOS/Android
            emit('close')
            isDragging = false
        }
    }
}

const handleTouchEnd = (e) => {
    isDragging = false
    
    // Adicionar feedback tátil se disponível
    if (navigator.vibrate) {
        navigator.vibrate(10) // Vibração sutil
    }
}

onMounted(() => {
    // Adicionar listeners de touch apenas em dispositivos móveis
    if ('ontouchstart' in window) {
        document.addEventListener('touchstart', handleTouchStart, { passive: false })
        document.addEventListener('touchmove', handleTouchMove, { passive: false })
        document.addEventListener('touchend', handleTouchEnd, { passive: true })
    }
    
    // Detectar iOS
    const isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent) || 
                  (navigator.platform === 'MacIntel' && navigator.maxTouchPoints > 1)
    
    // Detectar Android
    const isAndroid = /Android/.test(navigator.userAgent)
    
    // Adicionar classes específicas para iOS/Android
    if (isIOS) {
        document.body.classList.add('ios-device')
    } else if (isAndroid) {
        document.body.classList.add('android-device')
    }
})

onUnmounted(() => {
    // Remover listeners
    if ('ontouchstart' in window) {
        document.removeEventListener('touchstart', handleTouchStart)
        document.removeEventListener('touchmove', handleTouchMove)
        document.removeEventListener('touchend', handleTouchEnd)
    }
})

// Dados dos usuários online
const onlineUsers = ref([
    { 
        id: 3, 
        name: 'Ana gabrielle', 
        email: 'gabyribeiro001@gmail.com',
        avatar: null,
        lastSeen: new Date()
    },
    { 
        id: 5, 
        name: 'T Drummont', 
        email: 'tdrummontt@gmail.com',
        avatar: null,
        lastSeen: new Date()
    }
])

const totalUsers = ref(2) // Total de usuários no sistema

// Conectar com WebSocket para atualizações em tempo real
onMounted(() => {
    // Importar WebSocketService
    import('../services/WebSocketService.js').then(({ default: WebSocketService }) => {
        // Listener para usuários online
        WebSocketService.on('users_online', (users) => {
            console.log('👥 Usuários online atualizados no sidebar:', users);
            if (users && Array.isArray(users)) {
                onlineUsers.value = users.map(user => ({
                    id: user.id,
                    name: user.name,
                    email: user.email,
                    avatar: user.avatar,
                    lastSeen: new Date()
                }));
            }
        });
        
        // Listener para usuário entrou
        WebSocketService.on('user_joined', (user) => {
            console.log('👋 Usuário entrou no sidebar:', user);
            if (user && !onlineUsers.value.find(u => u.id === user.id)) {
                onlineUsers.value.push({
                    id: user.id,
                    name: user.name,
                    email: user.email,
                    avatar: user.avatar,
                    lastSeen: new Date()
                });
            }
        });
        
        // Listener para usuário saiu
        WebSocketService.on('user_left', (user) => {
            console.log('👋 Usuário saiu do sidebar:', user);
            if (user) {
                onlineUsers.value = onlineUsers.value.filter(u => u.id !== user.id);
            }
        });
    });
});

// Função para obter iniciais do nome
const getInitials = (name) => {
    if (!name) return '?'
    return name.split(' ')
        .map(word => word.charAt(0))
        .join('')
        .toUpperCase()
        .substring(0, 2)
}

// Função para formatar tempo online
const getOnlineTime = (lastSeen) => {
    const now = new Date()
    const diff = now - new Date(lastSeen)
    const minutes = Math.floor(diff / 60000)
    
    if (minutes < 1) return 'Agora mesmo'
    if (minutes < 60) return `${minutes} min atrás`
    
    const hours = Math.floor(minutes / 60)
    if (hours < 24) return `${hours}h atrás`
    
    return 'Há muito tempo'
}
</script>

<style scoped>
/* Animações customizadas */
@keyframes pulse {
    0%, 100% {
        opacity: 1;
    }
    50% {
        opacity: 0.5;
    }
}

.animate-pulse {
    animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

@keyframes ping {
    75%, 100% {
        transform: scale(2);
        opacity: 0;
    }
}

.animate-ping {
    animation: ping 1s cubic-bezier(0, 0, 0.2, 1) infinite;
}

/* iOS e Android específicos */
@supports (-webkit-touch-callout: none) {
    /* iOS Safari */
    .ios-header {
        -webkit-backdrop-filter: blur(20px);
        backdrop-filter: blur(20px);
    }
    
    .ios-button {
        -webkit-tap-highlight-color: rgba(255, 255, 255, 0.2);
        -webkit-touch-callout: none;
        -webkit-user-select: none;
    }
    
    .ios-list-item {
        -webkit-tap-highlight-color: rgba(0, 0, 0, 0.1);
        -webkit-touch-callout: none;
        -webkit-user-select: none;
    }
    
    /* Melhor scroll no iOS */
    .overflow-y-auto {
        -webkit-overflow-scrolling: touch;
        overscroll-behavior: contain;
    }
}

/* Android Chrome */
@media screen and (-webkit-min-device-pixel-ratio: 0) {
    .ios-button {
        -webkit-tap-highlight-color: rgba(255, 255, 255, 0.2);
    }
    
    .ios-list-item {
        -webkit-tap-highlight-color: rgba(0, 0, 0, 0.1);
    }
}

/* Safe area support para iOS */
.safe-area-inset-left {
    padding-left: env(safe-area-inset-left);
}

/* Melhorias para dispositivos móveis */
@media (max-width: 640px) {
    /* iOS notch support */
    .ios-header {
        padding-top: max(1rem, env(safe-area-inset-top));
    }
    
    /* Android navigation bar */
    .ios-list-item {
        padding-bottom: max(0.5rem, env(safe-area-inset-bottom));
    }
    
    /* Melhor área de toque */
    .ios-button {
        min-width: 44px;
        min-height: 44px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    /* Scroll suave */
    .overflow-y-auto {
        scroll-behavior: smooth;
        -webkit-overflow-scrolling: touch;
    }
}

/* Detecção de iOS */
@supports (-webkit-touch-callout: none) {
    .ios-header {
        background: linear-gradient(135deg, #3B82F6 0%, #8B5CF6 100%);
        -webkit-backdrop-filter: saturate(180%) blur(20px);
        backdrop-filter: saturate(180%) blur(20px);
    }
}

/* Detecção de Android */
@media screen and (-webkit-min-device-pixel-ratio: 0) and (min-resolution: 0.001dpcm) {
    .ios-header {
        background: linear-gradient(135deg, #3B82F6 0%, #8B5CF6 100%);
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    }
}

/* Melhorias de performance */
.ios-list-item {
    will-change: transform;
    transform: translateZ(0);
    -webkit-transform: translateZ(0);
}

/* Prevenção de zoom em inputs (iOS) */
@media screen and (max-width: 640px) {
    input, textarea, select {
        font-size: 16px !important;
    }
}

/* Melhorias de acessibilidade */
@media (prefers-reduced-motion: reduce) {
    .animate-pulse,
    .animate-ping {
        animation: none;
    }
    
    .transition-all {
        transition: none;
    }
}

/* Dark mode support */
@media (prefers-color-scheme: dark) {
    .ios-header {
        background: linear-gradient(135deg, #1E40AF 0%, #7C3AED 100%);
    }
}
</style>
