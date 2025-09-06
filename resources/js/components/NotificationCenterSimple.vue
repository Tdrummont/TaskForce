<template>
    <div class="relative">
        <!-- Botão de Notificações -->
        <button
            @click="toggleNotifications"
            class="bg-white bg-opacity-20 hover:bg-opacity-30 text-white p-2 rounded-md transition-all duration-200 backdrop-blur-sm relative"
        >
            <!-- Ícone de sino -->
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM4.828 7l2.586 2.586a2 2 0 002.828 0L12.828 7H4.828zM4.828 17h8l-2.586-2.586a2 2 0 00-2.828 0L4.828 17z" />
            </svg>
            
            <!-- Badge de notificações não lidas -->
            <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center animate-pulse">
                3
            </span>
        </button>

        <!-- Dropdown de Notificações -->
        <div
            v-if="showNotifications"
            class="absolute right-0 mt-2 w-80 bg-white rounded-md shadow-lg ring-1 ring-black ring-opacity-5 z-50"
        >
            <!-- Header -->
            <div class="px-4 py-3 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Notificações</h3>
            </div>

            <!-- Lista de Notificações -->
            <div class="max-h-96 overflow-y-auto">
                <div class="px-4 py-3 border-b border-gray-100">
                    <div class="flex items-start space-x-3">
                        <div class="w-8 h-8 rounded-full bg-green-500 flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900">Nova Tarefa Atribuída</p>
                            <p class="text-sm text-gray-600 mt-1">Tarefa "Teste" foi atribuída para você</p>
                            <p class="text-xs text-gray-400 mt-1">Agora mesmo</p>
                        </div>
                        <div class="w-2 h-2 bg-blue-500 rounded-full flex-shrink-0"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue'

const showNotifications = ref(false)

const toggleNotifications = () => {
    showNotifications.value = !showNotifications.value
}
</script>
