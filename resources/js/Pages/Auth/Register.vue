<template>
    <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-[#0f172a] via-[#111827] to-[#1f2937] relative">
        <!-- Botão de idioma no topo -->
        <div class="absolute top-4 right-6">
            <AuthLangToggle />
        </div>

        <div class="w-full max-w-md bg-white/5 backdrop-blur-2xl rounded-2xl shadow-2xl ring-1 ring-white/10 p-6">
            <div class="text-center mb-6 flex flex-col items-center">
                <img src="/logoyggdra.png" alt="TaskForce" class="h-39 sm:h-44 md:h-56 w-auto mb-4 drop-shadow-[0_12px_40px_rgba(124,58,237,0.65)]" />
                <h1 class="text-2xl font-bold text-white">Registrar</h1>
            </div>
            <form @submit.prevent="submit" class="space-y-4">
                <!-- Nome -->
                <div>
                    <label class="block text-sm font-medium text-slate-200 mb-1">
                        Nome
                    </label>
                    <input
                        v-model="form.name"
                        type="text"
                        autocomplete="name"
                        class="w-full px-3 py-2 rounded-md bg-white/10 border border-white/10 text-white placeholder-slate-300 focus:ring-[#7c3aed] focus:border-[#7c3aed]"
                        required
                        autofocus
                    />
                    <ValidationError :error="form.errors.name" />
                </div>

                <!-- Email -->
                <div>
                    <label class="block text-sm font-medium text-slate-200 mb-1">
                        Email
                    </label>
                    <input
                        v-model="form.email"
                        type="email"
                        autocomplete="username"
                        class="w-full px-3 py-2 rounded-md bg-white/10 border border-white/10 text-white placeholder-slate-300 focus:ring-[#7c3aed] focus:border-[#7c3aed]"
                        required
                    />
                    <ValidationError :error="form.errors.email" />
                </div>

                <!-- Senha -->
                <div>
                    <label class="block text-sm font-medium text-slate-200 mb-1">
                        Senha
                    </label>
                    <input
                        v-model="form.password"
                        type="password"
                        autocomplete="new-password"
                        class="w-full px-3 py-2 rounded-md bg-white/10 border border-white/10 text-white placeholder-slate-300 focus:ring-[#7c3aed] focus:border-[#7c3aed]"
                        required
                    />
                    <ValidationError :error="form.errors.password" />
                </div>

                <!-- Confirmar Senha -->
                <div>
                    <label class="block text-sm font-medium text-slate-200 mb-1">
                        Confirmar Senha
                    </label>
                    <input
                        v-model="form.password_confirmation"
                        type="password"
                        autocomplete="new-password"
                        class="w-full px-3 py-2 rounded-md bg-white/10 border border-white/10 text-white placeholder-slate-300 focus:ring-[#7c3aed] focus:border-[#7c3aed]"
                        required
                    />
                    <ValidationError :error="form.errors.password_confirmation" />
                </div>

                <!-- Submit -->
                <button
                    type="submit"
                    :disabled="form.processing"
                    class="w-full py-2 rounded-md text-white bg-gradient-to-r from-[#1d4ed8] via-[#7c3aed] to-[#9333ea] hover:from-[#2563eb] hover:via-[#8b5cf6] hover:to-[#a855f7] disabled:opacity-50"
                >
                    Registrar
                </button>
            </form>

            <!-- Login link -->
            <div class="text-center mt-4 text-sm text-slate-200">
                Já tem conta?
                <Link :href="routeL('login')" class="text-indigo-300 hover:underline">
                    Faça login aqui
                </Link>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Link, useForm } from '@inertiajs/vue3';
import { useLocale } from '@/Components/useLocale';
import AuthLangToggle from '@/Components/AuthLangToggle.vue';
import ValidationError from '@/Components/ValidationError.vue';

const { routeL } = useLocale();

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(routeL('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>
