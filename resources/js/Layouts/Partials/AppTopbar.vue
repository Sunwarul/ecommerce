<script setup>
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import BranchSwitcher from "@/Components/BranchSwitcher.vue";
import LanguageSwitcher from "@/Components/LanguageSwitcher.vue";
import useAuth from "@/Composables/useAuth";
import { useLocale } from "@/Composables/useLocale";
import { LOCALE_NAMES } from "@/Stores/localeStore";
import { Link, router, usePage } from "@inertiajs/vue3";
import Button from "primevue/button";
import Menu from "primevue/menu";
import Popover from "primevue/popover";
import { computed, ref } from "vue";
import { useI18n } from "vue-i18n";
import AppConfigurator from "./AppConfigurator.vue";
import { useLayout } from "./LayoutComposable";

const { t } = useI18n();
const page = usePage();
const { toggleMenu, toggleDarkMode, isDarkTheme } = useLayout();
const { user, hasAnyPermission, hasRole } = useAuth();
const { changeLocale, currentLocale, supportedLocales } = useLocale();

const userMenu = ref();
const mobileMenu = ref();
const appearance = ref();

const can = (perm) => hasRole("Super Admin") || hasAnyPermission([perm]);
const isActive = (name) => route().current(name);
const initials = computed(() =>
    (user.value?.name || "?").split(/\s+/).map((w) => w[0]).slice(0, 2).join("").toUpperCase()
);

const logout = () => router.post(route("logout"));
const unread = computed(() => page.props.unread_contact_messages || 0);

const userItems = computed(() => [
    { label: user.value?.name, disabled: true, class: "font-semibold opacity-100" },
    { separator: true },
    ...(route().has("profile.edit")
        ? [{ label: t("menu.profile"), icon: "pi pi-user-edit", command: () => router.visit(route("profile.edit")) }]
        : []),
    ...(can("Setting Index") && route().has("settings.general")
        ? [{ label: t("menu.general_settings"), icon: "pi pi-cog", command: () => router.visit(route("settings.general")) }]
        : []),
    { label: t("auth.logout"), icon: "pi pi-sign-out", command: logout },
]);

// Phones: everything that does not fit goes into one overflow menu
const mobileItems = computed(() => {
    const branches = page.props.branches || [];
    const branchId = page.props.auth?.user?.branch_id;
    return [
        ...(can("Order Index") ? [{ label: t("menu.orders"), icon: "pi pi-receipt", command: () => router.visit(route("pos.orders.index")) }] : []),
        ...(branches.length
            ? [{
                label: t("layout.branch"),
                icon: "pi pi-building",
                items: branches.map((b) => ({
                    label: b.name,
                    icon: b.id === branchId ? "pi pi-check" : "pi pi-fw",
                    command: () => b.id !== branchId && router.post(route("branch.switch"), { branch_id: b.id }, { preserveScroll: true }),
                })),
            }]
            : []),
        {
            label: t("layout.language"),
            icon: "pi pi-globe",
            items: supportedLocales.value.map((loc) => ({
                label: LOCALE_NAMES[loc] || loc,
                icon: loc === currentLocale.value ? "pi pi-check" : "pi pi-fw",
                command: () => changeLocale(loc),
            })),
        },
        { label: isDarkTheme.value ? t("layout.light_mode") : t("layout.dark_mode"), icon: isDarkTheme.value ? "pi pi-sun" : "pi pi-moon", command: toggleDarkMode },
        { separator: true },
        ...userItems.value.slice(2),
    ];
});
</script>

<template>
    <div class="layout-topbar gap-2">
        <!-- Start: menu toggle + brand -->
        <div class="flex items-center gap-2 min-w-0">
            <button type="button" class="layout-menu-button layout-topbar-action" :aria-label="t('layout.toggle_menu')" @click="toggleMenu">
                <i class="pi pi-bars" />
            </button>
            <Link :href="route('dashboard')" class="layout-topbar-logo min-w-0" :aria-label="t('menu.dashboard')">
                <ApplicationLogo size="sm" class="max-w-[10rem] sm:max-w-[16rem]" />
            </Link>
        </div>

        <!-- End: actions -->
        <div class="ms-auto flex items-center gap-2">
            <!-- Primary shortcuts -->
            <Button v-if="can('POS Index')" :label="t('menu.pos')" icon="pi pi-desktop" size="small"
                :severity="isActive('pos.index') ? 'primary' : 'secondary'" :outlined="!isActive('pos.index')"
                class="!hidden sm:!inline-flex" @click="router.visit(route('pos.index'))" />
            <Button v-if="can('POS Index')" icon="pi pi-desktop" size="small" rounded :aria-label="t('menu.pos')"
                :severity="isActive('pos.index') ? 'primary' : 'secondary'" :text="!isActive('pos.index')"
                class="sm:!hidden" @click="router.visit(route('pos.index'))" />
            <Button v-if="can('Order Index')" :label="t('menu.orders')" icon="pi pi-receipt" size="small"
                :severity="isActive('pos.orders.index') ? 'primary' : 'secondary'" :outlined="!isActive('pos.orders.index')"
                class="!hidden lg:!inline-flex" @click="router.visit(route('pos.orders.index'))" />

            <!-- Help: the user manual is open to every signed-in user -->
            <Button v-if="route().has('docs.index')" :label="t('menu.user_manual')" icon="pi pi-book" size="small"
                :severity="isActive('docs.index') ? 'primary' : 'secondary'" :outlined="!isActive('docs.index')"
                class="!hidden md:!inline-flex" @click="router.visit(route('docs.index'))" />
            <Button v-if="route().has('docs.index')" icon="pi pi-book" size="small" rounded :aria-label="t('menu.user_manual')"
                severity="secondary" text class="md:!hidden" @click="router.visit(route('docs.index'))" />

            <!-- Website contact messages waiting to be read -->
            <Link v-if="can('Contact Message Index')" :href="route('contact-messages.index', { filter: unread ? 'unread' : undefined })"
                class="relative inline-flex" :aria-label="t('menu.contact_messages')" v-tooltip.bottom="t('menu.contact_messages')">
                <Button icon="pi pi-inbox" severity="secondary" text rounded size="small" tabindex="-1" />
                <span v-if="unread" class="absolute -top-0.5 -end-0.5 min-w-[1.1rem] h-[1.1rem] px-1 rounded-full bg-red-500 text-white text-[10px] font-bold flex items-center justify-center">
                    {{ unread > 99 ? "99+" : unread }}
                </span>
            </Link>

            <!-- Desktop only -->
            <div class="hidden lg:flex items-center gap-2">
                <BranchSwitcher />
                <LanguageSwitcher />
                <Button :icon="isDarkTheme ? 'pi pi-sun' : 'pi pi-moon'" severity="secondary" text rounded size="small"
                    :aria-label="isDarkTheme ? t('layout.light_mode') : t('layout.dark_mode')"
                    v-tooltip.bottom="isDarkTheme ? t('layout.light_mode') : t('layout.dark_mode')" @click="toggleDarkMode" />
                <Button icon="pi pi-palette" severity="secondary" text rounded size="small" :aria-label="t('layout.appearance')"
                    v-tooltip.bottom="t('layout.appearance')" @click="appearance.toggle($event)" />
                <Button severity="secondary" outlined size="small" aria-haspopup="true" @click="userMenu.toggle($event)">
                    <span class="avatar-initials w-6 h-6">{{ initials }}</span>
                    <span class="max-w-[9rem] truncate">{{ user?.name }}</span>
                    <i class="pi pi-angle-down text-xs" />
                </Button>
                <Menu ref="userMenu" :model="userItems" :popup="true" />
            </div>

            <!-- Phones / tablets -->
            <div class="flex lg:hidden items-center gap-1">
                <Button icon="pi pi-palette" severity="secondary" text rounded size="small" :aria-label="t('layout.appearance')"
                    @click="appearance.toggle($event)" />
                <Button severity="secondary" text rounded size="small" :aria-label="t('layout.more')" aria-haspopup="true"
                    @click="mobileMenu.toggle($event)">
                    <span class="avatar-initials w-7 h-7">{{ initials }}</span>
                </Button>
                <Menu ref="mobileMenu" :model="mobileItems" :popup="true" class="min-w-[14rem]" />
            </div>

            <Popover ref="appearance">
                <AppConfigurator />
            </Popover>
        </div>
    </div>
</template>
