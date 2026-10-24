<script setup>
import Button from "primevue/button";
import { cleanParams } from "@/Helpers/format";

const props = defineProps({
    routeName: { type: String, required: true },
    params: { type: Object, default: () => ({}) },
    formats: { type: Array, default: () => ["pdf", "csv", "excel"] },
    size: { type: String, default: "small" },
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
    <div class="flex gap-1">
        <Button v-for="f in formats" :key="f" :icon="meta[f].icon" :label="meta[f].label" :severity="meta[f].severity"
            outlined :size="size" @click="download(f)" v-tooltip.top="`Export ${meta[f].label}`" />
    </div>
</template>
