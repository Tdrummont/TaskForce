<template>
    <div class="flex items-center space-x-3">
        <!-- Indicador de Status da Conexão -->
        <div class="flex items-center space-x-2 bg-white bg-opacity-10 backdrop-blur-sm rounded-full px-3 py-2">
            <!-- Ponto de Status Animado -->
            <div class="relative">
                <div 
                    :class="[
                        'w-3 h-3 rounded-full transition-all duration-500',
                        connectionStatus === 'connected' ? 'bg-green-400' : 
                        connectionStatus === 'connecting' ? 'bg-yellow-400' : 
                        'bg-red-400'
                    ]"
                ></div>
                <!-- Efeito de pulsação para online -->
                <div 
                    v-if="connectionStatus === 'connected'"
                    class="absolute inset-0 w-3 h-3 bg-green-400 rounded-full animate-ping opacity-75"
                ></div>
                <!-- Efeito de pulsação para conectando -->
                <div 
                    v-if="connectionStatus === 'connecting'"
                    class="absolute inset-0 w-3 h-3 bg-yellow-400 rounded-full animate-pulse opacity-75"
                ></div>
            </div>
            
            <!-- Texto de Status -->
            <span class="text-xs font-medium text-white">
                {{ connectionStatus === 'connected' ? 'Conectado' : 
                   connectionStatus === 'connecting' ? 'Conectando...' : 'Desconectado' }}
            </span>
        </div>

        <!-- Usuários Online com Avatares -->
        <div v-if="onlineUsers.length > 0" class="flex items-center space-x-2">
            <!-- Container dos Avatares -->
            <div class="flex -space-x-2">
                <div 
                    v-for="(user, index) in displayUsers" 
                    :key="user.id"
                    class="relative group"
                >
                    <!-- Avatar do Usuário -->
                    <div 
                        class="w-8 h-8 rounded-full border-2 border-white bg-gradient-to-br from-blue-400 to-purple-500 flex items-center justify-center text-white text-xs font-bold shadow-lg transition-transform duration-200 hover:scale-110"
                        :title="user.name"
                    >
                        {{ user.name.charAt(0).toUpperCase() }}
                    </div>
                    
                    <!-- Indicador Online no Avatar -->
                    <div class="absolute -bottom-0.5 -right-0.5 w-3 h-3 bg-green-400 border-2 border-white rounded-full">
                        <div class="w-full h-full bg-green-400 rounded-full animate-pulse"></div>
                    </div>
                    
                    <!-- Tooltip com Nome -->
                    <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-2 py-1 bg-gray-900 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity duration-200 pointer-events-none whitespace-nowrap z-50">
                        {{ user.name }}
                        <div class="absolute top-full left-1/2 transform -translate-x-1/2 w-0 h-0 border-l-4 border-r-4 border-t-4 border-transparent border-t-gray-900"></div>
                    </div>
                </div>
                
                <!-- Indicador de Mais Usuários -->
                <div 
                    v-if="onlineUsers.length > 3"
                    class="w-8 h-8 rounded-full border-2 border-white bg-gray-600 flex items-center justify-center text-white text-xs font-bold shadow-lg"
                    :title="`+${onlineUsers.length - 3} usuários online`"
                >
                    +{{ onlineUsers.length - 3 }}
                </div>
            </div>
            
            <!-- Contador de Usuários Online -->
            <div class="flex items-center space-x-1 bg-white bg-opacity-10 backdrop-blur-sm rounded-full px-2 py-1">
                <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                <span class="text-xs font-medium text-white">
                    {{ onlineUsers.length }} {{ onlineUsers.length === 1 ? 'online' : 'online' }}
                </span>
            </div>
        </div>

        <!-- Indicador de Atividade Recente -->
        <div v-if="recentActivity" class="flex items-center space-x-1 bg-white bg-opacity-10 backdrop-blur-sm rounded-full px-2 py-1">
            <div class="w-2 h-2 bg-blue-400 rounded-full animate-pulse"></div>
            <span class="text-xs text-white">Ativo</span>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'

const connectionStatus = ref('connected')
const onlineUsers = ref([
    { id: 1, name: 'T Drummont' },
    { id: 2, name: 'Ana Gabrielle' }
])
const recentActivity = ref(true)

// Mostrar apenas os primeiros 3 usuários
const displayUsers = computed(() => {
    return onlineUsers.value.slice(0, 3)
})

// Simular mudanças de status para demonstração
onMounted(() => {
    // Simular usuários entrando e saindo
    setInterval(() => {
        if (Math.random() > 0.7) {
            const newUser = {
                id: Date.now(),
                name: `Usuário ${Math.floor(Math.random() * 100)}`
            }
            onlineUsers.value.push(newUser)
            
            // Remover após 10 segundos
            setTimeout(() => {
                onlineUsers.value = onlineUsers.value.filter(u => u.id !== newUser.id)
            }, 10000)
        }
    }, 5000)
    
    // Simular atividade recente
    setInterval(() => {
        recentActivity.value = !recentActivity.value
    }, 3000)
})

// Expor métodos para uso externo
defineExpose({
    updateConnectionStatus: (status) => {
        connectionStatus.value = status
    },
    updateOnlineUsers: (users) => {
        onlineUsers.value = users
    }
})
</script>

<style scoped>
/* Animações customizadas */
@keyframes pulse-glow {
    0%, 100% {
        box-shadow: 0 0 5px rgba(34, 197, 94, 0.5);
    }
    50% {
        box-shadow: 0 0 20px rgba(34, 197, 94, 0.8);
    }
}

.animate-pulse-glow {
    animation: pulse-glow 2s ease-in-out infinite;
}

/* Efeito de hover nos avatares */
.group:hover .group-hover\:scale-110 {
    transform: scale(1.1);
}

/* Transições suaves */
.transition-all {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}
</style>
