<template>
  <div class="flex items-center">
    <!-- Toggle Switch -->
    <button
      @click="toggleTheme"
      class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
      :class="isDark ? 'bg-blue-600' : 'bg-gray-200'"
    >
      <!-- Toggle Circle -->
      <span
        class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform"
        :class="isDark ? 'translate-x-6' : 'translate-x-1'"
      >
        <!-- Sun Icon (Light Mode) -->
        <svg
          v-if="!isDark"
          class="h-3 w-3 text-yellow-500"
          fill="none"
          stroke="currentColor"
          viewBox="0 0 24 24"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"
          />
        </svg>
        
        <!-- Moon Icon (Dark Mode) -->
        <svg
          v-else
          class="h-3 w-3 text-blue-600"
          fill="none"
          stroke="currentColor"
          viewBox="0 0 24 24"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"
          />
        </svg>
      </span>
    </button>
    
    <!-- Theme Label -->
    <span class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-300">
      {{ isDark ? '🌙' : '☀️' }}
    </span>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue'

const isDark = ref(false)

const toggleTheme = () => {
  isDark.value = !isDark.value
  applyTheme()
  saveTheme()
}

const applyTheme = () => {
  if (isDark.value) {
    document.documentElement.classList.add('dark')
    document.body.classList.add('dark-mode')
  } else {
    document.documentElement.classList.remove('dark')
    document.body.classList.remove('dark-mode')
  }
}

const saveTheme = () => {
  localStorage.setItem('theme', isDark.value ? 'dark' : 'light')
}

const loadTheme = () => {
  const savedTheme = localStorage.getItem('theme')
  if (savedTheme) {
    isDark.value = savedTheme === 'dark'
  } else {
    // Detectar preferência do sistema
    isDark.value = window.matchMedia('(prefers-color-scheme: dark)').matches
  }
  applyTheme()
}

onMounted(() => {
  loadTheme()
  
  // Escutar mudanças na preferência do sistema
  window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
    if (!localStorage.getItem('theme')) {
      isDark.value = e.matches
      applyTheme()
    }
  })
})

// Aplicar tema quando mudar
watch(isDark, () => {
  applyTheme()
})
</script>

<style scoped>
/* Transições suaves */
.transition-colors {
  transition: background-color 0.3s ease;
}

.transition-transform {
  transition: transform 0.3s ease;
}

/* Estilos para modo escuro */
:global(.dark-mode) {
  background-color: #1f2937;
  color: #f9fafb;
}

:global(.dark-mode .bg-white) {
  background-color: #374151;
}

:global(.dark-mode .text-gray-700) {
  color: #d1d5db;
}

:global(.dark-mode .border-gray-300) {
  border-color: #4b5563;
}
</style>
