<template>
  <div v-if="show" class="fixed top-4 right-4 z-50 max-w-sm">
    <div class="bg-yellow-100 border-l-4 border-yellow-500 p-4 rounded-lg shadow-lg">
      <div class="flex">
        <div class="flex-shrink-0">
          <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
          </svg>
        </div>
        <div class="ml-3">
          <p class="text-sm font-medium text-yellow-800">
            {{ holiday.name }}
          </p>
          <p class="text-sm text-yellow-700 mt-1">
            Esta data é um feriado nacional
          </p>
        </div>
        <div class="ml-auto pl-3">
          <div class="-mx-1.5 -my-1.5">
            <button @click="close" class="inline-flex bg-yellow-100 rounded-md p-1.5 text-yellow-500 hover:bg-yellow-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-yellow-100 focus:ring-yellow-600">
              <span class="sr-only">Fechar</span>
              <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue'

const show = ref(false)
const holiday = ref<any>(null)
let timeoutId: number | null = null

const close = () => {
  show.value = false
  holiday.value = null
  if (timeoutId) {
    clearTimeout(timeoutId)
    timeoutId = null
  }
}

const showHoliday = (holidayData: any, duration = 5000) => {
  holiday.value = holidayData
  show.value = true
  
  if (timeoutId) {
    clearTimeout(timeoutId)
  }
  
  timeoutId = setTimeout(() => {
    close()
  }, duration)
}

// Expor função globalmente para uso em outros componentes
onMounted(() => {
  window.$holidayToast = {
    show: showHoliday
  }
})

onUnmounted(() => {
  if (timeoutId) {
    clearTimeout(timeoutId)
  }
  if (window.$holidayToast) {
    delete window.$holidayToast
  }
})
</script>
