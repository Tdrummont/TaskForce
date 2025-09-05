import { ref, watch, onMounted } from 'vue';

export function useDarkMode() {
    const isDark = ref(false);

    // Função para alternar o modo escuro
    const toggleDarkMode = () => {
        console.log('Toggle dark mode clicked, current state:', isDark.value);
        isDark.value = !isDark.value;
        console.log('New state:', isDark.value);
        updateTheme();
    };

    // Função para aplicar o tema
    const updateTheme = () => {
        if (isDark.value) {
            document.documentElement.classList.add('dark');
            localStorage.setItem('darkMode', 'true');
        } else {
            document.documentElement.classList.remove('dark');
            localStorage.setItem('darkMode', 'false');
        }
    };

    // Função para inicializar o tema
    const initTheme = () => {
        const savedTheme = localStorage.getItem('darkMode');
        const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        
        if (savedTheme !== null) {
            isDark.value = savedTheme === 'true';
        } else {
            isDark.value = prefersDark;
        }
        
        updateTheme();
    };

    // Watcher para mudanças no tema
    watch(isDark, () => {
        updateTheme();
    });

    // Inicializar quando o composable for montado
    onMounted(() => {
        initTheme();
    });

    return {
        isDark,
        toggleDarkMode,
        updateTheme,
        initTheme
    };
}