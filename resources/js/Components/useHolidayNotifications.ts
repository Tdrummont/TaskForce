import { ref, computed } from 'vue';

interface Holiday {
    date: string;
    name: string;
    type: 'feriado' | 'facultativo';
    level: 'nacional' | 'estadual' | 'municipal';
    law?: string;
}

interface HolidayCheckResult {
    is_holiday: boolean;
    holiday: Holiday | null;
}

interface ToastOptions {
    duration?: number;
    showImmediately?: boolean;
}

export function useHolidayNotifications() {
    const isChecking = ref(false);
    const currentHoliday = ref<Holiday | null>(null);
    const error = ref<string | null>(null);
    const lastCheckedDate = ref<string | null>(null);

    // Computed properties
    const hasHoliday = computed(() => currentHoliday.value !== null);
    const isHolidayType = computed(() => currentHoliday.value?.type === 'feriado');
    const isFacultativeType = computed(() => currentHoliday.value?.type === 'facultativo');

    // Métodos principais
    const checkHoliday = async (date: string, state?: string): Promise<HolidayCheckResult> => {
        if (!date) {
            error.value = 'Data não fornecida';
            return { is_holiday: false, holiday: null };
        }

        isChecking.value = true;
        error.value = null;
        lastCheckedDate.value = date;

        try {
            const params = new URLSearchParams({ date });
            if (state) params.append('state', state);

            const response = await fetch(`/api/holidays/check?${params.toString()}`);
            
            if (!response.ok) {
                throw new Error(`Erro na API: ${response.status}`);
            }

            const data: HolidayCheckResult = await response.json();
            
            currentHoliday.value = data.holiday;
            
            return data;

        } catch (err) {
            const message = err instanceof Error ? err.message : 'Erro desconhecido';
            error.value = message;
            currentHoliday.value = null;
            return { is_holiday: false, holiday: null };
        } finally {
            isChecking.value = false;
        }
    };

    const showHolidayToast = (holiday: Holiday, options: ToastOptions = {}) => {
        const { duration = 8000, showImmediately = true } = options;
        
        if (showImmediately && window.$holidayToast) {
            return window.$holidayToast.show(holiday, duration);
        }
        
        return null;
    };

    const showHolidayWarning = (date: string, holidayName: string, type: 'feriado' | 'facultativo' = 'feriado') => {
        if (window.$holidayToast) {
            return window.$holidayToast.warning(date, holidayName, type);
        }
        
        return null;
    };

    const clearHolidayInfo = () => {
        currentHoliday.value = null;
        error.value = null;
        lastCheckedDate.value = null;
    };

    const validateDateWithNotification = async (date: string, state?: string) => {
        const result = await checkHoliday(date, state);
        
        if (result.is_holiday && result.holiday) {
            // Mostrar toast de aviso
            showHolidayToast(result.holiday, { duration: 6000 });
            
            // Emitir evento para o componente pai
            return {
                ...result,
                shouldWarn: true,
                warningMessage: `${date} é ${result.holiday.type === 'feriado' ? 'feriado' : 'ponto facultativo'}: ${result.holiday.name}`
            };
        }
        
        return {
            ...result,
            shouldWarn: false,
            warningMessage: null
        };
    };

    // Utilitários
    const formatDate = (dateString: string): string => {
        if (!dateString) return '';
        
        const date = new Date(dateString);
        return date.toLocaleDateString('pt-BR', {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
    };

    const getHolidayIcon = (type: string): string => {
        switch (type) {
            case 'feriado':
                return '🎉';
            case 'facultativo':
                return '⚠️';
            default:
                return '📅';
        }
    };

    const getHolidayColor = (type: string): string => {
        switch (type) {
            case 'feriado':
                return 'text-red-600';
            case 'facultativo':
                return 'text-yellow-600';
            default:
                return 'text-blue-600';
        }
    };

    const getHolidayBgColor = (type: string): string => {
        switch (type) {
            case 'feriado':
                return 'bg-red-50 border-red-200';
            case 'facultativo':
                return 'bg-yellow-50 border-yellow-200';
            default:
                return 'bg-blue-50 border-blue-200';
        }
    };

    return {
        // Estado
        isChecking,
        currentHoliday,
        error,
        lastCheckedDate,
        
        // Computed
        hasHoliday,
        isHolidayType,
        isFacultativeType,
        
        // Métodos principais
        checkHoliday,
        showHolidayToast,
        showHolidayWarning,
        clearHolidayInfo,
        validateDateWithNotification,
        
        // Utilitários
        formatDate,
        getHolidayIcon,
        getHolidayColor,
        getHolidayBgColor
    };
}

// Tipos para uso externo
export type { Holiday, HolidayCheckResult, ToastOptions };
