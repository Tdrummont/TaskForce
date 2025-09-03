<template>
    <EmailSnackbar
        v-if="showSnackbar"
        :show="showSnackbar"
        :title="snackbarData.title"
        :message="snackbarData.message"
        :type="snackbarData.type"
        :duration="5000"
        @close="closeSnackbar"
    />
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import { usePage } from '@inertiajs/vue3';
import EmailSnackbar from './EmailSnackbar.vue';

const page = usePage();

const showSnackbar = ref(false);
const snackbarData = ref({
    title: '',
    message: '',
    type: 'success'
});

// Computed para verificar se há mensagens flash
const hasEmailSent = computed(() => page.props.email_sent);
const hasEmailError = computed(() => page.props.email_error);

// Watch para mudanças nas mensagens flash
watch([hasEmailSent, hasEmailError], ([emailSent, emailError]) => {
    if (emailSent) {
        showSnackbar.value = true;
        snackbarData.value = {
            title: emailSent.title,
            message: emailSent.message,
            type: emailSent.type
        };
        
        // Limpar a mensagem flash após mostrar
        setTimeout(() => {
            page.props.email_sent = null;
        }, 100);
        
    } else if (emailError) {
        showSnackbar.value = true;
        snackbarData.value = {
            title: emailError.title,
            message: emailError.message,
            type: emailError.type
        };
        
        // Limpar a mensagem flash após mostrar
        setTimeout(() => {
            page.props.email_error = null;
        }, 100);
    }
}, { immediate: true });

const closeSnackbar = () => {
    showSnackbar.value = false;
};
</script>
