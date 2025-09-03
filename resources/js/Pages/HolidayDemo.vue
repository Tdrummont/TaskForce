<template>
    <div class="min-h-screen bg-gray-50 py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-gray-900 mb-2">
                    🎉 Sistema de Notificações de Feriados
                </h1>
                <p class="text-lg text-gray-600">
                    Demonstração completa das funcionalidades implementadas
                </p>
            </div>

            <!-- Componentes de Demonstração -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                
                <!-- 1. Input de Data com Validação -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">
                        📅 Input de Data com Validação
                    </h2>
                    
                    <HolidayDateInput
                        v-model="selectedDate"
                        label="Data de Vencimento"
                        placeholder="Selecione uma data"
                        :required="true"
                        state="SP"
                        help-text="Digite uma data para verificar se é feriado"
                        @date-validated="onDateValidated"
                        @holiday-detected="onHolidayDetected"
                    />
                    
                    <div class="mt-4 p-3 bg-blue-50 rounded-md">
                        <h4 class="font-medium text-blue-900">Eventos Emitidos:</h4>
                        <pre class="text-xs text-blue-700 mt-2">{{ JSON.stringify(lastEvent, null, 2) }}</pre>
                    </div>
                </div>

                <!-- 2. Verificação Manual -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">
                        🔍 Verificação Manual
                    </h2>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Data para verificar
                            </label>
                            <input
                                type="date"
                                v-model="manualDate"
                                class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            />
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Estado (UF)
                            </label>
                            <select
                                v-model="selectedState"
                                class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            >
                                <option value="">Todos os estados</option>
                                <option value="SP">São Paulo</option>
                                <option value="RJ">Rio de Janeiro</option>
                                <option value="MG">Minas Gerais</option>
                                <option value="RS">Rio Grande do Sul</option>
                            </select>
                        </div>
                        
                        <button
                            @click="checkManualDate"
                            :disabled="!manualDate || isChecking"
                            class="w-full bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            <span v-if="isChecking">Verificando...</span>
                            <span v-else>Verificar Feriado</span>
                        </button>
                    </div>
                    
                    <div v-if="manualResult" class="mt-4 p-3 rounded-md" :class="manualResultClasses">
                        <h4 class="font-medium" :class="manualResultTitleClasses">
                            {{ manualResult.is_holiday ? 'Feriado Detectado!' : 'Dia Útil' }}
                        </h4>
                        <p v-if="manualResult.holiday" class="text-sm mt-1">
                            {{ manualResult.holiday.name }} - {{ formatDate(manualResult.holiday.date) }}
                        </p>
                    </div>
                </div>

                <!-- 3. Controles de Toast -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">
                        🎯 Controles de Toast
                    </h2>
                    
                    <div class="space-y-3">
                        <button
                            @click="showSampleHoliday"
                            class="w-full bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700"
                        >
                            Mostrar Toast de Feriado
                        </button>
                        
                        <button
                            @click="showSampleFacultative"
                            class="w-full bg-yellow-600 text-white px-4 py-2 rounded-md hover:bg-yellow-700"
                        >
                            Mostrar Toast de Ponto Facultativo
                        </button>
                        
                        <button
                            @click="clearAllToasts"
                            class="w-full bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700"
                        >
                            Limpar Todos os Toasts
                        </button>
                    </div>
                    
                    <div class="mt-4 p-3 bg-gray-50 rounded-md">
                        <h4 class="font-medium text-gray-900">Status:</h4>
                        <p class="text-sm text-gray-600 mt-1">
                            Toasts ativos: {{ activeToastsCount }}
                        </p>
                    </div>
                </div>

                <!-- 4. Histórico de Verificações -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">
                        📋 Histórico de Verificações
                    </h2>
                    
                    <div v-if="verificationHistory.length === 0" class="text-center py-8 text-gray-500">
                        Nenhuma verificação realizada ainda
                    </div>
                    
                    <div v-else class="space-y-3">
                        <div
                            v-for="(item, index) in verificationHistory"
                            :key="index"
                            class="p-3 rounded-md border"
                            :class="item.is_holiday ? 'border-red-200 bg-red-50' : 'border-green-200 bg-green-50'"
                        >
                            <div class="flex items-center justify-between">
                                <div>
                                    <span class="font-medium">{{ item.date }}</span>
                                    <span v-if="item.holiday" class="ml-2 text-sm">
                                        - {{ item.holiday.name }}
                                    </span>
                                </div>
                                <span
                                    class="px-2 py-1 text-xs rounded-full"
                                    :class="item.is_holiday ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800'"
                                >
                                    {{ item.is_holiday ? 'Feriado' : 'Dia Útil' }}
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <button
                        v-if="verificationHistory.length > 0"
                        @click="clearHistory"
                        class="w-full mt-4 bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700"
                    >
                        Limpar Histórico
                    </button>
                </div>
            </div>

            <!-- Informações da API -->
            <div class="mt-8 bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">
                    🔧 Informações da API
                </h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h4 class="font-medium text-gray-900 mb-2">Status da API:</h4>
                        <div class="flex items-center space-x-2">
                            <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                            <span class="text-sm text-gray-600">API funcionando</span>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">
                            Endpoint: /api/holidays/check
                        </p>
                    </div>
                    
                    <div>
                        <h4 class="font-medium text-gray-900 mb-2">Cache:</h4>
                        <div class="flex items-center space-x-2">
                            <div class="w-3 h-3 bg-blue-500 rounded-full"></div>
                            <span class="text-sm text-gray-600">Redis ativo</span>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">
                            Duração: 24 horas
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gerenciador de Toasts (Global) -->
        <HolidayToastManager ref="toastManager" />
    </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import HolidayDateInput from '@/Components/HolidayDateInput.vue';
import HolidayToastManager from '@/Components/HolidayToastManager.vue';
import { useHolidayNotifications } from '@/Components/useHolidayNotifications';

// Estado local
const selectedDate = ref('');
const manualDate = ref('');
const selectedState = ref('SP');
const lastEvent = ref(null);
const verificationHistory = ref([]);
const toastManager = ref();

// Composable
const {
    isChecking,
    currentHoliday,
    error,
    checkHoliday,
    formatDate
} = useHolidayNotifications();

// Computed
const manualResultClasses = computed(() => {
    if (!manualResult.value) return '';
    
    return manualResult.value.is_holiday
        ? 'border-red-400 bg-red-50'
        : 'border-green-400 bg-green-50';
});

const manualResultTitleClasses = computed(() => {
    if (!manualResult.value) return '';
    
    return manualResult.value.is_holiday
        ? 'text-red-800'
        : 'text-green-800';
});

const activeToastsCount = computed(() => {
    return toastManager.value?.activeToasts?.length || 0;
});

// Estado para resultado manual
const manualResult = ref(null);

// Methods
const onDateValidated = (result) => {
    lastEvent.value = {
        type: 'date-validated',
        timestamp: new Date().toISOString(),
        result
    };
    
    // Adicionar ao histórico
    verificationHistory.value.unshift({
        date: selectedDate.value,
        is_holiday: result.is_holiday,
        holiday: result.holiday,
        timestamp: new Date().toISOString()
    });
    
    // Limitar histórico
    if (verificationHistory.value.length > 10) {
        verificationHistory.value = verificationHistory.value.slice(0, 10);
    }
};

const onHolidayDetected = (holiday) => {
    lastEvent.value = {
        type: 'holiday-detected',
        timestamp: new Date().toISOString(),
        holiday
    };
};

const checkManualDate = async () => {
    if (!manualDate.value) return;
    
    try {
        const result = await checkHoliday(manualDate.value, selectedState.value);
        manualResult.value = result;
        
        // Adicionar ao histórico
        verificationHistory.value.unshift({
            date: manualDate.value,
            is_holiday: result.is_holiday,
            holiday: result.holiday,
            timestamp: new Date().toISOString()
        });
        
        // Limitar histórico
        if (verificationHistory.value.length > 10) {
            verificationHistory.value = verificationHistory.value.slice(0, 10);
        }
        
    } catch (err) {
        console.error('Erro na verificação manual:', err);
    }
};

const showSampleHoliday = () => {
    const sampleHoliday = {
        date: '2025-12-25',
        name: 'Natal',
        type: 'feriado',
        level: 'nacional'
    };
    
    if (window.$holidayToast) {
        window.$holidayToast.show(sampleHoliday, 8000);
    }
};

const showSampleFacultative = () => {
    const sampleFacultative = {
        date: '2025-12-24',
        name: 'Véspera de Natal',
        type: 'facultativo',
        level: 'nacional'
    };
    
    if (window.$holidayToast) {
        window.$holidayToast.show(sampleFacultative, 8000);
    }
};

const clearAllToasts = () => {
    if (window.$holidayToast) {
        window.$holidayToast.clear();
    }
};

const clearHistory = () => {
    verificationHistory.value = [];
};

// Lifecycle
onMounted(() => {
    // Definir data padrão para hoje
    const today = new Date();
    selectedDate.value = today.toISOString().split('T')[0];
    manualDate.value = today.toISOString().split('T')[0];
});
</script>

<style scoped>
/* Estilos específicos da página */
.min-h-screen {
    min-height: 100vh;
}

/* Animações suaves */
.transition-all {
    transition: all 0.2s ease-in-out;
}

/* Responsividade */
@media (max-width: 768px) {
    .grid {
        grid-template-columns: 1fr;
    }
}
</style>
