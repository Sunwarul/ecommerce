<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { router, usePage } from "@inertiajs/vue3";
import { useToast } from "primevue/usetoast";
import { computed, onMounted, onUnmounted, ref, watch } from "vue";

import { resolveImagePath } from "@/Helpers/imageHelper";

// PrimeVue
import SessionBar from "@/Components/POS/SessionBar.vue";
import AutoComplete from "primevue/autocomplete";
import Badge from "primevue/badge";
import Button from "primevue/button";
import Dialog from "primevue/dialog";
import Dropdown from "primevue/dropdown";
import InputGroup from "primevue/inputgroup";
import InputGroupAddon from "primevue/inputgroupaddon";
import InputNumber from "primevue/inputnumber";
import InputText from "primevue/inputtext";
import Textarea from "primevue/textarea";

const props = defineProps({
    products: { type: Array, default: () => [] },
    customers: { type: Array, default: () => [] }, // not required now, but ok
    paymentMethods: { type: Array, default: () => [] },
    currentSession: { type: Object, default: null },
    warehouses: { type: Array, default: () => [] },
    branches: { type: Array, default: () => [] },
    order: { type: Object, default: null }, // ✅ Edit Draft Order
});

const toast = useToast();
const page = usePage();

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




// product search
// product search
const search = ref("");
const warranty_info = ref("");


// -----------------------------
// ✅ Customer Remote Search (Backend)
// -----------------------------
const selectedCustomer = ref(null); // object or null (walk-in)
const customerSuggestions = ref([]);
const customerLoading = ref(false);

let customerSearchTimer = null;
let customerAbort = null;

function customerLabel(c) {
    if (!c) return "Walk-in customer";
    const phone = c.phone ? ` • ${c.phone}` : "";
    const email = c.email ? ` • ${c.email}` : "";
    return `${c.name || "Customer"}${phone}${email}`;
}

// Called by PrimeVue AutoComplete
function onCustomerComplete(event) {
    const q = (event.query || "").trim();

    // allow clearing suggestions when empty
    if (!q) {
        customerSuggestions.value = [];
        return;
    }

    // debounce
    if (customerSearchTimer) clearTimeout(customerSearchTimer);
    customerSearchTimer = setTimeout(() => {
        fetchCustomers(q);
    }, 300);
}

watch(selectedCustomer, (v) => console.log("selectedCustomer =", v));

async function fetchCustomers(q) {
    try {
        customerLoading.value = true;

        // cancel previous request
        if (customerAbort) customerAbort.abort();
        customerAbort = new AbortController();

        // ✅ backend route (create this route in Laravel)
        // Example: route('pos.customers.search', { q })
        const url = route("pos.customers.search", { q });

        const res = await fetch(url, {
            method: "GET",
            headers: {
                Accept: "application/json",
                "X-Requested-With": "XMLHttpRequest",
            },
            signal: customerAbort.signal,
        });

        if (!res.ok) throw new Error("Customer search failed");
        const data = await res.json();

        // Expect: { data: [ {id,name,phone,email}, ... ] } OR just array
        customerSuggestions.value = Array.isArray(data)
            ? data
            : data.data || [];
    } catch (e) {
        // ignore abort errors
        if (String(e?.name) === "AbortError") return;

        toast.add({
            severity: "warn",
            summary: "Search error",
            detail: "Could not load customers",
            life: 2000,
        });
    } finally {
        customerLoading.value = false;
    }
}

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

// ✅ Populate if editing draft
watch(() => props.order, (newOrder) => {
    if (newOrder) {
        // Customer
        if (newOrder.customer) {
            selectedCustomer.value = newOrder.customer;
        }
        if (newOrder.warranty_info) {
            warranty_info.value = newOrder.warranty_info;
        }

        let totalLineDiscount = 0;

        // Cart Items
        if (newOrder.items && Array.isArray(newOrder.items)) {
            cartItems.value = newOrder.items.map((i) => {
                const qty = Number(i.quantity) || 1;
                const unitPrice = Number(i.unit_price) || 0;

                // if we strictly assume unit_price is what was sold
                const sellPrice = unitPrice;

                // discount_amount in table is total for the line? or per unit? 
                // Usually line_total = (unit * qty) - discount + tax.
                // But here let's assume discount_amount is TOTAL line discount
                const lineDisc = Number(i.discount_amount) || 0;
                const lineTax = Number(i.tax_amount) || 0;

                totalLineDiscount += lineDisc;

                return {
                    product_id: i.product_id,
                    variation_id: i.variation_id,
                    name: i.name,
                    sku: i.sku,
                    unit_price: unitPrice,
                    sell_price: sellPrice,
                    quantity: qty,

                    // we can't easily revert to original base price if not stored
                    // so we assume discount_price is 0 for editing purpose unless we refetch product
                    discount_price: 0,

                    line_discount_amount: lineDisc,
                    tax_amount: lineTax,
                };
            });
        }

        // Order Discount
        // Total discount stored in order = sum(line discounts) + order_discount
        const totalOrderDiscount = Number(newOrder.discount_amount) || 0;
        const diff = totalOrderDiscount - totalLineDiscount;

        if (diff > 0.01) {
            discountMode.value = 'fixed';
            discountValue.value = diff;
        } else {
            discountMode.value = 'none';
            discountValue.value = 0;
        }
    }
}, { immediate: true });

function formatCurrency(value) {
    if (value === undefined || value === null) {
        return "";
    }
    return new Intl.NumberFormat("en-US", {
        style: "currency",
        currency: page.props.currency || "BDT",
    }).format(value);
}
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
        unit_price: getProductUnitPrice(product),
        discount_price: getProductDiscountPrice(product),
        sell_price: sellPrice,
        quantity: 1,
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
// Payments (✅ partial allowed)
// -----------------------------
let paymentRowId = 1;
const payments = ref([
    {
        id: paymentRowId++,
        payment_method_id: null,
        amount: 0,

        transaction_ref: null,
        notes: null,

        meta: {
            customer_bank_name: null,
            customer_account_no: null,
            received_to_bank_account_id: null,
            txn_ref: null,
        },
    },
]);

function blankPaymentRow() {
    return {
        id: paymentRowId++,
        payment_method_id: null,
        amount: 0,
        transaction_ref: null,
        notes: null,
        meta: {
            customer_bank_name: null,
            customer_account_no: null,
            received_to_bank_account_id: null,
            txn_ref: null,
        },
    };
}

function addPaymentRow() {
    payments.value.push(blankPaymentRow());
}

function removePaymentRow(index) {
    if (payments.value.length === 1) {
        payments.value = [blankPaymentRow()];
        return;
    }
    payments.value.splice(index, 1);
}

const totalPaid = computed(() =>
    payments.value.reduce((sum, row) => sum + n(row.amount), 0)
);
const due = computed(() => Math.max(0, total.value - totalPaid.value));
const change = computed(() => Math.max(0, totalPaid.value - total.value));

// --- helpers for Bank method detection ---
const bankMethodIds = computed(() => {
    // detect by name (adjust if your method name is different)
    const list = props.paymentMethods || [];
    return list
        .filter((m) => (m.name || "").toLowerCase().includes("bank"))
        .map((m) => m.id);
});

function isBankRow(row) {
    if (!row?.payment_method_id) return false;
    return bankMethodIds.value.includes(row.payment_method_id);
}

// clear meta when method changes away from Bank
watch(
    () => payments.value.map(p => p.payment_method_id),
    () => {
        payments.value.forEach(row => {
            if (!isBankRow(row)) {
                row.meta = null;
            }
        });
    }
);


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

    const validPayments = payments.value.filter(
        (p) => p.payment_method_id && n(p.amount) > 0
    );

    // ✅ payments required only for complete / complete_print
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
        if (totalPaid.value <= 0) {
            toast.add({
                severity: "warn",
                summary: "Payment required",
                detail: "Payment must be greater than 0",
                life: 2200,
            });
            return;
        }
        // ✅ no "insufficient" block; partial is allowed
    }

    const payload = {
        action,

        pos_session_id: posSession.value.id,
        branch_id: posSession.value.branch_id,
        warehouse_id: posSession.value.warehouse_id,

        // ✅ allow order without customer
        customer_id: selectedCustomer.value?.id ?? null,

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
                ? []
                : validPayments.map((p) => ({
                    payment_method_id: p.payment_method_id,
                    amount: p.amount,

                    transaction_ref: p.transaction_ref || null,
                    notes: p.notes || null,

                    meta: isBankRow(p)
                        ? {
                            customer_bank_name:
                                p.meta?.customer_bank_name || null,
                            customer_account_no:
                                p.meta?.customer_account_no || null,
                            received_to_bank_account_id:
                                p.meta?.received_to_bank_account_id || null,
                            txn_ref: p.meta?.txn_ref || null,
                        }
                        : null,
                })),

        order_discount_type: discountMode.value,
        order_discount_value: discountValue.value,

        // ✅ optional helpers for backend
        total_amount: total.value,
        paid_amount: totalPaid.value,
        due_amount: due.value,

        notes: null,
        warranty_info: warranty_info.value,
    };

    if (props.order?.id && props.order.status === 'draft') {
        router.put(route("pos.orders.update", props.order.id), payload, {
            preserveScroll: true,
            onSuccess: handleSuccess,
        });
    } else {
        router.post(route("pos.orders.store"), payload, {
            preserveScroll: true,
            onSuccess: handleSuccess,
        });
    }
}

function handleSuccess(page) {
    toast.add({
        severity: "success",
        summary: "Success",
        detail: "Order saved",
        life: 2000,
    });

    // Checking flash or prop? router.reload might clear flash.
    // But usually we get the result.
    // If action was complete_print, redirect happens in controller likely, or handled here.

    // Logic handled in controller redirect usually.
    // But if we stayed on page (draft save), clear cart.

    // However, if we edited a draft, we might want to stay or go to keys?
    // If user clicked "Save Draft", we stay?
    cartItems.value = [];
    selectedCustomer.value = null;
    discountMode.value = "none";
    discountValue.value = 0;
    warranty_info.value = "";
    payments.value = [
        { id: paymentRowId++, payment_method_id: null, amount: 0 },
    ];
}
</script>

<template>
    <AuthenticatedLayout>
        <div class="h-[95vh] flex overflow-y-auto">
            <main
                class="flex-1 flex flex-col overflow-hidden bg-white rounded-2xl shadow-sm border border-slate-100 p-4">
                <!-- Header -->
                <header class="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-6">
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
                            <SessionBar :currentSession="props.currentSession" :branches="props.branches"
                                :warehouses="props.warehouses" />
                        </div>

                        <span
                            class="px-3 py-1 rounded-full text-lg mb-3 bg-emerald-50 text-emerald-600 flex items-center gap-2">
                            <i class="pi pi-clock text-4xl" />
                            {{ now.toLocaleTimeString() }}
                        </span>
                    </div>
                </header>

                <!-- BODY -->
                <div class="flex-1 flex flex-col lg:flex-row overflow-hidden">
                    <!-- PRODUCTS -->
                    <section class="flex-1 p-5 overflow-y-auto">
                        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3 mb-4">
                            <div>
                                <h2 class="text-xl font-semibold text-slate-800">
                                    Product Catalog
                                </h2>
                                <p class="text-xs text-slate-400">
                                    Click a product card to add it. Variable
                                    products will ask for variation.
                                </p>
                            </div>

                            <div class="flex items-center gap-3">
                                <InputGroup class="w-full max-w-4xl">
                                    <InputText type="search" v-model="search"
                                        placeholder="Search by product name, SKU, barcode, etc"
                                        class="w-full !w-[400px]" />
                                    <InputGroupAddon><i class="pi pi-search"></i></InputGroupAddon>
                                </InputGroup>
                            </div>
                        </div>

                        <div class="grid gap-4 grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
                            <div v-for="product in filteredProducts" :key="product.id"
                                class="col-12 sm:col-6 md:col-4 xl:col-3">
                                <div class="rounded-2xl shadow-sm border border-slate-300 bg-slate-100 p-3 flex flex-col h-full cursor-pointer hover:shadow-md hover:-translate-y-0.5 transition"
                                    @click="addProduct(product)">
                                    <div
                                        class="relative rounded-xl bg-white mb-3 h-28 flex items-center justify-center overflow-hidden">
                                        <span v-if="!product.thumbnail"
                                            class="text-slate-300 text-xs uppercase tracking-wide">Image</span>

                                        <img v-else :src="resolveImagePath(
                                            product.thumbnail
                                        )
                                            " alt="" class="h-full object-contain" />

                                        <div class="absolute top-2 left-2">
                                            <Badge :severity="product.type === 'variable'
                                                ? 'info'
                                                : 'secondary'
                                                " :value="product.type" />
                                        </div>

                                        <div v-if="
                                            getProductDiscountPrice(product)
                                        "
                                            class="absolute top-2 right-2 bg-rose-600 text-white text-[11px] font-semibold px-2 py-1 rounded-full">
                                            -{{
                                                productDiscountPercent(product)
                                            }}%
                                        </div>
                                    </div>

                                    <div class="flex-1 flex flex-col justify-between gap-2">
                                        <div>
                                            <div class="text-sm font-semibold text-slate-800 truncate">
                                                {{ product.name }}
                                            </div>
                                            <div class="text-xs text-slate-400 mt-0.5 truncate">
                                                {{ product.sku }}
                                            </div>
                                        </div>

                                        <div class="flex items-end justify-between mt-1 gap-2">
                                            <div class="min-w-0">
                                                <div class="text-base font-bold text-emerald-600">
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

                                                <div v-if="
                                                    getProductDiscountPrice(
                                                        product
                                                    )
                                                " class="text-xs text-slate-400 flex items-center gap-2">
                                                    <span class="line-through">{{
                                                        getProductUnitPrice(
                                                            product
                                                        ).toFixed(2)
                                                    }}</span>
                                                    <span class="text-rose-600 font-semibold">Save
                                                        {{
                                                            productDiscountPercent(
                                                                product
                                                            )
                                                        }}%</span>
                                                </div>
                                            </div>

                                            <Button icon="pi pi-plus"
                                                class="p-button-rounded p-button-sm p-button-success" type="button" />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div v-if="!filteredProducts.length"
                                class="col-12 text-center text-slate-400 text-sm py-10">
                                No products found
                            </div>
                        </div>
                    </section>

                    <!-- ORDER PANEL -->
                    <aside
                        class="w-full lg:w-96 bg-slate-50 border-l border-slate-200 p-4 flex flex-col overflow-y-auto h-1/2 lg:h-auto border-t lg:border-t-0">
                        <div class="flex flex-col flex-1">
                            <!-- header -->
                            <div class="flex items-center justify-between mb-3 pb-3 border-b border-slate-100">
                                <div>
                                    <h3 class="text-base font-semibold text-slate-800">
                                        Order Summary
                                    </h3>
                                    <p class="text-xs text-slate-400">
                                        {{
                                            selectedCustomer
                                                ? selectedCustomer.name
                                                : "Walk-in customer"
                                        }}
                                    </p>
                                </div>

                                <span
                                    class="px-2.5 py-1 rounded-full text-xs bg-emerald-50 text-emerald-600 font-medium">
                                    {{ cartItems.length }} items
                                </span>
                            </div>

                            <!-- ✅ customer remote search -->
                            <div class="mb-4">
                                <p class="text-xs text-slate-500 mb-1">
                                    Customer Search
                                </p>
                                <AutoComplete v-model="selectedCustomer" :suggestions="customerSuggestions"
                                    :optionLabel="customerLabel" class="w-full" inputClass="w-full text-sm"
                                    placeholder="Search by name or phone" :loading="customerLoading"
                                    @complete="onCustomerComplete">
                                    <template #option="slotProps">
                                        <div class="flex flex-col">
                                            <span class="font-medium">{{ slotProps.option.name }}</span>
                                            <span class="text-xs text-gray-500">{{ slotProps.option.phone || 'No phone'
                                                }}</span>
                                        </div>
                                    </template>
                                </AutoComplete>
                                <!-- warranty info -->
                                <div class="mt-2">
                                    <label class="text-xs text-slate-500">Warranty Info</label>
                                    <Textarea v-model="warranty_info" rows="2" class="w-full text-sm mt-1"
                                        placeholder="Warranty details..." />
                                </div>
                            </div>


                            <!-- cart items -->
                            <div class="flex-1 overflow-y-auto space-y-3 pr-1 mb-3">
                                <div v-for="(item, index) in cartItems" :key="`${item.product_id}-${item.variation_id || 'simple'
                                    }`"
                                    class="flex items-start justify-between bg-white border-slate-300 border rounded-xl px-3 py-2">
                                    <div class="flex-1">
                                        <div class="text-xs font-semibold text-slate-800">
                                            {{ item.name }}
                                        </div>
                                        <div class="text-[11px] text-slate-400 mb-1">
                                            {{ item.sku }}
                                        </div>

                                        <div class="text-[11px] text-slate-500 mb-1">
                                            <span class="font-semibold text-slate-700">{{
                                                Number(
                                                    item.sell_price || 0
                                                ).toFixed(2)
                                            }}</span>
                                            <span v-if="item.discount_price" class="ml-2 text-rose-600">
                                                (was
                                                {{
                                                    Number(
                                                        item.unit_price || 0
                                                    ).toFixed(2)
                                                }})
                                            </span>
                                        </div>

                                        <div class="flex items-center gap-2">
                                            <InputNumber v-model="item.quantity" :min="1" inputClass="!w-14 !text-xs"
                                                showButtons buttonLayout="horizontal" incrementButtonIcon="pi pi-plus"
                                                decrementButtonIcon="pi pi-minus"
                                                incrementButtonClass="p-button-text p-button-sm"
                                                decrementButtonClass="p-button-text p-button-sm" />

                                            <span class="text-[11px] text-slate-400">
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
                                        <div class="text-xs font-semibold text-slate-800">
                                            {{
                                                (
                                                    Number(
                                                        item.sell_price || 0
                                                    ) *
                                                    Number(item.quantity || 0)
                                                ).toFixed(2)
                                            }}
                                        </div>

                                        <Button icon="pi pi-trash"
                                            class="p-button-text p-button-danger p-button-sm mt-1"
                                            @click="removeFromCart(index)" type="button" />
                                    </div>
                                </div>

                                <div v-if="!cartItems.length" class="text-center text-xs text-slate-400 py-6">
                                    No items in order yet
                                </div>
                            </div>

                            <!-- ORDER DISCOUNT -->
                            <div class="mb-3 p-3 rounded-2xl bg-white border border-slate-200">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-sm font-semibold text-slate-800">Order Discount</span>
                                    <span v-if="orderDiscount > 0" class="text-sm font-semibold text-rose-600">
                                        -{{ orderDiscount.toFixed(2) }}
                                    </span>
                                </div>

                                <div class="flex items-center gap-2 mb-1 min-w-0">
                                    <Dropdown v-model="discountMode" :options="[
                                        { label: 'None', value: 'none' },
                                        {
                                            label: 'Percent %',
                                            value: 'percent',
                                        },
                                        { label: 'Fixed', value: 'fixed' },
                                    ]" optionLabel="label" optionValue="value" class="flex-1 min-w-0 text-xs" />
                                    <InputNumber v-model="discountValue" :min="0" class="w-24 shrink-0"
                                        inputClass="!text-xs !w-full" :suffix="discountMode === 'percent'
                                            ? '%'
                                            : ''
                                            " :disabled="discountMode === 'none'" />
                                </div>
                            </div>

                            <!-- totals -->
                            <div class="border-t border-slate-200 pt-3 space-y-1">
                                <div class="flex items-center justify-between text-xs text-slate-500">
                                    <span>Subtotal</span>
                                    <span class="font-medium text-slate-700">{{
                                        subtotal.toFixed(2)
                                        }}</span>
                                </div>

                                <div class="flex items-center justify-between text-xs text-slate-500">
                                    <span>Discount</span>
                                    <span class="font-medium text-rose-600">-{{ discountTotal.toFixed(2) }}</span>
                                </div>

                                <div class="flex items-center justify-between text-xs text-slate-500">
                                    <span>Tax</span>
                                    <span class="font-medium text-slate-700">{{
                                        taxTotal.toFixed(2)
                                        }}</span>
                                </div>

                                <div class="flex items-center justify-between text-sm mt-2">
                                    <span class="font-semibold text-slate-800">Total Payable</span>
                                    <span class="text-lg font-bold text-emerald-600">{{ total.toFixed(2) }}</span>
                                </div>
                            </div>

                            <!-- payments -->
                            <div class="mt-3 space-y-2">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs text-slate-500">Payments</span>
                                    <Button icon="pi pi-plus" class="p-button-text p-button-sm" label="Add"
                                        @click="addPaymentRow" type="button" />
                                </div>

                                <div class="space-y-3 max-h-72 overflow-y-auto pr-1">
                                    <div v-for="(row, index) in payments" :key="row.id"
                                        class="bg-white border border-slate-200 rounded-xl p-3">
                                        <div class="flex items-center gap-2">
                                            <Dropdown v-model="row.payment_method_id" :options="paymentMethods"
                                                optionLabel="name" optionValue="id" placeholder="Method"
                                                class="flex-1 text-xs" />
                                            <InputNumber v-model="row.amount" :min="0" class="w-28"
                                                inputClass="!text-xs" />
                                            <Button icon="pi pi-times" class="p-button-text p-button-danger p-button-sm"
                                                @click="removePaymentRow(index)" type="button" />
                                        </div>

                                        <!-- common extra fields -->
                                        <div class="grid grid-cols-1 gap-2 mt-2">
                                            <InputText v-model="row.transaction_ref"
                                                placeholder="Transaction ref (optional)" class="w-full text-xs" />
                                            <InputText v-model="row.notes" placeholder="Notes (optional)"
                                                class="w-full text-xs" />
                                        </div>

                                        <!-- ✅ BANK META FIELDS -->
                                        <div v-if="isBankRow(row)"
                                            class="mt-3 p-3 rounded-xl bg-slate-50 border border-slate-200">
                                            <div
                                                class="text-xs font-semibold text-slate-700 mb-2 flex items-center gap-2">
                                                <i class="pi pi-building"></i>
                                                Bank Details
                                            </div>

                                            <div class="grid grid-cols-1 gap-2">
                                                <InputText v-model="row.meta
                                                    .customer_bank_name
                                                    " placeholder="Customer bank name" class="w-full text-xs" />
                                                <InputText v-model="row.meta
                                                    .customer_account_no
                                                    " placeholder="Customer account no" class="w-full text-xs" />

                                                <!-- If you don't have bank accounts list yet, keep it as InputText or InputNumber -->
                                                <InputNumber v-model="row.meta
                                                    .received_to_bank_account_id
                                                    " :min="1" placeholder="Received to bank account ID" class="w-full"
                                                    inputClass="!text-xs" />

                                                <InputText v-model="row.meta.txn_ref"
                                                    placeholder="Bank txn ref / cheque no" class="w-full text-xs" />
                                            </div>

                                            <div class="text-[11px] text-slate-400 mt-2">
                                                These fields go to
                                                <b>payments.*.meta</b> in
                                                backend.
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex items-center justify-between text-xs text-slate-500 mt-1">
                                    <span>Total Paid</span>
                                    <span class="font-semibold text-slate-800">{{ totalPaid.toFixed(2) }}</span>
                                </div>
                                <div class="flex items-center justify-between text-xs text-slate-500">
                                    <span>Change</span>
                                    <span class="font-semibold text-slate-800">{{ change.toFixed(2) }}</span>
                                </div>
                                <div class="flex items-center justify-between text-xs text-slate-500">
                                    <span>Due</span>
                                    <span class="font-semibold text-rose-600">{{
                                        due.toFixed(2)
                                        }}</span>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 gap-2 mt-4">
                                <Button label="Save Draft" icon="pi pi-save" class="w-full" severity="war"
                                    :disabled="!cartItems.length || !posSession" @click="submitOrder('draft')"
                                    type="button" />

                                <Button label="Complete" icon="pi pi-check" class="w-full" severity="info"
                                    :disabled="!cartItems.length || !posSession" @click="submitOrder('complete')"
                                    type="button" />

                                <Button label="Complete & Print" icon="pi pi-print" class="w-full"
                                    :disabled="!cartItems.length || !posSession" @click="submitOrder('complete_print')"
                                    type="button" severity="success" />
                            </div>
                        </div>
                    </aside>
                </div>
            </main>
        </div>

        <!-- Variation Dialog -->
        <Dialog v-model:visible="showVariationDialog" modal header="Select Variation" :style="{ width: '420px' }">
            <div v-if="dialogProduct" class="space-y-3">
                <div class="text-sm font-semibold text-slate-800">
                    {{ dialogProduct.name }}
                </div>
                <div class="text-xs text-slate-500">
                    Choose a variation to add to cart.
                </div>

                <Dropdown v-model="selectedVariationId" :options="dialogVariations" optionLabel="label"
                    optionValue="value" placeholder="Select variation" class="w-full" showClear />

                <div v-if="selectedVariation" class="p-3 rounded-xl border border-slate-200 bg-slate-50">
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

                        <div v-if="getVariationDiscountPrice(selectedVariation)"
                            class="text-sm text-slate-400 line-through">
                            {{
                                getVariationUnitPrice(
                                    selectedVariation
                                ).toFixed(2)
                            }}
                        </div>

                        <div v-if="getVariationDiscountPrice(selectedVariation)"
                            class="text-xs text-rose-600 font-semibold">
                            -{{ variationDiscountPercent(selectedVariation) }}%
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-2 pt-2">
                    <Button label="Cancel" class="p-button-text" @click="showVariationDialog = false" />
                    <Button label="Add to Cart" icon="pi pi-check" :disabled="!selectedVariationId"
                        @click="confirmAddVariation" />
                </div>
            </div>
        </Dialog>
    </AuthenticatedLayout>
</template>