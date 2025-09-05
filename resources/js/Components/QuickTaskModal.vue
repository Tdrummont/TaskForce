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
  due_date: '',
  status: 'pending'
})

const holiday = ref(null)
const checkingHoliday = ref(false)
const stateUF = ref(props.userState || 'SP')

const isOpen = computed(() => props.show)

function close() {
  emit('close')
  // limpa o formulário
  form.reset()
  form.clearErrors()
  holiday.value = null
}

async function checkHoliday() {
  holiday.value = null
  if (!form.due_date) return
  
  // Verificação simples e não-bloqueante
  try {
    checkingHoliday.value = true
    
    // Usar AbortController para timeout
    const controller = new AbortController()
    const timeoutId = setTimeout(() => controller.abort(), 2000) // 2 segundos timeout
    
    const r = await fetch(`/api/holidays/check?date=${encodeURIComponent(form.due_date)}&state=${encodeURIComponent(stateUF.value)}`, {
      headers: { 'Accept': 'application/json' },
      signal: controller.signal
    })
    
    clearTimeout(timeoutId)
    
    if (!r.ok) {
      console.warn('Holiday check failed, continuing anyway')
      return
    }
    
    const data = await r.json()
    holiday.value = data.is_holiday ? data.holiday : null
    
    // Mostrar snackbar simples se for feriado
    if (data.is_holiday && data.holiday) {
      // Usar um snackbar simples sem dependências externas
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
  
  // Criar elementos usando métodos seguros
  const container = document.createElement('div')
  container.className = 'flex items-center'
  
  // Ícone de aviso
  const icon = document.createElement('svg')
  icon.className = 'w-5 h-5 mr-2'
  icon.setAttribute('fill', 'currentColor')
  icon.setAttribute('viewBox', '0 0 20 20')
  icon.innerHTML = '<path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>'
  
  // Conteúdo do alerta
  const content = document.createElement('div')
  const title = document.createElement('p')
  title.className = 'font-medium'
  title.textContent = holidayData.name || 'Feriado'
  
  const message = document.createElement('p')
  message.className = 'text-sm'
  message.textContent = 'A data selecionada é um feriado'
  
  content.appendChild(title)
  content.appendChild(message)
  
  // Botão de fechar
  const closeBtn = document.createElement('button')
  closeBtn.className = 'ml-2 text-yellow-600 hover:text-yellow-800'
  closeBtn.addEventListener('click', () => {
    alertDiv.remove()
  })
  
  const closeIcon = document.createElement('svg')
  closeIcon.className = 'w-4 h-4'
  closeIcon.setAttribute('fill', 'currentColor')
  closeIcon.setAttribute('viewBox', '0 0 20 20')
  closeIcon.innerHTML = '<path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>'
  
  closeBtn.appendChild(closeIcon)
  
  // Montar a estrutura
  container.appendChild(icon)
  container.appendChild(content)
  container.appendChild(closeBtn)
  alertDiv.appendChild(container)
  
  document.body.appendChild(alertDiv)
  
  // Remover automaticamente após 5 segundos
  setTimeout(() => {
    if (alertDiv.parentElement) {
      alertDiv.remove()
    }
  }, 5000)
}

watch(() => form.due_date, checkHoliday)

async function submit() {
  console.log('🚀 Enviando formulário:', form.data())
  console.log('📍 Rota:', routeL('tasks.store'))
  
  try {
    await form.post(routeL('tasks.store'), {
      onSuccess: (page) => {
        console.log('✅ Tarefa criada com sucesso!', page)
        emit('created')
        close()
      },
      onError: (errors) => {
        console.error('❌ Erro ao criar tarefa:', errors)
        console.error('📊 Dados do formulário:', form.data())
        console.error('🔍 Erros detalhados:', form.errors)
      },
      onFinish: () => {
        console.log('🏁 Requisição finalizada')
        // Forçar fechamento do modal após a requisição
        if (!form.hasErrors) {
          emit('created')
          close()
        }
      }
    })
  } catch (error) {
    console.error('💥 Erro na requisição:', error)
  }
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

          <!-- Alerta de Feriado Sutil -->
          <div
            v-if="holiday"
            class="mt-1 p-2 rounded-md border-l-2 border-rose-300 bg-rose-25"
          >
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <svg class="h-3 w-3 text-rose-500" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                </svg>
              </div>
              <div class="ml-2">
                <span class="text-xs font-medium text-rose-700">
                  🎉 {{ holiday.name }} - {{ holiday.type === 'feriado' ? 'Feriado' : 'Ponto Facultativo' }}
                </span>
              </div>
            </div>
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