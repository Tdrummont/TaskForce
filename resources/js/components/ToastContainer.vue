<template>
    <div class="fixed top-4 right-4 z-50 space-y-2">
        <NotificationToast
            v-for="toast in toasts"
            :key="toast.id"
            :notification="toast"
            @close="removeToast"
        />
    </div>
</template>

<script setup>
import { ref } from 'vue'
import NotificationToast from './NotificationToast.vue'

const toasts = ref([])

const addToast = (notification) => {
    const toast = {
        id: Date.now() + Math.random(),
        ...notification
    }
    toasts.value.push(toast)
}

const removeToast = (toastId) => {
    toasts.value = toasts.value.filter(t => t.id !== toastId)
}

// Expor métodos para uso externo
defineExpose({
    addToast,
    removeToast
})
</script>
