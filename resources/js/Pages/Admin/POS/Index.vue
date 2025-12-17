<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { router } from "@inertiajs/vue3";
import { useToast } from "primevue/usetoast";
import { computed, onMounted, onUnmounted, ref, watch } from "vue";

import { resolveImagePath } from "@/Helpers/imageHelper";

// PrimeVue
import SessionBar from "@/Components/POS/SessionBar.vue";
import Badge from "primevue/badge";
import Button from "primevue/button";
import Dialog from "primevue/dialog";
import Dropdown from "primevue/dropdown";
import InputGroup from "primevue/inputgroup";
import InputGroupAddon from "primevue/inputgroupaddon";
import InputNumber from "primevue/inputnumber";
import InputText from "primevue/inputtext";

const props = defineProps({
    products: { type: Array, default: () => [] },
    customers: { type: Array, default: () => [] },
    paymentMethods: { type: Array, default: () => [] },
    currentSession: { type: Object, default: null },
    warehouses: { type: Array, default: () => [] },
    branches: { type: Array, default: () => [] },
});

const toast = useToast();

// session + clock
const posSession = ref(props.currentSession);
const now = ref(new Date());
let timer = null;

onMounted(() => {
    timer = setInterval(() => (now.value = new Date()), 1000);
});
onUnmounted(() => {
    if (timer) clearInterval(timer);
});

// search
const search = ref("");
const selectedCustomerId = ref(null);

// cart
const cartItems = ref([]);

// order discount
const discountMode = ref("none"); // none | percent | fixed
const discountValue = ref(0);

// -----------------------------
// Price helpers
// -----------------------------
const n = (v) => Number(v || 0);

const getProductUnitPrice = (p) => n(p.base_price);

const getProductDiscountPrice = (p) => {
    const up = getProductUnitPrice(p);
    const dp = n(p.base_discount_price);
    return dp > 0 && dp < up ? dp : null;
};

const productDiscountPercent = (p) => {
    const up = getProductUnitPrice(p);
    const dp = getProductDiscountPrice(p);
    if (!dp || up <= 0) return 0;
    return Math.round(((up - dp) / up) * 100);
};

const getVariationUnitPrice = (v) => n(v.price);

const getVariationDiscountPrice = (v) => {
    const up = getVariationUnitPrice(v);
    const dp = n(v.discount_price);
    return dp > 0 && dp < up ? dp : null;
};

const variationDiscountPercent = (v) => {
    const up = getVariationUnitPrice(v);
    const dp = getVariationDiscountPrice(v);
    if (!dp || up <= 0) return 0;
    return Math.round(((up - dp) / up) * 100);
};

// -----------------------------
// filtered products
// -----------------------------
const filteredProducts = computed(() => {
    let list = props.products || [];
    if (!search.value) return list;

    const t = search.value.toLowerCase();
    return list.filter(
        (p) =>
            (p.name && p.name.toLowerCase().includes(t)) ||
            (p.sku && p.sku.toLowerCase().includes(t)) ||
            (p.barcode && p.barcode.toLowerCase().includes(t))
    );
});

// -----------------------------
// variation picker dialog
// -----------------------------
const showVariationDialog = ref(false);
const dialogProduct = ref(null);
const selectedVariationId = ref(null);

const dialogVariations = computed(() => {
    const p = dialogProduct.value;
    if (!p) return [];
    return (p.variations || []).map((v) => ({
        label: v.sku || `Variation #${v.id}`,
        value: v.id,
        raw: v,
    }));
});

const selectedVariation = computed(() => {
    const p = dialogProduct.value;
    if (!p || !selectedVariationId.value) return null;
    return (p.variations || []).find((v) => v.id === selectedVariationId.value);
});

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

// -----------------------------
// Add to cart
// - simple product: add directly
// - variable product: open dialog to choose variation
// -----------------------------
function addProduct(product) {
    if (!ensureSession()) return;

    if (product.type === "variable") {
        dialogProduct.value = product;
        selectedVariationId.value = null;
        showVariationDialog.value = true;
        return;
    }

    addSimpleToCart(product);
}

function addSimpleToCart(product) {
    const sellPrice =
        getProductDiscountPrice(product) ?? getProductUnitPrice(product);

    const found = cartItems.value.find(
        (i) => i.product_id === product.id && !i.variation_id
    );

    if (found) {
        found.quantity += 1;
        return;
    }

    cartItems.value.push({
        product_id: product.id,
        variation_id: null,
        name: product.name,
        sku: product.sku,
        unit_price: getProductUnitPrice(product), // original
        discount_price: getProductDiscountPrice(product), // default discount price (nullable)
        sell_price: sellPrice, // price used for selling
        quantity: 1,

        // optional manual extras
        line_discount_amount: 0,
        tax_amount: 0,
    });
}

function confirmAddVariation() {
    const p = dialogProduct.value;
    const v = selectedVariation.value;

    if (!p || !v) {
        toast.add({
            severity: "warn",
            summary: "Select variation",
            detail: "Please select a variation first",
            life: 2000,
        });
        return;
    }

    const sellPrice = getVariationDiscountPrice(v) ?? getVariationUnitPrice(v);

    const found = cartItems.value.find(
        (i) => i.product_id === p.id && i.variation_id === v.id
    );

    if (found) {
        found.quantity += 1;
    } else {
        cartItems.value.push({
            product_id: p.id,
            variation_id: v.id,

            name: `${p.name} (${v.sku})`,
            sku: v.sku || p.sku,

            unit_price: getVariationUnitPrice(v),
            discount_price: getVariationDiscountPrice(v),
            sell_price: sellPrice,

            quantity: 1,
            line_discount_amount: 0,
            tax_amount: 0,
        });
    }

    showVariationDialog.value = false;
    dialogProduct.value = null;
    selectedVariationId.value = null;
}

function removeFromCart(index) {
    cartItems.value.splice(index, 1);
}

// -----------------------------
// Totals
// -----------------------------
const subtotal = computed(() =>
    cartItems.value.reduce((sum, item) => {
        const price = n(item.sell_price || item.unit_price);
        return sum + price * n(item.quantity);
    }, 0)
);

const lineDiscountTotal = computed(() =>
    cartItems.value.reduce((sum, item) => sum + n(item.line_discount_amount), 0)
);

const orderDiscount = computed(() => {
    const base = subtotal.value;
    const val = n(discountValue.value);

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
    cartItems.value.reduce((sum, item) => sum + n(item.tax_amount), 0)
);

const total = computed(
    () => subtotal.value - discountTotal.value + taxTotal.value
);

// -----------------------------
// Payments
// -----------------------------
let paymentRowId = 1;
const payments = ref([
    { id: paymentRowId++, payment_method_id: null, amount: 0 },
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
        payments.value[0].payment_method_id = null;
        payments.value[0].amount = 0;
        return;
    }
    payments.value.splice(index, 1);
}

const totalPaid = computed(() =>
    payments.value.reduce((sum, row) => sum + n(row.amount), 0)
);

const change = computed(() => Math.max(0, totalPaid.value - total.value));

// reset payments if total changes and paid became smaller (optional)
watch(total, () => {
    // keep user input; no hard reset
});

// -----------------------------
// Submit
// -----------------------------
function submitOrder(action = "complete") {
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

    // ✅ payments required only for complete / complete_print
    const validPayments = payments.value.filter(
        (p) => p.payment_method_id && n(p.amount) > 0
    );

    if (action !== "draft") {
        if (!validPayments.length) {
            toast.add({
                severity: "warn",
                summary: "Payment required",
                detail: "Add at least one payment",
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
    }

    const payload = {
        action, // ✅ REQUIRED BY BACKEND

        pos_session_id: posSession.value.id,
        branch_id: posSession.value.branch_id,
        warehouse_id: posSession.value.warehouse_id,
        customer_id: selectedCustomerId.value,

        items: cartItems.value.map((item) => ({
            product_id: item.product_id,
            variation_id: item.variation_id,
            quantity: item.quantity,
            unit_price: item.sell_price,
            discount_amount: item.line_discount_amount || 0,
            tax_amount: item.tax_amount || 0,
        })),

        payments:
            action === "draft"
                ? [] // ✅ no payment needed for draft
                : validPayments.map((p) => ({
                      payment_method_id: p.payment_method_id,
                      amount: p.amount,
                  })),

        order_discount_type: discountMode.value,
        order_discount_value: discountValue.value,
        notes: null,
    };

    router.post(route("pos.orders.store"), payload, {
        preserveScroll: true,
        onSuccess: (page) => {
            toast.add({
                severity: "success",
                summary: "Success",
                detail: "Order saved",
                life: 2000,
            });

            // ✅ If you return order_id from backend, redirect to invoice for print
            // Example: page.props.flash?.order_id
            if (action === "complete_print") {
                const orderId = page?.props?.flash?.order_id;
                if (orderId) router.visit(route("pos.orders.invoice", orderId));
            }

            cartItems.value = [];
            selectedCustomerId.value = null;
            discountMode.value = "none";
            discountValue.value = 0;
            payments.value = [
                { id: paymentRowId++, payment_method_id: null, amount: 0 },
            ];
        },
    });
}
</script>

<template>
    <AuthenticatedLayout>
        <div class="h-[95vh] flex">
            <main
                class="flex-1 flex flex-col overflow-hidden bg-white rounded-2xl shadow-sm border border-slate-100 p-4"
            >
                <!-- Header -->
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
                        <div class="flex items-center justify-between mb-3">
                            <!-- <div>
                                <h1 class="text-lg font-semibold">
                                    Point of Sale
                                </h1>
                                <p class="text-xs text-gray-500">
                                    {{
                                        props.currentSession
                                            ? "Session Open"
                                            : "No active session"
                                    }}
                                </p>
                            </div> -->

                            <SessionBar
                                :currentSession="props.currentSession"
                                :branches="props.branches"
                                :warehouses="props.warehouses"
                            />
                        </div>
                        <span
                            class="px-3 py-1 rounded-full text-lg mb-3 bg-emerald-50 text-emerald-600 flex items-center gap-2"
                        >
                            <i class="pi pi-clock text-4xl" />
                            {{ now.toLocaleTimeString() }}
                        </span>
                    </div>
                </header>

                <!-- BODY -->
                <div class="flex-1 flex overflow-hidden">
                    <!-- PRODUCTS -->
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
                                    Click a product card to add it. Variable
                                    products will ask for variation.
                                </p>
                            </div>

                            <div class="flex items-center gap-3">
                                <InputGroup class="w-full max-w-md">
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
                                    @click="addProduct(product)"
                                >
                                    <div
                                        class="relative rounded-xl bg-white mb-3 h-28 flex items-center justify-center overflow-hidden"
                                    >
                                        <span
                                            v-if="!product.thumbnail"
                                            class="text-slate-300 text-xs uppercase tracking-wide"
                                        >
                                            Image
                                        </span>

                                        <img
                                            v-else
                                            :src="
                                                resolveImagePath(
                                                    product.thumbnail
                                                )
                                            "
                                            alt=""
                                            class="h-full object-contain"
                                        />

                                        <!-- Type badge -->
                                        <div class="absolute top-2 left-2">
                                            <Badge
                                                :severity="
                                                    product.type === 'variable'
                                                        ? 'info'
                                                        : 'secondary'
                                                "
                                                :value="product.type"
                                            />
                                        </div>

                                        <!-- Discount badge (simple product) -->
                                        <div
                                            v-if="
                                                getProductDiscountPrice(product)
                                            "
                                            class="absolute top-2 right-2 bg-rose-600 text-white text-[11px] font-semibold px-2 py-1 rounded-full"
                                        >
                                            -{{
                                                productDiscountPercent(product)
                                            }}%
                                        </div>
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
                                                class="text-xs text-slate-400 mt-0.5 truncate"
                                            >
                                                {{ product.sku }}
                                            </div>
                                        </div>

                                        <div
                                            class="flex items-end justify-between mt-1 gap-2"
                                        >
                                            <div class="min-w-0">
                                                <div
                                                    class="text-base font-bold text-emerald-600"
                                                >
                                                    {{
                                                        (
                                                            getProductDiscountPrice(
                                                                product
                                                            ) ??
                                                            getProductUnitPrice(
                                                                product
                                                            )
                                                        ).toFixed(2)
                                                    }}
                                                </div>

                                                <div
                                                    v-if="
                                                        getProductDiscountPrice(
                                                            product
                                                        )
                                                    "
                                                    class="text-xs text-slate-400 flex items-center gap-2"
                                                >
                                                    <span class="line-through">
                                                        {{
                                                            getProductUnitPrice(
                                                                product
                                                            ).toFixed(2)
                                                        }}
                                                    </span>
                                                    <span
                                                        class="text-rose-600 font-semibold"
                                                    >
                                                        Save
                                                        {{
                                                            productDiscountPercent(
                                                                product
                                                            )
                                                        }}%
                                                    </span>
                                                </div>
                                            </div>

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

                    <!-- ORDER PANEL -->
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
                                    optionLabel="name"
                                    optionValue="id"
                                    placeholder="Walk-in Customer"
                                    showClear
                                    class="w-full text-sm"
                                />
                            </div>

                            <!-- cart items -->
                            <div
                                class="flex-1 overflow-y-auto space-y-3 pr-1 mb-3"
                            >
                                <div
                                    v-for="(item, index) in cartItems"
                                    :key="`${item.product_id}-${
                                        item.variation_id || 'simple'
                                    }`"
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

                                        <!-- show discount info -->
                                        <div
                                            class="text-[11px] text-slate-500 mb-1"
                                        >
                                            <span
                                                class="font-semibold text-slate-700"
                                            >
                                                {{
                                                    Number(
                                                        item.sell_price || 0
                                                    ).toFixed(2)
                                                }}
                                            </span>
                                            <span
                                                v-if="item.discount_price"
                                                class="ml-2 text-rose-600"
                                            >
                                                (was
                                                {{
                                                    Number(
                                                        item.unit_price || 0
                                                    ).toFixed(2)
                                                }})
                                            </span>
                                        </div>

                                        <div class="flex items-center gap-2">
                                            <InputNumber
                                                v-model="item.quantity"
                                                :min="1"
                                                inputClass="!w-14 !text-xs"
                                                showButtons
                                                buttonLayout="horizontal"
                                                incrementButtonIcon="pi pi-plus"
                                                decrementButtonIcon="pi pi-minus"
                                                incrementButtonClass="p-button-text p-button-sm"
                                                decrementButtonClass="p-button-text p-button-sm"
                                            />

                                            <span
                                                class="text-[11px] text-slate-400"
                                            >
                                                x
                                                {{
                                                    Number(
                                                        item.sell_price || 0
                                                    ).toFixed(2)
                                                }}
                                            </span>
                                        </div>
                                    </div>

                                    <div class="text-right ml-2">
                                        <div
                                            class="text-xs font-semibold text-slate-800"
                                        >
                                            {{
                                                (
                                                    Number(
                                                        item.sell_price || 0
                                                    ) *
                                                    Number(item.quantity || 0)
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

                            <!-- ORDER DISCOUNT -->
                            <div
                                class="mb-3 p-3 rounded-2xl bg-white border border-slate-200"
                            >
                                <div
                                    class="flex items-center justify-between mb-2"
                                >
                                    <span
                                        class="text-sm font-semibold text-slate-800"
                                        >Order Discount</span
                                    >
                                    <span
                                        v-if="orderDiscount > 0"
                                        class="text-sm font-semibold text-rose-600"
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
                                            { label: 'None', value: 'none' },
                                            {
                                                label: 'Percent %',
                                                value: 'percent',
                                            },
                                            { label: 'Fixed', value: 'fixed' },
                                        ]"
                                        optionLabel="label"
                                        optionValue="value"
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
                                    <span class="font-semibold text-slate-800"
                                        >Total Payable</span
                                    >
                                    <span
                                        class="text-lg font-bold text-emerald-600"
                                        >{{ total.toFixed(2) }}</span
                                    >
                                </div>
                            </div>

                            <!-- payments -->
                            <div class="mt-3 space-y-2">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs text-slate-500"
                                        >Payments</span
                                    >
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
                                            optionLabel="name"
                                            optionValue="id"
                                            placeholder="Method"
                                            class="flex-1 text-xs"
                                        />
                                        <InputNumber
                                            v-model="row.amount"
                                            :min="0"
                                            class="w-28"
                                            inputClass="!text-xs"
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
                                    <span
                                        class="font-semibold text-slate-800"
                                        >{{ totalPaid.toFixed(2) }}</span
                                    >
                                </div>
                                <div
                                    class="flex items-center justify-between text-xs text-slate-500"
                                >
                                    <span>Change</span>
                                    <span
                                        class="font-semibold text-slate-800"
                                        >{{ change.toFixed(2) }}</span
                                    >
                                </div>
                            </div>

                            <div class="grid grid-cols-1 gap-2 mt-4">
                                <Button
                                    label="Save Draft"
                                    icon="pi pi-save"
                                    class="w-full"
                                    severity="war"
                                    :disabled="!cartItems.length || !posSession"
                                    @click="submitOrder('draft')"
                                    type="button"
                                />

                                <Button
                                    label="Complete"
                                    icon="pi pi-check"
                                    class="w-full"
                                    severity="info"
                                    :disabled="!cartItems.length || !posSession"
                                    @click="submitOrder('complete')"
                                    type="button"
                                />

                                <Button
                                    label="Complete & Print"
                                    icon="pi pi-print"
                                    class="w-full"
                                    :disabled="!cartItems.length || !posSession"
                                    @click="submitOrder('complete_print')"
                                    type="button"
                                    severity="success"
                                />
                            </div>
                        </div>
                    </aside>
                </div>
            </main>
        </div>

        <!-- Variation Dialog -->
        <Dialog
            v-model:visible="showVariationDialog"
            modal
            header="Select Variation"
            :style="{ width: '420px' }"
        >
            <div v-if="dialogProduct" class="space-y-3">
                <div class="text-sm font-semibold text-slate-800">
                    {{ dialogProduct.name }}
                </div>
                <div class="text-xs text-slate-500">
                    Choose a variation to add to cart.
                </div>

                <Dropdown
                    v-model="selectedVariationId"
                    :options="dialogVariations"
                    optionLabel="label"
                    optionValue="value"
                    placeholder="Select variation"
                    class="w-full"
                    showClear
                />

                <div
                    v-if="selectedVariation"
                    class="p-3 rounded-xl border border-slate-200 bg-slate-50"
                >
                    <div class="text-xs text-slate-500">Price</div>
                    <div class="flex items-end gap-2">
                        <div class="text-lg font-bold text-emerald-600">
                            {{
                                (
                                    getVariationDiscountPrice(
                                        selectedVariation
                                    ) ??
                                    getVariationUnitPrice(selectedVariation)
                                ).toFixed(2)
                            }}
                        </div>

                        <div
                            v-if="getVariationDiscountPrice(selectedVariation)"
                            class="text-sm text-slate-400 line-through"
                        >
                            {{
                                getVariationUnitPrice(
                                    selectedVariation
                                ).toFixed(2)
                            }}
                        </div>

                        <div
                            v-if="getVariationDiscountPrice(selectedVariation)"
                            class="text-xs text-rose-600 font-semibold"
                        >
                            -{{ variationDiscountPercent(selectedVariation) }}%
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-2 pt-2">
                    <Button
                        label="Cancel"
                        class="p-button-text"
                        @click="showVariationDialog = false"
                    />
                    <Button
                        label="Add to Cart"
                        icon="pi pi-check"
                        :disabled="!selectedVariationId"
                        @click="confirmAddVariation"
                    />
                </div>
            </div>
        </Dialog>
    </AuthenticatedLayout>
</template>
