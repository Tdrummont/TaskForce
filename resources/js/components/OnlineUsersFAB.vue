<template>
    <!-- Botão Flutuante de Usuários Online -->
    <div 
        class="fixed bottom-6 left-6 z-[999999]"
        style="position: fixed !important; bottom: 1.5rem !important; left: 1.5rem !important; z-index: 999999 !important;"
    >
        <!-- Botão Principal -->
        <button
            @click="toggleFab"
            class="bg-gradient-to-r from-orange-500 to-red-500 hover:from-orange-600 hover:to-red-600 text-white w-16 h-16 rounded-full shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-110 flex items-center justify-center border-4 border-white"
            style="box-shadow: 0 8px 32px rgba(0,0,0,0.3);"
            :title="`Ver usuários online (${onlineUsers.length} usuários)`"
        >
            <!-- Contador de Usuários -->
            <div 
                v-if="onlineUsers.length > 0"
                class="absolute -top-2 -right-2 w-6 h-6 bg-red-500 text-white text-xs font-bold rounded-full flex items-center justify-center border-2 border-white"
            >
                {{ onlineUsers.length }}
            </div>
            
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
        </button>

        <!-- Menu de Usuários Online (quando aberto) -->
        <div 
            v-if="showFabMenu"
            class="absolute bottom-20 left-0 bg-white rounded-lg shadow-xl border border-gray-200 p-4 min-w-[280px] max-w-[320px]"
            style="z-index: 999998;"
        >
            <!-- Header do Menu -->
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-lg font-semibold text-gray-800">Usuários Online</h3>
                <button 
                    @click="toggleFab"
                    class="text-gray-400 hover:text-gray-600 transition-colors"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Lista de Usuários Online -->
            <div class="space-y-2 max-h-64 overflow-y-auto">
                <div 
                    v-for="user in onlineUsers" 
                    :key="user.id"
                    class="flex items-center space-x-3 p-2 rounded-lg hover:bg-gray-50 transition-colors"
                >
                    <!-- Avatar do Usuário -->
                    <div class="relative">
                        <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-purple-500 rounded-full flex items-center justify-center text-white font-semibold text-sm">
                            {{ getUserInitials(user.name) }}
                        </div>
                        <!-- Indicador Online -->
                        <div class="absolute -bottom-1 -right-1 w-4 h-4 bg-green-500 border-2 border-white rounded-full"></div>
                    </div>
                    
                    <!-- Informações do Usuário -->
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 truncate">{{ user.name }}</p>
                        <p class="text-xs text-gray-500 truncate">{{ user.email }}</p>
                    </div>
                </div>

                <!-- Mensagem quando não há usuários online -->
                <div 
                    v-if="onlineUsers.length === 0"
                    class="text-center py-4 text-gray-500"
                >
                    <svg class="w-12 h-12 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <p class="text-sm">Nenhum usuário online</p>
                </div>
            </div>
        </div>

        <!-- Overlay para fechar o menu -->
        <div 
            v-if="showFabMenu" 
            @click="toggleFab"
            class="fixed inset-0 bg-black bg-opacity-25 z-[999997]"
        ></div>
    </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import { usePage } from '@inertiajs/vue3'


// Estado reativo
const showFabMenu = ref(false)
const onlineUsers = ref([])
const webSocketService = ref(null)

// Props
const props = defineProps({
    onlineUsersCount: {
        type: Number,
        default: 0
    }
})

// Função para obter iniciais do nome
const getUserInitials = (name) => {
    if (!name) return '?'
    return name
        .split(' ')
        .map(word => word.charAt(0))
        .join('')
        .toUpperCase()
        .slice(0, 2)
}

// Função para alternar o menu FAB
const toggleFab = () => {
    showFabMenu.value = !showFabMenu.value
    console.log('🎯 Botão FAB de usuários online clicado!', showFabMenu.value)
}


// Função para lidar com usuários online
const handleUsersOnline = (users) => {
    console.log('👥 Usuários online recebidos:', users)
    onlineUsers.value = users || []
}

// Função para lidar com usuário que entrou
const handleUserJoined = (user) => {
    console.log('👋 Usuário entrou:', user)
    // Verificar se o usuário já não está na lista
    const existingUser = onlineUsers.value.find(u => u.id === user.id)
    if (!existingUser) {
        onlineUsers.value.push(user)
    }
}

// Função para lidar com usuário que saiu
const handleUserLeft = (user) => {
    console.log('👋 Usuário saiu:', user)
    onlineUsers.value = onlineUsers.value.filter(u => u.id !== user.id)
}

// Inicializar WebSocket quando o componente for montado
onMounted(async () => {
    try {
        // Importar o WebSocketService
        const module = await import('../services/WebSocketService.js')
        webSocketService.value = module.default
        
        console.log('🔌 Conectando ao WebSocketService para usuários online...')
        
        // Configurar listeners para eventos de usuários online
        webSocketService.value.on('users_online', handleUsersOnline)
        webSocketService.value.on('user_joined', handleUserJoined)
        webSocketService.value.on('user_left', handleUserLeft)
        
        console.log('✅ Listeners de usuários online configurados')
        
    } catch (error) {
        console.error('❌ Erro ao inicializar WebSocketService:', error)
        // Não adicionar usuários de exemplo - manter lista vazia até receber dados reais
    }
})

// Limpar listeners quando o componente for desmontado
onUnmounted(() => {
    if (webSocketService.value) {
        webSocketService.value.off('users_online', handleUsersOnline)
        webSocketService.value.off('user_joined', handleUserJoined)
        webSocketService.value.off('user_left', handleUserLeft)
        console.log('🧹 Listeners de usuários online removidos')
        
        // Não desconectar o WebSocketService aqui pois pode estar sendo usado por outros componentes
        // A desconexão será feita no AuthenticatedLayout
    }
})
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

/* Melhorias para dispositivos móveis */
@media (max-width: 640px) {
    .fixed.bottom-6.left-6 {
        bottom: 1rem;
        left: 1rem;
    }
    
    .absolute.bottom-20.left-0 {
        bottom: 5rem;
        left: 0;
    }
}

/* Melhorias de performance */
button {
    will-change: transform;
    transform: translateZ(0);
    -webkit-transform: translateZ(0);
}

/* Melhorias de acessibilidade */
@media (prefers-reduced-motion: reduce) {
    .animate-pulse {
        animation: none;
    }
    
    .transition-all {
        transition: none;
    }
}
</style>
