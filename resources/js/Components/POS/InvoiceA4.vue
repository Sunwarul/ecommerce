<script setup>
import { computed } from "vue";

const props = defineProps({
    order: { type: Object, required: true },
    shop: { type: Object, required: true },
    width: { type: Number, default: 80 }, // 58 or 80
});

const money = (v) => Number(v || 0).toFixed(2);

const totalPaid = computed(() =>
    (props.order.payments || []).reduce(
        (sum, p) => sum + Number(p.amount || 0),
        0
    )
);

const paidAmount = computed(() =>
    Number(props.order.paid_amount ?? totalPaid.value)
);

const due = computed(() =>
    Math.max(0, Number(props.order.total_amount || 0) - paidAmount.value)
);

const change = computed(() =>
    Math.max(0, paidAmount.value - Number(props.order.total_amount || 0))
);

const receiptMaxWidth = computed(() =>
    props.width === 58 ? "280px" : "380px"
);
function isBankPayment(p) {
    const name = (p.paymentMethod?.name || "").toLowerCase();
    return name.includes("bank");
}
</script>

<template>
    <div class="receipt" :style="{ maxWidth: receiptMaxWidth }">
        <div v-if="order.status === 'draft'" class="watermark">DRAFT</div>

        <div class="center">
            <div class="shop-name">{{ shop.name }}</div>
            <div class="small">{{ shop.address }}</div>
            <div class="small">Phone: {{ shop.phone }}</div>
        </div>

        <div class="sep"></div>

        <div class="meta small">
            <div>Invoice: {{ order.invoice_no || `#${order.id}` }}</div>
            <div>Date: {{ new Date(order.created_at).toLocaleString() }}</div>
            <div>Cashier: {{ order.user?.name || "—" }}</div>
            <div>Branch: {{ order.branch?.name || "—" }}</div>
        </div>

        <div class="sep"></div>

        <div class="small">
            <div><b>Customer:</b> {{ order.customer?.name || "Walk-in" }}</div>
            <div v-if="order.customer?.phone">
                Phone: {{ order.customer.phone }}
            </div>
            <div v-if="order.customer?.email">
                Email: {{ order.customer.email }}
            </div>
        </div>

        <div class="sep"></div>

        <div class="row head"><span>Item</span><span class="r">Amt</span></div>
        <div class="sep thin"></div>

        <div v-for="it in order.items" :key="it.id" class="item">
            <div class="row">
                <span class="bold">{{ it.name }}</span>
                <span class="r bold">{{ money(it.line_total || 0) }}</span>
            </div>
            <div class="row small">
                <span>{{ it.sku }}</span>
                <span class="r"
                    >{{ it.quantity }} × {{ money(it.unit_price) }}</span
                >
            </div>
        </div>

        <div class="sep"></div>

        <div class="row">
            <span>Subtotal</span
            ><span class="r">{{ money(order.subtotal) }}</span>
        </div>
        <div class="row">
            <span>Discount</span
            ><span class="r neg">-{{ money(order.discount_amount) }}</span>
        </div>
        <div class="row">
            <span>Tax</span><span class="r">{{ money(order.tax_amount) }}</span>
        </div>

        <div class="sep"></div>

        <div class="row big">
            <span>Total</span
            ><span class="r">{{ money(order.total_amount) }}</span>
        </div>
        <div class="row">
            <span>Paid</span><span class="r">{{ money(paidAmount) }}</span>
        </div>
        <div class="row" v-if="change > 0">
            <span>Change</span><span class="r">{{ money(change) }}</span>
        </div>
        <div class="row" v-if="due > 0">
            <span>Due</span><span class="r due">{{ money(due) }}</span>
        </div>

        <div v-if="order.payments?.length">
            <div class="sep"></div>
            <div class="bold">Payments</div>

            <div v-for="p in order.payments" :key="p.id" class="pay-block">
                <!-- main line -->
                <div class="row small">
                    <span class="bold">{{
                        p.payment_method?.name || "Method"
                    }}</span>
                    <span class="r bold">{{ money(p.amount) }}</span>
                </div>

                <!-- refs -->
                <div v-if="p.transaction_ref" class="row small">
                    <span class="muted">Txn Ref</span>
                    <span class="r">{{ p.transaction_ref }}</span>
                </div>

                <div v-if="p.notes" class="row small">
                    <span class="muted">Note</span>
                    <span class="r">{{ p.notes }}</span>
                </div>

                <!-- meta (bank info) -->
                <div v-if="p.meta" class="meta-wrap">
                    <div v-if="p.meta.customer_bank_name" class="row small">
                        <span class="muted">Customer Bank</span>
                        <span class="r">{{ p.meta.customer_bank_name }}</span>
                    </div>

                    <div v-if="p.meta.customer_account_no" class="row small">
                        <span class="muted">Customer A/C</span>
                        <span class="r">{{ p.meta.customer_account_no }}</span>
                    </div>

                    <div
                        v-if="p.meta.received_to_bank_account_id"
                        class="row small"
                    >
                        <span class="muted">Received To</span>
                        <span class="r"
                            >#{{ p.meta.received_to_bank_account_id }}</span
                        >
                    </div>

                    <div v-if="p.meta.txn_ref" class="row small">
                        <span class="muted">Bank Txn/Cheque</span>
                        <span class="r">{{ p.meta.txn_ref }}</span>
                    </div>
                </div>

                <div class="sep thin"></div>
            </div>
        </div>

        <div class="sep"></div>
        <div class="center bold">Thank you!</div>
        <div class="center small">Powered by POS</div>
    </div>
</template>

<style scoped>
.receipt {
    position: relative;
    margin: 0 auto;
    padding: 12px;
    border: 1px solid #ddd;
    background: #fff;
    font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas,
        "Liberation Mono", "Courier New", monospace;
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
    text-align: right;
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
.sep.thin {
    border-top-style: dotted;
    margin: 6px 0;
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
