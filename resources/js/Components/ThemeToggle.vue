<template>
  <div class="flex items-center">
    <!-- Botão de estilo elegante -->
    <button
      @click="toggleStyle"
      class="relative inline-flex h-8 w-16 items-center rounded-full bg-gradient-to-r from-blue-500 to-purple-600 transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-offset-2 hover:from-blue-600 hover:to-purple-700 shadow-lg"
    >
      <!-- Ícone central -->
      <span class="absolute inset-0 flex items-center justify-center">
        <svg class="h-4 w-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zM21 5a2 2 0 00-2-2h-4a2 2 0 00-2 2v12a4 4 0 004 4h4a2 2 0 002-2V5z"
          />
        </svg>
      </span>
      
      <!-- Indicador de estilo ativo -->
      <span
        class="inline-block h-6 w-6 transform rounded-full bg-white shadow-md transition-all duration-300"
        :class="isActive ? 'translate-x-9' : 'translate-x-1'"
      >
        <!-- Ícone de estrela -->
        <svg class="h-4 w-4 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
          <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
        </svg>
      </span>
    </button>
    
    <!-- Label do estilo -->
    <span class="ml-3 text-sm font-medium text-gray-700">
      {{ isActive ? '✨ Premium' : '🎨 Elegante' }}
    </span>
  </div>
</template>

<script setup>
import { ref } from 'vue'

const isActive = ref(false)

const toggleStyle = () => {
  isActive.value = !isActive.value
  
  // Aplicar efeitos visuais
  if (isActive.value) {
    document.body.classList.add('premium-style')
    // Adicionar animação de partículas
    addParticleEffect()
  } else {
    document.body.classList.remove('premium-style')
    // Remover efeitos
    removeParticleEffect()
  }
}

const addParticleEffect = () => {
  // Criar efeito de partículas brilhantes
  const particles = document.createElement('div')
  particles.className = 'particles-container'
  particles.innerHTML = `
    <style>
      .particles-container {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        pointer-events: none;
        z-index: 9999;
        overflow: hidden;
      }
      .particle {
        position: absolute;
        width: 4px;
        height: 4px;
        background: linear-gradient(45deg, #ff6b6b, #4ecdc4, #45b7d1, #96ceb4);
        border-radius: 50%;
        animation: float 3s ease-in-out infinite;
      }
      @keyframes float {
        0%, 100% { transform: translateY(0px) rotate(0deg); opacity: 1; }
        50% { transform: translateY(-20px) rotate(180deg); opacity: 0.7; }
      }
    </style>
  `
  
  // Adicionar partículas
  for (let i = 0; i < 20; i++) {
    const particle = document.createElement('div')
    particle.className = 'particle'
    particle.style.left = Math.random() * 100 + '%'
    particle.style.top = Math.random() * 100 + '%'
    particle.style.animationDelay = Math.random() * 3 + 's'
    particles.appendChild(particle)
  }
  
  document.body.appendChild(particles)
}

const removeParticleEffect = () => {
  const particles = document.querySelector('.particles-container')
  if (particles) {
    particles.remove()
  }
}
</script>

<style scoped>
/* Estilos para modo premium */
:global(.premium-style) {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

:global(.premium-style .bg-white) {
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(10px);
}

:global(.premium-style .text-gray-700) {
  color: #2d3748;
}

:global(.premium-style .border-gray-300) {
  border-color: rgba(255, 255, 255, 0.3);
}
</style>
