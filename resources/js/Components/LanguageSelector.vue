<template>
  <div class="relative">
    <!-- Botão do seletor de idioma -->
    <button
      @click="isOpen = !isOpen"
      class="flex items-center space-x-2 px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200"
    >
      <!-- Ícone de idioma -->
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"></path>
      </svg>
      
      <!-- Idioma atual -->
      <span>{{ currentLanguageLabel }}</span>
      
      <!-- Seta -->
      <svg class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': isOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
      </svg>
    </button>

    <!-- Dropdown de idiomas -->
    <div
      v-if="isOpen"
      class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg z-50 border border-gray-200 animate-fade-in"
    >
      <div class="py-1">
        <button
          @click="changeLanguage('pt')"
          class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors duration-150"
          :class="{ 'bg-indigo-50 text-indigo-700': currentLocale === 'pt' }"
        >
          <span class="text-lg mr-3">🇧🇷</span>
          <span>Português</span>
          <svg v-if="currentLocale === 'pt'" class="ml-auto w-4 h-4 text-indigo-600" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
          </svg>
        </button>
        
        <button
          @click="changeLanguage('en')"
          class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors duration-150"
          :class="{ 'bg-indigo-50 text-indigo-700': currentLocale === 'en' }"
        >
          <span class="text-lg mr-3">🇺🇸</span>
          <span>English</span>
          <svg v-if="currentLocale === 'en'" class="ml-auto w-4 h-4 text-indigo-600" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
          </svg>
        </button>

        <button
          @click="changeLanguage('es')"
          class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors duration-150"
          :class="{ 'bg-indigo-50 text-indigo-700': currentLocale === 'es' }"
        >
          <span class="text-lg mr-3">🇪🇸</span>
          <span>Español</span>
          <svg v-if="currentLocale === 'es'" class="ml-auto w-4 h-4 text-indigo-600" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
          </svg>
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { router } from '@inertiajs/vue3'

const isOpen = ref(false)
const currentLocale = ref('pt')

const currentLanguageLabel = computed(() => {
  const labels = {
    'pt': '🇧🇷 PT',
    'en': '🇺🇸 EN',
    'es': '🇪🇸 ES'
  }
  return labels[currentLocale.value] || labels['pt']
})

const changeLanguage = (locale) => {
  currentLocale.value = locale
  isOpen.value = false
  
  // Navegar para a rota de mudança de idioma
  router.visit(route('language.change', locale), {
    method: 'get',
    preserveState: true,
    onSuccess: () => {
      // Recarregar a página para aplicar o novo idioma
      window.location.reload()
    }
  })
}

// Fechar dropdown quando clicar fora
const handleClickOutside = (event) => {
  if (!event.target.closest('.relative')) {
    isOpen.value = false
  }
}

onMounted(() => {
  // Obter idioma atual da API
  fetch(route('language.current'))
    .then(response => response.json())
    .then(data => {
      currentLocale.value = data.current_locale
    })
    .catch(error => {
      console.error('Erro ao obter idioma atual:', error)
    })
  
  document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
})
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
