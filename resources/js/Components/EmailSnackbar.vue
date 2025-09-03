<template>
    <div v-if="show" class="email-snackbar" :class="type">
        <div class="snackbar-content">
            <div class="snackbar-icon">
                <svg v-if="type === 'success'" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
                <svg v-else-if="type === 'error'" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                </svg>
                <svg v-else class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                </svg>
            </div>
            <div class="snackbar-message">
                <h4 class="snackbar-title">{{ title }}</h4>
                <p class="snackbar-text">{{ message }}</p>
            </div>
            <button @click="close" class="snackbar-close">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                </svg>
            </button>
        </div>
        <div class="snackbar-progress" :style="{ width: progressWidth + '%' }"></div>
    </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, watch } from 'vue';

const props = defineProps({
    show: {
        type: Boolean,
        default: false
    },
    title: {
        type: String,
        default: 'Email Enviado'
    },
    message: {
        type: String,
        default: 'O email foi enviado com sucesso!'
    },
    type: {
        type: String,
        default: 'success',
        validator: (value) => ['success', 'error', 'info', 'warning'].includes(value)
    },
    duration: {
        type: Number,
        default: 5000
    }
});

const emit = defineEmits(['close']);

const progressWidth = ref(100);
let progressInterval;
let closeTimeout;

const close = () => {
    emit('close');
};

const startProgress = () => {
    if (props.duration > 0) {
        const step = 100 / (props.duration / 50); // Atualizar a cada 50ms
        progressInterval = setInterval(() => {
            progressWidth.value -= step;
            if (progressWidth.value <= 0) {
                clearInterval(progressInterval);
                close();
            }
        }, 50);
    }
};

const startAutoClose = () => {
    if (props.duration > 0) {
        closeTimeout = setTimeout(() => {
            close();
        }, props.duration);
    }
};

onMounted(() => {
    if (props.show) {
        startProgress();
        startAutoClose();
    }
});

onUnmounted(() => {
    if (progressInterval) clearInterval(progressInterval);
    if (closeTimeout) clearTimeout(closeTimeout);
});

// Watch para mudanças na prop show
watch(() => props.show, (newValue) => {
    if (newValue) {
        progressWidth.value = 100;
        startProgress();
        startAutoClose();
    }
});
</script>

<style scoped>
.email-snackbar {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 9999;
    min-width: 320px;
    max-width: 400px;
    background: white;
    border-radius: 8px;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
    border-left: 4px solid;
    animation: slideIn 0.3s ease-out;
    overflow: hidden;
}

.email-snackbar.success {
    border-left-color: #10b981;
}

.email-snackbar.error {
    border-left-color: #ef4444;
}

.email-snackbar.info {
    border-left-color: #3b82f6;
}

.email-snackbar.warning {
    border-left-color: #f59e0b;
}

.snackbar-content {
    display: flex;
    align-items: flex-start;
    padding: 16px;
    gap: 12px;
}

.snackbar-icon {
    flex-shrink: 0;
    margin-top: 2px;
}

.snackbar-icon.success {
    color: #10b981;
}

.snackbar-icon.error {
    color: #ef4444;
}

.snackbar-icon.info {
    color: #3b82f6;
}

.snackbar-icon.warning {
    color: #f59e0b;
}

.snackbar-message {
    flex: 1;
    min-width: 0;
}

.snackbar-title {
    font-weight: 600;
    font-size: 14px;
    color: #111827;
    margin: 0 0 4px 0;
    line-height: 1.2;
}

.snackbar-text {
    font-size: 13px;
    color: #6b7280;
    margin: 0;
    line-height: 1.4;
}

.snackbar-close {
    flex-shrink: 0;
    background: none;
    border: none;
    color: #9ca3af;
    cursor: pointer;
    padding: 4px;
    border-radius: 4px;
    transition: all 0.2s;
}

.snackbar-close:hover {
    background-color: #f3f4f6;
    color: #6b7280;
}

.snackbar-progress {
    height: 3px;
    background: linear-gradient(90deg, #10b981, #34d399);
    transition: width 0.05s linear;
}

.snackbar-progress.success {
    background: linear-gradient(90deg, #10b981, #34d399);
}

.snackbar-progress.error {
    background: linear-gradient(90deg, #ef4444, #f87171);
}

.snackbar-progress.info {
    background: linear-gradient(90deg, #3b82f6, #60a5fa);
}

.snackbar-progress.warning {
    background: linear-gradient(90deg, #f59e0b, #fbbf24);
}

@keyframes slideIn {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

@keyframes slideOut {
    from {
        transform: translateX(0);
        opacity: 1;
    }
    to {
        transform: translateX(100%);
        opacity: 0;
    }
}

.email-snackbar.closing {
    animation: slideOut 0.3s ease-in;
}
</style>
