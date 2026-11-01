<script setup>
import { computed } from "vue";
import { useI18n } from "vue-i18n";
import { money, date } from "@/Helpers/format";
import { amountInWords } from "@/Helpers/amountInWords";
import { usePage } from "@inertiajs/vue3";

const props = defineProps({
    order: { type: Object, required: true },
    shop: { type: Object, default: () => ({}) },
});

const { t, locale } = useI18n();
const page = usePage();

const due = computed(() =>
    props.order?.status === "completed"
        ? Math.max(0, Number(props.order.total_amount || 0) - Number(props.order.paid_amount || 0))
        : 0
);

const hasCharges = (key) => Number(props.order?.[key] || 0) > 0;
const statusLabel = (s) => t(`shared.status.${s}`);

// Bangla wording ("... টাকা মাত্র") while the interface is in Bangla
const amountWords = computed(() => amountInWords(props.order?.total_amount, locale.value));
const logo = computed(() => page.props.configs?.logo);
</script>

<template>
    <div class="invoice-paper max-w-4xl mx-auto bg-white text-gray-800 p-4 sm:p-6 text-sm shadow-sm rounded-lg print:shadow-none print:rounded-none">
        <!-- Header -->
        <div class="relative border-b border-gray-300 pb-2 mb-3 min-h-[65px]">
            <div class="absolute top-3 start-2 hidden sm:block">
                <img src="../../../assets/images/brand.jpeg" alt="" class="h-[35px] object-contain" />
            </div>
            <div class="text-center px-0 sm:px-24">
                <h1 class="font-semibold uppercase tracking-wide text-xs text-gray-600">{{ t("invoice.sales_receipt") }}</h1>
                <p class="text-xl font-bold uppercase">{{ shop?.name ?? "" }}</p>
                <p class="text-xs">{{ shop?.address ?? "" }}</p>
                <p v-if="shop?.phone" class="text-xs ltr-nums">{{ shop.phone }}</p>
            </div>
            <div class="absolute top-0 end-2 hidden sm:block">
                <img v-if="logo" :src="logo" alt="" class="h-[58px] object-contain" />
            </div>
            <div v-if="order.status && order.status !== 'completed'"
                class="mx-auto mt-1 w-fit text-[10px] font-bold uppercase px-2 py-0.5 rounded border"
                :class="order.status === 'void' ? 'border-red-600 text-red-700' : 'border-sky-600 text-sky-700'">
                {{ statusLabel(order.status) }}
            </div>
        </div>

        <!-- Customer & invoice info -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 mb-3 text-[11px]">
            <div class="space-y-0.5">
                <p><span class="font-semibold">{{ t("invoice.customer_name") }}:</span> {{ order?.customer?.name ?? t("shared.terms.walk_in") }}</p>
                <p><span class="font-semibold">{{ t("shared.terms.phone") }}:</span> <span class="ltr-nums">{{ order?.customer?.phone ?? "" }}</span></p>
                <p><span class="font-semibold">{{ t("common.address") }}:</span> {{ order?.customer?.address ?? "" }}</p>
            </div>
            <div class="sm:text-end space-y-0.5">
                <p><span class="font-semibold">{{ t("shared.terms.date") }}:</span> <span class="ltr-nums">{{ order?.created_at }}</span></p>
                <p><span class="font-semibold">{{ t("invoice.invoice_no") }}:</span> <span class="ltr-nums">{{ order?.invoice_no ?? `#${order?.id}` }}</span></p>
                <p v-if="order?.user?.name"><span class="font-semibold">{{ t("shared.terms.cashier") }}:</span> {{ order.user.name }}</p>
                <p v-if="order?.branch?.name">{{ order.branch.name }}<span v-if="order.branch.code"> #{{ order.branch.code }}</span></p>
                <p v-if="order?.branch?.address">{{ order.branch.address }}</p>
            </div>
        </div>

        <!-- Items -->
        <div class="overflow-x-auto">
            <table class="w-full border border-gray-700 text-xs min-w-[560px]">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="border border-gray-400 px-2 py-1 text-start">{{ t("invoice.sku") }}</th>
                        <th class="border border-gray-400 px-2 py-1 text-start">{{ t("invoice.product") }}</th>
                        <th class="border border-gray-400 px-2 py-1 text-end">{{ t("invoice.unit_price") }}</th>
                        <th class="border border-gray-400 px-2 py-1 text-center">{{ t("invoice.qty") }}</th>
                        <th class="border border-gray-400 px-2 py-1 text-end">{{ t("pos.discount") }}</th>
                        <th class="border border-gray-400 px-2 py-1 text-end">{{ t("pos.tax") }}</th>
                        <th class="border border-gray-400 px-2 py-1 text-end">{{ t("shared.terms.total") }}</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="item in order?.items ?? []" :key="item.id">
                        <td class="border border-gray-300 px-2 py-1 text-[10px] ltr-nums">
                            {{ item?.product?.barcode ?? item?.variation?.sku ?? item?.sku ?? "" }}
                        </td>
                        <td class="border border-gray-300 px-2 py-1 text-[10px]">{{ item?.name ?? "" }}</td>
                        <td class="border border-gray-300 px-2 py-1 text-end tabular-nums">{{ money(item?.unit_price) }}</td>
                        <td class="border border-gray-300 px-2 py-1 text-center tabular-nums">{{ item?.quantity ?? 0 }}</td>
                        <td class="border border-gray-300 px-2 py-1 text-end tabular-nums">{{ money(item?.discount_amount) }}</td>
                        <td class="border border-gray-300 px-2 py-1 text-end tabular-nums">{{ money(item?.tax_amount) }}</td>
                        <td class="border border-gray-300 px-2 py-1 text-end font-semibold tabular-nums">{{ money(item?.line_total) }}</td>
                    </tr>
                    <tr v-if="!order?.items?.length">
                        <td colspan="7" class="text-center py-3">{{ t("invoice.no_items") }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Summary -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-3">
            <div class="text-xs sm:col-span-2 space-y-1">
                <p v-if="order?.warranty_info">
                    <span class="font-semibold">{{ t("pos_screen.warranty_info") }}:</span>
                    <span class="whitespace-pre-line">{{ order.warranty_info }}</span>
                </p>
                <p v-if="order?.notes"><span class="font-semibold">{{ t("shared.terms.notes") }}:</span> {{ order.notes }}</p>
                <p class="italic">
                    {{ t("invoice.amount_in_words") }}: <strong :dir="locale === 'bn' ? 'auto' : 'ltr'">{{ amountWords }}</strong>
                </p>
            </div>

            <div class="border border-gray-300 text-[11px] tabular-nums">
                <div class="flex justify-between border-b border-gray-300 px-2 py-0.5"><span>{{ t("orders.subtotal") }}</span><span>{{ money(order?.subtotal) }}</span></div>
                <div class="flex justify-between border-b border-gray-300 px-2 py-0.5"><span>{{ t("pos.discount") }}</span><span>{{ money(order?.discount_amount) }}</span></div>
                <div class="flex justify-between border-b border-gray-300 px-2 py-0.5"><span>{{ t("pos.tax") }}</span><span>{{ money(order?.tax_amount) }}</span></div>
                <div v-if="hasCharges('emi_charge_amount')" class="flex justify-between border-b border-gray-300 px-2 py-0.5">
                    <span>{{ t("pos_screen.emi_charge") }}</span><span>{{ money(order.emi_charge_amount) }}</span>
                </div>
                <div v-if="hasCharges('installment_charge_amount')" class="flex justify-between border-b border-gray-300 px-2 py-0.5">
                    <span>{{ t("invoice.installment_charge") }}</span><span>{{ money(order.installment_charge_amount) }}</span>
                </div>
                <div v-if="hasCharges('late_fee_amount')" class="flex justify-between border-b border-gray-300 px-2 py-0.5">
                    <span>{{ t("invoice.late_fee") }}</span><span>{{ money(order.late_fee_amount) }}</span>
                </div>
                <div class="flex justify-between border-b border-gray-300 px-2 py-0.5 font-bold"><span>{{ t("orders.grand_total") }}</span><span>{{ money(order?.total_amount) }}</span></div>
                <div class="flex justify-between px-2 py-0.5 font-bold" :class="due > 0 ? 'border-b border-gray-300' : ''">
                    <span>{{ t("shared.terms.paid") }}</span><span>{{ money(order?.paid_amount) }}</span>
                </div>
                <div v-if="due > 0" class="flex justify-between px-2 py-0.5 font-bold text-red-700">
                    <span>{{ t("shared.terms.due") }}</span><span>{{ money(due) }}</span>
                </div>
            </div>
        </div>

        <!-- Payments -->
        <div v-if="order?.payments?.length" class="mt-3 text-[10px]">
            <p class="font-semibold mb-1">{{ t("pos_screen.payments") }}</p>
            <table class="w-full border border-gray-300">
                <tbody>
                    <tr v-for="p in order.payments" :key="p.id">
                        <td class="border border-gray-300 px-1">{{ p.payment_method?.name }}</td>
                        <td class="border border-gray-300 px-1 ltr-nums">{{ p.transaction_ref || "" }}</td>
                        <td class="border border-gray-300 px-1 ltr-nums">{{ p.paid_at ? date(p.paid_at) : "" }}</td>
                        <td class="border border-gray-300 px-1 text-end tabular-nums">{{ money(p.amount) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Bank EMI -->
        <div v-if="order?.emi_transactions?.length" class="mt-3 text-[10px]">
            <p class="font-semibold mb-1">{{ t("shared.sale_type.emi") }}</p>
            <table class="w-full border border-gray-300">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="border border-gray-300 px-1 text-start">{{ t("invoice.bank") }}</th>
                        <th class="border border-gray-300 px-1">{{ t("invoice.tenure") }}</th>
                        <th class="border border-gray-300 px-1">{{ t("invoice.card") }}</th>
                        <th class="border border-gray-300 px-1">{{ t("invoice.approval") }}</th>
                        <th class="border border-gray-300 px-1 text-end">{{ t("invoice.card_amount") }}</th>
                        <th class="border border-gray-300 px-1 text-end">{{ t("invoice.monthly") }}</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="e in order.emi_transactions" :key="e.id">
                        <td class="border border-gray-300 px-1">{{ e.bank?.name }}</td>
                        <td class="border border-gray-300 px-1 text-center">{{ t("invoice.months", { n: e.months }) }}</td>
                        <td class="border border-gray-300 px-1 text-center ltr-nums">{{ e.card_last4 ? "**** " + e.card_last4 : "-" }}</td>
                        <td class="border border-gray-300 px-1 text-center ltr-nums">{{ e.approval_code || "-" }}</td>
                        <td class="border border-gray-300 px-1 text-end tabular-nums">{{ money(e.card_amount) }}</td>
                        <td class="border border-gray-300 px-1 text-end tabular-nums">{{ money(e.monthly_amount) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Installment schedule -->
        <div v-if="order?.installment_sale" class="mt-3 text-[10px]">
            <p class="font-semibold mb-1">
                {{ t("invoice.installment_agreement", {
                    no: order.installment_sale.agreement_no,
                    n: order.installment_sale.number_of_installments,
                    amount: money(order.installment_sale.installment_amount),
                }) }}
                — {{ t("shared.terms.down_payment") }} {{ money(order.installment_sale.down_payment) }},
                {{ t("invoice.financed") }} {{ money(order.installment_sale.financed_amount) }}
            </p>
            <table class="w-full border border-gray-300">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="border border-gray-300 px-1">#</th>
                        <th class="border border-gray-300 px-1">{{ t("invoice.due_date") }}</th>
                        <th class="border border-gray-300 px-1 text-end">{{ t("shared.terms.amount") }}</th>
                        <th class="border border-gray-300 px-1 text-end">{{ t("shared.terms.paid") }}</th>
                        <th class="border border-gray-300 px-1">{{ t("common.status") }}</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="s in order.installment_sale.schedules" :key="s.id">
                        <td class="border border-gray-300 px-1 text-center">{{ s.installment_no }}</td>
                        <td class="border border-gray-300 px-1 text-center ltr-nums">{{ date(s.due_date) }}</td>
                        <td class="border border-gray-300 px-1 text-end tabular-nums">{{ money(s.amount) }}</td>
                        <td class="border border-gray-300 px-1 text-end tabular-nums">{{ money(s.paid_amount) }}</td>
                        <td class="border border-gray-300 px-1 text-center">{{ statusLabel(s.status) }}</td>
                    </tr>
                </tbody>
            </table>
            <p v-if="Number(order.installment_sale.late_fee_amount) > 0" class="mt-1 italic">
                {{ t("invoice.late_fee_terms", {
                    fee: order.installment_sale.late_fee_type === "percent"
                        ? Number(order.installment_sale.late_fee_amount) + "%"
                        : money(order.installment_sale.late_fee_amount),
                    days: order.installment_sale.grace_days,
                }) }}
            </p>
            <div class="grid grid-cols-2 gap-8 mt-10">
                <div class="border-t border-gray-500 text-center pt-1">{{ t("invoice.customer_signature") }}</div>
                <div class="border-t border-gray-500 text-center pt-1">{{ t("invoice.authorized_signature") }}</div>
            </div>
        </div>

        <div class="text-center mt-6 text-xs text-gray-600">{{ t("invoice.thank_you") }}</div>
        <div v-if="shop?.footer" class="text-center mt-1 text-[11px] text-gray-500 whitespace-pre-line">{{ shop.footer }}</div>
    </div>
</template>

<style scoped>
/* Invoices are paper: always white with dark ink, also in dark mode
   (overrides the global .app-dark utility remap inside the invoice). */
:global(.app-dark) .invoice-paper {
    background-color: #fff !important;
    color: #1f2937 !important;
}
:global(.app-dark) .invoice-paper .bg-gray-100,
:global(.app-dark) .invoice-paper .bg-gray-50 {
    background-color: #f3f4f6 !important;
}
:global(.app-dark) .invoice-paper .text-gray-600 {
    color: #4b5563 !important;
}
:global(.app-dark) .invoice-paper .text-gray-800 {
    color: #1f2937 !important;
}
:global(.app-dark) .invoice-paper .border,
:global(.app-dark) .invoice-paper .border-b,
:global(.app-dark) .invoice-paper .border-t,
:global(.app-dark) .invoice-paper [class*="border-gray-"] {
    border-color: #d1d5db !important;
}
</style>
