<template>
    <!-- Componente invisível que detecta feriados automaticamente -->
</template>

<script setup>
import { onMounted, ref } from 'vue';
import { useHolidayNotifications } from '@/Components/useHolidayNotifications';

const { checkHoliday, showHolidayToast } = useHolidayNotifications();
const hasCheckedToday = ref(false);

// Verificar se já verificou hoje (usando localStorage)
const getTodayKey = () => {
    const today = new Date().toISOString().split('T')[0];
    return `holiday_checked_${today}`;
};

const hasCheckedTodayStorage = () => {
    return localStorage.getItem(getTodayKey()) === 'true';
};

const markAsCheckedToday = () => {
    localStorage.setItem(getTodayKey(), 'true');
    hasCheckedToday.value = true;
};

// Verificar feriados automaticamente
const checkTodayForHolidays = async () => {
    if (hasCheckedTodayStorage()) {
        hasCheckedToday.value = true;
        return;
    }

    try {
        const today = new Date().toISOString().split('T')[0];
        const result = await checkHoliday(today, 'PA'); // Pará como padrão
        
        if (result.is_holiday && result.holiday) {
            // Mostrar snackbar de feriado
            showHolidayToast(result.holiday, { 
                duration: 10000, // 10 segundos para feriados
                showImmediately: true 
            });
        }
        
        // Marcar como verificado hoje
        markAsCheckedToday();
        
    } catch (error) {
        console.warn('Erro ao verificar feriados:', error);
    }
};

// Verificar feriados quando o componente é montado
onMounted(() => {
    // Aguardar um pouco para não interferir com o carregamento da página
    setTimeout(() => {
        checkTodayForHolidays();
    }, 2000);
});
</script>
