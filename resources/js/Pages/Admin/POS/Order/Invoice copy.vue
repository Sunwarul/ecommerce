<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { router, usePage } from "@inertiajs/vue3";
import Button from "primevue/button";
import { computed, onMounted } from "vue";

const props = defineProps({
  order: { type: Object, required: true },

  // Optional (if you pass from backend later)
  shop: {
    type: Object,
    default: () => ({
      name: "Your Shop Name",
      address: "Your Address",
      phone: "0123456789",
    }),
  },
});

const page = usePage();

const money = (v) => Number(v || 0).toFixed(2);

const totalPaid = computed(() =>
  (props.order.payments || []).reduce((sum, p) => sum + Number(p.amount || 0), 0)
);

const paidAmount = computed(() =>
  Number(props.order.paid_amount ?? totalPaid.value)
);

const due = computed(() =>
  Math.max(0, Number(props.order.total_amount || 0) - paidAmount.value)
);

const change = computed(() => Math.max(0, paidAmount.value - Number(props.order.total_amount || 0)));

const invoiceTitle = computed(() => (props.order.status === "draft" ? "Draft Invoice" : "Invoice"));

function printInvoice() {
  window.print();
}

// ✅ Optional: auto-print if URL has ?autoprint=1
onMounted(() => {
  const params = new URLSearchParams(window.location.search);
  if (params.get("autoprint") === "1") {
    setTimeout(() => window.print(), 250);
  }
});
</script>

<template>
  <AuthenticatedLayout>
    <div class="invoice-wrap">
      <!-- Top actions (screen only) -->
      <div class="invoice-actions print:hidden">
        <div>
          <h1 class="invoice-h1">{{ invoiceTitle }}</h1>
          <p class="invoice-sub">
            Invoice No:
            <span class="font-semibold">{{ order.invoice_no || `#${order.id}` }}</span>
            <span v-if="order.status === 'draft'" class="badge-draft">DRAFT</span>
          </p>
          <p class="invoice-sub">Date: {{ new Date(order.created_at).toLocaleString() }}</p>
        </div>

        <div class="flex gap-2">
          <Button
            label="Back to POS"
            icon="pi pi-arrow-left"
            severity="secondary"
            @click="router.visit(route('pos.orders.index'))"
          />
          <Button label="Print" icon="pi pi-print" severity="success" @click="printInvoice" />
        </div>
      </div>

      <!-- PRINT HEADER (thermal + print only) -->
      <div class="print-header hidden print:block">
        <div class="print-shop-name">{{ shop.name }}</div>
        <div class="print-shop-line">{{ shop.address }}</div>
        <div class="print-shop-line">Phone: {{ shop.phone }}</div>
        <div class="print-sep"></div>

        <div class="print-meta">
          <div>Invoice: {{ order.invoice_no || `#${order.id}` }}</div>
          <div>Date: {{ new Date(order.created_at).toLocaleString() }}</div>
          <div>Cashier: {{ order.user?.name || "—" }}</div>
          <div>Branch: {{ order.branch?.name || "—" }}</div>
        </div>

        <div class="print-sep"></div>
      </div>

      <!-- Main invoice card -->
      <div class="invoice-card">
        <!-- Watermark (print only) -->
        <div v-if="order.status === 'draft'" class="draft-watermark print:block hidden">
          DRAFT
        </div>

        <!-- Header (screen view inside card) -->
        <div class="invoice-head print:hidden">
          <div>
            <div class="text-sm text-slate-500">Invoice</div>
            <div class="text-2xl font-bold text-slate-900">
              {{ order.invoice_no || `#${order.id}` }}
            </div>
            <div class="text-xs text-slate-500">
              {{ new Date(order.created_at).toLocaleString() }}
            </div>
          </div>

          <div class="status-pill">
            <span class="text-xs text-slate-500">Payment</span>
            <span class="text-sm font-semibold capitalize">{{ order.payment_status }}</span>
          </div>
        </div>

        <!-- Customer + Info -->
        <div class="invoice-grid">
          <div class="box">
            <div class="box-title">Customer</div>
            <div class="box-value">{{ order.customer?.name || "Walk-in customer" }}</div>
            <div class="box-sub" v-if="order.customer?.phone">Phone: {{ order.customer.phone }}</div>
            <div class="box-sub" v-if="order.customer?.email">Email: {{ order.customer.email }}</div>
          </div>

          <div class="box">
            <div class="box-title">Order Info</div>
            <div class="box-sub">Branch: <span class="font-medium">{{ order.branch?.name || "—" }}</span></div>
            <div class="box-sub">Warehouse: <span class="font-medium">{{ order.warehouse?.name || "—" }}</span></div>
            <div class="box-sub">Status: <span class="font-medium capitalize">{{ order.status }}</span></div>
          </div>
        </div>

        <!-- Items Table (screen) -->
        <div class="table-wrap print:hidden">
          <table class="invoice-table">
            <thead>
              <tr>
                <th>Item</th>
                <th class="r">Qty</th>
                <th class="r">Unit</th>
                <th class="r">Disc</th>
                <th class="r">Tax</th>
                <th class="r">Total</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="it in order.items" :key="it.id">
                <td>
                  <div class="item-name">{{ it.name }}</div>
                  <div class="item-sku">{{ it.sku }}</div>
                </td>
                <td class="r">{{ it.quantity }}</td>
                <td class="r">{{ money(it.unit_price) }}</td>
                <td class="r">{{ money(it.discount_amount || 0) }}</td>
                <td class="r">{{ money(it.tax_amount || 0) }}</td>
                <td class="r strong">{{ money(it.line_total || 0) }}</td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Items (print thermal layout) -->
        <div class="print-items hidden print:block">
          <div class="print-row-head">
            <span>Item</span><span class="r">Amt</span>
          </div>
          <div class="print-sep"></div>

          <div v-for="it in order.items" :key="it.id" class="print-item">
            <div class="print-item-top">
              <div class="print-item-name">{{ it.name }}</div>
              <div class="print-item-amt r">{{ money(it.line_total || 0) }}</div>
            </div>
            <div class="print-item-sub">
              <span>{{ it.sku }}</span>
              <span class="r">{{ it.quantity }} x {{ money(it.unit_price) }}</span>
            </div>
          </div>

          <div class="print-sep"></div>
        </div>

        <!-- Totals -->
        <div class="totals">
          <div class="totals-box">
            <div class="tot-row">
              <span>Subtotal</span><span class="v">{{ money(order.subtotal || 0) }}</span>
            </div>
            <div class="tot-row">
              <span>Discount</span><span class="v neg">-{{ money(order.discount_amount || 0) }}</span>
            </div>
            <div class="tot-row">
              <span>Tax</span><span class="v">{{ money(order.tax_amount || 0) }}</span>
            </div>

            <div class="tot-sep"></div>

            <div class="tot-row big">
              <span>Total</span><span class="v">{{ money(order.total_amount || 0) }}</span>
            </div>
            <div class="tot-row">
              <span>Paid</span><span class="v">{{ money(paidAmount) }}</span>
            </div>
            <div class="tot-row" v-if="change > 0">
              <span>Change</span><span class="v">{{ money(change) }}</span>
            </div>
            <div class="tot-row" v-if="due > 0">
              <span>Due</span><span class="v due">{{ money(due) }}</span>
            </div>
          </div>
        </div>

        <!-- Payments -->
        <div v-if="order.payments?.length" class="payments">
          <div class="payments-title">Payments</div>

          <div class="pay-list">
            <div v-for="p in order.payments" :key="p.id" class="pay-row">
              <span>{{ p.paymentMethod?.name || "Method" }}</span>
              <span class="font-semibold">{{ money(p.amount || 0) }}</span>
            </div>
          </div>
        </div>

        <!-- Print footer -->
        <div class="print-footer hidden print:block">
          <div class="print-sep"></div>
          <div class="print-thanks">Thank you for your purchase!</div>
          <div class="print-small">Powered by POS</div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<style scoped>
/* Screen layout */
.invoice-wrap {
  max-width: 980px;
  margin: 0 auto;
  padding: 24px;
}
.invoice-actions {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  gap: 16px;
  margin-bottom: 16px;
}
.invoice-h1 {
  font-size: 26px;
  font-weight: 800;
  color: #0f172a;
  line-height: 1.1;
}
.invoice-sub {
  font-size: 13px;
  color: #64748b;
}
.badge-draft {
  margin-left: 8px;
  padding: 2px 8px;
  border-radius: 999px;
  background: #fff7ed;
  color: #c2410c;
  font-weight: 700;
  font-size: 12px;
}
.invoice-card {
  position: relative;
  background: white;
  border: 1px solid #e2e8f0;
  border-radius: 16px;
  box-shadow: 0 6px 18px rgba(15, 23, 42, 0.06);
  padding: 18px;
}
.invoice-head {
  display: flex;
  justify-content: space-between;
  gap: 12px;
  padding-bottom: 14px;
  border-bottom: 1px solid #eef2f7;
  margin-bottom: 14px;
}
.status-pill {
  border: 1px solid #e2e8f0;
  border-radius: 14px;
  padding: 10px 12px;
  min-width: 150px;
  text-align: right;
  background: #f8fafc;
}
.invoice-grid {
  display: grid;
  grid-template-columns: 1fr;
  gap: 12px;
  margin-bottom: 14px;
}
@media (min-width: 768px) {
  .invoice-grid {
    grid-template-columns: 1fr 1fr;
  }
}
.box {
  border: 1px solid #e2e8f0;
  border-radius: 14px;
  padding: 12px;
  background: #ffffff;
}
.box-title {
  font-size: 12px;
  color: #64748b;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.06em;
  margin-bottom: 6px;
}
.box-value {
  font-size: 14px;
  color: #0f172a;
  font-weight: 700;
}
.box-sub {
  font-size: 13px;
  color: #334155;
  margin-top: 2px;
}
.table-wrap {
  border: 1px solid #e2e8f0;
  border-radius: 14px;
  overflow: hidden;
}
.invoice-table {
  width: 100%;
  border-collapse: collapse;
  font-size: 13px;
}
.invoice-table thead th {
  background: #0f172a;
  color: white;
  text-align: left;
  padding: 10px;
  font-weight: 700;
}
.invoice-table tbody td {
  padding: 10px;
  border-bottom: 1px solid #eef2f7;
  vertical-align: top;
}
.item-name {
  font-weight: 700;
  color: #0f172a;
}
.item-sku {
  font-size: 12px;
  color: #64748b;
}
.r {
  text-align: right;
}
.strong {
  font-weight: 800;
}
.totals {
  display: flex;
  justify-content: flex-end;
  margin-top: 14px;
}
.totals-box {
  width: 100%;
  max-width: 380px;
  border: 1px solid #e2e8f0;
  border-radius: 14px;
  padding: 12px;
  background: #f8fafc;
}
.tot-row {
  display: flex;
  justify-content: space-between;
  gap: 10px;
  font-size: 13px;
  color: #334155;
  margin: 4px 0;
}
.tot-row .v {
  font-weight: 700;
  color: #0f172a;
}
.tot-row.big {
  font-size: 16px;
  margin-top: 6px;
}
.tot-row.big .v {
  font-weight: 900;
}
.tot-sep {
  height: 1px;
  background: #e2e8f0;
  margin: 10px 0;
}
.neg {
  color: #b91c1c;
}
.due {
  color: #c2410c;
}
.payments {
  margin-top: 14px;
}
.payments-title {
  font-size: 13px;
  font-weight: 800;
  color: #0f172a;
  margin-bottom: 8px;
}
.pay-list {
  border: 1px solid #e2e8f0;
  border-radius: 14px;
  overflow: hidden;
}
.pay-row {
  display: flex;
  justify-content: space-between;
  padding: 10px 12px;
  border-bottom: 1px solid #eef2f7;
  font-size: 13px;
}
.pay-row:last-child {
  border-bottom: none;
}

/* Print layout (thermal + clean) */
@media print {
  /* Use receipt width if printer supports it */
  @page {
    margin: 6mm;
  }

  .invoice-wrap {
    padding: 0;
    max-width: 100%;
  }
  .invoice-card {
    border: none;
    box-shadow: none;
    padding: 0;
  }

  /* Hide layout-only elements */
  .print\\:hidden {
    display: none !important;
  }

  /* Improve print font */
  * {
    -webkit-print-color-adjust: exact;
    print-color-adjust: exact;
    font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
  }

  /* Draft watermark */
  .draft-watermark {
    position: fixed;
    top: 35%;
    left: 50%;
    transform: translate(-50%, -50%) rotate(-18deg);
    font-size: 64px;
    font-weight: 900;
    color: rgba(0, 0, 0, 0.08);
    letter-spacing: 0.2em;
    z-index: 0;
    pointer-events: none;
  }

  .print-header,
  .print-items,
  .print-footer {
    z-index: 1;
    position: relative;
  }

  .print-shop-name {
    font-weight: 900;
    font-size: 16px;
    text-align: center;
    margin-bottom: 2px;
  }
  .print-shop-line {
    font-size: 12px;
    text-align: center;
    margin: 1px 0;
  }
  .print-meta {
    font-size: 12px;
    line-height: 1.35;
  }
  .print-sep {
    border-top: 1px dashed #111;
    margin: 8px 0;
  }
  .print-row-head {
    display: flex;
    justify-content: space-between;
    font-size: 12px;
    font-weight: 800;
  }
  .print-item {
    padding: 6px 0;
    border-bottom: 1px dashed rgba(0, 0, 0, 0.25);
  }
  .print-item-top {
    display: flex;
    justify-content: space-between;
    gap: 8px;
    font-size: 12px;
    font-weight: 800;
  }
  .print-item-sub {
    display: flex;
    justify-content: space-between;
    font-size: 11px;
    opacity: 0.9;
    margin-top: 2px;
  }
  .print-thanks {
    text-align: center;
    font-weight: 900;
    margin-top: 8px;
    font-size: 12px;
  }
  .print-small {
    text-align: center;
    font-size: 11px;
    opacity: 0.85;
    margin-top: 2px;
  }
}
</style>
