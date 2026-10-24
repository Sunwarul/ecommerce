<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import Button from 'primevue/button';
import InputText from 'primevue/inputtext';
import Message from 'primevue/message';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

defineProps({
    status: { type: String },
});

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('password.email'));
};
</script>

<template>
    <GuestLayout :title="t('auth_pages.forgot_title')" :subtitle="t('auth_pages.forgot_subtitle')">
        <Head :title="t('auth.forgot_password')" />

        <Message v-if="status" severity="success" class="mb-4">{{ status }}</Message>

        <form class="flex flex-col gap-4" @submit.prevent="submit" novalidate>
            <div class="flex flex-col gap-1.5">
                <label for="email" class="text-sm font-medium text-slate-700">{{ t('auth.email') }}</label>
                <InputText id="email" v-model="form.email" type="email" autocomplete="username" autofocus required
                    :invalid="!!form.errors.email" fluid />
                <small v-if="form.errors.email" class="text-red-600">{{ form.errors.email }}</small>
            </div>

            <Button type="submit" :label="t('auth.send_reset_link')" icon="pi pi-envelope" :loading="form.processing" fluid />
        </form>

        <template #footer>
            <p class="mt-6 text-center text-sm">
                <Link :href="route('login')" class="font-semibold text-emerald-700 hover:underline">
                    <i class="pi pi-arrow-left text-xs me-1" />{{ t('auth_pages.back_to_login') }}
                </Link>
            </p>
        </template>
    </GuestLayout>
</template>
