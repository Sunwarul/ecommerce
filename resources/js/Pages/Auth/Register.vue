<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import Button from 'primevue/button';
import InputText from 'primevue/inputtext';
import Password from 'primevue/password';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout :title="t('auth_pages.register_title')" :subtitle="t('auth_pages.register_subtitle')">
        <Head :title="t('auth.register')" />

        <form class="flex flex-col gap-4" @submit.prevent="submit" novalidate>
            <div class="flex flex-col gap-1.5">
                <label for="name" class="text-sm font-medium text-slate-700">{{ t('auth_pages.name') }}</label>
                <InputText id="name" v-model="form.name" autocomplete="name" autofocus required :invalid="!!form.errors.name" fluid />
                <small v-if="form.errors.name" class="text-red-600">{{ form.errors.name }}</small>
            </div>

            <div class="flex flex-col gap-1.5">
                <label for="email" class="text-sm font-medium text-slate-700">{{ t('auth.email') }}</label>
                <InputText id="email" v-model="form.email" type="email" autocomplete="username" required :invalid="!!form.errors.email" fluid />
                <small v-if="form.errors.email" class="text-red-600">{{ form.errors.email }}</small>
            </div>

            <div class="flex flex-col gap-1.5">
                <label for="password" class="text-sm font-medium text-slate-700">{{ t('auth.password') }}</label>
                <Password inputId="password" v-model="form.password" toggleMask autocomplete="new-password" required
                    :invalid="!!form.errors.password" :promptLabel="t('auth_pages.password_prompt')" :weakLabel="t('auth_pages.weak')"
                    :mediumLabel="t('auth_pages.medium')" :strongLabel="t('auth_pages.strong')" fluid />
                <small v-if="form.errors.password" class="text-red-600">{{ form.errors.password }}</small>
            </div>

            <div class="flex flex-col gap-1.5">
                <label for="password_confirmation" class="text-sm font-medium text-slate-700">{{ t('auth.confirm_password') }}</label>
                <Password inputId="password_confirmation" v-model="form.password_confirmation" :feedback="false" toggleMask
                    autocomplete="new-password" required :invalid="!!form.errors.password_confirmation" fluid />
                <small v-if="form.errors.password_confirmation" class="text-red-600">{{ form.errors.password_confirmation }}</small>
            </div>

            <Button type="submit" :label="t('auth.register')" icon="pi pi-user-plus" :loading="form.processing" fluid />
        </form>

        <template #footer>
            <p class="mt-6 text-center text-sm text-slate-600">
                {{ t('auth.already_registered') }}
                <Link :href="route('login')" class="font-semibold text-emerald-700 hover:underline">{{ t('auth.login') }}</Link>
            </p>
        </template>
    </GuestLayout>
</template>
