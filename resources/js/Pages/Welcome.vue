<template>
    <div class="min-h-screen bg-gradient-to-br from-[#0f172a] via-[#111827] to-[#1f2937] flex items-center justify-center relative overflow-hidden">
      <!-- Background decorativo -->
      <div class="pointer-events-none absolute -top-24 -right-24 w-[28rem] h-[28rem] bg-[#7c3aed]/20 rounded-full blur-3xl"></div>
      <div class="pointer-events-none absolute -bottom-24 -left-24 w-[28rem] h-[28rem] bg-[#2563eb]/20 rounded-full blur-3xl"></div>

      <div class="absolute top-4 right-6">
        <AuthLangToggle />
      </div>
      
      <!-- Watermark de logo ao fundo -->
      <img src="/logoyggdra.png" alt="Logo Watermark" 
           class="pointer-events-none select-none absolute inset-0 m-auto w-[60rem] max-w-[92vw] opacity-[0.08] blur-[1px] z-0" />

      <div class="relative z-10 max-w-xl w-full mx-auto p-10 bg-white/5 backdrop-blur-2xl rounded-3xl shadow-2xl ring-1 ring-white/10">
        <div class="text-center mb-8 flex flex-col items-center">
          <img src="/logoyggdra.png" alt="TaskForce" class="h-40 sm:h-48 md:h-56 w-auto mb-4 drop-shadow-[0_12px_40px_rgba(124,58,237,0.65)]" />
          <h1 class="text-4xl md:text-5xl font-extrabold text-white tracking-tight mt-1 mb-3">{{ t('app.title') }}</h1>
          <p class="text-slate-300 max-w-md">{{ t('app.subtitle') }}</p>
        </div>

        <div class="h-px w-full bg-gradient-to-r from-transparent via-white/15 to-transparent mb-6"></div>
  
        <div class="space-y-4">
          <a
            :href="routeL('login')"
            class="w-full block text-center py-3.5 px-4 rounded-xl shadow-sm text-sm font-semibold text-white bg-gradient-to-r from-[#1d4ed8] via-[#7c3aed] to-[#9333ea] hover:from-[#2563eb] hover:via-[#8b5cf6] hover:to-[#a855f7] transition-colors"
          >
            {{ t('auth.login') }}
          </a>
  
          <a
            :href="routeL('register')"
            class="w-full block text-center py-3.5 px-4 rounded-xl shadow-sm text-sm font-semibold text-white bg-white/10 hover:bg-white/15 border border-white/10"
          >
            {{ t('auth.register') }}
          </a>
        </div>

        <p class="mt-6 text-center text-xs text-slate-400">© {{ new Date().getFullYear() }} TaskForce. All rights reserved.</p>
      </div>
    </div>
  </template>
  
  <script setup lang="ts">
import { useLocale } from '@/Components/useLocale'
import AuthLangToggle from '@/Components/AuthLangToggle.vue'

const { t, routeL } = useLocale()
</script>
  