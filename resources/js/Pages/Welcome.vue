<template>
    <div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 flex items-center justify-center">
      <div class="absolute top-4 right-6">
        <AuthLangToggle />
      </div>
      
      <div class="max-w-md w-full mx-auto p-6">
        <div class="text-center mb-8">
          <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ t('app.title') }}</h1>
          <p class="text-gray-600">{{ t('app.subtitle') }}</p>
        </div>
  
        <div class="space-y-4">
          <a
            :href="routeL('login')"
            class="w-full block text-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700"
          >
            {{ t('auth.login') }}
          </a>
  
          <a
            :href="routeL('register')"
            class="w-full block text-center py-3 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
          >
            {{ t('auth.register') }}
          </a>
        </div>
      </div>
    </div>
  </template>
  
  <script setup lang="ts">
import { useLocale } from '@/Components/useLocale'
import AuthLangToggle from '@/Components/AuthLangToggle.vue'

const { t, routeL } = useLocale()
</script>
  