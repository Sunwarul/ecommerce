<script setup>
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import LanguageSwitcher from "@/Components/LanguageSwitcher.vue";
import { useLocale } from "@/Composables/useLocale";
import FavIcons from "@/Layouts/Partials/FavIcons.vue";
import { useLayout } from "@/Layouts/Partials/LayoutComposable";
import { Head, Link, usePage } from "@inertiajs/vue3";
import Button from "primevue/button";
import Toast from "primevue/toast";
import { computed, ref } from "vue";
import { useI18n } from "vue-i18n";

const props = defineProps({
    site: { type: Object, default: () => ({}) },
    title: { type: String, default: "" },
    description: { type: String, default: "" },
    image: { type: String, default: "" },
});

const { t } = useI18n();
const page = usePage();
const { initLocale } = useLocale();
const { isDarkTheme, toggleDarkMode } = useLayout();
initLocale();

const mobileOpen = ref(false);
const loggedIn = computed(() => !!page.props.auth?.user);
const shopName = computed(() => page.props.configs?.shop_name || page.props.configs?.app_name || "");
const metaDescription = computed(() => props.description || props.site.meta_description || props.site.hero_subtitle || t("website.home.lead"));

const nav = computed(() => [
    { label: t("website.nav.home"), href: route("welcome"), active: route().current("welcome") },
    ...(props.site.show_blog ? [{ label: t("website.nav.blog"), href: route("website.blog"), active: route().current("website.blog") || route().current("website.post") }] : []),
    ...(props.site.show_faq ? [{ label: t("website.nav.faq"), href: route("website.faq"), active: route().current("website.faq") }] : []),
    ...(props.site.show_contact ? [{ label: t("website.nav.contact"), href: route("website.contact"), active: route().current("website.contact") }] : []),
]);

const SOCIAL_ICONS = { facebook: "pi-facebook", instagram: "pi-instagram", youtube: "pi-youtube", linkedin: "pi-linkedin", tiktok: "pi-tiktok" };
const whatsappLink = computed(() => {
    const digits = String(props.site.whatsapp || "").replace(/[^\d]/g, "");
    return digits ? `https://wa.me/${digits.replace(/^0/, "880")}` : null;
});
const year = new Date().getFullYear();
</script>

<template>
    <Head :title="title || shopName">
        <meta name="description" :content="metaDescription" />
        <meta property="og:title" :content="title || shopName" />
        <meta property="og:description" :content="metaDescription" />
        <meta property="og:type" content="website" />
        <meta v-if="image || site.hero_image" property="og:image" :content="image || site.hero_image" />
    </Head>
    <FavIcons />

    <div class="min-h-screen flex flex-col bg-white dark:bg-slate-950 text-slate-700">
        <!-- Header -->
        <header class="sticky top-0 z-50 bg-white/90 dark:bg-slate-950/90 backdrop-blur border-b border-slate-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 h-16 flex items-center justify-between gap-3">
                <Link :href="route('welcome')" class="min-w-0"><ApplicationLogo size="sm" /></Link>

                <nav class="hidden md:flex items-center gap-1" :aria-label="t('website.nav.main')">
                    <Link v-for="item in nav" :key="item.href" :href="item.href"
                        class="px-3 py-2 rounded-lg text-sm font-medium transition-colors"
                        :class="item.active ? 'text-emerald-700 bg-emerald-50' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100'">
                        {{ item.label }}
                    </Link>
                </nav>

                <div class="flex items-center gap-1 sm:gap-2">
                    <LanguageSwitcher compact />
                    <Button :icon="isDarkTheme ? 'pi pi-sun' : 'pi pi-moon'" severity="secondary" text rounded size="small"
                        :aria-label="isDarkTheme ? t('layout.light_mode') : t('layout.dark_mode')" @click="toggleDarkMode" />
                    <Link :href="loggedIn ? route('dashboard') : route('login')" class="hidden sm:block">
                        <Button :label="loggedIn ? t('menu.dashboard') : t('auth.login')" :icon="loggedIn ? 'pi pi-th-large' : 'pi pi-sign-in'" size="small" />
                    </Link>
                    <Button class="md:!hidden" :icon="mobileOpen ? 'pi pi-times' : 'pi pi-bars'" severity="secondary" text rounded
                        :aria-label="t('website.nav.menu')" :aria-expanded="mobileOpen" @click="mobileOpen = !mobileOpen" />
                </div>
            </div>
            <!-- Mobile menu -->
            <nav v-if="mobileOpen" class="md:hidden border-t border-slate-200 px-4 py-3 flex flex-col gap-1 bg-white dark:bg-slate-950">
                <Link v-for="item in nav" :key="item.href" :href="item.href" class="px-3 py-2.5 rounded-lg font-medium"
                    :class="item.active ? 'text-emerald-700 bg-emerald-50' : 'text-slate-700 hover:bg-slate-100'" @click="mobileOpen = false">
                    {{ item.label }}
                </Link>
                <Link :href="loggedIn ? route('dashboard') : route('login')" class="px-3 py-2.5 rounded-lg font-medium text-emerald-700">
                    <i :class="loggedIn ? 'pi pi-th-large' : 'pi pi-sign-in'" class="me-2" />{{ loggedIn ? t('menu.dashboard') : t('auth.login') }}
                </Link>
            </nav>
        </header>

        <main class="flex-1">
            <slot />
        </main>

        <!-- Footer -->
        <footer class="bg-slate-900 text-slate-300 dark:bg-black">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 py-12 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                <div class="lg:col-span-2 space-y-3">
                    <div class="text-white text-lg font-semibold">{{ shopName }}</div>
                    <p class="text-sm text-slate-400 max-w-md">{{ site.footer_about || site.hero_subtitle || t('website.home.lead') }}</p>
                    <div v-if="Object.keys(site.socials || {}).length" class="flex gap-2 pt-1">
                        <a v-for="(url, key) in site.socials" :key="key" :href="url" target="_blank" rel="noopener nofollow"
                            class="w-9 h-9 rounded-full bg-slate-800 hover:bg-emerald-600 text-white flex items-center justify-center transition-colors" :aria-label="key">
                            <i :class="['pi', SOCIAL_ICONS[key]]" />
                        </a>
                    </div>
                </div>
                <div>
                    <div class="text-white font-semibold mb-3">{{ t('website.footer.links') }}</div>
                    <ul class="space-y-2 text-sm">
                        <li v-for="item in nav" :key="item.href"><Link :href="item.href" class="hover:text-white">{{ item.label }}</Link></li>
                        <li><Link :href="route('login')" class="hover:text-white">{{ t('website.footer.staff_login') }}</Link></li>
                    </ul>
                </div>
                <div>
                    <div class="text-white font-semibold mb-3">{{ t('website.footer.contact') }}</div>
                    <ul class="space-y-2 text-sm">
                        <li v-if="site.address" class="flex gap-2"><i class="pi pi-map-marker mt-0.5 text-emerald-400" /><span>{{ site.address }}</span></li>
                        <li v-if="site.contact_phone" class="flex gap-2"><i class="pi pi-phone mt-0.5 text-emerald-400" /><a :href="`tel:${site.contact_phone}`" class="hover:text-white" dir="ltr">{{ site.contact_phone }}</a></li>
                        <li v-if="site.contact_email" class="flex gap-2"><i class="pi pi-envelope mt-0.5 text-emerald-400" /><a :href="`mailto:${site.contact_email}`" class="hover:text-white break-all">{{ site.contact_email }}</a></li>
                        <li v-if="site.opening_hours" class="flex gap-2"><i class="pi pi-clock mt-0.5 text-emerald-400" /><span>{{ site.opening_hours }}</span></li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-slate-800">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 py-4 text-xs text-slate-500 flex flex-wrap justify-between gap-2">
                    <span>© {{ year }} {{ shopName }}. {{ t('website.footer.rights') }}</span>
                </div>
            </div>
        </footer>

        <!-- Floating WhatsApp chat -->
        <a v-if="whatsappLink" :href="whatsappLink" target="_blank" rel="noopener" :aria-label="t('website.contact.whatsapp')"
            class="fixed bottom-5 end-5 z-40 w-14 h-14 rounded-full bg-[#25D366] text-white shadow-lg flex items-center justify-center hover:scale-105 transition-transform">
            <i class="pi pi-whatsapp text-2xl" />
        </a>
    </div>
    <Toast />
</template>
