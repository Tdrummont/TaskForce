<script setup>
import { ref, watch } from 'vue'
import { Link, useForm } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { useLocale } from '@/Components/useLocale'

const props = defineProps({
  task: Object,
  users: Array,
  // passe isso do controller: $user->state ?? 'SP'
  userState: { type: String, default: 'SP' }
})

const { routeL, t } = useLocale()

const form = useForm({
  title: props.task.title ?? '',
  description: props.task.description ?? '',
  due_date: props.task.due_date ?? '',
  status: props.task.status ?? 'pending',
  priority: props.task.priority ?? 'medium',
  assigned_to: props.task.assigned_to ?? ''
})

const holiday = ref(null) // { name, date } | null
const checkingHoliday = ref(false)
const stateUF = ref(props.userState || 'SP')

async function checkHoliday() {
  holiday.value = null
  const date = form.due_date
  if (!date) return
  
  // Verificação simples e não-bloqueante
  try {
    checkingHoliday.value = true
    
    // Usar AbortController para timeout
    const controller = new AbortController()
    const timeoutId = setTimeout(() => controller.abort(), 2000) // 2 segundos timeout
    
    const r = await fetch(`/api/holidays/check?date=${encodeURIComponent(date)}&state=${encodeURIComponent(stateUF.value)}`, {
      headers: { 'Accept': 'application/json' },
      signal: controller.signal
    })
    
    clearTimeout(timeoutId)
    
    if (!r.ok) {
      console.warn('Holiday check failed, continuing anyway', r.status)
      return
    }
    
    const data = await r.json()
    holiday.value = data.is_holiday ? data.holiday : null
    
    // Mostrar snackbar simples se for feriado
    if (data.is_holiday && data.holiday) {
      showSimpleHolidayAlert(data.holiday)
    }
  } catch (e) {
    if (e.name !== 'AbortError') {
      console.warn('Holiday check error, continuing anyway:', e)
    }
    // Não bloquear o processo se houver erro
  } finally {
    checkingHoliday.value = false
  }
}

// Função simples para mostrar alerta de feriado
function showSimpleHolidayAlert(holidayData) {
  // Criar um alerta simples sem dependências
  const alertDiv = document.createElement('div')
  alertDiv.className = 'fixed bottom-4 right-4 bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded shadow-lg z-50 max-w-sm'
  alertDiv.innerHTML = `
    <div class="flex items-center">
      <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
      </svg>
      <div>
        <p class="font-medium">${holidayData.name || 'Feriado'}</p>
        <p class="text-sm">A data selecionada é um feriado</p>
      </div>
      <button onclick="this.parentElement.parentElement.remove()" class="ml-2 text-yellow-600 hover:text-yellow-800">
        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
          <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
        </svg>
      </button>
    </div>
  `
  
  document.body.appendChild(alertDiv)
  
  // Remover automaticamente após 5 segundos
  setTimeout(() => {
    if (alertDiv.parentElement) {
      alertDiv.remove()
    }
  }, 5000)
}

watch(() => form.due_date, checkHoliday, { immediate: false })

function submit() {
  form.put(routeL('tasks.update', { task: props.task.id }))
}
</script>

<template>
  <AuthenticatedLayout>
    <template #header>
              <h2 class="text-xl font-semibold text-gray-800 leading-tight">
          {{ t('tasks.edit.page_title') /* ex.: "Edit Task" / "Editar Tarefa" */ }}
        </h2>
    </template>

    <div class="py-12">
      <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 space-y-6">
            <h1 class="text-2xl font-bold text-gray-900">
              {{ t('tasks.edit.page_title') }}
            </h1>

            <!-- Alerta de Feriado Sutil -->
            <div
              v-if="holiday"
              class="p-3 rounded-md border-l-2 border-rose-300 bg-rose-25"
            >
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <svg class="h-4 w-4 text-rose-500" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                  </svg>
                </div>
                <div class="ml-2">
                  <span class="text-sm font-medium text-rose-700">
                    🎉 {{ holiday.name }} - {{ holiday.type === 'feriado' ? 'Feriado' : 'Ponto Facultativo' }}
                  </span>
                </div>
              </div>
            </div>

            <form @submit.prevent="submit" class="space-y-6">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  {{ t('tasks.form.title') }}
                </label>
                <input
                  v-model="form.title"
                  type="text"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                  :placeholder="t('tasks.form.title_ph')"
                />
                <div v-if="form.errors.title" class="text-red-500 text-sm mt-1">{{ form.errors.title }}</div>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  {{ t('tasks.form.description') }}
                </label>
                <textarea
                  v-model="form.description"
                  rows="4"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                  :placeholder="t('tasks.form.description_ph')"
                ></textarea>
                <div v-if="form.errors.description" class="text-red-500 text-sm mt-1">{{ form.errors.description }}</div>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  {{ t('tasks.form.end_date') }}
                </label>
                <input
                  v-model="form.due_date"
                  type="date"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                />
                <div v-if="form.errors.due_date" class="text-red-500 text-sm mt-1">{{ form.errors.due_date }}</div>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  {{ t('tasks.form.status') }}
                </label>
                <select
                  v-model="form.status"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                >
                  <option value="pending">{{ t('status.pending') }}</option>
                  <option value="in_progress">{{ t('status.in_progress') }}</option>
                  <option value="completed">{{ t('status.completed') }}</option>
                </select>
                <div v-if="form.errors.status" class="text-red-500 text-sm mt-1">{{ form.errors.status }}</div>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  {{ t('tasks.form.priority') }}
                </label>
                <select
                  v-model="form.priority"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                >
                  <option value="low">{{ t('priority.low') }}</option>
                  <option value="medium">{{ t('priority.medium') }}</option>
                  <option value="high">{{ t('priority.high') }}</option>
                </select>
                <div v-if="form.errors.priority" class="text-red-500 text-sm mt-1">{{ form.errors.priority }}</div>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  {{ t('tasks.form.assignee') }}
                </label>
                <select
                  v-model="form.assigned_to"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                >
                  <option value="">{{ t('tasks.form.assignee_none') }}</option>
                  <option 
                    v-for="user in users" 
                    :key="user.id" 
                    :value="user.id"
                  >
                    {{ user.name }} ({{ user.email }})
                  </option>
                </select>
                <div v-if="form.errors.assigned_to" class="text-red-500 text-sm mt-1">{{ form.errors.assigned_to }}</div>
              </div>

              <div class="flex gap-2 justify-end">
                <button
                  type="submit"
                  class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 disabled:opacity-50"
                  :disabled="form.processing"
                >
                  {{ form.processing ? t('common.saving') : t('tasks.form.actions.update') }}
                </button>
                <Link
                  :href="routeL('tasks.index')"
                  class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600"
                >
                  {{ t('common.cancel') }}
                </Link>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>


