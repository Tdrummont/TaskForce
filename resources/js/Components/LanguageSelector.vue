<template>
  <div class="relative">
    <button
      @click="toggleDropdown"
      class="bg-white bg-opacity-20 hover:bg-opacity-30 text-white px-3 py-2 rounded-md text-sm font-medium transition-all duration-200 flex items-center space-x-2 backdrop-blur-sm"
    >
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
      </svg>
      <span>{{ current.toUpperCase() }}</span>
      <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
      </svg>
    </button>

    <!-- Dropdown Menu -->
    <div
      v-if="showDropdown"
      class="absolute right-0 mt-2 w-32 bg-white rounded-md shadow-lg py-1 z-50"
    >
      <button
        v-for="lang in languages"
        :key="lang.code"
        @click="switchLanguage(lang.code)"
        class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center space-x-2"
        :class="{ 'bg-blue-50 text-blue-700': current === lang.code }"
      >
        <span class="text-lg">{{ lang.flag }}</span>
        <span>{{ lang.name }}</span>
      </button>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { usePage, router } from '@inertiajs/vue3'

type Locale = 'pt' | 'en'
const page = usePage()

// pega o locale atual que você já compartilha via Inertia (__LOCALE__)
const current = computed<Locale>(() => {
  const loc = (page.props.__LOCALE__ as Locale) || 'pt'
  return (loc === 'en' || loc === 'pt') ? loc : 'pt'
})

const showDropdown = ref(false)

const languages = [
  { code: 'pt', name: 'Português', flag: '🇧🇷' },
  { code: 'en', name: 'English', flag: '🇺🇸' }
]

const toggleDropdown = () => {
  showDropdown.value = !showDropdown.value
}

function switchLanguage(langCode: string) {
  const next: Locale = langCode as Locale
  const url = new URL(window.location.href)
  const parts = url.pathname.split('/').filter(Boolean)

  // troca/insere o primeiro segmento
  if (parts[0] === 'pt' || parts[0] === 'en') {
    parts[0] = next
  } else {
    parts.unshift(next)
  }

  showDropdown.value = false

  router.visit('/' + parts.join('/') + url.search, {
    preserveScroll: true,
  })
}

// Fechar dropdown quando clicar fora
const handleClickOutside = (event: Event) => {
  const target = event.target as HTMLElement
  if (!target.closest('.relative')) {
    showDropdown.value = false
  }
}

onMounted(() => {
  document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
})
</script>
