<template>
  <button
    @click="toggleLanguage"
    class="flex items-center justify-center p-2 text-white bg-white bg-opacity-20 hover:bg-opacity-30 border border-white border-opacity-30 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-white transition-all duration-200 backdrop-blur-sm"
    :title="currentLocale === 'pt' ? 'Switch to English' : 'Mudar para Português'"
  >
    <!-- Ícone de globo -->
    <svg 
      class="w-5 h-5" 
      fill="none" 
      stroke="currentColor" 
      viewBox="0 0 24 24"
      xmlns="http://www.w3.org/2000/svg"
    >
      <path 
        stroke-linecap="round" 
        stroke-linejoin="round" 
        stroke-width="2" 
        d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
      />
    </svg>
  </button>
</template>

<script setup>
import { computed } from 'vue'
import { usePage, router } from '@inertiajs/vue3'

const page = usePage()
const currentLocale = computed(() => page.props.__LOCALE__ || 'pt')

const toggleLanguage = () => {
  const newLocale = currentLocale.value === 'pt' ? 'en' : 'pt'
  
  // Navegar para a mesma rota com novo locale
  const url = new URL(window.location.href)
  const parts = url.pathname.split('/').filter(Boolean)
  parts[0] = newLocale // troca o 1º segmento (locale)
  
  // Navegar mantendo query atual
  const newPath = '/' + parts.join('/') + url.search
  router.visit(newPath, { preserveScroll: true })
}
</script>

<style scoped>
.animate-fade-in {
  animation: fadeIn 0.2s ease-out;
}

@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(-10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}
</style>
