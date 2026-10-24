<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, router } from "@inertiajs/vue3";
import Button from "primevue/button";
import SelectButton from "primevue/selectbutton";
import { computed, onMounted } from "vue";
import { useI18n } from "vue-i18n";
import { useFlashToast } from "@/Composables/useFlashToast";

import InvoiceA4 from "@/Components/POS/InvoiceA4.vue";
import InvoiceThermal from "@/Components/POS/InvoiceThermal.vue";

const props = defineProps({
    order: { type: Object, required: true },
    shop: { type: Object, default: () => ({ name: "", address: "", phone: "" }) },
    mode: { type: String, default: "a4" }, // "a4" | "thermal"
    thermalWidth: { type: Number, default: 80 }, // 58 | 80
});

const { t } = useI18n();
useFlashToast();

const isThermal = computed(() => props.mode === "thermal");
const modeOptions = computed(() => [
    { label: t("invoice.mode_a4"), value: "a4", icon: "pi pi-file" },
    { label: t("invoice.mode_thermal"), value: "thermal", icon: "pi pi-receipt" },
]);

function switchMode(mode) {
    if (!mode || mode === props.mode) return;
    router.visit(route("pos.orders.invoice", props.order.id), {
        data: { mode },
        replace: true,
        preserveScroll: true,
    });
}

function printInvoice() {
    window.print();
}

onMounted(() => {
    if (new URLSearchParams(window.location.search).get("autoprint") === "1") {
        setTimeout(() => window.print(), 400);
    }
});
</script>

<template>
    <Head :title="`${t('shared.terms.invoice')} ${order.invoice_no || '#' + order.id}`" />

    <AuthenticatedLayout>
        <div class="p-2 sm:p-4">
            <!-- Actions (hidden on print) -->
            <div class="flex flex-wrap gap-2 items-center justify-between mb-4 print:hidden">
                <div class="flex flex-wrap gap-2">
                    <Button :label="t('shared.terms.back')" icon="pi pi-arrow-left" severity="secondary" outlined
                        @click="router.visit(route('pos.orders.index'))" />
                    <Button :label="t('invoice.new_sale')" icon="pi pi-plus" severity="secondary" outlined
                        @click="router.visit(route('pos.index'))" />
                    <Button :label="t('pos.print_receipt')" icon="pi pi-print" @click="printInvoice" />
                </div>
                <SelectButton :modelValue="mode" :options="modeOptions" optionLabel="label" optionValue="value" :allowEmpty="false"
                    @update:modelValue="switchMode">
                    <template #option="{ option }">
                        <i :class="option.icon" class="text-xs" /> <span>{{ option.label }}</span>
                    </template>
                </SelectButton>
            </div>

            <div id="print-area" class="bg-slate-100 rounded-xl p-2 sm:p-4 print:p-0 print:bg-white">
                <InvoiceThermal v-if="isThermal" :order="order" :shop="shop" :width="thermalWidth" />
                <InvoiceA4 v-else :order="order" :shop="shop" />
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style>
@media print {
    /* hide everything except the invoice */
    body * {
        visibility: hidden !important;
    }
    #print-area,
    #print-area * {
        visibility: visible !important;
    }
    #print-area {
        position: absolute !important;
        inset-inline-start: 0 !important;
        top: 0 !important;
        width: 100% !important;
        background: #fff !important;
    }
}
</style>
