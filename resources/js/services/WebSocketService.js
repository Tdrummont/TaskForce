/**
 * Serviço WebSocket para gerenciar conexões e eventos em tempo real
 */
class WebSocketService {
    constructor() {
        this.echo = null;
        this.userId = null;
        this.listeners = new Map();
        this.reconnectAttempts = 0;
        this.maxReconnectAttempts = 5;
        this.reconnectDelay = 1000; // 1 segundo
        this.isConnected = false;
        this.connectionCheckInterval = null;
        this.presenceChannel = null;
        this.userChannel = null;
    }

    /**
     * Inicializar o serviço WebSocket
     */
    init(user) {
        if (!user || !window.Echo) {
            return;
        }

        if (this.userId === user.id && this.echo === window.Echo && (this.userChannel || this.presenceChannel)) {
            return;
        }

        if (this.echo && this.userId !== user.id) {
            this.disconnect();
        }

        this.userId = user.id;
        this.echo = window.Echo;
        
        this.setupConnectionMonitoring();
        this.setupUserChannel();
        this.setupPresenceChannel();
        
        this.isConnected = true;
    }

    /**
     * Configurar monitoramento de conexão
     */
    setupConnectionMonitoring() {
        if (this.connectionCheckInterval) {
            clearInterval(this.connectionCheckInterval);
        }

        // Verificar conexão a cada 30 segundos
        this.connectionCheckInterval = setInterval(() => {
            if (this.echo && this.echo.connector && this.echo.connector.pusher) {
                const state = this.echo.connector.pusher.connection.state;
                if (state !== 'connected' && state !== 'connecting') {
                    this.handleReconnection();
                }
            }
        }, 30000);
    }

    /**
     * Configurar canal privado do usuário
     */
    setupUserChannel() {
        if (!this.echo || !this.userId) {
            return;
        }
        
        this.userChannel = this.echo.private(`App.Models.User.${this.userId}`);
        
        // Eventos de tarefas
        this.userChannel
            .listen('task.assigned', (data) => {
                this.handleTaskAssigned(data);
            })
            .listen('task.delegated', (data) => {
                this.handleTaskDelegated(data);
            })
            .listen('task.created', (data) => {
                this.handleTaskCreated(data);
            })
            .listen('task.status_updated', (data) => {
                this.handleTaskStatusUpdated(data);
            })
            .listen('task.comment_added', (data) => {
                this.handleTaskCommentAdded(data);
            })
            .notification((notification) => {
                this.handleLaravelNotification(notification);
            });
    }

    /**
     * Configurar canal de presença para usuários online
     */
    setupPresenceChannel() {
        if (!this.echo) return;

        try {
            this.presenceChannel = this.echo.join('online-users')
                .here((users) => {
                    this.emit('users_online', users);
                })
                .joining((user) => {
                    this.emit('user_joined', user);
                })
                .leaving((user) => {
                    this.emit('user_left', user);
                })
                .error((error) => {
                })
                .subscribed(() => {
                });
        } catch (error) {
        }
    }

    /**
     * Manipular evento de tarefa atribuída
     */
    handleTaskAssigned(data) {
        this.emit('task_assigned', {
            type: 'task_assigned',
            title: 'Nova Tarefa Atribuída',
            message: data.message,
            data: data,
            timestamp: data.timestamp
        });
    }

    /**
     * Manipular evento de tarefa delegada
     */
    handleTaskDelegated(data) {
        
        const notification = {
            type: 'task_delegated',
            title: 'Tarefa Delegada',
            message: data.message,
            data: data,
            timestamp: data.timestamp
        };
        this.emit('task_delegated', notification);
    }

    /**
     * Manipular evento de tarefa criada
     */
    handleTaskCreated(data) {
        this.emit('task_created', {
            type: 'task_created',
            title: 'Nova Tarefa Criada',
            message: data.message,
            data: data,
            timestamp: data.timestamp
        });
    }

    /**
     * Manipular notificações do Laravel
     */
    handleLaravelNotification(notification) {
        
        // Emitir evento genérico para notificações
        this.emit('laravel_notification', {
            type: notification.type || 'notification',
            title: notification.title || 'Nova Notificação',
            message: notification.message || 'Você tem uma nova notificação',
            data: notification,
            timestamp: notification.time || new Date().toISOString()
        });
    }

    /**
     * Manipular evento de status de tarefa atualizado
     */
    handleTaskStatusUpdated(data) {
        this.emit('task_status_updated', {
            type: 'task_status_updated',
            title: 'Status da Tarefa Atualizado',
            message: data.message,
            data: data,
            timestamp: data.timestamp
        });
    }

    /**
     * Manipular evento de comentário adicionado
     */
    handleTaskCommentAdded(data) {
        this.emit('task_comment_added', {
            type: 'task_comment_added',
            title: 'Novo Comentário',
            message: data.message,
            data: data,
            timestamp: data.timestamp
        });
    }

    /**
     * Adicionar listener para eventos
     */
    on(event, callback) {
        if (!this.listeners.has(event)) {
            this.listeners.set(event, []);
        }
        this.listeners.get(event).push(callback);
    }

    /**
     * Remover listener
     */
    off(event, callback) {
        if (this.listeners.has(event)) {
            const callbacks = this.listeners.get(event);
            const index = callbacks.indexOf(callback);
            if (index > -1) {
                callbacks.splice(index, 1);
            }
        }
    }

    /**
     * Emitir evento para listeners
     */
    emit(event, data) {
        if (this.listeners.has(event)) {
            this.listeners.get(event).forEach(callback => {
                try {
                    callback(data);
                } catch (error) {
                }
            });
        }
    }

    /**
     * Manipular reconexão
     */
    handleReconnection() {
        if (this.reconnectAttempts >= this.maxReconnectAttempts) {
            return;
        }

        this.reconnectAttempts++;

        setTimeout(() => {
            if (this.echo && this.echo.connector && this.echo.connector.pusher) {
                this.echo.connector.pusher.connect();
            }
        }, this.reconnectDelay * this.reconnectAttempts);
    }

    /**
     * Sair de um canal específico
     */
    leaveChannel(channelName) {
        if (this.echo) {
            this.echo.leave(channelName);
        }
    }

    /**
     * Sair de todos os canais e desconectar
     */
    leaveAllChannels() {
        if (this.echo) {
            this.echo.leave('online-users');
            if (this.userId) {
                this.echo.leave(`private-App.Models.User.${this.userId}`);
            }
        }

        this.presenceChannel = null;
        this.userChannel = null;
    }

    /**
     * Desconectar e limpar recursos
     */
    disconnect() {
        if (this.connectionCheckInterval) {
            clearInterval(this.connectionCheckInterval);
        }

        if (this.echo) {
            this.leaveAllChannels();
            this.echo.disconnect();
        }

        this.listeners.clear();
        this.isConnected = false;
        this.echo = null;
        this.userId = null;
    }

    /**
     * Verificar se está conectado
     */
    isWebSocketConnected() {
        return this.isConnected && this.echo && 
               this.echo.connector && 
               this.echo.connector.pusher && 
               this.echo.connector.pusher.connection.state === 'connected';
    }
}

// Criar instância singleton
const webSocketService = new WebSocketService();

export default webSocketService;
