<script setup>
import { ref, watch, computed } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { useLocale } from '@/Components/useLocale'

const props = defineProps({
  show: { type: Boolean, default: false },
  onClose: { type: Function, default: () => {} },
  categories: { type: Array, default: () => [] },
  // passe do layout/controller: $user->state ?? 'SP'
  userState: { type: String, default: 'SP' }
})

// Categorias predefinidas como fallback (chaves de tradução)
const defaultCategories = [
  'development',
  'design',
  'marketing',
  'sales',
  'support',
  'administrative',
  'financial',
  'human_resources',
  'operations',
  'quality',
  'research',
  'training',
  'maintenance',
  'infrastructure',
  'security'
]

// Usar categorias passadas como prop ou as predefinidas
const availableCategories = computed(() => {
  return props.categories.length > 0 ? props.categories : defaultCategories
})

const emit = defineEmits(['close', 'created'])

const { routeL, t } = useLocale()

const form = useForm({
  title: '',
  description: '',
  priority: 'medium',
  category: '',
  due_date: ''
})

const holiday = ref(null)
const checkingHoliday = ref(false)
const stateUF = ref(props.userState || 'SP')

const isOpen = computed(() => props.show)

function close() {
  emit('close')
  // limpa (opcional)
  form.reset()
  holiday.value = null
}

async function checkHoliday() {
  holiday.value = null
  if (!form.due_date) return
  try {
    checkingHoliday.value = true
    const r = await fetch(`/api/holidays/check?date=${encodeURIComponent(form.due_date)}&state=${encodeURIComponent(stateUF.value)}`, {
      headers: { 'Accept': 'application/json' }
    })
    if (!r.ok) throw new Error('holiday request failed')
    const data = await r.json()
    holiday.value = data.is_holiday ? data.holiday : null
  } catch (e) {
    console.error('holiday check error', e)
  } finally {
    checkingHoliday.value = false
  }
}

watch(() => form.due_date, checkHoliday)

async function submit() {
  await form.post(routeL('tasks.store'), {
    onSuccess: () => {
      emit('created')
      close()
    }
  })
}

function setQuickDue(days) {
  // days: 0 (hoje), 1 (amanhã), 7 (próx semana) etc.
  const d = new Date()
  d.setDate(d.getDate() + days)
  const yyyy = d.getFullYear()
  const mm = String(d.getMonth() + 1).padStart(2, '0')
  const dd = String(d.getDate()).padStart(2, '0')
  form.due_date = `${yyyy}-${mm}-${dd}`
}

function getCategoryDisplayName(category) {
  // Se a categoria for uma chave de tradução (ex: 'sales'), traduz
  const translationKey = `categories.${category}`
  const translation = t(translationKey)
  
  if (translation !== translationKey) {
    return translation
  }
  
  // Se não for uma chave de tradução, retorna a categoria como está
  return category
}
</script>

<template>
  <div v-if="isOpen" class="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-4">
    <!-- backdrop -->
    <div class="fixed inset-0 bg-black/40" @click="close"></div>

    <div class="relative w-full max-w-lg bg-white rounded-xl shadow-xl p-6 space-y-4">
      <div class="flex items-center justify-between">
        <h3 class="text-lg font-semibold">
          {{ t('quick.new_quick_task') /* "Nova Tarefa Rápida" / "New Quick Task" */ }}
        </h3>
        <button @click="close" class="text-gray-500 hover:text-gray-700">✕</button>
      </div>

      <form @submit.prevent="submit" class="space-y-4">
        <!-- Título -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">
            {{ t('tasks.form.title') }} *
          </label>
          <input
            v-model="form.title"
            type="text"
            class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500"
            :placeholder="t('tasks.form.title_ph')"
          />
          <div v-if="form.errors.title" class="text-red-500 text-sm mt-1">{{ form.errors.title }}</div>
        </div>

        <!-- Descrição -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">
            {{ t('tasks.form.description') }}
          </label>
          <textarea
            v-model="form.description"
            rows="3"
            class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500"
            :placeholder="t('tasks.form.description_ph')"
          ></textarea>
          <div v-if="form.errors.description" class="text-red-500 text-sm mt-1">{{ form.errors.description }}</div>
        </div>

        <!-- Prioridade / Categoria -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
              {{ t('tasks.form.priority') }}
            </label>
            <select
              v-model="form.priority"
              class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500"
            >
              <option value="low">{{ t('priority.low') }}</option>
              <option value="medium">{{ t('priority.medium') }}</option>
              <option value="high">{{ t('priority.high') }}</option>
            </select>
            <div v-if="form.errors.priority" class="text-red-500 text-sm mt-1">{{ form.errors.priority }}</div>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
              {{ t('tasks.form.category') }}
            </label>
            <select
              v-model="form.category"
              class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500"
            >
              <option value="">{{ t('tasks.form.category_ph') }}</option>
              <option v-for="c in availableCategories" :key="c" :value="c">
                {{ getCategoryDisplayName(c) }}
              </option>
            </select>
            <div v-if="form.errors.category" class="text-red-500 text-sm mt-1">{{ form.errors.category }}</div>
          </div>
        </div>

        <!-- Data de vencimento -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">
            {{ t('tasks.form.end_date') }}
          </label>

          <!-- atalhos -->
          <div class="flex items-center gap-2 mb-2">
            <button type="button" class="px-2 py-1 text-xs rounded bg-gray-100 hover:bg-gray-200" @click="setQuickDue(0)">
              {{ t('quick.today') }}
            </button>
            <button type="button" class="px-2 py-1 text-xs rounded bg-gray-100 hover:bg-gray-200" @click="setQuickDue(1)">
              {{ t('quick.tomorrow') }}
            </button>
            <button type="button" class="px-2 py-1 text-xs rounded bg-gray-100 hover:bg-gray-200" @click="setQuickDue(7)">
              {{ t('quick.next_week') }}
            </button>
          </div>

          <input
            v-model="form.due_date"
            type="date"
            class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500"
          />

          <!-- alerta de feriado -->
          <div
            v-if="holiday"
            class="mt-2 rounded-md border border-yellow-300 bg-yellow-50 p-2 text-xs text-yellow-900"
          >
            <strong>{{ t('holidays.alert') }}:</strong>
            {{ t('holidays.on_date') }}
            <span class="font-medium">{{ holiday.name }}</span>
          </div>

          <div v-if="form.errors.due_date" class="text-red-500 text-sm mt-1">{{ form.errors.due_date }}</div>
        </div>

        <!-- ações -->
        <div class="flex justify-end gap-2 pt-2">
          <button type="button" @click="close" class="px-4 py-2 rounded bg-gray-500 text-white hover:bg-gray-600">
            {{ t('common.cancel') }}
          </button>
          <button type="submit" :disabled="form.processing" class="px-4 py-2 rounded bg-blue-600 text-white hover:bg-blue-700 disabled:opacity-50">
            {{ form.processing ? t('common.saving') : t('quick.create_task') }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template> 