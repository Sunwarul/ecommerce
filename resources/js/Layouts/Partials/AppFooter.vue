<script setup>
import { usePage } from "@inertiajs/vue3";
import { computed } from "vue";
import { useI18n } from "vue-i18n";

const { t } = useI18n();
const page = usePage();
const name = computed(() => page.props.configs?.shop_name || page.props.configs?.app_name || "");
const year = new Date().getFullYear();
</script>

<template>
    <footer class="layout-footer flex flex-wrap items-center justify-center gap-x-2 gap-y-1 text-xs text-slate-500 py-4">
        <span>© {{ year }} {{ name }}.</span>
        <span>{{ t("layout.rights") }}</span>
        <span class="hidden sm:inline">·</span>
        <span>{{ t("layout.developed_by") }} <span class="font-semibold text-slate-700">Sunwarul Islam &amp; Team</span></span>
    </footer>
</template>
