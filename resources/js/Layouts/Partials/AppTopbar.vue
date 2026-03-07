<script setup>
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import BranchSwitcher from "@/Components/BranchSwitcher.vue";
import LanguageSwitcher from "@/Components/LanguageSwitcher.vue";
import useAuth from "@/Composables/useAuth";
import { Link, router } from "@inertiajs/vue3";
import { computed } from "vue";
import { useLayout } from "./LayoutComposable";
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

const { toggleMenu } = useLayout();
const { user, roles, permissions, can } = useAuth();

const current = computed(() => route().current());

const isActive = (name) => current.value.startsWith(name);
</script>

<template>
    <div class="layout-topbar flex items-center justify-between px-4 bg-white border-b shadow-sm">
        <!-- LEFT SIDE: Menu Toggle + Logo -->
        <div class="flex items-center gap-3">
            <button class="layout-menu-button layout-topbar-action" @click="toggleMenu">
                <i class="pi pi-bars text-xl"></i>
            </button>

            <Link href="/dashboard" class="layout-topbar-logo flex items-center gap-2">
                <ApplicationLogo />
            </Link>
        </div>

        <!-- RIGHT SIDE: ACTIONS -->
        <div class="flex items-center gap-4">
            <LanguageSwitcher />
            <BranchSwitcher />
            <!-- POS BUTTON (TOPBAR BUTTON — NOT DROPDOWN) -->
            <button @click="router.visit(route('pos.index'))"
                class="flex items-center gap-2 px-3 py-2 rounded-lg transition" :class="isActive('pos.index')
                    ? 'bg-indigo-600 text-white shadow-sm'
                    : 'hover:bg-indigo-50 text-slate-700'
                    ">
                <i class="pi pi-desktop text-lg"></i>
                <span class="text-sm font-medium">{{ t('menu.pos') }}</span>
            </button>

            <!-- ORDERS BUTTON -->
            <button @click="router.visit(route('pos.orders.index'))"
                class="flex items-center gap-2 px-3 py-2 rounded-lg transition" :class="isActive('pos.orders.index')
                    ? 'bg-emerald-600 text-white shadow-sm'
                    : 'hover:bg-emerald-50 text-slate-700'
                    ">
                <i class="pi pi-receipt text-lg"></i>
                <span class="text-sm font-medium">{{ t('menu.orders') }}</span>
            </button>

            <!-- PROFILE DROPDOWN -->
            <Menu ref="menu" :popup="true" id="top_profile_menu" :model="[
                {
                    label: t('menu.settings'),
                    icon: 'pi pi-cog',
                    command: () => router.visit(route('profile.edit')),
                },
                {
                    label: t('auth.logout'),
                    icon: 'pi pi-sign-out',
                    command: () => router.post(route('logout')),
                },
            ]" />

            <Button type="button" :label="user.name" icon="pi pi-user" class="layout-topbar-action1" variant="outlined"
                @click="$refs.menu.toggle($event)" />
        </div>
    </div>
</template>
