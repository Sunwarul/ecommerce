<script setup>
import { computed } from "vue";
import { useI18n } from "vue-i18n";
import { money } from "@/Helpers/format";

const props = defineProps({
    order: { type: Object, required: true },
    shop: { type: Object, required: true },
    width: { type: Number, default: 80 }, // 58 or 80 (mm)
});

const { t } = useI18n();

const totalPaid = computed(() => (props.order.payments || []).reduce((sum, p) => sum + Number(p.amount || 0), 0));
const paidAmount = computed(() => Number(props.order.paid_amount ?? totalPaid.value));
const isCompleted = computed(() => props.order.status === "completed");
const due = computed(() => (isCompleted.value ? Math.max(0, Number(props.order.total_amount || 0) - paidAmount.value) : 0));
const change = computed(() => Math.max(0, paidAmount.value - Number(props.order.total_amount || 0)));
const receiptMaxWidth = computed(() => (props.width === 58 ? "280px" : "380px"));
const hasCharge = (key) => Number(props.order[key] || 0) > 0;
</script>

<template>
    <div class="receipt" :style="{ maxWidth: receiptMaxWidth }">
        <div v-if="order.status === 'draft' || order.status === 'void'" class="watermark">
            {{ t(`shared.status.${order.status}`) }}
        </div>

        <div class="center">
            <div class="shop-name">{{ shop.name }}</div>
            <div class="small">{{ shop.address }}</div>
            <div v-if="shop.phone" class="small">{{ t("shared.terms.phone") }}: <span class="ltr">{{ shop.phone }}</span></div>
        </div>

        <div class="sep"></div>

        <div class="meta small">
            <div>{{ t("shared.terms.invoice") }}: <span class="ltr">{{ order.invoice_no || `#${order.id}` }}</span></div>
            <div>{{ t("shared.terms.date") }}: <span class="ltr">{{ order.created_at }}</span></div>
            <div>{{ t("shared.terms.cashier") }}: {{ order.user?.name || "—" }}</div>
            <div>{{ t("shared.terms.branch") }}: {{ order.branch?.name || "—" }}</div>
        </div>

        <div class="sep"></div>

        <div class="small">
            <div><b>{{ t("shared.terms.customer") }}:</b> {{ order.customer?.name || t("shared.terms.walk_in") }}</div>
            <div v-if="order.customer?.phone">{{ t("shared.terms.phone") }}: <span class="ltr">{{ order.customer.phone }}</span></div>
        </div>

        <div class="sep"></div>

        <div class="row head"><span>{{ t("invoice.item") }}</span><span class="r">{{ t("shared.terms.amount") }}</span></div>
        <div class="sep thin"></div>

        <div v-for="it in order.items" :key="it.id" class="item">
            <div class="row">
                <span class="bold">{{ it.name }}</span>
                <span class="r bold">{{ money(it.line_total) }}</span>
            </div>
            <div class="row small">
                <span class="ltr">{{ it.sku }}</span>
                <span class="r">{{ it.quantity }} × {{ money(it.unit_price) }}</span>
            </div>
        </div>

        <div class="sep"></div>

        <div class="row"><span>{{ t("orders.subtotal") }}</span><span class="r">{{ money(order.subtotal) }}</span></div>
        <div class="row"><span>{{ t("pos.discount") }}</span><span class="r neg">-{{ money(order.discount_amount) }}</span></div>
        <div class="row"><span>{{ t("pos.tax") }}</span><span class="r">{{ money(order.tax_amount) }}</span></div>
        <div v-if="hasCharge('emi_charge_amount')" class="row">
            <span>{{ t("pos_screen.emi_charge") }}</span><span class="r">{{ money(order.emi_charge_amount) }}</span>
        </div>
        <div v-if="hasCharge('installment_charge_amount')" class="row">
            <span>{{ t("invoice.installment_charge") }}</span><span class="r">{{ money(order.installment_charge_amount) }}</span>
        </div>
        <div v-if="hasCharge('late_fee_amount')" class="row">
            <span>{{ t("invoice.late_fee") }}</span><span class="r">{{ money(order.late_fee_amount) }}</span>
        </div>

        <div class="sep"></div>

        <div class="row big"><span>{{ t("shared.terms.total") }}</span><span class="r">{{ money(order.total_amount) }}</span></div>
        <div class="row"><span>{{ t("shared.terms.paid") }}</span><span class="r">{{ money(paidAmount) }}</span></div>
        <div v-if="change > 0" class="row"><span>{{ t("pos.change") }}</span><span class="r">{{ money(change) }}</span></div>
        <div v-if="due > 0" class="row"><span>{{ t("shared.terms.due") }}</span><span class="r due">{{ money(due) }}</span></div>

        <div v-if="order.installment_sale">
            <div class="sep"></div>
            <div class="bold">{{ t("invoice.installments_title", { no: order.installment_sale.agreement_no }) }}</div>
            <div v-for="s in order.installment_sale.schedules" :key="s.id" class="row small">
                <span class="ltr">#{{ s.installment_no }} {{ s.due_date }}</span>
                <span class="r">{{ money(s.amount) }}{{ s.status === "paid" ? " ✓" : "" }}</span>
            </div>
        </div>

        <div v-if="order.emi_transactions?.length">
            <div class="sep"></div>
            <div v-for="e in order.emi_transactions" :key="e.id" class="row small">
                <span>{{ t("shared.sale_type.emi") }} · {{ e.bank?.name }}</span>
                <span class="r">{{ e.months }} × {{ money(e.monthly_amount) }}</span>
            </div>
        </div>

        <div v-if="order.payments?.length">
            <div class="sep"></div>
            <div class="bold">{{ t("pos_screen.payments") }}</div>

            <div v-for="p in order.payments" :key="p.id" class="pay-block">
                <div class="row small">
                    <span class="bold">{{ p.payment_method?.name || t("shared.terms.method") }}</span>
                    <span class="r bold">{{ money(p.amount) }}</span>
                </div>
                <div v-if="p.transaction_ref" class="row small">
                    <span class="muted">{{ t("shared.terms.reference") }}</span><span class="r ltr">{{ p.transaction_ref }}</span>
                </div>
                <div v-if="p.notes" class="row small">
                    <span class="muted">{{ t("shared.terms.notes") }}</span><span class="r">{{ p.notes }}</span>
                </div>
                <div v-if="p.meta" class="meta-wrap">
                    <div v-if="p.meta.customer_bank_name" class="row small">
                        <span class="muted">{{ t("pos_screen.customer_bank_name") }}</span><span class="r">{{ p.meta.customer_bank_name }}</span>
                    </div>
                    <div v-if="p.meta.customer_account_no" class="row small">
                        <span class="muted">{{ t("pos_screen.customer_account_no") }}</span><span class="r ltr">{{ p.meta.customer_account_no }}</span>
                    </div>
                    <div v-if="p.meta.received_to_bank_account_id" class="row small">
                        <span class="muted">{{ t("invoice.received_to") }}</span><span class="r ltr">#{{ p.meta.received_to_bank_account_id }}</span>
                    </div>
                    <div v-if="p.meta.txn_ref" class="row small">
                        <span class="muted">{{ t("pos_screen.bank_txn_ref") }}</span><span class="r ltr">{{ p.meta.txn_ref }}</span>
                    </div>
                    <div v-if="p.meta.card_last4" class="row small">
                        <span class="muted">{{ t("invoice.card") }}</span><span class="r ltr">**** {{ p.meta.card_last4 }}</span>
                    </div>
                </div>
                <div class="sep thin"></div>
            </div>
        </div>

        <div class="sep"></div>
        <div class="center bold">{{ t("invoice.thank_you_short") }}</div>
        <div v-if="shop.footer" class="center small" style="white-space: pre-line">{{ shop.footer }}</div>
    </div>
</template>

<style scoped>
/* Receipt paper: black on white regardless of app theme */
.receipt {
    position: relative;
    margin: 0 auto;
    padding: 12px;
    border: 1px solid #ddd;
    background: #fff;
    color: #000;
    font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New",
        var(--font-bengali, "Noto Sans Bengali"), "Noto Sans Arabic", "Noto Sans Devanagari", monospace;
    font-size: 13px;
}
.center {
    text-align: center;
}
.shop-name {
    font-weight: 900;
    font-size: 16px;
}
.small {
    font-size: 11px;
}
.bold {
    font-weight: 800;
}
.r {
    text-align: end;
    white-space: nowrap;
}
.ltr {
    direction: ltr;
    unicode-bidi: isolate;
}
.neg {
    color: #b91c1c;
}
.due {
    color: #c2410c;
}
.row {
    display: flex;
    justify-content: space-between;
    gap: 8px;
}
.row.big {
    font-size: 15px;
    font-weight: 900;
    margin-top: 4px;
}
.head {
    font-weight: 900;
}
.item {
    padding: 6px 0;
    border-bottom: 1px dashed rgba(0, 0, 0, 0.25);
}
.sep {
    border-top: 1px dashed #000;
    margin: 8px 0;
}
.sep.thin {
    border-top-style: dotted;
    margin: 6px 0;
}
.watermark {
    position: absolute;
    inset: 0;
    display: flex;
    justify-content: center;
    align-items: center;
    font-size: 56px;
    font-weight: 900;
    opacity: 0.07;
    pointer-events: none;
    text-transform: uppercase;
}
.pay-block {
    margin-top: 6px;
}
.meta-wrap {
    margin-top: 4px;
}
.muted {
    opacity: 0.75;
}

@media print {
    @page {
        margin: 6mm;
    }
    .receipt {
        border: none;
        padding: 0;
        max-width: 100% !important;
        font-size: 11px;
    }
}
</style>
