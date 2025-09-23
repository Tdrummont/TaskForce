<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3'
import { useLocale } from '@/Components/useLocale'
import AuthLangToggle from '@/Components/AuthLangToggle.vue'
import ValidationError from '@/Components/ValidationError.vue'

const { t, routeL } = useLocale()

const form = useForm({
  email: '',
  password: '',
  remember: false,
})

function submit() {
  form.post(routeL('login')) // POST {locale}/login
}
</script>

<template>
  <Head :title="t('auth.sign_in')" />

  <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-[#0f172a] via-[#111827] to-[#1f2937] relative">
    <!-- Botão de idioma no topo -->
    <div class="absolute top-4 right-6">
      <AuthLangToggle />
    </div>

    <div class="w-full max-w-md bg-white/5 backdrop-blur-2xl rounded-2xl shadow-2xl ring-1 ring-white/10 p-6">
      <div class="text-center mb-6 flex flex-col items-center">
        <img src="/logoyggdra.png" alt="TaskForce" class="h-39 sm:h-44 md:h-56 w-auto mb-4 drop-shadow-[0_12px_40px_rgba(124,58,237,0.65)]" />
        <h1 class="text-2xl font-bold text-white">Login</h1>
      </div>

      <form @submit.prevent="submit" class="space-y-4">
        <!-- Email -->
        <div>
          <label class="block text-sm font-medium text-slate-200 mb-1">
            {{ t('auth.email') }}
          </label>
          <input
            v-model="form.email"
            type="email"
            autocomplete="username"
            class="w-full px-3 py-2 rounded-md bg-white/10 border border-white/10 text-white placeholder-slate-300 focus:ring-[#7c3aed] focus:border-[#7c3aed]"
          />
          <ValidationError :error="form.errors.email" />
        </div>

        <!-- Password -->
        <div>
          <label class="block text-sm font-medium text-slate-200 mb-1">
            {{ t('auth.password') }}
          </label>
          <input
            v-model="form.password"
            type="password"
            autocomplete="current-password"
            class="w-full px-3 py-2 rounded-md bg-white/10 border border-white/10 text-white placeholder-slate-300 focus:ring-[#7c3aed] focus:border-[#7c3aed]"
          />
          <ValidationError :error="form.errors.password" />
        </div>

        <!-- Remember + Forgot -->
        <div class="flex items-center justify-between">
          <label class="inline-flex items-center gap-2 text-sm text-slate-200">
            <input v-model="form.remember" type="checkbox" />
            <span>{{ t('auth.remember') }}</span>
          </label>

          <Link
            :href="routeL('password.request')"
            class="text-sm text-indigo-300 hover:underline"
          >
            {{ t('auth.forgot') }}
          </Link>
        </div>

        <!-- Submit -->
        <button
          type="submit"
          :disabled="form.processing"
          class="w-full py-2 rounded-md text-white bg-gradient-to-r from-[#1d4ed8] via-[#7c3aed] to-[#9333ea] hover:from-[#2563eb] hover:via-[#8b5cf6] hover:to-[#a855f7] disabled:opacity-50"
        >
          {{ t('auth.sign_in') }}
        </button>
      </form>

      <!-- Divider -->
      <div class="flex items-center my-4">
        <div class="flex-grow h-px bg-white/15"></div>
        <span class="px-3 text-sm text-slate-300">{{ t('auth.or') }}</span>
        <div class="flex-grow h-px bg-white/15"></div>
      </div>

      <!-- Google -->
      <a
        :href="routeL('google.redirect')"
        class="w-full inline-flex justify-center items-center gap-2 py-2 border border-white/10 rounded-md hover:bg-white/10 text-white"
      >
        <img src="https://www.google.com/favicon.ico" alt="G" class="w-4 h-4" />
        {{ t('auth.sign_in_with_google') }}
      </a>

      <!-- Register link -->
      <div class="text-center mt-4 text-sm text-slate-200">
        {{ t('auth.no_account') }}
        <Link :href="routeL('register')" class="text-indigo-300 hover:underline">
          {{ t('auth.register_here') }}
        </Link>
      </div>
    </div>
  </div>
</template>
