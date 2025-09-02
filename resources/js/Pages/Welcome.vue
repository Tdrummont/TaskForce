<template>
    <div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 flex items-center justify-center">
        <div class="max-w-md w-full mx-auto p-6">
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Sistema de Tarefas</h1>
                <p class="text-gray-600">Organize suas tarefas de forma simples e prática.</p>
            </div>

            <div class="space-y-4">
                <!-- Use links simples HTML por enquanto -->
                <a
                    href="/login"
                    class="w-full block text-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700"
                >
                    Login
                </a>

                <a
                    href="/register"
                    class="w-full block text-center py-3 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
                >
                    Registrar
                </a>
            </div>
        </div>
    </div>
</template>

<script setup>
// Sem dependências por enquanto
</script>
