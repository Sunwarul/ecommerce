<script setup>
import { useI18n } from "vue-i18n";
import AppMenu from "./AppMenu.vue";

const { t } = useI18n();
</script>

<template>
    <aside class="layout-sidebar card !p-2 sm:!px-4" :aria-label="t('layout.main_navigation')">
        <nav>
            <AppMenu />
        </nav>
    </aside>
</template>
