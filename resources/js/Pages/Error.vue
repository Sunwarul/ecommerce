<script setup>
import { Head, Link, usePage } from "@inertiajs/vue3";
import { computed } from "vue";
import { useI18n } from "vue-i18n";
import Button from "primevue/button";

const props = defineProps({
    status: { type: Number, required: true },
    message: { type: String, default: null },
});

const { t } = useI18n();
const page = usePage();

const icon = computed(() => ({
    403: "pi pi-lock",
    404: "pi pi-compass",
    500: "pi pi-exclamation-triangle",
    503: "pi pi-wrench",
}[props.status] || "pi pi-info-circle"));

const signedIn = computed(() => !!page.props.auth?.user);
const goBack = () => window.history.back();
</script>

<template>
    <Head :title="t(`errors.${status}.title`)" />
    <main class="min-h-screen flex items-center justify-center p-6 bg-slate-50 dark:bg-surface-950">
        <div class="max-w-md w-full text-center bg-white rounded-2xl border border-slate-200 p-8">
            <div class="mx-auto mb-4 w-14 h-14 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center">
                <i :class="icon" class="!text-2xl" />
            </div>
            <p class="text-sm font-semibold text-emerald-600 ltr-nums">{{ status }}</p>
            <h1 class="mt-1 text-2xl font-bold text-slate-800">{{ t(`errors.${status}.title`) }}</h1>
            <p class="mt-2 text-slate-600">{{ message || t(`errors.${status}.description`) }}</p>
            <div class="mt-6 flex flex-wrap gap-2 justify-center">
                <Button :label="t('errors.go_back')" icon="pi pi-arrow-left" severity="secondary" outlined @click="goBack" />
                <Link :href="signedIn ? route('dashboard') : route('login')">
                    <Button :label="signedIn ? t('errors.dashboard') : t('errors.sign_in')" icon="pi pi-home" />
                </Link>
            </div>
        </div>
    </main>
</template>
