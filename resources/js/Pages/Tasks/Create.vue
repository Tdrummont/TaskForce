<script setup>
import { ref, computed, watch } from 'vue'
import { Link, useForm } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { useLocale } from '@/Components/useLocale' // ajuste se o caminho for diferente

const props = defineProps({
  users: Array,
  parentTasks: Array,
  categories: Array,
  // opcional: se você enviou a UF no share
  userState: { type: String, default: 'SP' },
})

const { routeL, t, locale } = useLocale()

// Função para obter a tradução da categoria
const getCategoryLabel = (categoryKey) => {
  return t(`categories.${categoryKey}`)
}

const displayCategories = computed(() => {
  return (props.categories || []).map((cat) => {
    // Se a categoria for uma chave de tradução (ex: 'development'), usar tradução
    // Se for texto direto, usar como está
    const label = cat.includes(' ') ? cat : getCategoryLabel(cat)
    return { value: cat, label }
  })
})

const form = useForm({
  title: '',
  description: '',
  due_date: '',
  start_date: '',
  status: 'pending',
  priority: 'medium',
  category: '',
  tags: [],
  estimated_hours: '',
  assigned_to: '',
  parent_task_id: ''
})

const newCategory = ref('')

// 🔔 Feriados
const stateUF = ref(props.userState || 'SP')
const holidayInfo = ref(null) // { name, date } | null
const startDateHoliday = ref(null) // { name, date } | null
const checkingHoliday = ref(false)
const checkingStartHoliday = ref(false)

async function checkHoliday() {
  holidayInfo.value = null
  if (!form.due_date || !stateUF.value) return

  // Verificação simples e não-bloqueante
  try {
    checkingHoliday.value = true
    
    // Usar AbortController para timeout
    const controller = new AbortController()
    const timeoutId = setTimeout(() => controller.abort(), 2000) // 2 segundos timeout
    
    const params = new URLSearchParams({
      date: form.due_date,
      state: stateUF.value
    })
    const res = await fetch(`/api/holidays/check?${params.toString()}`, {
      headers: { 'Accept': 'application/json' },
      signal: controller.signal
    })
    
    clearTimeout(timeoutId)
    
    if (!res.ok) {
      console.warn('Holiday check failed, continuing anyway', res.status)
      return
    }
    
    const data = await res.json()
    holidayInfo.value = data.is_holiday ? data.holiday : null
    
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

async function checkStartDateHoliday() {
  startDateHoliday.value = null
  if (!form.start_date || !stateUF.value) return

  // Verificação simples e não-bloqueante
  try {
    checkingStartHoliday.value = true
    
    // Usar AbortController para timeout
    const controller = new AbortController()
    const timeoutId = setTimeout(() => controller.abort(), 2000) // 2 segundos timeout
    
    const params = new URLSearchParams({
      date: form.start_date,
      state: stateUF.value
    })
    const res = await fetch(`/api/holidays/check?${params.toString()}`, {
      headers: { 'Accept': 'application/json' },
      signal: controller.signal
    })
    
    clearTimeout(timeoutId)
    
    if (!res.ok) {
      console.warn('Start date holiday check failed, continuing anyway', res.status)
      return
    }
    
    const data = await res.json()
    startDateHoliday.value = data.is_holiday ? data.holiday : null
    
    // Mostrar snackbar simples se for feriado
    if (data.is_holiday && data.holiday) {
      showSimpleHolidayAlert(data.holiday)
    }
  } catch (e) {
    if (e.name !== 'AbortError') {
      console.warn('Start date holiday check error, continuing anyway:', e)
    }
    // Não bloquear o processo se houver erro
  } finally {
    checkingStartHoliday.value = false
  }
}

watch(() => form.due_date, checkHoliday)
watch(() => form.start_date, checkStartDateHoliday)
watch(stateUF, checkHoliday)
watch(stateUF, checkStartDateHoliday)

// helpers de opções traduzidas
const statusOptions = [
  { value: 'pending', labelKey: 'status.pending' },
  { value: 'in_progress', labelKey: 'status.in_progress' },
  { value: 'completed', labelKey: 'status.completed' },
]

const priorityOptions = [
  { value: 'low', labelKey: 'priority.low' },
  { value: 'medium', labelKey: 'priority.medium' },
  { value: 'high', labelKey: 'priority.high' },
]

// como o teu t() não tem params, faço manual
const charsText = computed(() => {
  const len = (form.description || '').length
  return locale === 'en' ? `${len}/1000 characters` : `${len}/1000 caracteres`
})

function submit() {
  form.post(routeL('tasks.store'))
}

function addNewCategory() {
  if (newCategory.value.trim()) {
    form.category = newCategory.value.trim()
    newCategory.value = ''
  }
}

function cancelNewCategory() {
  form.category = ''
  newCategory.value = ''
}

function clearForm() {
  form.reset()
  newCategory.value = ''
}
</script>

<template>
  <AuthenticatedLayout>
    <template #header>
   
    </template>

    <div class="py-12">
  <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
    <div class="bg-white/10 backdrop-blur border border-white/10 text-white overflow-hidden shadow-sm sm:rounded-lg">
      <div class="p-8">
        <h1 class="text-2xl font-bold mb-6 text-white">
          {{ t('tasks.create.header') }}
        </h1>

        <form @submit.prevent="submit" class="grid grid-cols-6 gap-6">
          <!-- Título -->
          <div class="col-span-6 md:col-span-4">
            <label class="block text-sm font-medium text-slate-200 mb-2">{{ t('tasks.form.title') }}</label>
            <input v-model="form.title" type="text"
              class="w-full px-3 py-2 rounded-md bg-white/10 border border-white/10 text-white" />
          </div>

          <!-- Estado -->
          <div class="col-span-6 md:col-span-2">
            <label class="block text-sm font-medium text-slate-200 mb-2">{{ t('holiday.state.label') }}</label>
            <input v-model="stateUF" type="text" maxlength="2"
              class="w-full px-3 py-2 rounded-md bg-white/10 border border-white/10 text-white uppercase" />
          </div>

          <!-- Descrição -->
          <div class="col-span-6">
            <label class="block text-sm font-medium text-slate-200 mb-2">{{ t('tasks.form.description') }}</label>
            <textarea v-model="form.description" rows="4"
              class="w-full px-3 py-2 rounded-md bg-white/10 border border-white/10 text-white"></textarea>
          </div>

          <!-- Datas -->
          <div class="col-span-6 md:col-span-3">
            <label class="block text-sm font-medium text-slate-200 mb-2">{{ t('tasks.form.start_date') }}</label>
            <input v-model="form.start_date" type="date"
              class="w-full px-3 py-2 rounded-md bg-white/10 border border-white/10 text-white" />
          </div>

          <div class="col-span-6 md:col-span-3">
            <label class="block text-sm font-medium text-slate-200 mb-2">{{ t('tasks.form.end_date') }}</label>
            <input v-model="form.due_date" type="date"
              class="w-full px-3 py-2 rounded-md bg-white/10 border border-white/10 text-white" />
          </div>

          <!-- Status e Prioridade -->
          <div class="col-span-6 md:col-span-3">
            <label class="block text-sm font-medium text-slate-200 mb-2">{{ t('tasks.form.status') }}</label>
            <select v-model="form.status"
              class="w-full px-3 py-2 rounded-md bg-white/10 border border-white/10 text-white">
              <option v-for="o in statusOptions" :key="o.value" :value="o.value">{{ t(o.labelKey) }}</option>
            </select>
          </div>

          <div class="col-span-6 md:col-span-3">
            <label class="block text-sm font-medium text-slate-200 mb-2">{{ t('tasks.form.priority') }}</label>
            <select v-model="form.priority"
              class="w-full px-3 py-2 rounded-md bg-white/10 border border-white/10 text-white">
              <option v-for="o in priorityOptions" :key="o.value" :value="o.value">{{ t(o.labelKey) }}</option>
            </select>
          </div>

          <!-- Categoria e Horas -->
          <div class="col-span-6 md:col-span-3">
            <label class="block text-sm font-medium text-slate-200 mb-2">{{ t('tasks.form.category') }}</label>
            <select v-model="form.category"
              class="w-full px-3 py-2 rounded-md bg-white/10 border border-white/10 text-white">
              <option value="">{{ t('tasks.form.category_ph') }}</option>
              <option v-for="c in displayCategories" :key="c.value" :value="c.value">{{ c.label }}</option>
            </select>
          </div>

          <div class="col-span-6 md:col-span-3">
            <label class="block text-sm font-medium text-slate-200 mb-2">{{ t('tasks.form.estimated_hours') }}</label>
            <input v-model="form.estimated_hours" type="number"
              class="w-full px-3 py-2 rounded-md bg-white/10 border border-white/10 text-white" />
          </div>

          <!-- Tarefa Pai -->
          <div class="col-span-6 md:col-span-3">
            <label class="block text-sm font-medium text-slate-200 mb-2">{{ t('tasks.form.parent_task') }}</label>
            <select v-model="form.parent_task_id"
              class="w-full px-3 py-2 rounded-md bg-white/10 border border-white/10 text-white">
              <option value="">{{ t('tasks.form.parent_task_none') }}</option>
              <option v-for="task in parentTasks" :key="task.id" :value="task.id">{{ task.title }}</option>
            </select>
          </div>

          <!-- Usuário -->
          <div class="col-span-6 md:col-span-3">
            <label class="block text-sm font-medium text-slate-200 mb-2">{{ t('tasks.form.assignee') }}</label>
            <select v-model="form.assigned_to"
              class="w-full px-3 py-2 rounded-md bg-white/10 border border-white/10 text-white">
              <option value="">{{ t('tasks.form.assignee_none') }}</option>
              <option v-for="user in users" :key="user.id" :value="user.id">{{ user.name }}</option>
            </select>
          </div>

          <!-- Botões -->
          <div class="col-span-6 flex justify-end space-x-4 pt-6 border-t border-white/10">
            <!-- seus botões aqui -->
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

  </AuthenticatedLayout>
</template>


