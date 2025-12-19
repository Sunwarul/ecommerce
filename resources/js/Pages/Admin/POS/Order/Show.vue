<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { router } from "@inertiajs/vue3";
import Button from "primevue/button";
import Dialog from "primevue/dialog";
import Divider from "primevue/divider";
import Dropdown from "primevue/dropdown";
import InputNumber from "primevue/inputnumber";
import InputText from "primevue/inputtext";
import Tag from "primevue/tag";
import { useToast } from "primevue/usetoast";
import { computed, ref } from "vue";

const toast = useToast();

const props = defineProps({
    order: Object,
    paymentMethods: Array,
});

/* ---------------- Helpers ---------------- */
const money = (v) => Number(v || 0).toFixed(2);

const paidTotal = computed(() =>
    props.order.payments?.reduce((s, p) => s + Number(p.amount || 0), 0)
);

const due = computed(() =>
    Math.max(0, Number(props.order.total_amount || 0) - paidTotal.value)
);

const canAddPayment = computed(
    () => props.order.status !== "void" && due.value > 0
);

/* ---------------- Status UI ---------------- */
function paymentSeverity(v) {
    if (v === "paid") return "success";
    if (v === "partial") return "warn";
    return "danger";
}
function statusSeverity(v) {
    if (v === "completed") return "success";
    if (v === "draft") return "info";
    return "danger";
}

/* ---------------- Add Payment ---------------- */
const showPayModal = ref(false);
const payLoading = ref(false);

const payment = ref({
    payment_method_id: null,
    amount: 0,
    transaction_ref: "",
    notes: "",
    meta: {
        customer_bank_name: "",
        customer_account_no: "",
        received_to_bank_account_id: null,
        txn_ref: "",
    },
});

function needsBankMeta() {
    const pm = props.paymentMethods.find(
        (m) => m.id === payment.value.payment_method_id
    );
    return pm?.name?.toLowerCase().includes("bank");
}

function submitPayment() {
    if (!payment.value.payment_method_id || payment.value.amount <= 0) {
        toast.add({
            severity: "warn",
            summary: "Invalid payment",
            detail: "Select method and amount",
            life: 2500,
        });
        return;
    }

    if (needsBankMeta()) {
        if (
            !payment.value.meta.customer_bank_name ||
            !payment.value.meta.customer_account_no
        ) {
            toast.add({
                severity: "warn",
                summary: "Bank info required",
                detail: "Customer bank name & account no required",
                life: 3000,
            });
            return;
        }
    }

    payLoading.value = true;

    router.post(
        route("pos.orders.payments.store", props.order.id),
        payment.value,
        {
            preserveScroll: true,
            onSuccess: () => {
                toast.add({
                    severity: "success",
                    summary: "Payment added",
                    detail: "Order updated successfully",
                    life: 2200,
                });
                showPayModal.value = false;
            },
            onFinish: () => (payLoading.value = false),
        }
    );
}

/* ---------------- Void ---------------- */
function voidOrder() {
    if (!confirm("Are you sure you want to VOID this order?")) return;

    router.post(
        route("pos.orders.void", props.order.id),
        {},
        {
            onSuccess: () => {
                toast.add({
                    severity: "success",
                    summary: "Voided",
                    detail: "Order has been voided",
                    life: 2200,
                });
            },
        }
    );
}
</script>

<template>
    <AuthenticatedLayout>
        <div class="max-w-5xl mx-auto p-6 space-y-6">
            <!-- HEADER -->
            <div class="flex justify-between items-start gap-3">
                <div>
                    <h2 class="text-2xl font-bold">
                        Invoice {{ order.invoice_no || `#${order.id}` }}
                    </h2>
                    <p class="text-sm text-gray-500">
                        {{ new Date(order.created_at).toLocaleString() }}
                    </p>
                </div>

                <div class="flex gap-2 flex-wrap">
                    <Tag
                        :value="order.payment_status"
                        :severity="paymentSeverity(order.payment_status)"
                    />
                    <Tag
                        :value="order.status"
                        :severity="statusSeverity(order.status)"
                    />
                </div>
            </div>

            <!-- SUMMARY -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="border rounded-lg p-4">
                    <div class="text-xs text-gray-500">Customer</div>
                    <div class="font-semibold">
                        {{ order.customer?.name || "Walk-in" }}
                    </div>
                    <div class="text-xs" v-if="order.customer?.phone">
                        {{ order.customer.phone }}
                    </div>
                </div>

                <div class="border rounded-lg p-4">
                    <div class="text-xs text-gray-500">Totals</div>
                    <div>Total: {{ money(order.total_amount) }}</div>
                    <div>Paid: {{ money(paidTotal) }}</div>
                    <div class="text-red-600">Due: {{ money(due) }}</div>
                </div>

                <div class="border rounded-lg p-4 space-y-2">
                    <Button
                        label="Print Invoice"
                        icon="pi pi-print"
                        class="w-full"
                        @click="
                            window.open(
                                route('pos.orders.invoice', order.id) +
                                    '?autoprint=1',
                                '_blank'
                            )
                        "
                    />
                    <Button
                        v-if="canAddPayment"
                        label="Add Payment"
                        icon="pi pi-plus"
                        class="w-full p-button-success"
                        @click="showPayModal = true"
                    />
                    <Button
                        v-if="order.status !== 'void'"
                        label="Void Order"
                        icon="pi pi-ban"
                        class="w-full p-button-danger"
                        @click="voidOrder"
                    />
                </div>
            </div>

            <!-- ITEMS -->
            <div class="border rounded-lg">
                <div class="p-3 font-semibold">Items</div>
                <Divider />
                <div
                    v-for="it in order.items"
                    :key="it.id"
                    class="flex justify-between px-4 py-2 text-sm"
                >
                    <div>
                        <div class="font-medium">{{ it.name }}</div>
                        <div class="text-xs text-gray-500">
                            {{ it.quantity }} × {{ money(it.unit_price) }}
                        </div>
                    </div>
                    <div class="font-semibold">
                        {{ money(it.line_total) }}
                    </div>
                </div>
            </div>

            <!-- PAYMENTS -->
            <div class="border rounded-lg">
                <div class="p-3 font-semibold">Payments</div>
                <Divider />

                <div
                    v-for="p in order.payments"
                    :key="p.id"
                    class="px-4 py-3 border-b last:border-b-0 text-sm"
                >
                    <div class="flex justify-between">
                        <span class="font-medium">
                            {{ p.payment_method?.name }}
                        </span>
                        <span class="font-semibold">
                            {{ money(p.amount) }}
                        </span>
                    </div>

                    <div class="text-xs text-gray-500" v-if="p.transaction_ref">
                        Txn: {{ p.transaction_ref }}
                    </div>

                    <div v-if="p.meta" class="mt-1 text-xs text-gray-600">
                        <div v-if="p.meta.customer_bank_name">
                            Bank: {{ p.meta.customer_bank_name }}
                        </div>
                        <div v-if="p.meta.customer_account_no">
                            A/C: {{ p.meta.customer_account_no }}
                        </div>
                        <div v-if="p.meta.txn_ref">
                            Bank Txn: {{ p.meta.txn_ref }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ADD PAYMENT MODAL -->
        <Dialog
            v-model:visible="showPayModal"
            modal
            header="Add Payment"
            :style="{ width: '520px' }"
        >
            <div class="space-y-3">
                <Dropdown
                    v-model="payment.payment_method_id"
                    :options="paymentMethods"
                    optionLabel="name"
                    optionValue="id"
                    placeholder="Payment method"
                    class="w-full"
                />

                <InputNumber
                    v-model="payment.amount"
                    :min="0"
                    class="w-full"
                    inputClass="w-full"
                    placeholder="Amount"
                />

                <InputText
                    v-model="payment.transaction_ref"
                    placeholder="Transaction ref"
                    class="w-full"
                />

                <div
                    v-if="needsBankMeta()"
                    class="border rounded p-3 bg-slate-50"
                >
                    <div class="font-semibold text-sm mb-2">Bank Info</div>

                    <InputText
                        v-model="payment.meta.customer_bank_name"
                        placeholder="Customer bank name"
                        class="w-full mb-2"
                    />
                    <InputText
                        v-model="payment.meta.customer_account_no"
                        placeholder="Customer account no"
                        class="w-full mb-2"
                    />
                    <InputText
                        v-model="payment.meta.txn_ref"
                        placeholder="Bank txn / cheque no"
                        class="w-full"
                    />
                </div>

                <div class="flex justify-end gap-2 pt-2">
                    <Button
                        label="Cancel"
                        class="p-button-text"
                        @click="showPayModal = false"
                    />
                    <Button
                        label="Save Payment"
                        icon="pi pi-check"
                        class="p-button-success"
                        :loading="payLoading"
                        @click="submitPayment"
                    />
                </div>
            </div>
        </Dialog>
    </AuthenticatedLayout>
</template>
