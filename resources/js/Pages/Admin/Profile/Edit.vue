<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import { Head, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import { useI18n } from 'vue-i18n';

defineProps({
    mustVerifyEmail: { type: Boolean },
    status: { type: String },
});

const { t } = useI18n();
const user = computed(() => usePage().props.auth.user);
const initials = computed(() => String(user.value?.name || '?').trim().split(/\s+/).slice(0, 2).map((p) => p[0]).join('').toUpperCase());
</script>

<template>
    <Head :title="t('profile.title')" />

    <AuthenticatedLayout>
        <div class="max-w-4xl mx-auto space-y-4">
            <div class="flex items-center gap-4">
                <span class="w-14 h-14 rounded-full bg-emerald-50 text-emerald-700 text-lg font-semibold flex items-center justify-center shrink-0">
                    {{ initials }}
                </span>
                <div class="min-w-0">
                    <h1 class="text-xl font-semibold text-slate-800 truncate">{{ user?.name }}</h1>
                    <p class="text-sm text-slate-500 truncate">{{ t('profile.subtitle') }}</p>
                </div>
            </div>

            <div class="card">
                <UpdateProfileInformationForm :must-verify-email="mustVerifyEmail" :status="status" />
            </div>

            <div class="card">
                <UpdatePasswordForm />
            </div>

            <div class="card border border-red-200">
                <DeleteUserForm />
            </div>
        </div>
    </AuthenticatedLayout>
</template>
