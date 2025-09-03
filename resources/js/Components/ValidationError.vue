<template>
  <div v-if="error" class="text-red-500 text-sm mt-1">
    {{ t(error) }}
  </div>
</template>

<script setup>
import { useLocale } from '@/Components/useLocale'

const { t } = useLocale()

defineProps({
  error: {
    type: String,
    default: ''
  }
})
</script>
