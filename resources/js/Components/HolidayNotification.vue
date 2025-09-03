<template>
    <Transition
        enter-active-class="transition ease-out duration-300"
        enter-from-class="transform translate-x-full opacity-0"
        enter-to-class="transform translate-x-0 opacity-100"
        leave-active-class="transition ease-in duration-200"
        leave-from-class="transform translate-x-0 opacity-100"
        leave-to-class="transform translate-x-full opacity-0"
    >
        <div
            v-if="show"
            class="holiday-notification"
            :class="notificationType"
        >
            <div class="notification-content">
                <div class="notification-icon">
                    <svg v-if="notificationType === 'holiday'" class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                    </svg>
                    <svg v-else-if="notificationType === 'facultative'" class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                    </svg>
                    <svg v-else class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                
                <div class="notification-body">
                    <h4 class="notification-title">{{ title }}</h4>
                    <p class="notification-message">{{ message }}</p>
                    <div class="notification-details">
                        <span class="notification-date">{{ formatDate(holiday.date) }}</span>
                        <span class="notification-type">{{ getTypeLabel(holiday.type) }}</span>
                        <span class="notification-level">{{ getLevelLabel(holiday.level) }}</span>
                    </div>
                </div>
                
                <button @click="close" class="notification-close" :title="$t('Fechar')">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                    </svg>
                </button>
            </div>
            
            <div class="notification-progress" :style="{ width: progressWidth + '%' }"></div>
        </div>
    </Transition>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';

const props = defineProps({
    show: {
        type: Boolean,
        default: false
    },
    holiday: {
        type: Object,
        required: true
    },
    duration: {
        type: Number,
        default: 8000
    }
});

const emit = defineEmits(['close']);

const progressWidth = ref(100);
let progressInterval;
let closeTimeout;

// Computed properties
const title = computed(() => {
    if (props.holiday.type === 'feriado') {
        return '🎉 Feriado Nacional';
    } else if (props.holiday.type === 'facultativo') {
        return '⚠️ Ponto Facultativo';
    }
    return '📅 Data Especial';
});

const message = computed(() => {
    return `${props.holiday.name} - ${formatDate(props.holiday.date)}`;
});

const notificationType = computed(() => {
    if (props.holiday.type === 'feriado') return 'holiday';
    if (props.holiday.type === 'facultativo') return 'facultative';
    return 'info';
});

// Methods
const formatDate = (dateString) => {
    if (!dateString) return '';
    const date = new Date(dateString);
    return date.toLocaleDateString('pt-BR', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
};

const getTypeLabel = (type) => {
    const labels = {
        'feriado': 'Feriado',
        'facultativo': 'Ponto Facultativo'
    };
    return labels[type] || type;
};

const getLevelLabel = (level) => {
    const labels = {
        'nacional': 'Nacional',
        'estadual': 'Estadual',
        'municipal': 'Municipal'
    };
    return labels[level] || level;
};

const close = () => {
    emit('close');
};

const startProgress = () => {
    if (props.duration > 0) {
        const step = 100 / (props.duration / 50);
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

// Lifecycle
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
</script>

<style scoped>
.holiday-notification {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 9999;
    min-width: 380px;
    max-width: 450px;
    background: white;
    border-radius: 12px;
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    border-left: 5px solid;
    overflow: hidden;
    font-family: 'Inter', system-ui, sans-serif;
}

.holiday-notification.holiday {
    border-left-color: #dc2626;
    background: linear-gradient(135deg, #fef2f2 0%, #ffffff 100%);
}

.holiday-notification.facultative {
    border-left-color: #f59e0b;
    background: linear-gradient(135deg, #fffbeb 0%, #ffffff 100%);
}

.holiday-notification.info {
    border-left-color: #3b82f6;
    background: linear-gradient(135deg, #eff6ff 0%, #ffffff 100%);
}

.notification-content {
    display: flex;
    align-items: flex-start;
    padding: 20px;
    gap: 16px;
}

.notification-icon {
    flex-shrink: 0;
    margin-top: 2px;
}

.notification-icon.holiday {
    color: #dc2626;
}

.notification-icon.facultative {
    color: #f59e0b;
}

.notification-icon.info {
    color: #3b82f6;
}

.notification-body {
    flex: 1;
    min-width: 0;
}

.notification-title {
    font-weight: 700;
    font-size: 16px;
    color: #111827;
    margin: 0 0 8px 0;
    line-height: 1.2;
}

.notification-message {
    font-size: 14px;
    color: #374151;
    margin: 0 0 12px 0;
    line-height: 1.4;
    font-weight: 500;
}

.notification-details {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    align-items: center;
}

.notification-date,
.notification-type,
.notification-level {
    font-size: 11px;
    padding: 4px 8px;
    border-radius: 6px;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.025em;
}

.notification-date {
    background-color: #f3f4f6;
    color: #6b7280;
}

.notification-type {
    background-color: #dbeafe;
    color: #1e40af;
}

.notification-level {
    background-color: #fef3c7;
    color: #92400e;
}

.notification-close {
    flex-shrink: 0;
    background: none;
    border: none;
    color: #9ca3af;
    cursor: pointer;
    padding: 6px;
    border-radius: 6px;
    transition: all 0.2s;
    margin-top: 2px;
}

.notification-close:hover {
    background-color: #f3f4f6;
    color: #6b7280;
}

.notification-progress {
    height: 4px;
    background: linear-gradient(90deg, #10b981, #34d399);
    transition: width 0.05s linear;
}

.notification-progress.holiday {
    background: linear-gradient(90deg, #dc2626, #ef4444);
}

.notification-progress.facultative {
    background: linear-gradient(90deg, #f59e0b, #fbbf24);
}

.notification-progress.info {
    background: linear-gradient(90deg, #3b82f6, #60a5fa);
}

/* Responsive */
@media (max-width: 640px) {
    .holiday-notification {
        left: 20px;
        right: 20px;
        min-width: auto;
        max-width: none;
    }
}
</style>
