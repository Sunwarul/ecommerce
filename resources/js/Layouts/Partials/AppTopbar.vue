<script setup>
import { useLayout } from './LayoutComposable';
import AppConfigurator from './AppConfigurator.vue';
import { Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import AppLogo from './AppLogo.vue';
import ThemeSwitcher from '@/Components/ThemeSwitcher.vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';

const { toggleMenu, toggleDarkMode, isDarkTheme } = useLayout();

const items = ref([
    {
        label: 'Profile',
        items: [
            {
                label: 'Settings',
                icon: 'pi pi-cog',
                shortcut: '⌘+O'
            },
            {
                label: 'Messages',
                icon: 'pi pi-inbox',
                badge: 2
            },
            {
                label: 'Logout',
                icon: 'pi pi-sign-out',
                command: () => {
                    router.post(route('logout'), {}, {
                        onSuccess: () => {
                            window.location = '/'
                        }
                    })
                }
            }
        ]
    },
    {
        separator: true
    }
]);

const menu = ref(null);

const toggle = (event) => {
    menu.value.toggle(event);
};

</script>

<template>
    <div class="layout-topbar">
        <div class="layout-topbar-logo-container">
            <button class="layout-menu-button layout-topbar-action" @click="toggleMenu">
                <i class="pi pi-bars"></i>
            </button>
            <Link href="/" class="layout-topbar-logo">
            <span>
                <ApplicationLogo></ApplicationLogo>
            </span>
            </Link>
        </div>

        <div class="layout-topbar-actions">
            <!-- <ThemeSwitcher /> -->
            <div class="layout-topbar-menu hidden lg:block">
                <div class="layout-topbar-menu-content">
                    <!-- <button type="button" class="layout-topbar-action">
                        <i class="pi pi-calendar"></i>
                        <span>Calendar</span>
                    </button> -->
                    <button type="button" class="layout-topbar-action">
                        <i class="pi pi-inbox"></i>
                        <span>Messages</span>
                    </button>
                    <Button type="button" label="Profile" icon="pi pi-user" class="layout-topbar-action1"
                        variant="outlined" @click="toggle" :aria-haspopup="true" aria-controls="overlay_menu">
                    </Button>
                    <Menu :model="items" class="w-full md:w-60" id="overlay_menu" :popup="true" ref="menu">

                        <template #submenulabel="{ item }">
                            <span class="text-primary font-bold">{{ item.label }}</span>
                        </template>
                        <template #item="{ item, props }">
                            <a v-ripple class="flex items-center" v-bind="props.action">
                                <span :class="item.icon" />
                                <span>{{ item.label }}</span>
                                <Badge v-if="item.badge" class="ml-auto" :value="item.badge" />
                                <span v-if="item.shortcut"
                                    class="ml-auto border border-surface rounded bg-emphasis text-muted-color text-xs p-1">{{
                                        item.shortcut }}</span>
                            </a>
                        </template>
                        <template #end>
                            <button v-ripple
                                class="relative overflow-hidden w-full border-0 bg-transparent flex items-start p-2 pl-4 hover:bg-surface-100 dark:hover:bg-surface-800 rounded-none cursor-pointer transition-colors duration-200">
                                <Avatar image="https://primefaces.org/cdn/primevue/images/avatar/amyelsner.png"
                                    class="mr-2" shape="circle" />
                                <span class="inline-flex flex-col items-start">
                                    <span class="font-bold">Amy Elsner</span>
                                    <span class="text-sm">Admin</span>
                                </span>
                            </button>
                        </template>
                    </Menu>
                </div>
            </div>
        </div>
    </div>
</template>
