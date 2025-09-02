<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Debug Broadcasting</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .log { background: #f5f5f5; padding: 10px; margin: 10px 0; border-radius: 5px; }
        .error { background: #ffebee; color: #c62828; }
        .success { background: #e8f5e9; color: #2e7d32; }
        .warning { background: #fff3e0; color: #ef6c00; }
        .info { background: #e3f2fd; color: #1976d2; }
        button { padding: 10px 20px; margin: 5px; cursor: pointer; }
    </style>
</head>
<body>
    <h1>🧪 Debug de Broadcasting</h1>
    
    <div id="status" class="log info">
        Inicializando...
    </div>
    
    <button onclick="testAuth()">Testar Autenticação</button>
    <button onclick="testChannel()">Testar Canal</button>
    <button onclick="clearLogs()">Limpar Logs</button>
    
    <div id="logs"></div>

    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    <script src="https://unpkg.com/laravel-echo@1.16.1/dist/echo.iife.js"></script>

    <script>
        let echo;
        const logs = document.getElementById('logs');
        const status = document.getElementById('status');

        function addLog(message, type = 'info') {
            const div = document.createElement('div');
            div.className = `log ${type}`;
            div.innerHTML = `[${new Date().toLocaleTimeString()}] ${message}`;
            logs.appendChild(div);
            logs.scrollTop = logs.scrollHeight;
        }

        function clearLogs() {
            logs.innerHTML = '';
        }

        // Configurar axios
        axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
        
        // Obter CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        
        if (csrfToken) {
            addLog('✅ CSRF Token encontrado: ' + csrfToken.substring(0, 10) + '...', 'success');
            axios.defaults.headers.common['X-CSRF-TOKEN'] = csrfToken;
        } else {
            addLog('❌ CSRF Token não encontrado', 'error');
        }

        // Testar autenticação
        async function testAuth() {
            addLog('🔐 Testando autenticação...', 'info');
            
            try {
                const response = await axios.post('/broadcasting/auth', {
                    socket_id: 'test-socket-id',
                    channel_name: 'user.1'
                });
                
                addLog('✅ Autenticação bem-sucedida: ' + JSON.stringify(response.data), 'success');
                
            } catch (error) {
                addLog('❌ Erro de autenticação: ' + error.response?.data?.message || error.message, 'error');
                addLog('📊 Status: ' + error.response?.status, 'error');
                addLog('📊 Headers: ' + JSON.stringify(error.response?.headers || {}), 'error');
            }
        }

        // Testar canal
        function testChannel() {
            addLog('📡 Testando conexão com canal...', 'info');
            
            try {
                // Configurar Pusher
                const pusher = new Pusher('661cf3c78faa86d8e332', {
                    cluster: 'sa1',
                    encrypted: true
                });

                addLog('✅ Pusher inicializado', 'success');

                // Configurar Echo
                echo = new Echo({
                    broadcaster: 'pusher',
                    key: '661cf3c78faa86d8e332',
                    cluster: 'sa1',
                    forceTLS: true,
                    authorizer: (channel, options) => {
                        return {
                            authorize: (socketId, callback) => {
                                addLog(`🔐 Autenticando canal: ${channel.name}`, 'info');
                                
                                axios.post('/broadcasting/auth', {
                                    socket_id: socketId,
                                    channel_name: channel.name
                                })
                                .then(response => {
                                    addLog(`✅ Canal autenticado: ${channel.name}`, 'success');
                                    callback(null, response.data);
                                })
                                .catch(error => {
                                    addLog(`❌ Erro no canal: ${error.response?.data?.message || error.message}`, 'error');
                                    addLog(`📊 Status: ${error.response?.status}`, 'error');
                                    callback(error);
                                });
                            }
                        };
                    }
                });

                // Tentar se conectar ao canal
                const channel = echo.private('user.1');
                
                channel.listen('.task.assigned', (data) => {
                    addLog('🔔 Notificação recebida: ' + JSON.stringify(data), 'success');
                });

                addLog('✅ Echo configurado e escutando canal user.1', 'success');
                status.textContent = 'Conectado e escutando...';
                status.className = 'log success';

            } catch (error) {
                addLog('❌ Erro ao configurar canal: ' + error.message, 'error');
                status.textContent = 'Erro na conexão';
                status.className = 'log error';
            }
        }

        // Inicializar automaticamente
        addLog('🚀 Página carregada', 'info');
        
        // Testar se está logado
        axios.get('/api/user')
            .then(response => {
                addLog('✅ Usuário logado: ' + response.data.name, 'success');
            })
            .catch(error => {
                addLog('⚠️ Usuário não logado ou erro na API', 'warning');
            });
    </script>
</body>
</html> 