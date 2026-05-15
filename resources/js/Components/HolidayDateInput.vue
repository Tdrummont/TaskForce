<template>
    <div>
        <label v-if="label" class="mb-2 block text-sm font-medium text-gray-700">
            {{ label }}
            <span v-if="required" class="text-red-600">*</span>
        </label>

        <input
            type="date"
            :value="modelValue"
            :required="required"
            :placeholder="placeholder"
            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
            @input="onInput"
            @change="validateDate"
        />

        <p v-if="helpText" class="mt-2 text-sm text-gray-500">{{ helpText }}</p>
        <p v-if="isChecking" class="mt-2 text-sm text-blue-600">Verificando data...</p>
        <p v-if="error" class="mt-2 text-sm text-red-600">{{ error }}</p>

        <div
            v-if="currentHoliday"
            class="mt-3 rounded-md border border-yellow-300 bg-yellow-50 p-3 text-sm text-yellow-900"
        >
            Esta data e feriado: {{ currentHoliday.name }}
        </div>
    </div>
</template>

<script setup lang="ts">
import { useHolidayNotifications } from './useHolidayNotifications';
import type { Holiday, HolidayCheckResult } from './useHolidayNotifications';

const props = withDefaults(defineProps<{
    modelValue: string;
    label?: string;
    placeholder?: string;
    required?: boolean;
    state?: string;
    helpText?: string;
}>(), {
    label: '',
    placeholder: '',
    required: false,
    state: 'SP',
    helpText: '',
});

const emit = defineEmits<{
    'update:modelValue': [value: string];
    'date-validated': [result: HolidayCheckResult];
    'holiday-detected': [holiday: Holiday];
}>();

const { isChecking, currentHoliday, error, checkHoliday } = useHolidayNotifications();

const onInput = (event: Event) => {
    emit('update:modelValue', (event.target as HTMLInputElement).value);
};

const validateDate = async () => {
    if (!props.modelValue) {
        return;
    }

    const result = await checkHoliday(props.modelValue, props.state);
    emit('date-validated', result);

    if (result.holiday) {
        emit('holiday-detected', result.holiday);
    }
};
</script>
