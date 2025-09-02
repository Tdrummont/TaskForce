<script setup>
import { ref } from 'vue'
import { Link, useForm } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

const props = defineProps({
  users: Array,
  parentTasks: Array,
  categories: Array
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

function submit() {
  form.post('/tasks')
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
        Nova Tarefa
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6">
            <h1 class="text-2xl font-bold mb-6 text-gray-900">Criar Nova Tarefa</h1>
    <form @submit.prevent="submit" class="space-y-6">
      <!-- Título -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Título *</label>
        <input 
          v-model="form.title" 
          type="text" 
          class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" 
          placeholder="Digite o título da tarefa"
        />
        <div v-if="form.errors.title" class="text-red-500 text-sm mt-1">{{ form.errors.title }}</div>
      </div>

      <!-- Descrição -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Descrição</label>
        <textarea 
          v-model="form.description" 
          rows="4"
          maxlength="1000"
          class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
          placeholder="Descreva a tarefa..."
        ></textarea>
        <div class="flex justify-between items-center mt-1">
          <div v-if="form.errors.description" class="text-red-500 text-sm">{{ form.errors.description }}</div>
          <div class="text-gray-500 text-sm">{{ form.description.length }}/1000 caracteres</div>
        </div>
      </div>

      <!-- Datas -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Data de Início</label>
          <input 
            v-model="form.start_date" 
            type="date" 
            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" 
          />
          <div v-if="form.errors.start_date" class="text-red-500 text-sm mt-1">{{ form.errors.start_date }}</div>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Data de Vencimento</label>
          <input 
            v-model="form.due_date" 
            type="date" 
            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" 
          />
          <div v-if="form.errors.due_date" class="text-red-500 text-sm mt-1">{{ form.errors.due_date }}</div>
        </div>
      </div>

      <!-- Status e Prioridade -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Status *</label>
          <select 
            v-model="form.status" 
            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
          >
            <option value="pending">Pendente</option>
            <option value="in_progress">Em Progresso</option>
            <option value="completed">Concluída</option>
          </select>
          <div v-if="form.errors.status" class="text-red-500 text-sm mt-1">{{ form.errors.status }}</div>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Prioridade *</label>
          <select 
            v-model="form.priority" 
            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
          >
            <option value="low">Baixa</option>
            <option value="medium">Média</option>
            <option value="high">Alta</option>
          </select>
          <div v-if="form.errors.priority" class="text-red-500 text-sm mt-1">{{ form.errors.priority }}</div>
        </div>
      </div>

      <!-- Categoria e Horas Estimadas -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Categoria</label>
          <div class="relative">
            <select 
              v-model="form.category" 
              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
            >
              <option value="">Selecione uma categoria</option>
              <option v-for="category in categories" :key="category" :value="category">
                {{ category }}
              </option>
              <option value="__new__">+ Adicionar nova categoria</option>
            </select>
            
            <!-- Campo para nova categoria -->
            <div v-if="form.category === '__new__'" class="mt-2">
              <input 
                v-model="newCategory" 
                type="text" 
                class="w-full px-3 py-2 border border-blue-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                placeholder="Digite o nome da nova categoria"
                @keyup.enter="addNewCategory"
              />
              <div class="flex space-x-2 mt-2">
                <button 
                  type="button"
                  @click="addNewCategory"
                  class="px-3 py-1 text-sm bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors"
                >
                  Adicionar
                </button>
                <button 
                  type="button"
                  @click="cancelNewCategory"
                  class="px-3 py-1 text-sm bg-gray-500 text-white rounded-md hover:bg-gray-600 transition-colors"
                >
                  Cancelar
                </button>
              </div>
            </div>
          </div>
          <div v-if="form.errors.category" class="text-red-500 text-sm mt-1">{{ form.errors.category }}</div>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Horas Estimadas</label>
          <input 
            v-model="form.estimated_hours" 
            type="number" 
            min="0"
            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
            placeholder="Ex: 8"
          />
          <div v-if="form.errors.estimated_hours" class="text-red-500 text-sm mt-1">{{ form.errors.estimated_hours }}</div>
        </div>
      </div>

      <!-- Tarefa Pai e Usuário Atribuído -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Tarefa Pai</label>
          <select 
            v-model="form.parent_task_id" 
            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
          >
            <option value="">Nenhuma (tarefa principal)</option>
            <option v-for="task in parentTasks" :key="task.id" :value="task.id">
              {{ task.title }}
            </option>
          </select>
          <div v-if="form.errors.parent_task_id" class="text-red-500 text-sm mt-1">{{ form.errors.parent_task_id }}</div>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Atribuir para</label>
          <select 
            v-model="form.assigned_to" 
            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
          >
            <option value="">Ninguém</option>
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
          Limpar
        </button>
        <Link 
          :href="route('tasks.index')" 
          class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
        >
          Cancelar
        </Link>
        <button 
          type="submit" 
          :disabled="form.processing"
          class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
        >
          <span v-if="form.processing">Salvando...</span>
          <span v-else>Criar Tarefa</span>
        </button>
      </div>
    </form>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>


