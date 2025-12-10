<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { useForm } from "@inertiajs/vue3";
import { useToast } from "primevue/usetoast";
import { computed, ref } from "vue";

const props = defineProps({
    products: Array,
    customers: Array,
    paymentMethods: Array,
    currentSession: Object,
});

const toast = useToast();

const search = ref("");
const selectedCustomerId = ref(null);
const posSession = ref(props.currentSession);
const cartItems = ref([]);

// ---------- ORDER LEVEL DISCOUNT ----------
const discountMode = ref("none"); // 'none' | 'percent' | 'fixed'
const discountValue = ref(0);

// ---------- CART LOGIC ----------
const filteredProducts = computed(() => {
    let list = props.products || [];

    if (search.value) {
        const t = search.value.toLowerCase();
        list = list.filter(
            (p) =>
                (p.name && p.name.toLowerCase().includes(t)) ||
                (p.sku && p.sku.toLowerCase().includes(t)) ||
                (p.barcode && p.barcode.toLowerCase().includes(t))
        );
    }

    return list;
});

function addToCart(product) {
    const found = cartItems.value.find((i) => i.product_id === product.id);
    if (found) {
        found.quantity += 1;
    } else {
        cartItems.value.push({
            product_id: product.id,
            name: product.name,
            sku: product.sku,
            unit_price: Number(product.base_price) || 0,
            quantity: 1,
            discount_amount: 0,
            tax_amount: 0,
        });
    }
}

function removeFromCart(index) {
    cartItems.value.splice(index, 1);
}

// ---------- TOTALS ----------
const subtotal = computed(() =>
    cartItems.value.reduce(
        (sum, item) => sum + item.unit_price * item.quantity,
        0
    )
);

const lineDiscountTotal = computed(() =>
    cartItems.value.reduce(
        (sum, item) => sum + Number(item.discount_amount || 0),
        0
    )
);

const orderDiscount = computed(() => {
    const base = subtotal.value;
    const val = Number(discountValue.value) || 0;

    if (discountMode.value === "percent") {
        const p = Math.min(Math.max(val, 0), 100);
        return (base * p) / 100;
    }

    if (discountMode.value === "fixed") {
        return Math.min(Math.max(val, 0), base);
    }

    return 0;
});

const discountTotal = computed(
    () => lineDiscountTotal.value + orderDiscount.value
);

const taxTotal = computed(() =>
    cartItems.value.reduce((sum, item) => sum + Number(item.tax_amount || 0), 0)
);

const total = computed(
    () => subtotal.value - discountTotal.value + taxTotal.value
);

// ---------- MULTIPLE PAYMENTS ----------
let paymentRowId = 1;
const payments = ref([
    {
        id: paymentRowId++,
        payment_method_id: null,
        amount: 0,
    },
]);

function addPaymentRow() {
    payments.value.push({
        id: paymentRowId++,
        payment_method_id: null,
        amount: 0,
    });
}

function removePaymentRow(index) {
    if (payments.value.length === 1) {
        // reset last row instead of removing
        payments.value[0].payment_method_id = null;
        payments.value[0].amount = 0;
        return;
    }
    payments.value.splice(index, 1);
}

const totalPaid = computed(() =>
    payments.value.reduce((sum, row) => sum + (Number(row.amount) || 0), 0)
);

const change = computed(() => Math.max(0, totalPaid.value - total.value));

// ---------- SUBMIT ----------
const form = useForm({});

function ensureSession() {
    if (posSession.value) return true;
    toast.add({
        severity: "warn",
        summary: "No session",
        detail: "Please open POS session first",
        life: 2500,
    });
    return false;
}

function submitOrder() {
    if (!ensureSession()) return;

    if (!cartItems.value.length) {
        toast.add({
            severity: "warn",
            summary: "Empty cart",
            detail: "Add items first",
            life: 2000,
        });
        return;
    }

    const validPayments = payments.value.filter(
        (p) => p.payment_method_id && Number(p.amount) > 0
    );

    if (!validPayments.length) {
        toast.add({
            severity: "warn",
            summary: "Payment required",
            detail: "Add at least one payment method with amount",
            life: 2200,
        });
        return;
    }

    if (totalPaid.value < total.value) {
        toast.add({
            severity: "warn",
            summary: "Insufficient payment",
            detail: "Total paid is less than payable amount",
            life: 2500,
        });
        return;
    }

    const payload = {
        pos_session_id: posSession.value.id,
        branch_id: posSession.value.branch_id,
        warehouse_id: posSession.value.warehouse_id,
        customer_id: selectedCustomerId.value,
        // send items as before
        items: cartItems.value.map((item) => ({
            product_id: item.product_id,
            variation_id: null,
            quantity: item.quantity,
            unit_price: item.unit_price,
            discount_amount: item.discount_amount || 0,
            tax_amount: item.tax_amount || 0,
        })),
        // multiple payments
        payments: validPayments.map((p) => ({
            payment_method_id: p.payment_method_id,
            amount: p.amount,
        })),
        // optional: you can store order-level discount too
        order_discount_type: discountMode.value,
        order_discount_value: discountValue.value,
        notes: null,
    };

    form.post(route("pos.orders.store"), {
        preserveScroll: true,
        data: payload,
        onSuccess: () => {
            toast.add({
                severity: "success",
                summary: "Success",
                detail: "Order saved",
                life: 2000,
            });
            cartItems.value = [];
            selectedCustomerId.value = null;
            discountMode.value = "none";
            discountValue.value = 0;
            payments.value = [
                { id: paymentRowId++, payment_method_id: null, amount: 0 },
            ];
        },
        onError: () => {
            toast.add({
                severity: "error",
                summary: "Error",
                detail: "Failed to save order",
                life: 3000,
            });
        },
    });
}
</script>

<template>
    <AuthenticatedLayout>
        <div class="h-screen flex">
            <main
                class="flex-1 flex flex-col overflow-hidden bg-white rounded-2xl shadow-sm border border-slate-100 p-4"
            >
                <!-- Top mini header -->
                <header
                    class="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-6"
                >
                    <div>
                        <h1 class="text-lg font-semibold text-slate-800">
                            Point of Sale
                        </h1>
                        <p class="text-xs text-slate-400">
                            {{
                                posSession
                                    ? `Active Session #${posSession.id}`
                                    : "No active session"
                            }}
                        </p>
                    </div>
                    <div class="flex items-center gap-3 text-xs text-slate-500">
                        <span
                            class="px-3 py-1 rounded-full bg-emerald-50 text-emerald-600 flex items-center gap-2"
                        >
                            <i class="pi pi-clock text-xs" />
                            {{ new Date().toLocaleTimeString() }}
                        </span>
                    </div>
                </header>

                <!-- BODY -->
                <div class="flex-1 flex overflow-hidden">
                    <!-- CENTER: PRODUCTS -->
                    <section class="flex-1 p-5 overflow-y-auto">
                        <div
                            class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3 mb-4"
                        >
                            <div>
                                <h2
                                    class="text-xl font-semibold text-slate-800"
                                >
                                    Product Catalog
                                </h2>
                                <p class="text-xs text-slate-400">
                                    Tap a product card to add it to the order.
                                </p>
                            </div>

                            <div class="flex items-center gap-3">
                                <InputGroup>
                                    <InputGroupAddon>
                                        <i class="pi pi-search"></i>
                                    </InputGroupAddon>
                                    <InputText
                                        v-model="search"
                                        placeholder="Search product, SKU, barcode"
                                        class="w-full text-sm"
                                    />
                                </InputGroup>
                            </div>
                        </div>

                        <div
                            class="grid gap-4 grid-cols-2 md:grid-cols-3 lg:grid-cols-4"
                        >
                            <div
                                v-for="product in filteredProducts"
                                :key="product.id"
                                class="col-12 sm:col-6 md:col-4 xl:col-3"
                            >
                                <div
                                    class="rounded-2xl shadow-sm border border-slate-300 bg-slate-100 p-3 flex flex-col h-full cursor-pointer hover:shadow-md hover:-translate-y-0.5 transition"
                                    @click="addToCart(product)"
                                >
                                    <div
                                        class="rounded-xl bg-white mb-3 h-28 flex items-center justify-center overflow-hidden"
                                    >
                                        <!-- product image placeholder -->
                                        <span
                                            class="text-slate-300 text-xs uppercase tracking-wide"
                                        >
                                            {{
                                                product.thumbnail ? "" : "Image"
                                            }}
                                        </span>
                                        <!--
                                        <img v-if="product.thumbnail"
                                             :src="product.thumbnail"
                                             alt=""
                                             class="w-full h-full object-cover"
                                        />
                                        -->
                                    </div>

                                    <div
                                        class="flex-1 flex flex-col justify-between gap-2"
                                    >
                                        <div>
                                            <div
                                                class="text-sm font-semibold text-slate-800 truncate"
                                            >
                                                {{ product.name }}
                                            </div>
                                            <div
                                                class="text-xs text-slate-400 mt-0.5"
                                            >
                                                {{ product.sku }}
                                            </div>
                                        </div>

                                        <div
                                            class="flex items-center justify-between mt-1"
                                        >
                                            <span
                                                class="text-base font-bold text-emerald-600"
                                            >
                                                {{
                                                    Number(
                                                        product.base_price || 0
                                                    ).toFixed(2)
                                                }}
                                            </span>
                                            <Button
                                                icon="pi pi-plus"
                                                class="p-button-rounded p-button-sm p-button-success"
                                                type="button"
                                            />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div
                                v-if="!filteredProducts.length"
                                class="col-12 text-center text-slate-400 text-sm py-10"
                            >
                                No products found
                            </div>
                        </div>
                    </section>

                    <!-- RIGHT: ORDER PANEL -->
                    <aside
                        class="w-full lg:w-80 xl:w-96 bg-slate-50 border-l border-slate-200 p-4 flex flex-col"
                    >
                        <div class="flex flex-col flex-1">
                            <!-- header -->
                            <div
                                class="flex items-center justify-between mb-3 pb-3 border-b border-slate-100"
                            >
                                <div>
                                    <h3
                                        class="text-base font-semibold text-slate-800"
                                    >
                                        Order Summary
                                    </h3>
                                    <p class="text-xs text-slate-400">
                                        {{
                                            selectedCustomerId
                                                ? customers.find(
                                                      (c) =>
                                                          c.id ===
                                                          selectedCustomerId
                                                  )?.name || "Customer"
                                                : "Walk-in customer"
                                        }}
                                    </p>
                                </div>
                                <span
                                    class="px-2.5 py-1 rounded-full text-xs bg-emerald-50 text-emerald-600 font-medium"
                                >
                                    {{ cartItems.length }} items
                                </span>
                            </div>

                            <!-- customer -->
                            <div class="mb-4">
                                <p class="text-xs text-slate-500 mb-1">
                                    Customer
                                </p>
                                <Dropdown
                                    v-model="selectedCustomerId"
                                    :options="customers"
                                    option-label="name"
                                    option-value="id"
                                    placeholder="Walk-in Customer"
                                    show-clear
                                    class="w-full text-sm"
                                />
                            </div>

                            <!-- items list -->
                            <div
                                class="flex-1 overflow-y-auto space-y-3 pr-1 mb-3"
                            >
                                <div
                                    v-for="(item, index) in cartItems"
                                    :key="item.product_id"
                                    class="flex items-start justify-between bg-white border-slate-300 border rounded-xl px-3 py-2"
                                >
                                    <div class="flex-1">
                                        <div
                                            class="text-xs font-semibold text-slate-800"
                                        >
                                            {{ item.name }}
                                        </div>
                                        <div
                                            class="text-[11px] text-slate-400 mb-1"
                                        >
                                            {{ item.sku }}
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <InputNumber
                                                v-model="item.quantity"
                                                :min="1"
                                                input-class="!w-14 !text-xs"
                                                show-buttons
                                                button-layout="horizontal"
                                                increment-button-icon="pi pi-plus"
                                                decrement-button-icon="pi pi-minus"
                                                increment-button-class="p-button-text p-button-sm"
                                                decrement-button-class="p-button-text p-button-sm"
                                            />
                                            <span
                                                class="text-[11px] text-slate-400"
                                            >
                                                x
                                                {{ item.unit_price.toFixed(2) }}
                                            </span>
                                        </div>
                                    </div>

                                    <div class="text-right ml-2">
                                        <div
                                            class="text-xs font-semibold text-slate-800"
                                        >
                                            {{
                                                (
                                                    item.unit_price *
                                                    item.quantity
                                                ).toFixed(2)
                                            }}
                                        </div>
                                        <Button
                                            icon="pi pi-trash"
                                            class="p-button-text p-button-danger p-button-sm mt-1"
                                            @click="removeFromCart(index)"
                                            type="button"
                                        />
                                    </div>
                                </div>

                                <div
                                    v-if="!cartItems.length"
                                    class="text-center text-xs text-slate-400 py-6"
                                >
                                    No items in order yet
                                </div>
                            </div>

                            <!-- DISCOUNT CARD -->
                            <div
                                class="mb-3 p-3 rounded-2xl bg-gradient-to-r from-violet-500 to-fuchsia-500 text-white text-xs flex items-start gap-3"
                            >
                                <div
                                    class="w-9 h-9 rounded-full bg-white/15 flex items-center justify-center shrink-0"
                                >
                                    <i class="pi pi-percentage text-base"></i>
                                </div>
                                <div class="flex-1">
                                    <div
                                        class="flex justify-between items-center mb-1"
                                    >
                                        <span class="font-semibold text-sm"
                                            >Discount</span
                                        >
                                        <span
                                            v-if="orderDiscount > 0"
                                            class="font-semibold"
                                        >
                                            -{{ orderDiscount.toFixed(2) }}
                                        </span>
                                    </div>

                                    <div
                                        class="flex items-center gap-2 mb-1 min-w-0"
                                    >
                                        <Dropdown
                                            v-model="discountMode"
                                            :options="[
                                                {
                                                    label: 'None',
                                                    value: 'none',
                                                },
                                                {
                                                    label: 'Percent %',
                                                    value: 'percent',
                                                },
                                                {
                                                    label: 'Fixed',
                                                    value: 'fixed',
                                                },
                                            ]"
                                            option-label="label"
                                            option-value="value"
                                            class="flex-1 min-w-0 text-xs"
                                        />

                                        <InputNumber
                                            v-model="discountValue"
                                            :min="0"
                                            class="w-24 shrink-0"
                                            inputClass="!text-xs !w-full"
                                            :suffix="
                                                discountMode === 'percent'
                                                    ? '%'
                                                    : ''
                                            "
                                            :disabled="discountMode === 'none'"
                                        />
                                    </div>

                                    <p class="text-[11px] text-white/80">
                                        Example: 5% for loyalty customers or a
                                        fixed voucher amount.
                                    </p>
                                </div>
                            </div>

                            <!-- totals -->
                            <div
                                class="border-t border-slate-200 pt-3 space-y-1"
                            >
                                <div
                                    class="flex items-center justify-between text-xs text-slate-500"
                                >
                                    <span>Subtotal</span>
                                    <span class="font-medium text-slate-700">{{
                                        subtotal.toFixed(2)
                                    }}</span>
                                </div>
                                <div
                                    class="flex items-center justify-between text-xs text-slate-500"
                                >
                                    <span>Discount</span>
                                    <span class="font-medium text-rose-600"
                                        >-{{ discountTotal.toFixed(2) }}</span
                                    >
                                </div>
                                <div
                                    class="flex items-center justify-between text-xs text-slate-500"
                                >
                                    <span>Tax</span>
                                    <span class="font-medium text-slate-700">{{
                                        taxTotal.toFixed(2)
                                    }}</span>
                                </div>

                                <div
                                    class="flex items-center justify-between text-sm mt-2"
                                >
                                    <span class="font-semibold text-slate-800">
                                        Total Payable
                                    </span>
                                    <span
                                        class="text-lg font-bold text-emerald-600"
                                    >
                                        {{ total.toFixed(2) }}
                                    </span>
                                </div>
                            </div>

                            <!-- multiple payments -->
                            <div class="mt-3 space-y-2">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs text-slate-500">
                                        Payments
                                    </span>
                                    <Button
                                        icon="pi pi-plus"
                                        class="p-button-text p-button-sm"
                                        label="Add"
                                        @click="addPaymentRow"
                                        type="button"
                                    />
                                </div>

                                <div class="space-y-2 max-h-40 overflow-y-auto">
                                    <div
                                        v-for="(row, index) in payments"
                                        :key="row.id"
                                        class="flex items-center gap-2"
                                    >
                                        <Dropdown
                                            v-model="row.payment_method_id"
                                            :options="paymentMethods"
                                            option-label="name"
                                            option-value="id"
                                            placeholder="Method"
                                            class="flex-1 text-xs"
                                        />
                                        <InputNumber
                                            v-model="row.amount"
                                            :min="0"
                                            class="w-28"
                                            input-class="!text-xs"
                                        />
                                        <Button
                                            icon="pi pi-trash"
                                            class="p-button-text p-button-danger p-button-sm"
                                            @click="removePaymentRow(index)"
                                            type="button"
                                        />
                                    </div>
                                </div>

                                <div
                                    class="flex items-center justify-between text-xs text-slate-500 mt-1"
                                >
                                    <span>Total Paid</span>
                                    <span class="font-semibold text-slate-800">
                                        {{ totalPaid.toFixed(2) }}
                                    </span>
                                </div>
                                <div
                                    class="flex items-center justify-between text-xs text-slate-500"
                                >
                                    <span>Change</span>
                                    <span class="font-semibold text-slate-800">
                                        {{ change.toFixed(2) }}
                                    </span>
                                </div>
                            </div>

                            <Button
                                label="Process Order"
                                icon="pi pi-check"
                                class="w-full mt-4"
                                :disabled="!cartItems.length || !posSession"
                                @click="submitOrder"
                                type="button"
                            />
                        </div>
                    </aside>
                </div>
            </main>
        </div>
    </AuthenticatedLayout>
</template>
