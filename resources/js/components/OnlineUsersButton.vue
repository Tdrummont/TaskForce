<template>
    <button @click="$emit('open')" 
            class="relative bg-white bg-opacity-20 hover:bg-opacity-30 text-white p-1.5 sm:p-2 rounded-md transition-all duration-200 backdrop-blur-sm flex items-center space-x-1 sm:space-x-2 group touch-manipulation ios-button"
            style="min-width: 44px; min-height: 44px;">
        
        <!-- Ícone de Usuários -->
        <div class="relative">
            <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
            
            <!-- Indicador de Usuários Online -->
            <div v-if="onlineCount > 0" 
                 class="absolute -top-0.5 -right-0.5 sm:-top-1 sm:-right-1 w-2.5 h-2.5 sm:w-3 sm:h-3 bg-green-500 rounded-full border border-white animate-pulse"></div>
        </div>

        <!-- Contador de Usuários -->
        <span class="text-xs sm:text-sm font-medium">{{ onlineCount }}</span>

        <!-- Tooltip (apenas em desktop) -->
        <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-3 py-1 bg-gray-900 text-white text-xs rounded-md opacity-0 group-hover:opacity-100 transition-opacity duration-200 whitespace-nowrap z-50 hidden sm:block">
            {{ onlineCount }} usuário(s) online
            <div class="absolute top-full left-1/2 transform -translate-x-1/2 w-0 h-0 border-l-4 border-r-4 border-t-4 border-transparent border-t-gray-900"></div>
        </div>
    </button>
</template>

<script setup>
import { ref } from 'vue'

const props = defineProps({
    onlineCount: {
        type: Number,
        default: 0
    }
})

const emit = defineEmits(['open'])
</script>

<style scoped>
/* Animação de pulso para o indicador */
@keyframes pulse {
    0%, 100% {
        opacity: 1;
    }
    50% {
        opacity: 0.5;
    }
}

.animate-pulse {
    animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

/* iOS e Android específicos */
@supports (-webkit-touch-callout: none) {
    /* iOS Safari */
    .ios-button {
        -webkit-tap-highlight-color: rgba(255, 255, 255, 0.2);
        -webkit-touch-callout: none;
        -webkit-user-select: none;
        -webkit-backdrop-filter: blur(10px);
        backdrop-filter: blur(10px);
    }
}

/* Android Chrome */
@media screen and (-webkit-min-device-pixel-ratio: 0) {
    .ios-button {
        -webkit-tap-highlight-color: rgba(255, 255, 255, 0.2);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }
}

/* Melhorias para dispositivos móveis */
@media (max-width: 640px) {
    .ios-button {
        min-width: 44px;
        min-height: 44px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
}

/* Melhorias de performance */
.ios-button {
    will-change: transform;
    transform: translateZ(0);
    -webkit-transform: translateZ(0);
}

/* Melhorias de acessibilidade */
@media (prefers-reduced-motion: reduce) {
    .animate-pulse {
        animation: none;
    }
    
    .transition-all {
        transition: none;
    }
}

/* Dark mode support */
@media (prefers-color-scheme: dark) {
    .ios-button {
        background: rgba(255, 255, 255, 0.1);
    }
    
    .ios-button:hover {
        background: rgba(255, 255, 255, 0.2);
    }
}
</style>
