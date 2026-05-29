<template>
  <AuthenticatedLayout>

    <!-- Header da Página -->
<!--    <template #header>-->
<!--      <h2 class="font-semibold text-3xl text-white leading-tight">-->
<!--        {{ t('tasks.view_task') }}-->
<!--      </h2>-->
<!--    </template>-->

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

        <!-- Card Principal -->
        <div
          v-if="task"
          class="bg-slate-950 text-white overflow-hidden
                 shadow-2xl rounded-2xl"
        >

          <div class="p-8 text-white">

            <!-- Header da Task -->
            <div class="flex items-start justify-between mb-8">

              <div>
                <h1 class="font-semibold text-2xl text-00 text-white leading-tight">
                  {{ task.title }}
                </h1>

                <!-- Badges -->
                <div class="flex items-center gap-4 mt-4 text-sm">

                  <span class="flex items-center">
                    <span
                      class="w-2 h-2 rounded-full mr-2"
                      :class="getStatusColor(task.status)"
                    ></span>

                    {{ t(`status.${task.status}`) }}
                  </span>

                  <span class="flex items-center">
                    <span
                      class="w-2 h-2 rounded-full mr-2"
                      :class="getPriorityColor(task.priority)"
                    ></span>

                    {{ t(`priority.${task.priority}`) }}
                  </span>

                  <span
                    v-if="task.category"
                    class="bg-blue-800 text-white
                           text-xs font-medium px-2.5 py-1 rounded-lg"
                  >
                    {{ t(`categories.${task.category}`) }}
                  </span>
                </div>
              </div>

              <!-- Botões -->
              <div class="flex items-center gap-3">

                <Link
                  :href="route('tasks.edit', {
                    locale: locale,
                    task: task.id
                  })"
                  class="bg-blue-600 hover:bg-blue-700
                         text-white px-5 py-2.5 rounded-xl
                         transition-all duration-300"
                >
                  {{ t('tasks.edit') }}
                </Link>

                <Link
                  :href="route('tasks.index', {
                    locale: locale
                  })"
                  class="bg-zinc-700 hover:bg-zinc-600
                         text-white px-5 py-2.5 rounded-xl
                         transition-all duration-300"
                >
                  {{ t('tasks.back_to_list') }}
                </Link>
              </div>
            </div>

            <!-- Informações -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">

              <!-- Info -->
              <div class="bg-slate-900 p-6 rounded-2xl">

                <h3 class="font-semibold text-lg mb-5 text-white">
                  {{ t('tasks.task_information') }}
                </h3>

                <div class="space-y-4 text-sm">

                  <div class="flex justify-between">
                    <span class="text-gray-400">
                      {{ t('tasks.created_by') }}
                    </span>

                    <span class="font-medium">
                      {{ task.user?.name || 'N/A' }}
                    </span>
                  </div>

                  <div class="flex justify-between">
                    <span class="text-gray-400">
                      {{ t('tasks.assigned_to') }}
                    </span>

                    <span class="font-medium">
                      {{ task.assigned_to?.name || t('tasks.unassigned') }}
                    </span>
                  </div>

                  <div class="flex justify-between">
                    <span class="text-gray-400">
                      {{ t('tasks.created_at') }}
                    </span>

                    <span class="font-medium">
                      {{ formatDate(task.created_at) }}
                    </span>
                  </div>

                  <div
                    class="flex justify-between"
                    v-if="task.due_date"
                  >
                    <span class="text-gray-400">
                      {{ t('tasks.due_date') }}
                    </span>

                    <span class="font-medium">
                      {{ formatDate(task.due_date) }}
                    </span>
                  </div>
                </div>
              </div>

              <!-- Progress -->
              <div class="bg-slate-900 p-6 rounded-2xl">

                <h3 class="font-semibold text-lg mb-5 text-white">
                  {{ t('tasks.progress') }}
                </h3>

                <div class="space-y-4">

                  <div class="flex justify-between text-sm">
                    <span class="text-gray-500">
                      {{ t('tasks.completion_percentage') }}
                    </span>

                    <span class="font-semibold">
                      {{ task.completion_percentage || 0 }}%
                    </span>
                  </div>

                  <div class="w-full bg-zinc-700 rounded-full h-3">
                    <div
                      class="bg-blue-600 h-3 rounded-full transition-all duration-500"
                      :style="{
                        width: (task.completion_percentage || 0) + '%'
                      }"
                    ></div>
                  </div>

                </div>
              </div>
            </div>

            <!-- Descrição -->
            <div class="mb-8" v-if="task.description">

              <h3 class="font-semibold text-lg mb-3 text-white">
                {{ t('tasks.description') }}
              </h3>

              <textarea
                readonly
                class="w-full min-h-[180px]
                       bg-slate-900
                       border border-zinc-700
                       rounded-2xl p-5
                       text-white
                       resize-none
                       focus:ring-0 focus:outline-none"
              >{{ task.description }}</textarea>

            </div>

            <!-- Anexos -->
            <div class="mb-8">

              <div class="flex items-center mb-4">

                <h3 class="font-semibold text-lg text-white">
                  {{ t('tasks.attachments') }}
                </h3>

                <span
                  class="ml-2 text-xs bg-slate-800 text-white
                         px-2 py-1 rounded-lg"
                >
                  {{ task.attachments?.length || 0 }}
                </span>

              </div>

              <div class="flex flex-wrap gap-4">

                <!-- Arquivos -->
                <div
                  v-for="attachment in task.attachments"
                  :key="attachment.id"
                  class="w-72 bg-slate-900 border border-zinc-700
                         rounded-2xl p-4 hover:border-blue-500
                         transition-all duration-300"
                >

                  <div class="flex items-center gap-4">

                    <!-- Ícone -->
                    <div
                      class="w-12 h-12 bg-zinc-800 rounded-xl
                             flex items-center justify-center"
                    >
                      📎
                    </div>

                    <!-- Info -->
                    <div class="min-w-0">

                      <p class="text-sm text-white truncate">
                        {{ attachment.original_filename }}
                      </p>

                      <p class="text-xs text-gray-400">
                        {{ formatFileSize(attachment.file_size) }}
                      </p>

                    </div>
                  </div>
                </div>

                <!-- Botão Adicionar -->
                <button
                  @click="$refs.fileInput.click()"
                  class="w-24 h-24 border-2 border-dashed
                         border-zinc-600 hover:border-blue-500
                         rounded-2xl flex items-center justify-center
                         text-gray-400 hover:text-blue-400
                         transition-all duration-300"
                >
                  <svg
                    class="w-8 h-8"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M12 4v16m8-8H4"
                    />
                  </svg>
                </button>

                <!-- Input -->
                <input
                  ref="fileInput"
                  type="file"
                  multiple
                  class="hidden"
                  @change="handleFiles"
                />

              </div>

              <!-- Upload Preview -->
              <div
                v-if="selectedFiles.length > 0"
                class="mt-6 space-y-3"
              >

                <div
                  v-for="(file, index) in selectedFiles"
                  :key="index"
                  class="flex items-center justify-between
                         bg-slate-900 border border-zinc-700
                         rounded-xl px-4 py-3"
                >

                  <div>
                    <p class="text-sm text-white">
                      {{ file.name }}
                    </p>

                    <p class="text-xs text-gray-400">
                      {{ formatFileSize(file.size) }}
                    </p>
                  </div>

                  <button
                    @click="removeFile(index)"
                    class="text-red-400 hover:text-red-300"
                  >
                    ✕
                  </button>
                </div>

                <button
                  @click="uploadFiles"
                  class="bg-blue-600 hover:bg-blue-700
                         text-white px-5 py-2.5 rounded-xl
                         transition-all duration-300"
                >
                  Enviar Arquivos
                </button>

              </div>
            </div>

          </div>
        </div>

        <!-- Loading -->
        <div
          v-else
          class="text-center text-gray-500 py-12"
        >
          Carregando...
        </div>

      </div>
    </div>

  </AuthenticatedLayout>
</template>

<script setup>
import { ref } from 'vue'
import { Link, router, usePage } from '@inertiajs/vue3'
import { useLocale } from '@/Components/useLocale'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import axios from 'axios'
const { t, formatDate } = useLocale()
const page = usePage()
const locale = page.props.locale ?? 'pt'

const props = defineProps({
  task: Object
})

const selectedFiles = ref([])

const handleFiles = (event) => {
  selectedFiles.value = Array.from(event.target.files)
}
const removeFile = (index) => {
  selectedFiles.value.splice(index, 1)
}

const uploadFiles = async () => {

  if (!selectedFiles.value.length) return

  try {

    for (const file of selectedFiles.value) {

      const formData = new FormData()

      formData.append('file', file)

      await axios.post(
        route('tasks.attachments.store', {
          locale: locale,
          task: props.task.id
        }),
        formData,
        {
          headers: {
            'Content-Type': 'multipart/form-data'
          }
        }
      )
    }

    selectedFiles.value = []

    window.location.reload()

  } catch (error) {
    console.error(error)
  }
}

const getStatusColor = (status) => {
  const colors = {
    pending: 'bg-yellow-500',
    in_progress: 'bg-blue-500',
    completed: 'bg-green-500',
    cancelled: 'bg-red-500'
  }
  return colors[status] || 'bg-gray-500'
}

const getPriorityColor = (priority) => {
  const colors = {
    low: 'bg-green-500',
    medium: 'bg-yellow-500',
    high: 'bg-red-500'
  }
  return colors[priority] || 'bg-gray-500'
}

const getStatusBadgeClass = (status) => {
  const classes = {
    pending: 'bg-yellow-100 text-yellow-800',
    in_progress: 'bg-blue-100 text-blue-800',
    completed: 'bg-green-100 text-green-800',
    cancelled: 'bg-red-100 text-red-800'
  }
  return classes[status] || 'bg-gray-100 text-gray-800'
}

const formatFileSize = (bytes) => {
  if (bytes === 0) return '0 Bytes'
  const k = 1024
  const sizes = ['Bytes', 'KB', 'MB', 'GB']
  const i = Math.floor(Math.log(bytes) / Math.log(k))
  return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i]
}
</script>
