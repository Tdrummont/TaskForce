<template>
    <div class="notifications-container">
        <!-- Indicador de status da conexão -->
        <div class="connection-status" :class="connectionStatus">
            <span class="status-dot"></span>
            {{ connectionStatusText }}
        </div>

        <!-- Lista de notificações -->
        <div v-if="notifications.length > 0" class="notifications-list">
            <div 
                v-for="notification in notifications" 
                :key="notification.id"
                class="notification-item"
                :class="notification.type"
            >
                <div class="notification-content">
                    <h4 class="notification-title">{{ notification.title }}</h4>
                    <p class="notification-message">{{ notification.message }}</p>
                    <span class="notification-time">{{ formatTime(notification.created_at) }}</span>
                </div>
                <button 
                    @click="markAsRead(notification.id)"
                    class="mark-read-btn"
                    :disabled="notification.read_at"
                >
                    {{ notification.read_at ? 'Lido' : 'Marcar como lido' }}
                </button>
            </div>
        </div>

        <!-- Mensagem quando não há notificações -->
        <div v-else class="no-notifications">
            <p>Nenhuma notificação no momento</p>
        </div>

        <!-- Botão para limpar todas as notificações -->
        <button 
            v-if="notifications.length > 0"
            @click="clearAllNotifications"
            class="clear-all-btn"
        >
            Limpar todas
        </button>
    </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';

const props = defineProps({
    userId: {
        type: [Number, String],
        required: true
    }
});
        const notifications = ref([]);
        const connectionStatus = ref('disconnected');
        const connectionStatusText = ref('Desconectado');

        // Função para formatar o tempo
        const formatTime = (timestamp) => {
            const date = new Date(timestamp);
            const now = new Date();
            const diffInMinutes = Math.floor((now - date) / (1000 * 60));
            
            if (diffInMinutes < 1) return 'Agora mesmo';
            if (diffInMinutes < 60) return `${diffInMinutes} min atrás`;
            if (diffInMinutes < 1440) return `${Math.floor(diffInMinutes / 60)}h atrás`;
            return date.toLocaleDateString('pt-BR');
        };

        // Função para marcar notificação como lida
        const markAsRead = async (notificationId) => {
            try {
                // Usar rota web em vez de API
                const response = await axios.patch(`/notifications/${notificationId}/read`);
                
                if (response.status === 200) {
                    const notification = notifications.value.find(n => n.id === notificationId);
                    if (notification) {
                        notification.read_at = new Date().toISOString();
                    }
                }
            } catch (error) {
                console.error('Erro ao marcar notificação como lida:', error);
            }
        };

        // Função para limpar todas as notificações
        const clearAllNotifications = async () => {
            try {
                // Usar rota web em vez de API
                const response = await axios.delete('/notifications/clear-all');
                
                if (response.status === 200) {
                    notifications.value = [];
                }
            } catch (error) {
                console.error('Erro ao limpar notificações:', error);
            }
        };

        // Função para inicializar o Echo
        const initializeEcho = () => {
            if (window.Echo) {
                console.log('🔌 Inicializando Laravel Echo para notificações...');
                
                // Canal privado para notificações do usuário
                const channel = window.Echo.private(`user.${props.userId}`);
                
                // Escutar por novas notificações
                channel.listen('NotificationSent', (e) => {
                    console.log('📢 Nova notificação recebida:', e);
                    notifications.value.unshift({
                        id: e.notification.id,
                        title: e.notification.title,
                        message: e.notification.message,
                        type: e.notification.type || 'info',
                        created_at: new Date().toISOString(),
                        read_at: null
                    });
                });

                // Escutar por atualizações de tarefas
                channel.listen('TaskUpdated', (e) => {
                    console.log('📝 Tarefa atualizada:', e);
                    notifications.value.unshift({
                        id: Date.now(),
                        title: 'Tarefa Atualizada',
                        message: `A tarefa "${e.task.title}" foi atualizada`,
                        type: 'info',
                        created_at: new Date().toISOString(),
                        read_at: null
                    });
                });

                // Escutar por novas tarefas atribuídas
                channel.listen('TaskAssigned', (e) => {
                    console.log('📋 Nova tarefa atribuída:', e);
                    notifications.value.unshift({
                        id: Date.now(),
                        title: 'Nova Tarefa Atribuída',
                        message: `Você recebeu uma nova tarefa: "${e.task.title}"`,
                        type: 'success',
                        created_at: new Date().toISOString(),
                        read_at: null
                    });
                });

                // Escutar por tarefas delegadas
                channel.listen('TaskDelegated', (e) => {
                    console.log('🔄 Tarefa delegada:', e);
                    notifications.value.unshift({
                        id: Date.now(),
                        title: 'Tarefa Delegada',
                        message: `Tarefa "${e.task.title}" foi delegada para você por ${e.delegated_by.name}`,
                        type: 'info',
                        created_at: new Date().toISOString(),
                        read_at: null
                    });
                });

                // Atualizar status da conexão
                connectionStatus.value = 'connected';
                connectionStatusText.value = 'Conectado';
                
                console.log('✅ Laravel Echo configurado com sucesso para notificações');
            } else {
                console.error('❌ Laravel Echo não está disponível');
                connectionStatus.value = 'error';
                connectionStatusText.value = 'Erro na conexão';
            }
        };

        // Função para carregar notificações existentes
        const loadNotifications = async () => {
            try {
                // Usar rota da API para consistência
                const response = await axios.get('/api/notifications');
                if (response.data && response.data.success && response.data.notifications) {
                    notifications.value = response.data.notifications;
                    console.log('✅ Notificações carregadas:', response.data.notifications.length);
                } else {
                    console.warn('⚠️ Resposta da API não contém notificações válidas:', response.data);
                    notifications.value = [];
                }
            } catch (error) {
                console.error('❌ Erro ao carregar notificações:', error);
                // Se falhar, usar notificações em memória
                notifications.value = [];
            }
        };

        onMounted(async () => {
            // Aguardar um pouco para o Echo ser inicializado
            setTimeout(() => {
                initializeEcho();
                loadNotifications();
            }, 1000);
        });

        onUnmounted(() => {
            // Limpar listeners quando o componente for desmontado
            if (window.Echo) {
                window.Echo.leave(`user.${props.userId}`);
            }
        });
</script>

<style scoped>
.notifications-container {
    background-color: white;
    border-radius: 0.5rem;
    box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);
    padding: 1rem;
    max-width: 28rem;
}

.connection-status {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 1rem;
    padding: 0.5rem;
    border-radius: 0.375rem;
    font-size: 0.875rem;
    font-weight: 500;
}

.connection-status.connected {
    background-color: rgb(220 252 231);
    color: rgb(22 101 52);
}

.connection-status.disconnected {
    background-color: rgb(243 244 246);
    color: rgb(31 41 55);
}

.connection-status.error {
    background-color: rgb(254 226 226);
    color: rgb(153 27 27);
}

.status-dot {
    width: 0.5rem;
    height: 0.5rem;
    border-radius: 9999px;
}

.connected .status-dot {
    background-color: rgb(34 197 94);
}

.disconnected .status-dot {
    background-color: rgb(107 114 128);
}

.error .status-dot {
    background-color: rgb(239 68 68);
}

.notifications-list {
    margin-bottom: 1rem;
}

.notifications-list > * + * {
    margin-top: 0.75rem;
}

.notification-item {
    border: 1px solid rgb(229 231 235);
    border-radius: 0.5rem;
    padding: 0.75rem;
    transition: all 0.2s;
}

.notification-item:hover {
    box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);
}

.notification-item.info {
    border-color: rgb(191 219 254);
    background-color: rgb(239 246 255);
}

.notification-item.success {
    border-color: rgb(187 247 208);
    background-color: rgb(240 253 244);
}

.notification-item.warning {
    border-color: rgb(254 240 138);
    background-color: rgb(255 251 235);
}

.notification-item.error {
    border-color: rgb(254 202 202);
    background-color: rgb(254 242 242);
}

.notification-content {
    margin-bottom: 0.5rem;
}

.notification-title {
    font-size: 0.875rem;
    font-weight: 600;
    color: rgb(17 24 39);
    margin-bottom: 0.25rem;
}

.notification-message {
    font-size: 0.875rem;
    color: rgb(75 85 99);
    margin-bottom: 0.5rem;
}

.notification-time {
    font-size: 0.75rem;
    color: rgb(107 114 128);
}

.mark-read-btn {
    font-size: 0.75rem;
    padding: 0.25rem 0.5rem;
    border-radius: 0.25rem;
    background-color: rgb(59 130 246);
    color: white;
    border: none;
    cursor: pointer;
    transition: background-color 0.2s;
}

.mark-read-btn:hover:not(:disabled) {
    background-color: rgb(37 99 235);
}

.mark-read-btn:disabled {
    background-color: rgb(156 163 175);
    cursor: not-allowed;
}

.clear-all-btn {
    width: 100%;
    padding: 0.5rem 1rem;
    background-color: rgb(107 114 128);
    color: white;
    border-radius: 0.375rem;
    border: none;
    cursor: pointer;
    transition: background-color 0.2s;
}

.clear-all-btn:hover {
    background-color: rgb(75 85 99);
}

.no-notifications {
    text-align: center;
    color: rgb(107 114 128);
    padding: 2rem 0;
}
</style> 