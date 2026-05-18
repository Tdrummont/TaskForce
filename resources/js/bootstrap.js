import axios from 'axios';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

window.axios.defaults.withCredentials = true;
window.axios.defaults.withXSRFToken = true;

// CSRF global
const csrfToken = document
    .querySelector('meta[name="csrf-token"]')
    ?.getAttribute('content');

if (csrfToken) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = csrfToken;
}
// Configuração do Laravel Echo para WebSockets
window.Pusher = Pusher;

// Verificar se as variáveis de ambiente estão disponíveis
const reverbKey = import.meta.env.VITE_REVERB_APP_KEY;
const reverbHost = import.meta.env.VITE_REVERB_HOST || window.location.hostname;
const reverbPort = Number(import.meta.env.VITE_REVERB_PORT) || 8080;
const reverbScheme = import.meta.env.VITE_REVERB_SCHEME || 'http';

if (reverbKey) {
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

                    if (!csrfToken) {
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
                        callback(null, response.data);
                    })
                    .catch(error => {
                        callback(error);
                    });
                }
            };
        }
    });
}
