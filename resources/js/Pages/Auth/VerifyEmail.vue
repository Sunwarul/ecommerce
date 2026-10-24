<script setup>
import { computed } from 'vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import Button from 'primevue/button';
import Message from 'primevue/message';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

const props = defineProps({
    status: { type: String },
});

const form = useForm({});

const submit = () => {
    form.post(route('verification.send'));
};

const verificationLinkSent = computed(() => props.status === 'verification-link-sent');
</script>

<template>
    <GuestLayout :title="t('auth.verify_email')">
        <Head :title="t('auth.verify_email')" />

        <p class="mb-4 text-sm text-slate-600">{{ t('auth_pages.verify_intro') }}</p>

        <Message v-if="verificationLinkSent" severity="success" class="mb-4">{{ t('auth.verification_link_sent') }}</Message>

        <form class="flex flex-col sm:flex-row gap-3 sm:items-center sm:justify-between" @submit.prevent="submit">
            <Button type="submit" :label="t('auth_pages.resend_verification')" icon="pi pi-envelope" :loading="form.processing" />
            <Link :href="route('logout')" method="post" as="button" class="text-sm font-medium text-slate-600 hover:underline">
                {{ t('auth.logout') }}
            </Link>
        </form>
    </GuestLayout>
</template>
