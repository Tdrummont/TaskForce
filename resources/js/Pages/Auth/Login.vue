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

  <div class="min-h-screen flex items-center justify-center bg-gray-50">
    <!-- Botão de idioma no topo -->
    <div class="absolute top-4 right-6">
      <AuthLangToggle />
    </div>

    <div class="w-full max-w-md bg-white rounded-lg shadow p-6">
      <div class="text-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">{{ t('auth.title') }}</h1>
      </div>

      <form @submit.prevent="submit" class="space-y-4">
        <!-- Email -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">
            {{ t('auth.email') }}
          </label>
          <input
            v-model="form.email"
            type="email"
            autocomplete="username"
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
          />
          <ValidationError :error="form.errors.email" />
        </div>

        <!-- Password -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">
            {{ t('auth.password') }}
          </label>
          <input
            v-model="form.password"
            type="password"
            autocomplete="current-password"
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
          />
          <ValidationError :error="form.errors.password" />
        </div>

        <!-- Remember + Forgot -->
        <div class="flex items-center justify-between">
          <label class="inline-flex items-center gap-2 text-sm text-gray-700">
            <input v-model="form.remember" type="checkbox" />
            <span>{{ t('auth.remember') }}</span>
          </label>

          <Link
            :href="routeL('password.request')"
            class="text-sm text-blue-600 hover:underline"
          >
            {{ t('auth.forgot') }}
          </Link>
        </div>

        <!-- Submit -->
        <button
          type="submit"
          :disabled="form.processing"
          class="w-full py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 disabled:opacity-50"
        >
          {{ t('auth.sign_in') }}
        </button>
      </form>

      <!-- Divider -->
      <div class="flex items-center my-4">
        <div class="flex-grow h-px bg-gray-200"></div>
        <span class="px-3 text-sm text-gray-500">{{ t('auth.or') }}</span>
        <div class="flex-grow h-px bg-gray-200"></div>
      </div>

      <!-- Google -->
      <Link
        :href="routeL('login.google')"
        class="w-full inline-flex justify-center items-center gap-2 py-2 border rounded-md hover:bg-gray-50"
      >
        <img src="https://www.google.com/favicon.ico" alt="G" class="w-4 h-4" />
        {{ t('auth.sign_in_with_google') }}
      </Link>

      <!-- Register link -->
      <div class="text-center mt-4 text-sm text-gray-700">
        {{ t('auth.no_account') }}
        <Link :href="routeL('register')" class="text-blue-600 hover:underline">
          {{ t('auth.register_here') }}
        </Link>
      </div>
    </div>
  </div>
</template>
