<template>
    <div class="google-login-container">
        <!-- Botão de Login com Google -->
        <button
            @click="loginWithGoogle"
            :disabled="isLoading"
            class="google-login-btn"
            :class="{ 'loading': isLoading }"
        >
            <div v-if="isLoading" class="loading-spinner">
                <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </div>
            <svg v-else class="google-icon" viewBox="0 0 24 24">
                <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
            </svg>
            <span class="btn-text">
                {{ isLoading ? t('auth.signing_in') : t('auth.sign_in_with_google') }}
            </span>
        </button>

        <!-- Modal de Loading -->
        <div v-if="showLoadingModal" class="loading-modal">
            <div class="loading-content">
                <div class="loading-spinner-large">
                    <svg class="animate-spin h-12 w-12 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>
                <h3 class="loading-title">{{ t('auth.authenticating') }}</h3>
                <p class="loading-subtitle">{{ t('auth.please_wait') }}</p>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { router } from '@inertiajs/vue3';
import { useLocale } from '@/Components/useLocale';

const props = defineProps({
    redirectUrl: {
        type: String,
        default: '/dashboard'
    }
});

const { routeL, t } = useLocale();

const isLoading = ref(false);
const showLoadingModal = ref(false);

// Verificar se há token no localStorage
onMounted(() => {
    const token = localStorage.getItem('jwt_token');
    if (token) {
        // Token existe, verificar se ainda é válido
        checkTokenValidity(token);
    }
});

const checkTokenValidity = async (token) => {
    try {
        const response = await fetch('/auth/refresh', {
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });

        if (response.ok) {
            const data = await response.json();
            localStorage.setItem('jwt_token', data.token);
            router.visit(props.redirectUrl);
        } else {
            // Token inválido, remover do localStorage
            localStorage.removeItem('jwt_token');
        }
    } catch (error) {
        console.error('Erro ao verificar token:', error);
        localStorage.removeItem('jwt_token');
    }
};

const loginWithGoogle = async () => {
    try {
        isLoading.value = true;
        showLoadingModal.value = true;

        // Redirecionar para o Google via rota nomeada correta
        window.location.href = routeL('google.redirect');

    } catch (error) {
        console.error('Erro ao iniciar login com Google:', error);
        isLoading.value = false;
        showLoadingModal.value = false;
        alert(t('toast.error'));
    }
};

// Função para processar callback do Google
const processGoogleCallback = async (token, user) => {
    try {
        // Armazenar token no localStorage
        localStorage.setItem('jwt_token', token);
        localStorage.setItem('user', JSON.stringify(user));

        // Configurar headers para requisições futuras
        axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;

        // Redirecionar para dashboard
        router.visit(props.redirectUrl);

    } catch (error) {
        console.error('Erro ao processar callback:', error);
        alert(t('toast.error'));
    }
};

// Expor função para uso externo
defineExpose({
    processGoogleCallback
});
</script>

<style scoped>
.google-login-container {
    position: relative;
}

.google-login-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 12px;
    width: 100%;
    max-width: 300px;
    padding: 12px 24px;
    background: white;
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    font-size: 16px;
    font-weight: 500;
    color: #374151;
    cursor: pointer;
    transition: all 0.2s ease;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.google-login-btn:hover:not(:disabled) {
    background: #f9fafb;
    border-color: #d1d5db;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    transform: translateY(-1px);
}

.google-login-btn:active:not(:disabled) {
    transform: translateY(0);
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.google-login-btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.google-icon {
    width: 20px;
    height: 20px;
    flex-shrink: 0;
}

.btn-text {
    font-family: 'Roboto', sans-serif;
}

.loading-spinner {
    width: 20px;
    height: 20px;
    color: #6b7280;
}

/* Modal de Loading */
.loading-modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 9999;
}

.loading-content {
    background: white;
    padding: 32px;
    border-radius: 12px;
    text-align: center;
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    max-width: 400px;
    width: 90%;
}

.loading-spinner-large {
    margin-bottom: 16px;
}

.loading-title {
    font-size: 20px;
    font-weight: 600;
    color: #1f2937;
    margin-bottom: 8px;
}

.loading-subtitle {
    font-size: 14px;
    color: #6b7280;
    line-height: 1.5;
}

/* Responsividade */
@media (max-width: 640px) {
    .google-login-btn {
        padding: 10px 20px;
        font-size: 14px;
    }

    .loading-content {
        padding: 24px;
        margin: 16px;
    }

    .loading-title {
        font-size: 18px;
    }
}
</style> 