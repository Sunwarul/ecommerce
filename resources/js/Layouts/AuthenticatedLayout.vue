<script setup>
import { useLayout } from '@/Layouts/Partials/LayoutComposable';
import { useLocale } from '@/Composables/useLocale';
import { useFlashToast } from '@/Composables/useFlashToast';
import { router } from '@inertiajs/vue3';
import { computed, onBeforeUnmount, ref, watch } from 'vue';
import AppFooter from '@/Layouts/Partials/AppFooter.vue';
import AppSidebar from '@/Layouts/Partials/AppSidebar.vue';
import AppTopbar from '@/Layouts/Partials/AppTopbar.vue';
import FavIcons from '@/Layouts/Partials/FavIcons.vue';

const { layoutConfig, layoutState, isSidebarActive, closeMenus } = useLayout();
const { initLocale } = useLocale();

initLocale();

// Server-side errors (->with('error', ...)) were never shown anywhere; pages toast their own successes.
useFlashToast({ success: false, error: true });

const outsideClickListener = ref(null);

watch(isSidebarActive, (newVal) => {
    if (newVal) {
        bindOutsideClickListener();
    } else {
        unbindOutsideClickListener();
    }
});

const containerClass = computed(() => {
    return {
        'layout-overlay': layoutConfig.menuMode === 'overlay',
        'layout-static': layoutConfig.menuMode === 'static',
        'layout-static-inactive': layoutState.staticMenuDesktopInactive && layoutConfig.menuMode === 'static',
        'layout-overlay-active': layoutState.overlayMenuActive,
        'layout-mobile-active': layoutState.staticMenuMobileActive
    };
});

function bindOutsideClickListener() {
    if (!outsideClickListener.value) {
        outsideClickListener.value = (event) => {
            if (isOutsideClicked(event)) {
                layoutState.overlayMenuActive = false;
                layoutState.staticMenuMobileActive = false;
                layoutState.menuHoverActive = false;
            }
        };
        document.addEventListener('click', outsideClickListener.value);
    }
}

function unbindOutsideClickListener() {
    if (outsideClickListener.value) {
        // must remove the same function reference that was added
        document.removeEventListener('click', outsideClickListener.value);
        outsideClickListener.value = null;
    }
}

function isOutsideClicked(event) {
    const sidebarEl = document.querySelector('.layout-sidebar');
    const topbarEl = document.querySelector('.layout-menu-button');

    return !(sidebarEl?.contains(event.target) || topbarEl?.contains(event.target));
}

// Close the overlay / mobile sidebar after every page visit
const removeNavigateListener = router.on('navigate', () => closeMenus());

onBeforeUnmount(() => {
    unbindOutsideClickListener();
    removeNavigateListener();
});
</script>

<template>
    <FavIcons />
    <div class="layout-wrapper" :class="containerClass">
        <app-topbar></app-topbar>
        <app-sidebar></app-sidebar>
        <div class="layout-main-container">
            <div class="layout-main">
                <slot></slot>
            </div>
            <app-footer></app-footer>
        </div>
        <div class="layout-mask animate-fadein"></div>
    </div>
    <Toast />
</template>
