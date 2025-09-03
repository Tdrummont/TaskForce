import axios from 'axios';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';


 // 🔐 CSRF global para T-O-D-O request Axios (ex.: POST /change-language)
 const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
 if (csrfToken) {
   window.axios.defaults.headers.common['X-CSRF-TOKEN'] = csrfToken;
 } else {
   console.warn('⚠️ CSRF meta tag não encontrada. Confira seu Blade base.');
 }
// Configuração do Laravel Echo para WebSockets
// Só inicializar o Pusher se as variáveis de ambiente estiverem disponíveis
const pusherKey = import.meta.env.VITE_PUSHER_APP_KEY;
const pusherCluster = import.meta.env.VITE_PUSHER_APP_CLUSTER;

if (pusherKey && pusherKey !== 'null' && pusherKey !== 'undefined') {
    console.log('✅ Pusher configurado:', { key: pusherKey, cluster: pusherCluster });
    
    // Só definir window.Pusher se tivermos uma chave válida
    window.Pusher = Pusher;
    
    window.Echo = new Echo({
        broadcaster: 'pusher',
        key: pusherKey,
        cluster: pusherCluster || 'mt1',
        forceTLS: true,
        encrypted: true,
        authorizer: (channel, options) => {
            return {
                authorize: (socketId, callback) => {
                    // Usar apenas autenticação via CSRF token (sessão web)
                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                    
                    console.log('🔐 Autenticando canal:', channel.name);
                    console.log('🔑 CSRF Token:', csrfToken ? 'Presente' : 'Ausente');
                    
                    if (!csrfToken) {
                        console.error('❌ CSRF Token não encontrado');
                        callback(new Error('CSRF Token não encontrado'));
                        return;
                    }
                    
                    // Headers para autenticação via sessão (Web)
                    const headers = {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest'
                    };
                    
                    axios.post('/broadcasting/auth', {
                        socket_id: socketId,
                        channel_name: channel.name
                    }, { headers })
                    .then(response => {
                        console.log('✅ Canal autenticado com sucesso:', channel.name);
                        callback(null, response.data);
                    })
                    .catch(error => {
                        console.error('❌ Erro na autenticação do canal:', error);
                        console.error('📊 Detalhes do erro:', {
                            status: error.response?.status,
                            message: error.response?.data?.message || error.message,
                            channel: channel.name,
                            headers: headers
                        });
                        callback(error);
                    });
                }
            };
        }
    });
} else {
    console.warn('⚠️ VITE_PUSHER_APP_KEY não está configurado. WebSockets não funcionarão.');
    console.warn('📝 Configure no arquivo .env: VITE_PUSHER_APP_KEY=your_app_key');
    console.log('🚀 Aplicação funcionará sem WebSockets (modo estático)');
    
    // Criar um Echo mock para evitar erros
    window.Echo = {
        channel: () => ({
            listen: () => ({
                listen: () => null
            }),
            subscribed: () => false
        }),
        private: () => ({
            listen: () => ({
                listen: () => null
            }),
            subscribed: () => false
        }),
        join: () => ({
            listen: () => ({
                listen: () => null
            }),
            subscribed: () => false
        })
    };
}
