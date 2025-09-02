<template>
    <div class="min-h-screen bg-gray-100 flex items-center justify-center">
        <div class="max-w-md w-full bg-white rounded-lg shadow-lg p-8">
            <!-- Loading State -->
            <div v-if="isLoading" class="text-center">
                <div class="loading-spinner mb-4">
                    <svg class="animate-spin h-12 w-12 text-blue-600 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>
                <h2 class="text-xl font-semibold text-gray-900 mb-2">Processando Login</h2>
                <p class="text-gray-600">Aguarde enquanto finalizamos sua autenticação...</p>
            </div>

            <!-- Success State -->
            <div v-else-if="isSuccess" class="text-center">
                <div class="success-icon mb-4">
                    <svg class="h-12 w-12 text-green-600 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h2 class="text-xl font-semibold text-gray-900 mb-2">Login Realizado!</h2>
                <p class="text-gray-600 mb-6">Redirecionando para o dashboard...</p>
                <div class="loading-dots">
                    <div class="dot"></div>
                    <div class="dot"></div>
                    <div class="dot"></div>
                </div>
            </div>

            <!-- Error State -->
            <div v-else-if="error" class="text-center">
                <div class="error-icon mb-4">
                    <svg class="h-12 w-12 text-red-600 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h2 class="text-xl font-semibold text-gray-900 mb-2">Erro no Login</h2>
                <p class="text-gray-600 mb-6">{{ error }}</p>
                <div class="space-y-3">
                    <button
                        @click="retryLogin"
                        class="w-full bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors"
                    >
                        Tentar Novamente
                    </button>
                    <button
                        @click="goToLogin"
                        class="w-full bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700 transition-colors"
                    >
                        Voltar ao Login
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { router } from '@inertiajs/vue3';

const isLoading = ref(true);
const isSuccess = ref(false);
const error = ref(null);

onMounted(async () => {
    await processCallback();
});

const processCallback = async () => {
    try {
        // Simular delay para mostrar loading
        await new Promise(resolve => setTimeout(resolve, 1000));

        // Fazer requisição para processar callback
        const response = await fetch('/auth/google/callback', {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });

        const data = await response.json();

        if (data.success) {
            // Armazenar token e dados do usuário
            localStorage.setItem('jwt_token', data.token);
            localStorage.setItem('user', JSON.stringify(data.user));

            // Configurar headers para requisições futuras
            if (window.axios) {
                window.axios.defaults.headers.common['Authorization'] = `Bearer ${data.token}`;
            }

            // Mostrar sucesso
            isLoading.value = false;
            isSuccess.value = true;

            // Redirecionar após 2 segundos
            setTimeout(() => {
                router.visit('/dashboard');
            }, 2000);

        } else {
            throw new Error(data.message || 'Erro ao processar login');
        }

    } catch (err) {
        console.error('Erro no callback:', err);
        isLoading.value = false;
        error.value = err.message || 'Erro inesperado ao processar login';
    }
};

const retryLogin = () => {
    isLoading.value = true;
    error.value = null;
    processCallback();
};

const goToLogin = () => {
    router.visit('/login');
};
</script>

<style scoped>
.loading-spinner {
    animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

.success-icon {
    animation: bounce 0.6s ease-in-out;
}

.error-icon {
    animation: shake 0.5s ease-in-out;
}

.loading-dots {
    display: flex;
    justify-content: center;
    gap: 4px;
}

.dot {
    width: 8px;
    height: 8px;
    background: #3b82f6;
    border-radius: 50%;
    animation: dots 1.4s ease-in-out infinite both;
}

.dot:nth-child(1) { animation-delay: -0.32s; }
.dot:nth-child(2) { animation-delay: -0.16s; }

@keyframes dots {
    0%, 80%, 100% {
        transform: scale(0);
        opacity: 0.5;
    }
    40% {
        transform: scale(1);
        opacity: 1;
    }
}

@keyframes bounce {
    0%, 20%, 53%, 80%, 100% {
        transform: translate3d(0,0,0);
    }
    40%, 43% {
        transform: translate3d(0, -30px, 0);
    }
    70% {
        transform: translate3d(0, -15px, 0);
    }
    90% {
        transform: translate3d(0, -4px, 0);
    }
}

@keyframes shake {
    0%, 100% { transform: translateX(0); }
    10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
    20%, 40%, 60%, 80% { transform: translateX(5px); }
}

@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.5; }
}
</style> 