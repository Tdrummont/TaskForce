<template>
  <div>
    <label class="block text-sm font-medium text-gray-700 mb-2">
      {{ label }}
    </label>
    <div class="relative">
      <select 
        :value="modelValue" 
        @change="$emit('update:modelValue', $event.target.value)"
        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
      >
        <option value="">Selecione uma categoria</option>
        <option v-for="category in categories" :key="category" :value="category">
          {{ category }}
        </option>
        <option value="__new__">+ Adicionar nova categoria</option>
      </select>
      
      <!-- Campo para nova categoria -->
      <div v-if="modelValue === '__new__'" class="mt-2">
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
    <div v-if="error" class="text-red-500 text-sm mt-1">{{ error }}</div>
  </div>
</template>

<script setup>
import { ref } from 'vue'

const props = defineProps({
  modelValue: {
    type: String,
    default: ''
  },
  categories: {
    type: Array,
    default: () => []
  },
  label: {
    type: String,
    default: 'Categoria'
  },
  error: {
    type: String,
    default: ''
  }
})

const emit = defineEmits(['update:modelValue'])

const newCategory = ref('')

function addNewCategory() {
  if (newCategory.value.trim()) {
    emit('update:modelValue', newCategory.value.trim())
    newCategory.value = ''
  }
}

function cancelNewCategory() {
  emit('update:modelValue', '')
  newCategory.value = ''
}
</script> 