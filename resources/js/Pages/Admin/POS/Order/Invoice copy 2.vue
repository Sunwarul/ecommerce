<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { router } from "@inertiajs/vue3";
import { computed, onMounted } from "vue";
import Button from "primevue/button";

const props = defineProps({
  order: { type: Object, required: true },
  shop: {
    type: Object,
    default: () => ({
      name: "Your Shop Name",
      address: "Shop address line",
      phone: "0123456789",
    }),
  },
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

function printInvoice() {
  window.print();
}

// auto print support
onMounted(() => {
  const q = new URLSearchParams(window.location.search);
  if (q.get("autoprint") === "1") {
    setTimeout(() => window.print(), 300);
  }
});
</script>

<template>
  <AuthenticatedLayout>
    <div class="invoice-wrap">
      <!-- Screen actions -->
      <div class="actions print:hidden">
        <Button
          label="Back"
          icon="pi pi-arrow-left"
          severity="secondary"
          @click="router.visit(route('pos.orders.index'))"
        />
        <Button
          label="Print"
          icon="pi pi-print"
          severity="success"
          @click="printInvoice"
        />
      </div>

      <!-- Invoice -->
      <div class="invoice">
        <!-- Draft watermark -->
        <div v-if="order.status === 'draft'" class="watermark">DRAFT</div>

        <!-- Shop header -->
        <div class="center">
          <div class="shop-name">{{ shop.name }}</div>
          <div class="small">{{ shop.address }}</div>
          <div class="small">Phone: {{ shop.phone }}</div>
        </div>

        <div class="sep"></div>

        <!-- Meta -->
        <div class="meta">
          <div>Invoice: {{ order.invoice_no || `#${order.id}` }}</div>
          <div>Date: {{ new Date(order.created_at).toLocaleString() }}</div>
          <div>Cashier: {{ order.user?.name || "—" }}</div>
          <div>Branch: {{ order.branch?.name || "—" }}</div>
        </div>

        <div class="sep"></div>

        <!-- Customer -->
        <div>
          <strong>Customer</strong>
          <div>{{ order.customer?.name || "Walk-in customer" }}</div>
          <div class="small" v-if="order.customer?.phone">
            {{ order.customer.phone }}
          </div>
          <div class="small" v-if="order.customer?.email">
            {{ order.customer.email }}
          </div>
        </div>

        <div class="sep"></div>

        <!-- Items -->
        <div class="row head">
          <span>Item</span>
          <span class="r">Amt</span>
        </div>

        <div class="sep thin"></div>

        <div
          v-for="it in order.items"
          :key="it.id"
          class="item"
        >
          <div class="row">
            <span class="bold">{{ it.name }}</span>
            <span class="r bold">{{ money(it.line_total) }}</span>
          </div>
          <div class="row small">
            <span>{{ it.sku }}</span>
            <span class="r">
              {{ it.quantity }} × {{ money(it.unit_price) }}
            </span>
          </div>
        </div>

        <div class="sep"></div>

        <!-- Totals -->
        <div class="row">
          <span>Subtotal</span>
          <span class="r">{{ money(order.subtotal) }}</span>
        </div>
        <div class="row">
          <span>Discount</span>
          <span class="r neg">-{{ money(order.discount_amount) }}</span>
        </div>
        <div class="row">
          <span>Tax</span>
          <span class="r">{{ money(order.tax_amount) }}</span>
        </div>

        <div class="sep"></div>

        <div class="row big">
          <span>Total</span>
          <span class="r">{{ money(order.total_amount) }}</span>
        </div>

        <div class="row">
          <span>Paid</span>
          <span class="r">{{ money(paidAmount) }}</span>
        </div>

        <div class="row" v-if="change > 0">
          <span>Change</span>
          <span class="r">{{ money(change) }}</span>
        </div>

        <div class="row" v-if="due > 0">
          <span>Due</span>
          <span class="r due">{{ money(due) }}</span>
        </div>

        <!-- Payments -->
        <div v-if="order.payments?.length">
          <div class="sep"></div>
          <strong>Payments</strong>

          <div
            v-for="p in order.payments"
            :key="p.id"
            class="row small"
          >
            <span>{{ p.paymentMethod?.name }}</span>
            <span class="r">{{ money(p.amount) }}</span>
          </div>
        </div>

        <div class="sep"></div>

        <!-- Footer -->
        <div class="center bold">Thank you!</div>
        <div class="center small">Powered by POS</div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<style scoped>
/* -------- Screen -------- */
.invoice-wrap {
  padding: 20px;
}
.actions {
  display: flex;
  justify-content: space-between;
  margin-bottom: 12px;
}

/* Receipt width (80mm default) */
.invoice {
  position: relative;
  max-width: 380px;
  margin: auto;
  padding: 12px;
  border: 1px solid #ddd;
  background: white;
  font-family: monospace;
  font-size: 13px;
}

.center {
  text-align: center;
}
.shop-name {
  font-size: 16px;
  font-weight: 900;
}
.small {
  font-size: 11px;
}
.bold {
  font-weight: 800;
}
.neg {
  color: #b91c1c;
}
.due {
  color: #c2410c;
}

.sep {
  border-top: 1px dashed #000;
  margin: 8px 0;
}
.sep.thin {
  border-top-style: dotted;
}

.row {
  display: flex;
  justify-content: space-between;
}
.row.big {
  font-size: 15px;
  font-weight: 900;
}
.row.small {
  font-size: 11px;
}
.r {
  text-align: right;
}

.item {
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
}

/* -------- Print -------- */
@media print {
  @page {
    margin: 6mm;
  }
  .print\:hidden {
    display: none !important;
  }
  .invoice {
    border: none;
    box-shadow: none;
    font-size: 11px;
    max-width: 100%;
  }
}
</style>
