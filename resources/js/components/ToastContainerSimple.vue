<template>
    <div class="fixed top-4 right-4 z-50">
        <!-- Toast de exemplo -->
        <div class="bg-white rounded-lg shadow-lg p-4 mb-2 border-l-4 border-green-500">
            <div class="flex items-start">
                <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center mr-3">
                    <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-900">Nova Tarefa Atribuída</p>
                    <p class="text-sm text-gray-600">Tarefa "Teste" foi atribuída para você</p>
                </div>
                <button class="text-gray-400 hover:text-gray-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
// Componente simplificado para teste
</script>
