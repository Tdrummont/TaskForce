<template>
  <Transition
    enter-active-class="ease-out duration-300"
    enter-from-class="opacity-0"
    enter-to-class="opacity-100"
    leave-active-class="ease-in duration-200"
    leave-from-class="opacity-100"
    leave-to-class="opacity-0"
  >
    <div v-if="show" class="fixed inset-0 z-[9999] overflow-y-auto">
      <!-- Overlay -->
      <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" @click="closeModal"></div>
      
      <!-- Modal -->
      <div class="flex min-h-full items-center justify-center p-4">
        <Transition
          enter-active-class="ease-out duration-300"
          enter-from-class="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
          enter-to-class="opacity-100 translate-y-0 sm:scale-100"
          leave-active-class="ease-in duration-200"
          leave-from-class="opacity-100 translate-y-0 sm:scale-100"
          leave-to-class="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        >
          <div class="relative bg-white rounded-lg shadow-xl max-w-md w-full mx-auto transform transition-all">
        <!-- Header -->
        <div class="flex items-center justify-between p-6 border-b border-gray-200">
          <h3 class="text-lg font-semibold text-gray-900">
            Nova Tarefa Rápida
          </h3>
          <button
            @click="closeModal"
            class="text-gray-400 hover:text-gray-600 transition-colors"
          >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>

        <!-- Form -->
        <form @submit.prevent="submitForm" class="p-6">
          <!-- Título -->
          <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Título *
            </label>
            <input
              v-model="form.title"
              type="text"
              required
              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              placeholder="Digite o título da tarefa"
            />
            <div v-if="form.errors.title" class="text-red-500 text-sm mt-1">
              {{ form.errors.title }}
            </div>
          </div>

          <!-- Descrição -->
          <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Descrição
            </label>
            <textarea
              v-model="form.description"
              rows="3"
              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              placeholder="Descreva brevemente a tarefa..."
            ></textarea>
            <div v-if="form.errors.description" class="text-red-500 text-sm mt-1">
              {{ form.errors.description }}
            </div>
          </div>

          <!-- Prioridade e Categoria -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Prioridade
              </label>
              <select
                v-model="form.priority"
                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              >
                <option value="low">Baixa</option>
                <option value="medium">Média</option>
                <option value="high">Alta</option>
              </select>
              <div v-if="form.errors.priority" class="text-red-500 text-sm mt-1">
                {{ form.errors.priority }}
              </div>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Categoria
              </label>
              <select
                v-model="form.category"
                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              >
                <option value="">Selecione uma categoria</option>
                <option value="Desenvolvimento">Desenvolvimento</option>
                <option value="Design">Design</option>
                <option value="Marketing">Marketing</option>
                <option value="Vendas">Vendas</option>
                <option value="Suporte">Suporte</option>
                <option value="Administrativo">Administrativo</option>
                <option value="Financeiro">Financeiro</option>
                <option value="Recursos Humanos">Recursos Humanos</option>
                <option value="Operações">Operações</option>
                <option value="Qualidade">Qualidade</option>
                <option value="Pesquisa">Pesquisa</option>
                <option value="Treinamento">Treinamento</option>
                <option value="Manutenção">Manutenção</option>
                <option value="Infraestrutura">Infraestrutura</option>
                <option value="Segurança">Segurança</option>
              </select>
              <div v-if="form.errors.category" class="text-red-500 text-sm mt-1">
                {{ form.errors.category }}
              </div>
            </div>
          </div>

          <!-- Data de Vencimento -->
          <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Data de Vencimento
            </label>
            <div class="flex space-x-2 mb-2">
              <button
                type="button"
                @click="setDueDate('today')"
                class="px-3 py-1 text-xs bg-blue-100 text-blue-700 rounded-md hover:bg-blue-200 transition-colors"
              >
                Hoje
              </button>
              <button
                type="button"
                @click="setDueDate('tomorrow')"
                class="px-3 py-1 text-xs bg-green-100 text-green-700 rounded-md hover:bg-green-200 transition-colors"
              >
                Amanhã
              </button>
              <button
                type="button"
                @click="setDueDate('next_week')"
                class="px-3 py-1 text-xs bg-purple-100 text-purple-700 rounded-md hover:bg-purple-200 transition-colors"
              >
                Próxima Semana
              </button>
            </div>
            <input
              v-model="form.due_date"
              type="date"
              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            />
            <div v-if="form.errors.due_date" class="text-red-500 text-sm mt-1">
              {{ form.errors.due_date }}
            </div>
          </div>

          <!-- Botões -->
          <div class="flex items-center justify-end space-x-3">
            <button
              type="button"
              @click="closeModal"
              class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
            >
              Cancelar
            </button>
            <button
              type="submit"
              :disabled="form.processing"
              class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <span v-if="form.processing">Criando...</span>
              <span v-else>Criar Tarefa</span>
            </button>
          </div>
        </form>
          </div>
        </Transition>
      </div>
    </div>
  </Transition>
</template>

<script setup>
import { ref, watch, onMounted, onUnmounted } from 'vue'
import { useForm } from '@inertiajs/vue3'

const props = defineProps({
  show: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['close'])

const form = useForm({
  title: '',
  description: '',
  priority: 'medium',
  due_date: '',
  status: 'pending',
  category: '',
  tags: [],
  estimated_hours: '',
  assigned_to: '',
  parent_task_id: ''
})

const closeModal = () => {
  form.reset()
  emit('close')
}

const submitForm = () => {
  console.log('Submitting form with data:', form.data())
  
  form.post('/tasks', {
    onSuccess: () => {
      console.log('Task created successfully')
      closeModal()
    },
    onError: (errors) => {
      console.error('Error creating task:', errors)
    }
  })
}

const setDueDate = (type) => {
  const today = new Date()
  
  switch (type) {
    case 'today':
      form.due_date = today.toISOString().split('T')[0]
      break
    case 'tomorrow':
      const tomorrow = new Date(today)
      tomorrow.setDate(tomorrow.getDate() + 1)
      form.due_date = tomorrow.toISOString().split('T')[0]
      break
    case 'next_week':
      const nextWeek = new Date(today)
      nextWeek.setDate(nextWeek.getDate() + 7)
      form.due_date = nextWeek.toISOString().split('T')[0]
      break
  }
}

// Reset form when modal opens
watch(() => props.show, (newValue) => {
  if (newValue) {
    form.reset()
  }
})

// Handle ESC key to close modal
const handleKeydown = (event) => {
  if (event.key === 'Escape' && props.show) {
    closeModal()
  }
}

onMounted(() => {
  document.addEventListener('keydown', handleKeydown)
})

onUnmounted(() => {
  document.removeEventListener('keydown', handleKeydown)
})
</script> 