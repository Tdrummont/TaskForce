<script setup lang="ts">
import { computed } from 'vue'
import { usePage, router } from '@inertiajs/vue3'

type Locale = 'pt' | 'en'
const page = usePage()

// pega o locale atual que você já compartilha via Inertia (__LOCALE__)
const current = computed<Locale>(() => {
  const loc = (page.props.__LOCALE__ as Locale) || 'pt'
  return (loc === 'en' || loc === 'pt') ? loc : 'pt'
})

function toggleLocale() {
  const next: Locale = current.value === 'pt' ? 'en' : 'pt'
  const url = new URL(window.location.href)
  const parts = url.pathname.split('/').filter(Boolean)

  // troca/insere o primeiro segmento
  if (parts[0] === 'pt' || parts[0] === 'en') {
    parts[0] = next
  } else {
    parts.unshift(next)
  }

  router.visit('/' + parts.join('/') + url.search, {
    preserveScroll: true,
  })
}
</script>

<template>
  <button
    type="button"
    @click="toggleLocale"
    class="inline-flex items-center gap-2 px-3 py-2 text-sm text-gray-600 hover:text-blue-600 hover:bg-blue-50 rounded-md transition-colors duration-200"
    :title="current === 'pt' ? 'Switch to English' : 'Mudar para Português'"
  >
    <!-- Ícone de globo -->
    <svg 
      class="w-4 h-4" 
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
    
    <!-- Texto do idioma atual -->
    <span class="font-medium">
      {{ current === 'pt' ? 'EN' : 'PT' }}
    </span>
  </button>
</template>
