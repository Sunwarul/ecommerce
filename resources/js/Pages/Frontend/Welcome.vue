<script setup>
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import LanguageSwitcher from "@/Components/LanguageSwitcher.vue";
import { useLocale } from "@/Composables/useLocale";
import FavIcons from "@/Layouts/Partials/FavIcons.vue";
import { useLayout } from "@/Layouts/Partials/LayoutComposable";
import { Head, Link, usePage } from "@inertiajs/vue3";
import Button from "primevue/button";
import Toast from "primevue/toast";
import { computed } from "vue";
import { useI18n } from "vue-i18n";

const props = defineProps({
    app_name: String,
    general_address: String,
});

const { t } = useI18n();
const page = usePage();
const { initLocale } = useLocale();
const { isDarkTheme, toggleDarkMode } = useLayout();
initLocale();

const name = computed(() => props.app_name || page.props.configs?.app_name || "");
const loggedIn = computed(() => !!page.props.auth?.user);

const features = computed(() => [
    { icon: "pi pi-desktop", title: t("welcome.f_pos"), text: t("welcome.f_pos_text") },
    { icon: "pi pi-calendar-clock", title: t("welcome.f_installments"), text: t("welcome.f_installments_text") },
    { icon: "pi pi-box", title: t("welcome.f_inventory"), text: t("welcome.f_inventory_text") },
    { icon: "pi pi-chart-line", title: t("welcome.f_reports"), text: t("welcome.f_reports_text") },
    { icon: "pi pi-comments", title: t("welcome.f_sms"), text: t("welcome.f_sms_text") },
    { icon: "pi pi-globe", title: t("welcome.f_languages"), text: t("welcome.f_languages_text") },
]);
</script>

<template>
    <Head :title="t('welcome.title')" />
    <FavIcons />
    <div class="min-h-screen bg-white dark:bg-slate-950">
        <header class="sticky top-0 z-50 bg-white/90 dark:bg-slate-950/90 backdrop-blur border-b border-slate-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 py-3 flex items-center justify-between gap-3">
                <ApplicationLogo size="sm" />
                <div class="flex items-center gap-1 sm:gap-2">
                    <LanguageSwitcher compact />
                    <Button :icon="isDarkTheme ? 'pi pi-sun' : 'pi pi-moon'" severity="secondary" text rounded size="small"
                        :aria-label="isDarkTheme ? t('layout.light_mode') : t('layout.dark_mode')" @click="toggleDarkMode" />
                    <Link :href="loggedIn ? route('dashboard') : route('login')">
                        <Button :label="loggedIn ? t('menu.dashboard') : t('auth.login')" :icon="loggedIn ? 'pi pi-th-large' : 'pi pi-sign-in'" size="small" />
                    </Link>
                </div>
            </div>
        </header>

        <section class="max-w-7xl mx-auto px-4 sm:px-6 py-14 sm:py-24 grid grid-cols-1 md:grid-cols-2 gap-10 md:gap-16 items-center">
            <div>
                <span class="inline-flex items-center gap-2 rounded-full bg-emerald-50 text-emerald-700 text-xs font-semibold px-3 py-1">
                    <i class="pi pi-bolt text-xs" /> {{ name }}
                </span>
                <h1 class="mt-4 text-3xl sm:text-5xl font-bold text-slate-900 leading-tight">
                    {{ t("welcome.headline") }}
                    <span class="text-emerald-600">{{ t("welcome.headline_accent") }}</span>
                </h1>
                <p class="mt-5 text-base sm:text-lg text-slate-600">{{ t("welcome.lead") }}</p>
                <div class="mt-8 flex flex-wrap gap-3">
                    <Link :href="loggedIn ? route('pos.index') : route('login')">
                        <Button :label="t('welcome.open_pos')" icon="pi pi-arrow-right" iconPos="right" size="large" />
                    </Link>
                    <a href="#features">
                        <Button :label="t('welcome.see_features')" severity="secondary" outlined size="large" />
                    </a>
                </div>
                <p v-if="general_address" class="mt-6 text-sm text-slate-500"><i class="pi pi-map-marker me-1" />{{ general_address }}</p>
            </div>

            <div class="relative">
                <div class="absolute -inset-4 bg-emerald-100 rounded-3xl blur-2xl opacity-60"></div>
                <img src="../../../assets/images/pos-banner.jpg" :alt="t('welcome.banner_alt')" class="relative rounded-2xl shadow-lg w-full" loading="lazy" />
            </div>
        </section>

        <section id="features" class="bg-slate-50 border-t border-slate-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 py-14 sm:py-20">
                <h2 class="text-2xl sm:text-3xl font-bold text-slate-900 text-center">{{ t("welcome.features_title") }}</h2>
                <div class="mt-10 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div v-for="f in features" :key="f.icon" class="bg-white rounded-xl border border-slate-200 p-5">
                        <span class="w-10 h-10 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
                            <i :class="f.icon" />
                        </span>
                        <h3 class="mt-3 font-semibold text-slate-800">{{ f.title }}</h3>
                        <p class="mt-1 text-sm text-slate-600">{{ f.text }}</p>
                    </div>
                </div>
            </div>
        </section>

        <footer class="py-6 text-center text-xs text-slate-500">© {{ new Date().getFullYear() }} {{ name }}</footer>
    </div>
    <Toast />
</template>
