<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { router } from "@inertiajs/vue3";
import Button from "primevue/button";
import { computed, onMounted } from "vue";

import InvoiceA4 from "@/Components/POS/InvoiceA4.vue";
import InvoiceThermal from "@/Components/POS/InvoiceThermal.vue";

const props = defineProps({
    order: { type: Object, required: true },
    shop: {
        type: Object,
        default: () => ({ name: "Your Shop", address: "", phone: "" }),
    },

    // "a4" | "thermal"
    mode: { type: String, default: "a4" },

    // 58 | 80 (thermal width)
    thermalWidth: { type: Number, default: 80 },
});

const isThermal = computed(() => props.mode === "thermal");
const mode = props.mode;
function printInvoice() {
    // print thermal design only (even if user is viewing A4)
    // Option A: switch to thermal in same page for printing
    const url =
        route("pos.orders.invoice", props.order.id) +
        `?mode=${mode}&autoprint=1`;
    window.open(url, "_blank");
}

function openThermalPreview() {
    router.visit(route("pos.orders.invoice", props.order.id), {
        data: { mode: "thermal" },
        replace: true,
        preserveScroll: true,
    });
}
function openA4Preview() {
    router.visit(route("pos.orders.invoice", props.order.id), {
        data: { mode: "a4" },
        replace: true,
        preserveScroll: true,
    });
}

onMounted(() => {
    const params = new URLSearchParams(window.location.search);
    if (params.get("autoprint") === "1") {
        setTimeout(() => window.print(), 300);
    }
});
</script>

<template>
    <AuthenticatedLayout>
        <div class="p-4 md:p-6">
            <!-- Top actions (hidden on print) -->
            <div class="flex flex-wrap gap-2 items-center justify-between mb-4 print:hidden">
                <div class="flex gap-2">
                    <Button label="Back" icon="pi pi-arrow-left" severity="secondary"
                        @click="router.visit(route('pos.orders.index'))" />
                    <Button label="Print Receipt" icon="pi pi-print" severity="success" @click="printInvoice" />
                </div>

                <div class="flex gap-2">
                    <Button :label="isThermal ? 'Thermal (active)' : 'Thermal'" icon="pi pi-receipt"
                        :severity="isThermal ? 'info' : 'secondary'" @click="openThermalPreview" />
                    <Button :label="!isThermal ? 'A4 (active)' : 'A4'" icon="pi pi-file"
                        :severity="!isThermal ? 'info' : 'secondary'" @click="openA4Preview" />
                </div>
            </div>

            <!-- Render selected layout -->
            <div id="print-area" v-if="!isThermal">
                <InvoiceA4 :order="order" :shop="shop" />
            </div>
            <div v-else id="print-area">
                <InvoiceThermal :order="order" :shop="shop" :width="thermalWidth" />
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style>
@media print {

    /* hide everything */
    body * {
        visibility: hidden !important;
    }

    /* show only invoice */
    #print-area,
    #print-area * {
        visibility: visible !important;
    }

    /* place invoice at top-left */
    #print-area {
        position: absolute !important;
        left: 0 !important;
        top: 0 !important;
        width: 100% !important;
    }
}
</style>
