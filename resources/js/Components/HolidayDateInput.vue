<template>
    <div class="holiday-date-input">
        <label v-if="label" :for="inputId" class="block text-sm font-medium text-gray-700 mb-2">
            {{ label }}
            <span v-if="required" class="text-red-500">*</span>
        </label>
        
        <div class="relative">
            <input
                :id="inputId"
                type="date"
                :value="modelValue"
                @input="onDateChange"
                @blur="onDateBlur"
                :class="[
                    'block w-full px-3 py-2 border rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors',
                    inputClasses
                ]"
                :disabled="disabled || isChecking"
                :placeholder="placeholder"
            />
            
            <!-- Loading indicator -->
            <div v-if="isChecking" class="absolute inset-y-0 right-0 flex items-center pr-3">
                <svg class="animate-spin h-4 w-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </div>
        </div>
        
        <!-- Holiday warning -->
        <div v-if="currentHoliday && showHolidayWarning" class="mt-2 p-3 rounded-md border-l-4" :class="holidayWarningClasses">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <span class="text-lg">{{ getHolidayIcon(currentHoliday.type) }}</span>
                </div>
                <div class="ml-3">
                    <h4 class="text-sm font-medium" :class="holidayTitleClasses">
                        {{ currentHoliday.type === 'feriado' ? 'Feriado Nacional' : 'Ponto Facultativo' }}
                    </h4>
                    <p class="text-sm mt-1" :class="holidayMessageClasses">
                        {{ currentHoliday.name }} - {{ formatDate(currentHoliday.date) }}
                    </p>
                    <div class="mt-2 flex items-center space-x-2">
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium" :class="holidayBadgeClasses">
                            {{ getTypeLabel(currentHoliday.type) }}
                        </span>
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                            {{ getLevelLabel(currentHoliday.level) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Error message -->
        <div v-if="error" class="mt-2 text-sm text-red-600">
            {{ error }}
        </div>
        
        <!-- Help text -->
        <p v-if="helpText" class="mt-2 text-sm text-gray-500">
            {{ helpText }}
        </p>
    </div>
</template>

<script setup lang="ts">
import { ref, computed, watch, nextTick } from 'vue';
import { useHolidayNotifications } from './useHolidayNotifications';

interface Props {
    modelValue?: string;
    label?: string;
    placeholder?: string;
    required?: boolean;
    disabled?: boolean;
    state?: string;
    showHolidayWarning?: boolean;
    helpText?: string;
    inputId?: string;
}

const props = withDefaults(defineProps<Props>(), {
    showHolidayWarning: true,
    required: false,
    disabled: false,
    inputId: 'holiday-date-input'
});

const emit = defineEmits<{
    'update:modelValue': [value: string];
    'date-validated': [result: any];
    'holiday-detected': [holiday: any];
}>();

// Composable de notificações
const {
    isChecking,
    currentHoliday,
    error,
    checkHoliday,
    formatDate,
    getHolidayIcon,
    getHolidayColor,
    getHolidayBgColor
} = useHolidayNotifications();

// Computed properties
const inputClasses = computed(() => {
    if (currentHoliday.value) {
        return currentHoliday.value.type === 'feriado' 
            ? 'border-red-300 bg-red-50' 
            : 'border-yellow-300 bg-yellow-50';
    }
    return 'border-gray-300 bg-white';
});

const holidayWarningClasses = computed(() => {
    if (!currentHoliday.value) return '';
    
    return currentHoliday.value.type === 'feriado'
        ? 'border-red-400 bg-red-50'
        : 'border-yellow-400 bg-yellow-50';
});

const holidayTitleClasses = computed(() => {
    if (!currentHoliday.value) return '';
    
    return currentHoliday.value.type === 'feriado'
        ? 'text-red-800'
        : 'text-yellow-800';
});

const holidayMessageClasses = computed(() => {
    if (!currentHoliday.value) return '';
    
    return currentHoliday.value.type === 'feriado'
        ? 'text-red-700'
        : 'text-yellow-700';
});

const holidayBadgeClasses = computed(() => {
    if (!currentHoliday.value) return '';
    
    return currentHoliday.value.type === 'feriado'
        ? 'bg-red-100 text-red-800'
        : 'bg-yellow-100 text-yellow-800';
});

// Methods
const onDateChange = (event: Event) => {
    const target = event.target as HTMLInputElement;
    const newValue = target.value;
    
    emit('update:modelValue', newValue);
    
    if (newValue) {
        validateDate(newValue);
    }
};

const onDateBlur = () => {
    if (props.modelValue) {
        validateDate(props.modelValue);
    }
};

const validateDate = async (date: string) => {
    if (!date) return;
    
    try {
        const result = await checkHoliday(date, props.state);
        
        if (result.is_holiday && result.holiday) {
            emit('holiday-detected', result.holiday);
        }
        
        emit('date-validated', result);
        
    } catch (err) {
        console.error('Erro na validação da data:', err);
    }
};

const getTypeLabel = (type: string): string => {
    const labels = {
        'feriado': 'Feriado',
        'facultativo': 'Ponto Facultativo'
    };
    return labels[type] || type;
};

const getLevelLabel = (level: string): string => {
    const labels = {
        'nacional': 'Nacional',
        'estadual': 'Estadual',
        'municipal': 'Municipal'
    };
    return labels[level] || level;
};

// Watch para mudanças externas no modelValue
watch(() => props.modelValue, (newValue) => {
    if (newValue && newValue !== currentHoliday.value?.date) {
        nextTick(() => {
            validateDate(newValue);
        });
    }
}, { immediate: true });
</script>

<style scoped>
.holiday-date-input {
    @apply w-full;
}

/* Animações suaves */
.transition-colors {
    transition-property: color, background-color, border-color, text-decoration-color, fill, stroke;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 150ms;
}

/* Estilos para o input quando é feriado */
input[type="date"]:focus.border-red-300 {
    @apply ring-red-500 border-red-500;
}

input[type="date"]:focus.border-yellow-300 {
    @apply ring-yellow-500 border-yellow-500;
}
</style>
