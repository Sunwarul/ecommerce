<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import LanguageSwitcher from '@/Components/LanguageSwitcher.vue';
import { Link } from '@inertiajs/vue3';
import Button from 'primevue/button';
import Toast from 'primevue/toast';
import { useI18n } from 'vue-i18n';
import FavIcons from '@/Layouts/Partials/FavIcons.vue';
import { useLayout } from '@/Layouts/Partials/LayoutComposable';
import { useLocale } from '@/Composables/useLocale';

defineProps({
    title: { type: String, default: '' },
    subtitle: { type: String, default: '' },
});

const { t } = useI18n();
const { initLocale } = useLocale();
const { isDarkTheme, toggleDarkMode } = useLayout();

initLocale();
</script>

<template>
    <FavIcons />
    <div class="min-h-screen flex flex-col bg-slate-100 dark:bg-slate-950">
        <header class="flex items-center justify-between gap-2 px-4 sm:px-6 py-3">
            <Link href="/" class="min-w-0">
                <ApplicationLogo size="sm" />
            </Link>
            <div class="flex items-center gap-1">
                <LanguageSwitcher compact />
                <Button :icon="isDarkTheme ? 'pi pi-sun' : 'pi pi-moon'" severity="secondary" text rounded size="small"
                    :aria-label="isDarkTheme ? t('layout.light_mode') : t('layout.dark_mode')" @click="toggleDarkMode" />
            </div>
        </header>

        <main class="flex-1 flex items-center justify-center px-4 py-6">
            <div class="w-full max-w-md">
                <div v-if="title" class="mb-6 text-center">
                    <h1 class="text-2xl font-bold text-slate-900">{{ title }}</h1>
                    <p v-if="subtitle" class="mt-1 text-sm text-slate-500">{{ subtitle }}</p>
                </div>
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 sm:p-8">
                    <slot />
                </div>
                <slot name="footer" />
            </div>
        </main>
    </div>
    <Toast />
</template>
