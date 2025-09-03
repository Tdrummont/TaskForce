<!-- LanguageSwitcher.vue -->
<script setup lang="ts">
import { computed } from 'vue'
import { usePage, router } from '@inertiajs/vue3'

const page = usePage()
const supported = ['pt', 'en'] as const

// reativo: atualiza se você mudar de idioma em outro lugar
const current = computed(() => (page.props.__LOCALE__ as string) || 'pt')

function onChange(e: Event) {
  const to = (e.target as HTMLSelectElement).value as (typeof supported)[number]
  const url = new URL(window.location.href)

  // path em partes, ignorando vazios
  const parts = url.pathname.split('/').filter(Boolean) // ex: ['pt','dashboard']

  // caso 1: raiz -> /{to}
  if (parts.length === 0) {
    router.visit(`/${to}${url.search}`, { preserveScroll: true })
    return
  }

  // caso 2: já tem prefixo válido -> substitui
  if (supported.includes(parts[0] as any)) {
    parts[0] = to
    router.visit(`/${parts.join('/')}${url.search}`, {
      preserveScroll: true,
      preserveState: true,
    })
    return
  }

  // caso 3: rota sem prefixo (ex.: /login) -> tenta pré-pender
  // se /login não existe no grupo localizado, você pode preferir mandar pra home do idioma
  // aqui tentamos pré-pender; se não existir, o fallback de rota tratará
  const candidate = `/${[to, ...parts].join('/')}${url.search}`
  router.visit(candidate, { preserveScroll: true })
}
</script>

<template>
  <select :value="current" @change="onChange" class="form-select" aria-label="Language selector">
    <option value="pt">🇧🇷 Português</option>
    <option value="en">🇺🇸 English</option>
  </select>
</template>

<style scoped>
.form-select { 
  padding: .25rem .5rem; 
  border: 1px solid #d1d5db;
  border-radius: 0.375rem;
  background-color: white;
  font-size: 0.875rem;
  line-height: 1.25rem;
}
.form-select:focus {
  outline: none;
  border-color: #3b82f6;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}
</style>
