<template>
    <div class="fixed right-4 top-4 z-50 w-full max-w-sm space-y-3">
        <div
            v-for="toast in activeToasts"
            :key="toast.id"
            class="rounded-lg border border-yellow-300 bg-yellow-50 p-4 shadow-lg"
        >
            <div class="flex gap-3">
                <div class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-yellow-200 text-sm font-semibold text-yellow-900">
                    !
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-sm font-semibold text-yellow-900">{{ toast.holiday.name }}</p>
                    <p class="mt-1 text-sm text-yellow-800">
                        {{ formatDate(toast.holiday.date) }}
                    </p>
                </div>
                <button
                    type="button"
                    class="rounded-md px-2 text-yellow-700 hover:bg-yellow-100"
                    @click="dismiss(toast.id)"
                >
                    <span class="sr-only">Fechar</span>
                    x
                </button>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { onMounted, onUnmounted, ref } from 'vue';
import type { Holiday } from './useHolidayNotifications';

interface HolidayToast {
    id: number;
    holiday: Holiday;
    timeoutId: number;
}

const activeToasts = ref<HolidayToast[]>([]);
let nextId = 1;

const formatDate = (date: string) => {
    const [year, month, day] = date.split('-');
    return `${day}/${month}/${year}`;
};

const dismiss = (id: number) => {
    const toast = activeToasts.value.find((item) => item.id === id);
    if (toast) {
        clearTimeout(toast.timeoutId);
    }

    activeToasts.value = activeToasts.value.filter((item) => item.id !== id);
};

const show = (holiday: Holiday, duration = 5000) => {
    const id = nextId++;
    const timeoutId = window.setTimeout(() => dismiss(id), duration);

    activeToasts.value.unshift({ id, holiday, timeoutId });
};

const clear = () => {
    activeToasts.value.forEach((toast) => clearTimeout(toast.timeoutId));
    activeToasts.value = [];
};

onMounted(() => {
    window.$holidayToast = { show, clear };
});

onUnmounted(() => {
    clear();
    delete window.$holidayToast;
});

defineExpose({ activeToasts, show, clear });
</script>
