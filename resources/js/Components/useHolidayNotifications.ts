import { ref } from 'vue';

export interface Holiday {
    date: string;
    name: string;
    type?: string;
    level?: string;
}

export interface HolidayCheckResult {
    is_holiday: boolean;
    holiday: Holiday | null;
}

export function useHolidayNotifications() {
    const isChecking = ref(false);
    const currentHoliday = ref<Holiday | null>(null);
    const error = ref<string | null>(null);

    const checkHoliday = async (date: string, state = 'SP'): Promise<HolidayCheckResult> => {
        isChecking.value = true;
        error.value = null;

        try {
            const params = new URLSearchParams({ date, state });
            const response = await fetch(`/api/holidays/check?${params.toString()}`, {
                headers: {
                    Accept: 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
            });

            if (!response.ok) {
                throw new Error('Nao foi possivel verificar o feriado.');
            }

            const result = (await response.json()) as HolidayCheckResult;
            currentHoliday.value = result.holiday;

            if (result.holiday && window.$holidayToast) {
                window.$holidayToast.show(result.holiday);
            }

            return result;
        } catch (caught) {
            error.value = caught instanceof Error ? caught.message : 'Erro inesperado.';
            throw caught;
        } finally {
            isChecking.value = false;
        }
    };

    const formatDate = (date: string) => {
        const [year, month, day] = date.split('-');
        return `${day}/${month}/${year}`;
    };

    return {
        isChecking,
        currentHoliday,
        error,
        checkHoliday,
        formatDate,
    };
}
