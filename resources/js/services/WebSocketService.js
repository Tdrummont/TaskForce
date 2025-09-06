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
    }

    /**
     * Inicializar o serviço WebSocket
     */
    init(user) {
        if (!user || !window.Echo) {
            console.warn('⚠️ WebSocket não pode ser inicializado: usuário ou Echo não disponível');
            console.warn('👤 Usuário:', user);
            console.warn('🔊 Echo:', window.Echo);
            return;
        }

        this.userId = user.id;
        this.echo = window.Echo;
        
        console.log('🔊 Inicializando WebSocketService para usuário:', this.userId);
        console.log('👤 Dados do usuário:', user);
        console.log('🔊 Echo disponível:', !!this.echo);
        console.log('🔊 Echo object:', this.echo);
        
        this.setupConnectionMonitoring();
        this.setupUserChannel();
        this.setupPresenceChannel();
        
        this.isConnected = true;
        console.log('✅ WebSocketService inicializado com sucesso');
    }

    /**
     * Configurar monitoramento de conexão
     */
    setupConnectionMonitoring() {
        // Verificar conexão a cada 30 segundos
        this.connectionCheckInterval = setInterval(() => {
            if (this.echo && this.echo.connector && this.echo.connector.pusher) {
                const state = this.echo.connector.pusher.connection.state;
                if (state !== 'connected' && state !== 'connecting') {
                    console.warn('⚠️ Conexão WebSocket perdida, tentando reconectar...');
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
            console.warn('⚠️ Echo ou userId não disponível para configurar canal privado');
            console.warn('⚠️ Echo:', this.echo);
            console.warn('⚠️ userId:', this.userId);
            return;
        }

        console.log('🔌 Configurando canal privado para usuário:', this.userId);
        console.log('🔌 Echo disponível:', !!this.echo);
        console.log('🔌 Echo.private disponível:', typeof this.echo.private);
        
        const userChannel = this.echo.private(`App.Models.User.${this.userId}`);
        console.log('🔌 Canal privado criado:', userChannel);
        console.log('🔌 Nome do canal:', `App.Models.User.${this.userId}`);
        console.log('🔌 Canal tem método .notification:', typeof userChannel.notification);
        
        // Eventos de tarefas
        userChannel
            .listen('task.assigned', (data) => {
                console.log('📨 Evento task.assigned recebido:', data);
                this.handleTaskAssigned(data);
            })
            .listen('task.delegated', (data) => {
                console.log('📨 Evento task.delegated recebido:', data);
                this.handleTaskDelegated(data);
            })
            .listen('task.created', (data) => {
                console.log('📨 Evento task.created recebido:', data);
                this.handleTaskCreated(data);
            })
            .listen('task.status_updated', (data) => {
                console.log('📨 Evento task.status_updated recebido:', data);
                this.handleTaskStatusUpdated(data);
            })
            .listen('task.comment_added', (data) => {
                console.log('📨 Evento task.comment_added recebido:', data);
                this.handleTaskCommentAdded(data);
            })
            .notification((notification) => {
                console.log('🔔 NOTIFICATION RECEBIDA:', notification);
                console.log('🔔 Dados completos da notificação:', JSON.stringify(notification, null, 2));
                this.handleLaravelNotification(notification);
            });

        console.log('✅ Canal privado do usuário configurado para:', `App.Models.User.${this.userId}`);
    }

    /**
     * Configurar canal de presença para usuários online
     */
    setupPresenceChannel() {
        if (!this.echo) return;

        console.log('🔌 Tentando conectar ao canal de presença...');

        try {
            const presenceChannel = this.echo.join('online-users')
                .here((users) => {
                    console.log('👥 Usuários online recebidos:', users);
                    this.emit('users_online', users);
                })
                .joining((user) => {
                    console.log('👋 Usuário entrou:', user);
                    this.emit('user_joined', user);
                })
                .leaving((user) => {
                    console.log('👋 Usuário saiu:', user);
                    this.emit('user_left', user);
                })
                .error((error) => {
                    console.error('❌ Erro no canal de presença:', error);
                })
                .subscribed(() => {
                    console.log('✅ Conectado ao canal de presença com sucesso');
                });

            console.log('✅ Canal de presença configurado');
        } catch (error) {
            console.error('❌ Erro ao configurar canal de presença:', error);
        }
    }

    /**
     * Manipular evento de tarefa atribuída
     */
    handleTaskAssigned(data) {
        console.log('📨 Tarefa atribuída:', data);
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
        console.log('📨 Tarefa delegada recebida no WebSocketService:', data);
        console.log('📨 Dados completos:', JSON.stringify(data, null, 2));
        
        const notification = {
            type: 'task_delegated',
            title: 'Tarefa Delegada',
            message: data.message,
            data: data,
            timestamp: data.timestamp
        };
        
        console.log('📨 Emitindo evento task_delegated:', notification);
        this.emit('task_delegated', notification);
        console.log('✅ Evento task_delegated emitido com sucesso');
    }

    /**
     * Manipular evento de tarefa criada
     */
    handleTaskCreated(data) {
        console.log('📨 Tarefa criada:', data);
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
        console.log('🔔 Notificação Laravel processada:', notification);
        
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
        console.log('📨 Status da tarefa atualizado:', data);
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
        console.log('📨 Comentário adicionado:', data);
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
                    console.error(`❌ Erro no listener do evento ${event}:`, error);
                }
            });
        }
    }

    /**
     * Manipular reconexão
     */
    handleReconnection() {
        if (this.reconnectAttempts >= this.maxReconnectAttempts) {
            console.error('❌ Máximo de tentativas de reconexão atingido');
            return;
        }

        this.reconnectAttempts++;
        console.log(`🔄 Tentativa de reconexão ${this.reconnectAttempts}/${this.maxReconnectAttempts}`);

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
            console.log('🚪 Saindo do canal:', channelName);
            this.echo.leave(channelName);
        }
    }

    /**
     * Sair de todos os canais e desconectar
     */
    leaveAllChannels() {
        if (this.echo) {
            console.log('🚪 Saindo de todos os canais...');
            this.echo.leave('online-users');
            this.echo.leave('private-user.' + this.userId);
            this.echo.leave('tasks');
        }
    }

    /**
     * Desconectar e limpar recursos
     */
    disconnect() {
        if (this.connectionCheckInterval) {
            clearInterval(this.connectionCheckInterval);
        }

        if (this.echo) {
            console.log('🔌 Desconectando WebSocket...');
            this.leaveAllChannels();
            this.echo.disconnect();
        }

        this.listeners.clear();
        this.isConnected = false;
        console.log('✅ WebSocket desconectado completamente');
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
