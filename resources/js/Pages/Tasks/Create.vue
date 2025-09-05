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
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ t('tasks.create.page_title') }}
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6">
            <h1 class="text-2xl font-bold mb-6 text-gray-900">
              {{ t('tasks.create.header') }}
            </h1>

            <!-- 🔔 ALERTA DE FERIADO -->
            <div v-if="holidayInfo" class="mb-4 rounded-md border border-yellow-300 bg-yellow-50 p-3">
              <div class="flex items-start gap-2">
                <svg class="h-5 w-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01M4.93 19h14.14c1.54 0 2.5-1.67 1.73-3L13.73 4a2 2 0 00-3.46 0L3.2 16c-.77 1.33.19 3 1.73 3z"/>
                </svg>
                <div>
                  <p class="font-medium text-yellow-800">{{ t('holiday.alert.title') }}</p>
                  <p class="text-sm text-yellow-800">
                    {{ holidayInfo.name ? t('holiday.alert.body').replace('{name}', holidayInfo.name) : t('holiday.alert.generic') }}
                  </p>
                </div>
              </div>
            </div>

            <form @submit.prevent="submit" class="space-y-6">
              <!-- Título -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  {{ t('tasks.form.title') }} {{ t('tasks.form.required') }}
                </label>
                <input 
                  v-model="form.title" 
                  type="text" 
                  class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" 
                  :placeholder="t('tasks.form.title_ph')"
                />
                <div v-if="form.errors.title" class="text-red-500 text-sm mt-1">{{ form.errors.title }}</div>
              </div>

              <!-- UF (opcional, se desejar permitir troca) -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">{{ t('holiday.state.label') }}</label>
                <input v-model="stateUF" type="text" maxlength="2"
                       class="w-24 px-3 py-2 border rounded-md uppercase"
                       placeholder="SP" />
              </div>

              <!-- Descrição -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  {{ t('tasks.form.description') }}
                </label>
                <textarea 
                  v-model="form.description" 
                  rows="4"
                  maxlength="1000"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                  :placeholder="t('tasks.form.description_ph')"
                ></textarea>
                <div class="flex justify-between items-center mt-1">
                  <div v-if="form.errors.description" class="text-red-500 text-sm">{{ form.errors.description }}</div>
                  <div class="text-gray-500 text-sm">{{ charsText }}</div>
                </div>
              </div>

              <!-- Datas -->
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">
                    {{ t('tasks.form.start_date') }}
                  </label>
                  <input 
                    v-model="form.start_date" 
                    type="date" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" 
                  />
                  
                  <!-- Alerta de Feriado para Data de Início -->
                  <div
                    v-if="startDateHoliday"
                    class="mt-1 p-2 rounded-md border-l-2 border-amber-300 bg-amber-25"
                  >
                    <div class="flex items-center">
                      <div class="flex-shrink-0">
                        <svg class="h-3 w-3 text-amber-500" fill="currentColor" viewBox="0 0 20 20">
                          <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                      </div>
                      <div class="ml-2">
                        <span class="text-xs font-medium text-amber-700">
                          📅 {{ startDateHoliday.name }} - {{ startDateHoliday.type === 'feriado' ? 'Feriado' : 'Ponto Facultativo' }}
                        </span>
                      </div>
                    </div>
                  </div>
                  
                  <div v-if="form.errors.start_date" class="text-red-500 text-sm mt-1">{{ form.errors.start_date }}</div>
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">
                    {{ t('tasks.form.end_date') }}
                  </label>
                  <input 
                    v-model="form.due_date" 
                    type="date" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" 
                  />
                  
                  <!-- Alerta de Feriado Sutil -->
                  <div
                    v-if="holidayInfo"
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
                          🎉 {{ holidayInfo.name }} - {{ holidayInfo.type === 'feriado' ? 'Feriado' : 'Ponto Facultativo' }}
                        </span>
                      </div>
                    </div>
                  </div>
                  
                  <div v-if="form.errors.due_date" class="text-red-500 text-sm mt-1">{{ form.errors.due_date }}</div>
                </div>
              </div>

              <!-- Status e Prioridade -->
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">
                    {{ t('tasks.form.status') }} {{ t('tasks.form.required') }}
                  </label>
                  <select 
                    v-model="form.status" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                  >
                    <option v-for="o in statusOptions" :key="o.value" :value="o.value">
                      {{ t(o.labelKey) }}
                    </option>
                  </select>
                  <div v-if="form.errors.status" class="text-red-500 text-sm mt-1">{{ form.errors.status }}</div>
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">
                    {{ t('tasks.form.priority') }} {{ t('tasks.form.required') }}
                  </label>
                  <select 
                    v-model="form.priority" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                  >
                    <option v-for="o in priorityOptions" :key="o.value" :value="o.value">
                      {{ t(o.labelKey) }}
                    </option>
                  </select>
                  <div v-if="form.errors.priority" class="text-red-500 text-sm mt-1">{{ form.errors.priority }}</div>
                </div>
              </div>

              <!-- Categoria e Horas Estimadas -->
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">
                    {{ t('tasks.form.category') }}
                  </label>
                  <div class="relative">
                    <select 
                      v-model="form.category" 
                      class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                    >
                      <option value="">{{ t('tasks.form.category_ph') }}</option>
                      <!-- usa a lista com label traduzido e value original -->
                      <option v-for="c in displayCategories" :key="c.value" :value="c.value">
                        {{ c.label }}
                      </option>
                      <option value="__new__">
                        {{ locale === 'en' ? '+ Add new category' : '+ Adicionar nova categoria' }}
                      </option>
                    </select>
                    
                    <!-- Campo para nova categoria -->
                    <div v-if="form.category === '__new__'" class="mt-2">
                      <input 
                        v-model="newCategory" 
                        type="text" 
                        class="w-full px-3 py-2 border border-blue-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                        :placeholder="locale === 'en' ? 'Type the new category name' : 'Digite o nome da nova categoria'"
                        @keyup.enter="addNewCategory"
                      />
                      <div class="flex space-x-2 mt-2">
                        <button 
                          type="button"
                          @click="addNewCategory"
                          class="px-3 py-1 text-sm bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors"
                        >
                          {{ locale === 'en' ? 'Add' : 'Adicionar' }}
                        </button>
                        <button 
                          type="button"
                          @click="cancelNewCategory"
                          class="px-3 py-1 text-sm bg-gray-500 text-white rounded-md hover:bg-gray-600 transition-colors"
                        >
                          {{ t('tasks.form.actions.cancel') }}
                        </button>
                      </div>
                    </div>
                  </div>
                  <div v-if="form.errors.category" class="text-red-500 text-sm mt-1">{{ form.errors.category }}</div>
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">
                    {{ t('tasks.form.estimated_hours') }}
                  </label>
                  <input 
                    v-model="form.estimated_hours" 
                    type="number" 
                    min="0"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                    :placeholder="t('tasks.form.estimated_hours_ph')"
                  />
                  <div v-if="form.errors.estimated_hours" class="text-red-500 text-sm mt-1">{{ form.errors.estimated_hours }}</div>
                </div>
              </div>

              <!-- Tarefa Pai e Usuário Atribuído -->
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">
                    {{ t('tasks.form.parent_task') }}
                  </label>
                  <select 
                    v-model="form.parent_task_id" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                  >
                    <option value="">{{ t('tasks.form.parent_task_none') }}</option>
                    <option v-for="task in parentTasks" :key="task.id" :value="task.id">
                      {{ task.title }}
                    </option>
                  </select>
                  <div v-if="form.errors.parent_task_id" class="text-red-500 text-sm mt-1">{{ form.errors.parent_task_id }}</div>
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">
                    {{ t('tasks.form.assignee') }}
                  </label>
                  <select 
                    v-model="form.assigned_to" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                  >
                    <option value="">{{ t('tasks.form.assignee_none') }}</option>
                    <option v-for="user in users" :key="user.id" :value="user.id">
                      {{ user.name }}
                    </option>
                  </select>
                  <div v-if="form.errors.assigned_to" class="text-red-500 text-sm mt-1">{{ form.errors.assigned_to }}</div>
                </div>
              </div>

              <!-- Botões de Ação -->
              <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200">
                <button 
                  type="button"
                  @click="clearForm"
                  class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                >
                  {{ t('tasks.form.actions.clear') }}
                </button>

                <Link 
                  :href="routeL('tasks.index')" 
                  class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                >
                  {{ t('tasks.form.actions.cancel') }}
                </Link>

                <button 
                  type="submit" 
                  :disabled="form.processing"
                  class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
                >
                  <span v-if="form.processing">
                    {{ locale === 'en' ? 'Saving...' : 'Salvando...' }}
                  </span>
                  <span v-else>
                    {{ t('tasks.form.actions.create') }}
                  </span>
                </button>
              </div>
            </form>

          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>


