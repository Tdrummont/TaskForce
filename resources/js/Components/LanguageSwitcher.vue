<template>
  <div class="flex items-center space-x-2">
    <span class="text-sm text-gray-600">{{ t('language.select') }}:</span>
    <select
      v-model="selectedLanguage"
      @change="switchLanguage"
      class="text-sm border border-gray-300 rounded-md px-2 py-1 focus:outline-none focus:ring-2 focus:ring-blue-500"
    >
      <option value="pt">🇧🇷 Português</option>
      <option value="en">🇺🇸 English</option>
      <option value="es">🇪🇸 Español</option>
    </select>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useLocale } from '@/Components/useLocale'

const { t, currentLocale, switchLocale } = useLocale()
const selectedLanguage = ref(currentLocale.value)

const switchLanguage = async () => {
  try {
    await switchLocale(selectedLanguage.value)
    // Recarregar a página para aplicar as traduções
    window.location.reload()
  } catch (error) {
    console.error('Erro ao trocar idioma:', error)
  }
}

onMounted(() => {
  selectedLanguage.value = currentLocale.value
})
</script>
