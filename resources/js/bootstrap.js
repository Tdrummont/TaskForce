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
window.Pusher = Pusher;

// Verificar se as variáveis de ambiente estão disponíveis
const reverbKey = import.meta.env.VITE_REVERB_APP_KEY;
const reverbHost = import.meta.env.VITE_REVERB_HOST;
const reverbPort = import.meta.env.VITE_REVERB_PORT;
const reverbScheme = import.meta.env.VITE_REVERB_SCHEME;

console.log('🔧 Variáveis de ambiente Reverb:', {
    key: reverbKey,
    host: reverbHost,
    port: reverbPort,
    scheme: reverbScheme
});

if (!reverbKey) {
    console.warn('⚠️ VITE_REVERB_APP_KEY não está configurado. WebSockets não funcionarão.');
    console.warn('📝 Configure no arquivo .env: VITE_REVERB_APP_KEY=local');
} else {
    console.log('✅ Reverb configurado:', { key: reverbKey, host: reverbHost, port: reverbPort });
    
    window.Echo = new Echo({
        broadcaster: 'reverb',
        key: reverbKey,
        wsHost: reverbHost,
        wsPort: reverbPort,
        wssPort: reverbPort,
        forceTLS: reverbScheme === 'https',
        enabledTransports: ['ws', 'wss'],
        authEndpoint: '/broadcasting/auth',
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
}
