<template>
    <div class="flex items-center space-x-2">
        <!-- Indicador de Status da Conexão -->
        <div class="flex items-center space-x-1">
            <div 
                :class="[
                    'w-2 h-2 rounded-full transition-all duration-300',
                    connectionStatus === 'connected' ? 'bg-green-500 animate-pulse' : 
                    connectionStatus === 'connecting' ? 'bg-yellow-500 animate-pulse' : 
                    'bg-red-500'
                ]"
            ></div>
            <span class="text-xs text-gray-600">
                {{ connectionStatus === 'connected' ? 'Online' : 
                   connectionStatus === 'connecting' ? 'Conectando...' : 'Offline' }}
            </span>
        </div>

        <!-- Usuários Online -->
        <div class="flex items-center space-x-1" v-if="onlineUsers.length > 0">
            <div class="flex -space-x-1">
                <div 
                    v-for="user in onlineUsers.slice(0, 3)" 
                    :key="user.id"
                    class="w-6 h-6 rounded-full border-2 border-white bg-blue-500 flex items-center justify-center text-white text-xs font-medium"
                    :title="user.name"
                >
                    {{ user.name.charAt(0).toUpperCase() }}
                </div>
                <div 
                    v-if="onlineUsers.length > 3"
                    class="w-6 h-6 rounded-full border-2 border-white bg-gray-500 flex items-center justify-center text-white text-xs font-medium"
                >
                    +{{ onlineUsers.length - 3 }}
                </div>
            </div>
            <span class="text-xs text-gray-600">
                {{ onlineUsers.length }} online
            </span>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'

const connectionStatus = ref('disconnected')
const onlineUsers = ref([])

let webSocketService = null

onMounted(async () => {
    // Importar o serviço WebSocket
    const { default: WebSocketService } = await import('../services/WebSocketService.js')
    webSocketService = WebSocketService
    
    // Verificar status da conexão
    updateConnectionStatus()
    
    // Configurar listeners
    webSocketService.on('users_online', (users) => {
        onlineUsers.value = users
    })
    
    webSocketService.on('user_joined', (user) => {
        if (!onlineUsers.value.find(u => u.id === user.id)) {
            onlineUsers.value.push(user)
        }
    })
    
    webSocketService.on('user_left', (user) => {
        onlineUsers.value = onlineUsers.value.filter(u => u.id !== user.id)
    })
    
    // Verificar status da conexão a cada 5 segundos
    const statusInterval = setInterval(() => {
        updateConnectionStatus()
    }, 5000)
    
    onUnmounted(() => {
        clearInterval(statusInterval)
    })
})

const updateConnectionStatus = () => {
    if (webSocketService) {
        connectionStatus.value = webSocketService.isWebSocketConnected() ? 'connected' : 'disconnected'
    }
}
</script>
