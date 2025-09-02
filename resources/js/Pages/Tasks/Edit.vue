<script setup>
import { Link, useForm } from '@inertiajs/vue3'

const props = defineProps({ task: Object })

const form = useForm({
  title: props.task.title,
  description: props.task.description,
  due_date: props.task.due_date,
  status: props.task.status,
  priority: props.task.priority,
  assigned_to: props.task.assigned_to
})

function submit() {
  form.put(`/tasks/${props.task.id}`)
}
</script>

<template>
  <div>
    <h1 class="text-2xl font-bold mb-4">Editar Tarefa</h1>
    <form @submit.prevent="submit">
      <div class="mb-3">
        <label class="block mb-1">Título</label>
        <input v-model="form.title" type="text" class="border p-2 w-full rounded" />
        <div v-if="form.errors.title" class="text-red-500 text-sm">{{ form.errors.title }}</div>
      </div>

      <div class="mb-3">
        <label class="block mb-1">Descrição</label>
        <textarea v-model="form.description" class="border p-2 w-full rounded"></textarea>
        <div v-if="form.errors.description" class="text-red-500 text-sm">{{ form.errors.description }}</div>
      </div>

      <div class="mb-3">
        <label class="block mb-1">Data de entrega</label>
        <input v-model="form.due_date" type="date" class="border p-2 w-full rounded" />
        <div v-if="form.errors.due_date" class="text-red-500 text-sm">{{ form.errors.due_date }}</div>
      </div>

      <div class="mb-3">
        <label class="block mb-1">Status</label>
        <select v-model="form.status" class="border p-2 w-full rounded">
          <option value="pending">Pendente</option>
          <option value="in_progress">Em andamento</option>
          <option value="completed">Concluída</option>
        </select>
        <div v-if="form.errors.status" class="text-red-500 text-sm">{{ form.errors.status }}</div>
      </div>

      <div class="mb-3">
        <label class="block mb-1">Prioridade</label>
        <select v-model="form.priority" class="border p-2 w-full rounded">
          <option value="low">Baixa</option>
          <option value="medium">Média</option>
          <option value="high">Alta</option>
        </select>
        <div v-if="form.errors.priority" class="text-red-500 text-sm">{{ form.errors.priority }}</div>
      </div>

      <div class="mb-3">
        <label class="block mb-1">Atribuir para (ID do usuário)</label>
        <input v-model="form.assigned_to" type="number" class="border p-2 w-full rounded" />
        <div v-if="form.errors.assigned_to" class="text-red-500 text-sm">{{ form.errors.assigned_to }}</div>
      </div>

      <div class="flex gap-2">
        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Atualizar</button>
        <Link href="/tasks" class="bg-gray-500 text-white px-4 py-2 rounded">Cancelar</Link>
      </div>
    </form>
  </div>
</template>


