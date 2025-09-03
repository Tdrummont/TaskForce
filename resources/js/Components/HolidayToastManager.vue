<template>
    <div class="holiday-toast-manager">
        <TransitionGroup
            name="holiday-toast"
            tag="div"
            class="toast-container"
        >
            <HolidayNotification
                v-for="toast in activeToasts"
                :key="toast.id"
                :show="true"
                :holiday="toast.holiday"
                :duration="toast.duration"
                @close="removeToast(toast.id)"
            />
        </TransitionGroup>
    </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import HolidayNotification from './HolidayNotification.vue';

// Estado global dos toasts
const activeToasts = ref([]);
let toastCounter = 0;

// Métodos para gerenciar toasts
const addToast = (holiday, duration = 8000) => {
    const toast = {
        id: ++toastCounter,
        holiday,
        duration,
        timestamp: Date.now()
    };
    
    activeToasts.value.push(toast);
    
    // Limitar número máximo de toasts
    if (activeToasts.value.length > 3) {
        activeToasts.value.shift();
    }
    
    return toast.id;
};

const removeToast = (id) => {
    const index = activeToasts.value.findIndex(toast => toast.id === id);
    if (index > -1) {
        activeToasts.value.splice(index, 1);
    }
};

const clearAllToasts = () => {
    activeToasts.value = [];
};

// Expor métodos globalmente
const showHolidayToast = (holiday, duration) => {
    return addToast(holiday, duration);
};

const showHolidayWarning = (date, holidayName, type = 'feriado') => {
    const holiday = {
        date,
        name: holidayName,
        type,
        level: 'nacional'
    };
    
    return addToast(holiday, 6000);
};

// Expor no window para uso global
onMounted(() => {
    window.$holidayToast = {
        show: showHolidayToast,
        warning: showHolidayWarning,
        clear: clearAllToasts
    };
});

onUnmounted(() => {
    delete window.$holidayToast;
});

// Expor métodos para uso em componentes
defineExpose({
    show: showHolidayToast,
    warning: showHolidayWarning,
    clear: clearAllToasts
});
</script>

<style scoped>
.holiday-toast-manager {
    position: fixed;
    top: 0;
    right: 0;
    z-index: 9999;
    pointer-events: none;
}

.toast-container {
    position: relative;
}

/* Transições para os toasts */
.holiday-toast-enter-active {
    transition: all 0.3s ease-out;
}

.holiday-toast-leave-active {
    transition: all 0.2s ease-in;
}

.holiday-toast-enter-from {
    transform: translateX(100%);
    opacity: 0;
}

.holiday-toast-leave-to {
    transform: translateX(100%);
    opacity: 0;
}

.holiday-toast-move {
    transition: transform 0.3s ease;
}
</style>
