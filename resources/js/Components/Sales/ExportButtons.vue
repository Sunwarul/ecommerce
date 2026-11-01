<script setup>
import Button from "primevue/button";
import { useI18n } from "vue-i18n";
import { cleanParams } from "@/Helpers/format";
import useAuth from "@/Composables/useAuth";

const { t } = useI18n();
const { hasPermission } = useAuth();

const props = defineProps({
    routeName: { type: String, required: true },
    params: { type: Object, default: () => ({}) },
    formats: { type: Array, default: () => ["pdf", "csv", "excel"] },
    size: { type: String, default: "small" },
    permission: { type: String, default: null }, // hide entirely when the user lacks it
});

const meta = {
    pdf: { icon: "pi pi-file-pdf", label: "PDF", severity: "danger" },
    csv: { icon: "pi pi-file", label: "CSV", severity: "secondary" },
    excel: { icon: "pi pi-file-excel", label: "Excel", severity: "success" },
};

function download(format) {
    window.open(route(props.routeName, cleanParams({ ...props.params, export: format })), "_blank");
}
</script>

<template>
    <div v-if="!permission || hasPermission(permission)" class="flex gap-1 flex-wrap">
        <Button v-for="f in formats" :key="f" :icon="meta[f].icon" :label="meta[f].label" :severity="meta[f].severity"
            outlined :size="size" @click="download(f)" v-tooltip.top="`${t('shared.terms.export')} ${meta[f].label}`"
            :aria-label="`${t('shared.terms.export')} ${meta[f].label}`" />
    </div>
</template>
