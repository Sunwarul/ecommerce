<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import Button from 'primevue/button';
import Checkbox from 'primevue/checkbox';
import InputText from 'primevue/inputtext';
import Message from 'primevue/message';
import Password from 'primevue/password';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

const props = defineProps({
    canResetPassword: { type: Boolean },
    status: { type: String },
    isDebug: { type: Boolean, default: false },
});

const form = useForm({
    // demo credentials are only pre-filled when the server runs in debug mode
    email: props.isDebug ? 'admin@gmail.com' : '',
    password: props.isDebug ? 'password' : '',
    remember: props.isDebug,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <GuestLayout :title="t('auth_pages.login_title')" :subtitle="t('auth_pages.login_subtitle')">
        <Head :title="t('auth.login')" />

        <Message v-if="status" severity="success" class="mb-4">{{ status }}</Message>

        <form class="flex flex-col gap-4" @submit.prevent="submit" novalidate>
            <div class="flex flex-col gap-1.5">
                <label for="email" class="text-sm font-medium text-slate-700">{{ t('auth.email') }}</label>
                <InputText id="email" v-model="form.email" type="email" autocomplete="username" autofocus required
                    :invalid="!!form.errors.email" fluid />
                <small v-if="form.errors.email" class="text-red-600">{{ form.errors.email }}</small>
            </div>

            <div class="flex flex-col gap-1.5">
                <div class="flex items-center justify-between gap-2">
                    <label for="password" class="text-sm font-medium text-slate-700">{{ t('auth.password') }}</label>
                    <Link v-if="canResetPassword" :href="route('password.request')" class="text-xs font-medium text-emerald-700 hover:underline">
                        {{ t('auth.forgot_password') }}
                    </Link>
                </div>
                <Password inputId="password" v-model="form.password" :feedback="false" toggleMask autocomplete="current-password"
                    required :invalid="!!form.errors.password" fluid />
                <small v-if="form.errors.password" class="text-red-600">{{ form.errors.password }}</small>
            </div>

            <label class="flex items-center gap-2 text-sm text-slate-600 cursor-pointer select-none">
                <Checkbox v-model="form.remember" binary inputId="remember" />
                {{ t('auth.remember_me') }}
            </label>

            <Button type="submit" :label="t('auth.login')" icon="pi pi-sign-in" :loading="form.processing" fluid />
        </form>

        <template #footer>
            <p v-if="route().has('register')" class="mt-6 text-center text-sm text-slate-600">
                {{ t('auth_pages.no_account') }}
                <Link :href="route('register')" class="font-semibold text-emerald-700 hover:underline">{{ t('auth.create_account') }}</Link>
            </p>
        </template>
    </GuestLayout>
</template>
