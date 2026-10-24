<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import Button from 'primevue/button';
import Password from 'primevue/password';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

const form = useForm({
    password: '',
});

const submit = () => {
    form.post(route('password.confirm'), {
        onFinish: () => form.reset(),
    });
};
</script>

<template>
    <GuestLayout :title="t('auth_pages.confirm_title')" :subtitle="t('auth_pages.confirm_subtitle')">
        <Head :title="t('auth_pages.confirm_title')" />

        <form class="flex flex-col gap-4" @submit.prevent="submit" novalidate>
            <div class="flex flex-col gap-1.5">
                <label for="password" class="text-sm font-medium text-slate-700">{{ t('auth.password') }}</label>
                <Password inputId="password" v-model="form.password" :feedback="false" toggleMask autocomplete="current-password"
                    autofocus required :invalid="!!form.errors.password" fluid />
                <small v-if="form.errors.password" class="text-red-600">{{ form.errors.password }}</small>
            </div>

            <Button type="submit" :label="t('common.confirm')" icon="pi pi-lock" :loading="form.processing" fluid />
        </form>
    </GuestLayout>
</template>
