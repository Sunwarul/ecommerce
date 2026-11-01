<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, router, usePage } from "@inertiajs/vue3";
import { useToast } from "primevue/usetoast";
import { computed, onMounted, onUnmounted, ref, watch } from "vue";
import { useI18n } from "vue-i18n";

import { resolveImagePath } from "@/Helpers/imageHelper";
import { firstError, money } from "@/Helpers/format";
import { defaultInstallmentConfig, installmentPayload } from "@/Helpers/installment";
import { useFlashToast } from "@/Composables/useFlashToast";
import InstallmentConfigurator from "@/Components/Sales/InstallmentConfigurator.vue";
import EmiPaymentFields from "@/Components/Sales/EmiPaymentFields.vue";
import SessionBar from "@/Components/POS/SessionBar.vue";

import AutoComplete from "primevue/autocomplete";
import Badge from "primevue/badge";
import Button from "primevue/button";
import Dialog from "primevue/dialog";
import InputGroup from "primevue/inputgroup";
import InputGroupAddon from "primevue/inputgroupaddon";
import InputNumber from "primevue/inputnumber";
import InputText from "primevue/inputtext";
import Select from "primevue/select";
import SelectButton from "primevue/selectbutton";
import Textarea from "primevue/textarea";
import Tooltip from "primevue/tooltip";

const vTooltip = Tooltip;
const { t } = useI18n();
const toast = useToast();
const page = usePage();
useFlashToast();

const props = defineProps({
    products: { type: Array, default: () => [] },
    customers: { type: Array, default: () => [] },
    paymentMethods: { type: Array, default: () => [] },
    currentSession: { type: Object, default: null },
    warehouses: { type: Array, default: () => [] },
    branches: { type: Array, default: () => [] },
    order: { type: Object, default: null }, // editing a draft
    lastWarrantyInfo: { type: String, default: "" },
    warrantyTemplates: { type: Array, default: () => [] },
    installmentPlans: { type: Array, default: () => [] },
    emiBanks: { type: Array, default: () => [] },
    emiPaymentMethodId: { type: Number, default: null },
});

const dialogBreakpoints = { "960px": "75vw", "640px": "95vw" };
const n = (v) => Number(v || 0);

// -----------------------------
// Session, branch & clock
// -----------------------------
const posSession = ref(props.currentSession);
watch(() => props.currentSession, (v) => (posSession.value = v));

const currentBranchId = computed(() => page.props.auth?.user?.branch_id);
const branchMismatch = computed(() => !!posSession.value && posSession.value.branch_id !== currentBranchId.value);
const sessionWarehouseName = computed(
    () => props.warehouses.find((w) => w.id === posSession.value?.warehouse_id)?.name || ""
);

const now = ref(new Date());
let clockTimer = null;
onMounted(() => (clockTimer = setInterval(() => (now.value = new Date()), 1000)));
onUnmounted(() => clockTimer && clearInterval(clockTimer));
const clock = computed(() => now.value.toLocaleTimeString([], { hour: "2-digit", minute: "2-digit", second: "2-digit" }));

function ensureSession() {
    if (posSession.value) return true;
    toast.add({ severity: "warn", summary: t("pos_screen.no_session"), detail: t("pos_screen.no_session_hint"), life: 2500 });
    return false;
}

// Mobile: products and cart are two views switched from a bottom bar
const mobileView = ref("products"); // products | cart

// -----------------------------
// Price & stock helpers
// -----------------------------
const getProductUnitPrice = (p) => n(p.base_price);
const getProductDiscountPrice = (p) => {
    const up = getProductUnitPrice(p);
    const dp = n(p.base_discount_price);
    return dp > 0 && dp < up ? dp : null;
};
const productDiscountPercent = (p) => {
    const up = getProductUnitPrice(p);
    const dp = getProductDiscountPrice(p);
    return !dp || up <= 0 ? 0 : Math.round(((up - dp) / up) * 100);
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
    return !dp || up <= 0 ? 0 : Math.round(((up - dp) / up) * 100);
};

/** Stock rows for a product/variation (variationId null = simple product). */
const stockRows = (p, variationId = null) => (p?.stocks || []).filter((s) => (s.variation_id ?? null) === variationId);
const totalStock = (p, variationId = null) => stockRows(p, variationId).reduce((s, r) => s + n(r.quantity), 0);
/** Stock available in the open session's warehouse — that is where the sale is taken from. */
const sessionStock = (p, variationId = null) =>
    stockRows(p, variationId)
        .filter((r) => r.warehouse_id === posSession.value?.warehouse_id)
        .reduce((s, r) => s + n(r.quantity), 0);
const stockTooltip = (p, variationId = null) => {
    const rows = stockRows(p, variationId);
    if (!rows.length) return t("pos_screen.no_stock_info");
    return rows.map((r) => `${r.warehouse?.name || "#" + r.warehouse_id}: ${n(r.quantity).toFixed(0)}`).join("\n");
};
const warehouseNames = (p, variationId = null) =>
    stockRows(p, variationId)
        .filter((r) => n(r.quantity) > 0)
        .map((r) => r.warehouse?.name || `#${r.warehouse_id}`);

// -----------------------------
// Product search + barcode scanner
// -----------------------------
const search = ref("");
const searchInput = ref(null);

const filteredProducts = computed(() => {
    const list = props.products || [];
    const q = search.value.trim().toLowerCase();
    if (!q) return list;
    return list.filter(
        (p) =>
            p.name?.toLowerCase().includes(q) ||
            p.sku?.toLowerCase().includes(q) ||
            p.barcode?.toLowerCase().includes(q) ||
            (p.variations || []).some((v) => v.sku?.toLowerCase().includes(q))
    );
});

/** Enter in the search box (what barcode scanners send): add the exact barcode/SKU match. */
function onSearchEnter() {
    const q = search.value.trim().toLowerCase();
    if (!q) return;

    for (const p of props.products) {
        const variation = (p.variations || []).find((v) => v.sku?.toLowerCase() === q);
        if (variation) {
            addVariationToCart(p, variation);
            search.value = "";
            return;
        }
    }
    const exact = props.products.filter((p) => p.barcode?.toLowerCase() === q || p.sku?.toLowerCase() === q);
    const match = exact.length === 1 ? exact[0] : filteredProducts.value.length === 1 ? filteredProducts.value[0] : null;
    if (match) {
        addProduct(match);
        search.value = "";
        return;
    }
    toast.add({ severity: "warn", summary: t("pos_screen.not_found", { code: search.value.trim() }), life: 2000 });
}

// -----------------------------
// Customer remote search
// -----------------------------
const selectedCustomer = ref(null); // object or null (walk-in)
const customerSuggestions = ref([]);
const customerLoading = ref(false);
let customerSearchTimer = null;
let customerAbort = null;

function customerLabel(c) {
    if (!c) return t("shared.terms.walk_in");
    return [c.name, c.phone, c.email].filter(Boolean).join(" • ");
}

function onCustomerComplete(event) {
    const q = (event.query || "").trim();
    if (!q) {
        customerSuggestions.value = [];
        return;
    }
    clearTimeout(customerSearchTimer);
    customerSearchTimer = setTimeout(() => fetchCustomers(q), 300);
}

async function fetchCustomers(q) {
    try {
        customerLoading.value = true;
        customerAbort?.abort();
        customerAbort = new AbortController();
        const res = await fetch(route("pos.customers.search", { q }), {
            headers: { Accept: "application/json", "X-Requested-With": "XMLHttpRequest" },
            signal: customerAbort.signal,
        });
        if (!res.ok) throw new Error("Customer search failed");
        const data = await res.json();
        customerSuggestions.value = Array.isArray(data) ? data : data.data || [];
    } catch (e) {
        if (String(e?.name) === "AbortError") return;
        toast.add({ severity: "warn", summary: t("pos_screen.search_error"), detail: t("pos_screen.search_error_detail"), life: 2000 });
    } finally {
        customerLoading.value = false;
    }
}

// -----------------------------
// Warranty
// -----------------------------
const warranty_info = ref(props.order ? props.order.warranty_info || "" : props.lastWarrantyInfo || "");
const showWarrantyDialog = ref(false);

function selectWarrantyTemplate(template) {
    warranty_info.value = template.description;
    showWarrantyDialog.value = false;
    toast.add({ severity: "info", summary: t("pos_screen.template_applied"), detail: template.name, life: 1500 });
}

// -----------------------------
// Cart
// -----------------------------
const cartItems = ref([]);
const discountMode = ref("none"); // none | percent | fixed
const discountValue = ref(0);
const discountOptions = computed(() => [
    { label: t("pos_screen.discount_none"), value: "none" },
    { label: t("pos_screen.discount_percent"), value: "percent" },
    { label: t("pos_screen.discount_fixed"), value: "fixed" },
]);

const cartQuantity = computed(() => cartItems.value.reduce((s, i) => s + n(i.quantity), 0));

/** Max quantity sellable for a cart line from the session warehouse (null = unknown / no session). */
function lineMax(item) {
    if (!posSession.value) return null;
    const p = props.products.find((x) => x.id === item.product_id);
    return p ? sessionStock(p, item.variation_id ?? null) : null;
}

function warnStock(name, available) {
    toast.add({
        severity: "warn",
        summary: t("pos_screen.stock_limit"),
        detail: t("pos_screen.stock_limit_detail", { name, qty: available }),
        life: 2500,
    });
}

/** Adds one unit of a line, respecting the session warehouse stock. */
function pushLine(line, product, variationId) {
    const available = sessionStock(product, variationId);
    const found = cartItems.value.find((i) => i.product_id === line.product_id && (i.variation_id ?? null) === variationId);
    const nextQty = (found?.quantity || 0) + 1;
    if (nextQty > available) {
        warnStock(line.name, available);
        return false;
    }
    if (found) found.quantity = nextQty;
    else cartItems.value.push(line);
    return true;
}

// Clamp manual quantity edits to stock
watch(
    () => cartItems.value.map((i) => i.quantity),
    () => {
        for (const item of cartItems.value) {
            const max = lineMax(item);
            if (max !== null && n(item.quantity) > max) {
                warnStock(item.name, max);
                item.quantity = Math.max(1, max);
            }
        }
    }
);

function addProduct(product) {
    if (!ensureSession()) return;
    if (product.type === "variable") {
        dialogProduct.value = product;
        selectedVariationId.value = null;
        showVariationDialog.value = true;
        return;
    }
    pushLine(
        {
            product_id: product.id,
            variation_id: null,
            name: product.name,
            sku: product.sku,
            unit_price: getProductUnitPrice(product),
            discount_price: getProductDiscountPrice(product),
            sell_price: getProductDiscountPrice(product) ?? getProductUnitPrice(product),
            quantity: 1,
            line_discount_amount: 0,
            tax_amount: 0,
        },
        product,
        null
    );
}

function addVariationToCart(product, variation) {
    if (!ensureSession()) return false;
    return pushLine(
        {
            product_id: product.id,
            variation_id: variation.id,
            name: `${product.name} (${variation.sku})`,
            sku: variation.sku || product.sku,
            unit_price: getVariationUnitPrice(variation),
            discount_price: getVariationDiscountPrice(variation),
            sell_price: getVariationDiscountPrice(variation) ?? getVariationUnitPrice(variation),
            quantity: 1,
            line_discount_amount: 0,
            tax_amount: 0,
        },
        product,
        variation.id
    );
}

function removeFromCart(index) {
    cartItems.value.splice(index, 1);
}

// -----------------------------
// Variation picker
// -----------------------------
const showVariationDialog = ref(false);
const dialogProduct = ref(null);
const selectedVariationId = ref(null);

const dialogVariations = computed(() =>
    (dialogProduct.value?.variations || []).map((v) => ({ label: v.sku || `#${v.id}`, value: v.id, raw: v }))
);
const selectedVariation = computed(
    () => (dialogProduct.value?.variations || []).find((v) => v.id === selectedVariationId.value) || null
);

function confirmAddVariation() {
    if (!dialogProduct.value || !selectedVariation.value) {
        toast.add({ severity: "warn", summary: t("pos_screen.select_variation"), life: 2000 });
        return;
    }
    if (addVariationToCart(dialogProduct.value, selectedVariation.value)) {
        showVariationDialog.value = false;
        dialogProduct.value = null;
        selectedVariationId.value = null;
    }
}

// -----------------------------
// Quick stock adjust
// -----------------------------
const showAdjustStockDialog = ref(false);
const adjustProduct = ref(null);
const adjustVariation = ref(null);
const adjustmentForm = ref({}); // { warehouse_id: new_qty }

function openAdjustStock(product, variation = null) {
    adjustProduct.value = product;
    adjustVariation.value = variation;
    const form = {};
    props.warehouses.forEach((wh) => {
        form[wh.id] = stockRows(product, variation ? variation.id : null)
            .filter((s) => s.warehouse_id === wh.id)
            .reduce((s, r) => s + n(r.quantity), 0);
    });
    adjustmentForm.value = form;
    showAdjustStockDialog.value = true;
}

function submitQuickAdjust(warehouseId) {
    router.post(
        route("pos.stock.adjust"),
        {
            product_id: adjustProduct.value.id,
            variation_id: adjustVariation.value?.id || null,
            warehouse_id: warehouseId,
            branch_id: posSession.value?.branch_id || props.branches[0]?.id || 1,
            quantity: adjustmentForm.value[warehouseId],
            note: "Quick adjustment from POS catalog",
        },
        {
            preserveScroll: true,
            preserveState: true, // keep the cart while stock refreshes
            onSuccess: () => toast.add({ severity: "success", summary: t("pos_screen.stock_updated"), life: 1500 }),
        }
    );
}

// -----------------------------
// Populate when editing a draft
// -----------------------------
watch(
    () => props.order,
    (order) => {
        if (!order) return;
        if (order.customer) selectedCustomer.value = order.customer;
        if (order.warranty_info) warranty_info.value = order.warranty_info;

        let lineDiscounts = 0;
        cartItems.value = (order.items || []).map((i) => {
            const lineDisc = n(i.discount_amount);
            lineDiscounts += lineDisc;
            return {
                product_id: i.product_id,
                variation_id: i.variation_id,
                name: i.name,
                sku: i.sku,
                unit_price: n(i.unit_price),
                sell_price: n(i.unit_price),
                quantity: n(i.quantity) || 1,
                discount_price: 0,
                line_discount_amount: lineDisc,
                tax_amount: n(i.tax_amount),
            };
        });

        // stored discount = line discounts + order discount
        const orderDiscount = n(order.discount_amount) - lineDiscounts;
        discountMode.value = orderDiscount > 0.01 ? "fixed" : "none";
        discountValue.value = orderDiscount > 0.01 ? orderDiscount : 0;
    },
    { immediate: true }
);

// -----------------------------
// Totals
// -----------------------------
const subtotal = computed(() => cartItems.value.reduce((s, i) => s + n(i.sell_price || i.unit_price) * n(i.quantity), 0));
const lineDiscountTotal = computed(() => cartItems.value.reduce((s, i) => s + n(i.line_discount_amount), 0));
const orderDiscount = computed(() => {
    const val = n(discountValue.value);
    if (discountMode.value === "percent") return (subtotal.value * Math.min(Math.max(val, 0), 100)) / 100;
    if (discountMode.value === "fixed") return Math.min(Math.max(val, 0), subtotal.value);
    return 0;
});
const discountTotal = computed(() => lineDiscountTotal.value + orderDiscount.value);
const taxTotal = computed(() => cartItems.value.reduce((s, i) => s + n(i.tax_amount), 0));
const total = computed(() => subtotal.value - discountTotal.value + taxTotal.value);

// -----------------------------
// Payments (partial allowed)
// -----------------------------
let paymentRowId = 1;
const blankMeta = () => ({ customer_bank_name: null, customer_account_no: null, received_to_bank_account_id: null, txn_ref: null });
const blankEmi = () => ({ emi_bank_id: null, emi_bank_tenure_id: null, card_last4: "", approval_code: "", card_holder_name: "" });
const blankPaymentRow = () => ({
    id: paymentRowId++,
    payment_method_id: null,
    amount: 0,
    transaction_ref: null,
    notes: null,
    meta: blankMeta(),
    emi: blankEmi(),
});
const payments = ref([blankPaymentRow()]);

function addPaymentRow() {
    const row = blankPaymentRow();
    row.amount = Math.max(0, Number(due.value.toFixed(2)));
    payments.value.push(row);
}
function removePaymentRow(index) {
    if (payments.value.length === 1) payments.value = [blankPaymentRow()];
    else payments.value.splice(index, 1);
}
function fillDue(row) {
    row.amount = Number((n(row.amount) + due.value).toFixed(2));
}

const bankMethodIds = computed(() =>
    props.paymentMethods.filter((m) => (m.name || "").toLowerCase().includes("bank")).map((m) => m.id)
);
const isEmiRow = (row) => !!props.emiPaymentMethodId && row?.payment_method_id === props.emiPaymentMethodId;
const isBankRow = (row) => !!row?.payment_method_id && !isEmiRow(row) && bankMethodIds.value.includes(row.payment_method_id);

// Keep meta only for Bank rows; make sure EMI rows have an emi object to bind to
watch(
    () => payments.value.map((p) => p.payment_method_id),
    () => {
        payments.value.forEach((row) => {
            row.meta = isBankRow(row) ? row.meta || blankMeta() : null;
            if (isEmiRow(row) && !row.emi) row.emi = blankEmi();
        });
    }
);

// Bank EMI rows: the bank's customer charge is added on top of the row amount
function emiTenure(row) {
    const bank = props.emiBanks.find((b) => b.id === row.emi?.emi_bank_id);
    return bank?.tenures?.find((tn) => tn.id === row.emi?.emi_bank_tenure_id) || null;
}
function emiCharge(row) {
    const tenure = isEmiRow(row) ? emiTenure(row) : null;
    return tenure ? Math.round(n(row.amount) * n(tenure.customer_charge_percent)) / 100 : 0;
}
const emiChargeTotal = computed(() => payments.value.reduce((s, row) => s + emiCharge(row), 0));
const grandTotal = computed(() => total.value + emiChargeTotal.value);
const totalPaid = computed(() => payments.value.reduce((s, row) => s + n(row.amount) + emiCharge(row), 0));
const due = computed(() => Math.max(0, grandTotal.value - totalPaid.value));
const change = computed(() => Math.max(0, totalPaid.value - grandTotal.value));

// -----------------------------
// Sale type: regular (cash / due) or installment
// -----------------------------
const saleMode = ref("regular");
const saleModeOptions = computed(() => [
    { label: t("shared.sale_type.regular"), value: "regular", icon: "pi pi-wallet" },
    { label: t("shared.sale_type.installment"), value: "installment", icon: "pi pi-calendar-clock" },
]);
const installmentConfig = ref(defaultInstallmentConfig());
const installmentPreview = ref(null);
const isInstallment = computed(() => saleMode.value === "installment");

// -----------------------------
// Submit
// -----------------------------
const submitting = ref(false);

function warn(summary, detail) {
    toast.add({ severity: "warn", summary, detail, life: 2500 });
}

function submitOrder(action = "complete") {
    if (!ensureSession()) return;
    if (branchMismatch.value) {
        toast.add({ severity: "error", summary: t("pos_screen.branch_mismatch"), detail: t("pos_screen.branch_mismatch_detail"), life: 4000 });
        return;
    }
    if (!cartItems.value.length) return warn(t("pos_screen.empty_cart"), t("pos_screen.add_items_first"));

    const validPayments = payments.value.filter((p) => p.payment_method_id && n(p.amount) > 0);
    const installmentOn = action !== "draft" && isInstallment.value;

    if (installmentOn) {
        if (!selectedCustomer.value?.id) return warn(t("pos_screen.customer_required"), t("pos_screen.customer_required_installment"));
        const pv = installmentPreview.value;
        if (!pv || pv.financed_amount <= 0) return warn(t("shared.sale_type.installment"), t("pos_screen.nothing_to_finance"));
        if (pv.min_down_payment > 0 && totalPaid.value + 0.009 < pv.min_down_payment) {
            return warn(t("shared.terms.down_payment"), t("pos_screen.min_down_payment", { amount: money(pv.min_down_payment) }));
        }
    }
    for (const row of validPayments) {
        if (isEmiRow(row) && !row.emi?.emi_bank_tenure_id) return warn(t("shared.sale_type.emi"), t("pos_screen.emi_select_bank"));
    }
    // payments required for complete (installment allows zero down payment)
    if (action !== "draft" && !installmentOn && !validPayments.length) {
        return warn(t("pos_screen.payment_required"), t("pos_screen.add_payment_hint"));
    }

    const payload = {
        action,
        pos_session_id: posSession.value.id,
        branch_id: currentBranchId.value,
        warehouse_id: posSession.value.warehouse_id,
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
                    emi: isEmiRow(p)
                        ? {
                            emi_bank_tenure_id: p.emi.emi_bank_tenure_id,
                            card_last4: p.emi.card_last4 || null,
                            approval_code: p.emi.approval_code || null,
                            card_holder_name: p.emi.card_holder_name || null,
                        }
                        : null,
                    meta: isBankRow(p)
                        ? {
                            customer_bank_name: p.meta?.customer_bank_name || null,
                            customer_account_no: p.meta?.customer_account_no || null,
                            received_to_bank_account_id: p.meta?.received_to_bank_account_id || null,
                            txn_ref: p.meta?.txn_ref || null,
                        }
                        : null,
                })),
        order_discount_type: discountMode.value,
        order_discount_value: discountValue.value,
        notes: null,
        warranty_info: warranty_info.value,
        installment: installmentOn ? installmentPayload(installmentConfig.value) : null,
    };

    const options = {
        preserveScroll: true,
        onStart: () => (submitting.value = true),
        onFinish: () => (submitting.value = false),
        onSuccess: () => {
            toast.add({ severity: "success", summary: t("pos_screen.order_saved"), life: 2000 });
            resetSale();
        },
        onError: (errors) =>
            toast.add({ severity: "error", summary: t("pos_screen.could_not_save"), detail: firstError(errors) || t("pos_screen.check_order"), life: 4000 }),
    };

    if (props.order?.id && props.order.status === "draft") {
        router.put(route("pos.orders.update", props.order.id), payload, options);
    } else {
        router.post(route("pos.orders.store"), payload, options);
    }
}

function resetSale() {
    cartItems.value = [];
    selectedCustomer.value = null;
    discountMode.value = "none";
    discountValue.value = 0;
    warranty_info.value = "";
    payments.value = [blankPaymentRow()];
    saleMode.value = "regular";
    installmentConfig.value = defaultInstallmentConfig();
    installmentPreview.value = null;
    mobileView.value = "products";
}

// -----------------------------
// Warranty template suggestions (cart categories first)
// -----------------------------
const suggestedTemplates = computed(() => {
    const templates = props.warrantyTemplates || [];
    const cartCategoryIds = [
        ...new Set(cartItems.value.map((item) => props.products.find((p) => p.id === item.product_id)?.category_id).filter(Boolean)),
    ];
    if (!cartCategoryIds.length) return templates;
    const rank = (tpl) => (cartCategoryIds.includes(tpl.category_id) ? 0 : tpl.category_id === null ? 1 : 2);
    return [...templates].sort((a, b) => rank(a) - rank(b));
});
const isRelevantTemplate = (tpl) =>
    cartItems.value.some((item) => props.products.find((p) => p.id === item.product_id)?.category_id === tpl.category_id);

const canSubmit = computed(() => cartItems.value.length > 0 && !!posSession.value && !submitting.value);
</script>

<template>
    <Head :title="t('pos_screen.title')" />

    <AuthenticatedLayout>
        <div class="flex flex-col lg:h-[calc(100vh-9rem)] min-h-0 pb-20 lg:pb-0">
            <main class="flex-1 min-h-0 flex flex-col bg-white dark:bg-surface-900 rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                <!-- Header -->
                <header class="flex flex-wrap items-center justify-between gap-3 px-4 py-3 border-b border-slate-100">
                    <div class="flex flex-wrap items-center gap-3 min-w-0">
                        <div class="flex items-center gap-2 min-w-0">
                            <span class="w-9 h-9 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                                <i class="pi pi-desktop" />
                            </span>
                            <div class="min-w-0">
                                <h1 class="text-base font-semibold text-slate-800 leading-tight">{{ t("pos_screen.title") }}</h1>
                                <p class="text-xs text-slate-500 truncate">
                                    <template v-if="posSession">
                                        <span class="inline-flex items-center gap-1 text-emerald-700 font-medium">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500" />
                                            {{ t("pos_screen.session_no", { id: posSession.id }) }}
                                        </span>
                                        <span v-if="sessionWarehouseName"> · <i class="pi pi-warehouse !text-[10px]" /> {{ sessionWarehouseName }}</span>
                                    </template>
                                    <span v-else>{{ t("pos_screen.no_session") }}</span>
                                </p>
                            </div>
                        </div>
                        <SessionBar :currentSession="props.currentSession" :branches="props.branches" :warehouses="props.warehouses" />
                    </div>

                    <div class="flex items-center gap-2">
                        <span v-if="order?.id" class="px-2.5 py-1 rounded-lg text-xs bg-sky-50 text-sky-700 font-medium flex items-center gap-1.5">
                            <i class="pi pi-file-edit text-xs" /> {{ t("pos_screen.editing_draft", { id: order.id }) }}
                        </span>
                        <span class="px-3 py-1.5 rounded-lg text-sm bg-slate-100 text-slate-600 items-center gap-2 font-medium tabular-nums hidden sm:flex ltr-nums">
                            <i class="pi pi-clock text-sm" /> {{ clock }}
                        </span>
                    </div>
                </header>

                <div v-if="branchMismatch" class="mx-4 mt-3 px-3 py-2 rounded-lg text-sm bg-amber-50 text-amber-800 flex items-center gap-2 border border-amber-200">
                    <i class="pi pi-exclamation-triangle" /> {{ t("pos_screen.branch_mismatch_detail") }}
                </div>

                <div v-if="!posSession" class="mx-4 mt-3 px-3 py-2 rounded-lg text-sm bg-sky-50 text-sky-800 flex items-center gap-2 border border-sky-200">
                    <i class="pi pi-info-circle" /> {{ t("pos_screen.no_session_hint") }}
                </div>

                <!-- Body -->
                <div class="flex-1 min-h-0 flex flex-col lg:flex-row">
                    <!-- PRODUCTS -->
                    <section class="flex-1 min-h-0 p-4 overflow-y-auto" :class="mobileView === 'products' ? 'block' : 'hidden lg:block'">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
                            <div>
                                <h2 class="text-sm font-semibold text-slate-800">{{ t("menu.products") }}</h2>
                                <p class="text-xs text-slate-500">{{ t("pos_screen.products_hint") }}</p>
                            </div>
                            <InputGroup class="w-full sm:max-w-sm">
                                <InputGroupAddon><i class="pi pi-barcode" /></InputGroupAddon>
                                <InputText ref="searchInput" v-model="search" type="search" :placeholder="t('pos_screen.search_placeholder')"
                                    :aria-label="t('pos_screen.search_placeholder')" autofocus @keydown.enter.prevent="onSearchEnter" />
                            </InputGroup>
                        </div>

                        <div class="grid gap-3 grid-cols-2 sm:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5">
                            <div v-for="product in filteredProducts" :key="product.id" role="button" tabindex="0"
                                class="text-start rounded-xl border border-slate-200 bg-white p-3 flex flex-col h-full min-w-0 cursor-pointer hover:shadow-md hover:border-emerald-300 transition focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500"
                                @click="addProduct(product)" @keydown.enter.self.prevent="addProduct(product)" @keydown.space.self.prevent="addProduct(product)">
                                <div class="relative w-full rounded-lg bg-slate-50 mb-2 h-24 flex items-center justify-center overflow-hidden">
                                    <i v-if="!product.thumbnail" class="pi pi-image text-2xl text-slate-300" />
                                    <img v-else :src="resolveImagePath(product.thumbnail)" :alt="product.name" class="h-full object-contain" loading="lazy" />

                                    <Badge v-if="product.type === 'variable'" :value="t('pos_screen.variants')" severity="info"
                                        class="!absolute top-1.5 start-1.5 !text-[10px]" />
                                    <span v-if="getProductDiscountPrice(product)"
                                        class="absolute top-1.5 end-1.5 bg-rose-600 text-white text-[10px] font-semibold px-1.5 py-0.5 rounded-full ltr-nums">
                                        -{{ productDiscountPercent(product) }}%
                                    </span>

                                    <div class="absolute bottom-1.5 end-1.5 flex items-center gap-1">
                                        <Badge v-tooltip.top="stockTooltip(product)"
                                            :value="product.type === 'variable' ? t('pos_screen.multi') : totalStock(product)"
                                            :severity="product.type === 'variable' ? 'info' : totalStock(product) > 0 ? 'success' : 'danger'"
                                            class="!text-[10px]" />
                                        <Button v-if="product.type !== 'variable'" icon="pi pi-pencil" rounded size="small" severity="secondary"
                                            class="!w-6 !h-6" :aria-label="t('pos_screen.quick_adjust')" v-tooltip.top="t('pos_screen.quick_adjust')"
                                            @click.stop="openAdjustStock(product)" />
                                    </div>
                                </div>

                                <div class="text-sm font-semibold text-slate-800 line-clamp-2 leading-snug">{{ product.name }}</div>
                                <div class="text-xs text-slate-500 truncate ltr-nums">{{ product.sku }}</div>

                                <div v-if="warehouseNames(product).length" class="mt-1.5 flex flex-wrap gap-1">
                                    <span v-for="wh in warehouseNames(product).slice(0, 2)" :key="wh"
                                        class="text-[10px] px-1.5 py-0.5 bg-slate-100 text-slate-600 rounded">{{ wh }}</span>
                                    <span v-if="warehouseNames(product).length > 2" class="text-[10px] px-1.5 py-0.5 bg-slate-100 text-slate-500 rounded">
                                        +{{ warehouseNames(product).length - 2 }}
                                    </span>
                                </div>

                                <div class="flex items-end justify-between mt-auto pt-2 gap-2">
                                    <div class="min-w-0 ltr-nums">
                                        <div class="text-base font-bold text-emerald-700">
                                            {{ money(getProductDiscountPrice(product) ?? getProductUnitPrice(product)) }}
                                        </div>
                                        <div v-if="getProductDiscountPrice(product)" class="text-xs text-slate-400 line-through">
                                            {{ money(getProductUnitPrice(product)) }}
                                        </div>
                                    </div>
                                    <span class="w-8 h-8 rounded-full bg-emerald-600 text-white flex items-center justify-center shrink-0" aria-hidden="true">
                                        <i class="pi pi-plus text-sm" />
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div v-if="!filteredProducts.length" class="text-center text-slate-500 text-sm py-12">
                            <i class="pi pi-search text-3xl text-slate-300 mb-2 block" />
                            {{ t("pos_screen.no_products") }}
                        </div>
                    </section>

                    <!-- ORDER PANEL -->
                    <aside class="w-full lg:w-[400px] min-h-0 lg:border-s border-slate-200 p-4 overflow-y-auto flex-col"
                        :class="mobileView === 'cart' ? 'flex' : 'hidden lg:flex'">
                        <div class="flex items-center justify-between mb-3 pb-3 border-b border-slate-100">
                            <div>
                                <h3 class="text-sm font-semibold text-slate-800">{{ t("pos_screen.current_order") }}</h3>
                                <p class="text-xs text-slate-500">{{ selectedCustomer ? selectedCustomer.name : t("shared.terms.walk_in") }}</p>
                            </div>
                            <span class="px-2 py-1 rounded-lg text-xs bg-emerald-50 text-emerald-700 font-medium">
                                {{ t("pos_screen.items_count", { n: cartQuantity }) }}
                            </span>
                        </div>

                        <!-- customer -->
                        <div class="mb-3 space-y-3">
                            <div>
                                <label class="text-xs text-slate-600 mb-1 block font-medium">{{ t("shared.terms.customer") }}</label>
                                <AutoComplete v-model="selectedCustomer" :suggestions="customerSuggestions" :optionLabel="customerLabel"
                                    class="w-full" inputClass="w-full text-sm" :placeholder="t('pos_screen.customer_placeholder')"
                                    :loading="customerLoading" @complete="onCustomerComplete">
                                    <template #option="{ option }">
                                        <div class="flex flex-col">
                                            <span class="font-medium">{{ option.name }}</span>
                                            <span class="text-xs text-slate-500 ltr-nums">{{ option.phone || t("pos_screen.no_phone") }}</span>
                                        </div>
                                    </template>
                                </AutoComplete>
                            </div>

                            <div>
                                <div class="flex items-center justify-between mb-1">
                                    <label class="text-xs text-slate-600 font-medium">{{ t("pos_screen.warranty_info") }}</label>
                                    <Button :label="t('pos_screen.select_template')" icon="pi pi-list" text size="small"
                                        class="!py-0 !px-1 !text-xs" @click="showWarrantyDialog = true" />
                                </div>
                                <Textarea v-model="warranty_info" rows="2" class="w-full text-sm" :placeholder="t('pos_screen.warranty_placeholder')" autoResize />
                            </div>
                        </div>

                        <!-- cart items -->
                        <div class="space-y-2 mb-3">
                            <div v-for="(item, index) in cartItems" :key="`${item.product_id}-${item.variation_id || 'simple'}`"
                                class="flex items-start justify-between gap-2 bg-slate-50 border border-slate-200 rounded-lg px-3 py-2.5">
                                <div class="flex-1 min-w-0">
                                    <div class="text-xs font-semibold text-slate-800 line-clamp-2">{{ item.name }}</div>
                                    <div class="text-[10px] text-slate-500 mb-1.5 ltr-nums">{{ item.sku }}</div>
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <InputNumber v-model="item.quantity" :min="1" :max="lineMax(item) || undefined" showButtons buttonLayout="horizontal"
                                            inputClass="!w-12 !text-xs !text-center" incrementButtonIcon="pi pi-plus" decrementButtonIcon="pi pi-minus"
                                            :aria-label="t('common.quantity')" size="small" />
                                        <span class="text-[11px] text-slate-500 ltr-nums">
                                            × {{ money(item.sell_price) }}
                                            <span v-if="item.discount_price" class="line-through text-slate-400">{{ money(item.unit_price) }}</span>
                                        </span>
                                    </div>
                                </div>
                                <div class="text-end shrink-0">
                                    <div class="text-sm font-semibold text-slate-800 ltr-nums">{{ money(n(item.sell_price) * n(item.quantity)) }}</div>
                                    <Button icon="pi pi-trash" text severity="danger" size="small" class="mt-1" :aria-label="t('common.delete')"
                                        @click="removeFromCart(index)" />
                                </div>
                            </div>

                            <div v-if="!cartItems.length" class="text-center text-xs text-slate-500 py-8 border border-dashed border-slate-200 rounded-lg">
                                <i class="pi pi-shopping-cart text-2xl text-slate-300 block mb-2" />
                                {{ t("pos_screen.empty_cart_hint") }}
                            </div>
                        </div>

                        <!-- order discount -->
                        <div class="mb-3 p-3 rounded-xl border border-slate-200">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm font-semibold text-slate-800">{{ t("pos_screen.order_discount") }}</span>
                                <span v-if="orderDiscount > 0" class="text-sm font-semibold text-rose-600 ltr-nums">-{{ money(orderDiscount) }}</span>
                            </div>
                            <div class="flex items-center gap-2 min-w-0">
                                <Select v-model="discountMode" :options="discountOptions" optionLabel="label" optionValue="value"
                                    class="flex-1 min-w-0" size="small" />
                                <InputNumber v-model="discountValue" :min="0" class="w-28 shrink-0" inputClass="!w-full" size="small"
                                    :suffix="discountMode === 'percent' ? '%' : ''" :disabled="discountMode === 'none'" :aria-label="t('pos.discount')" />
                            </div>
                        </div>

                        <!-- totals -->
                        <div class="rounded-xl bg-slate-50 border border-slate-200 p-3 space-y-1 text-sm">
                            <div class="flex justify-between text-slate-600"><span>{{ t("orders.subtotal") }}</span><span class="ltr-nums">{{ money(subtotal) }}</span></div>
                            <div class="flex justify-between text-slate-600"><span>{{ t("pos.discount") }}</span><span class="text-rose-600 ltr-nums">-{{ money(discountTotal) }}</span></div>
                            <div class="flex justify-between text-slate-600"><span>{{ t("pos.tax") }}</span><span class="ltr-nums">{{ money(taxTotal) }}</span></div>
                            <div v-if="emiChargeTotal > 0" class="flex justify-between text-slate-600">
                                <span>{{ t("pos_screen.emi_charge") }}</span><span class="ltr-nums">+{{ money(emiChargeTotal) }}</span>
                            </div>
                            <div class="flex justify-between items-baseline pt-2 mt-1 border-t border-slate-200">
                                <span class="font-semibold text-slate-800">{{ t("orders.grand_total") }}</span>
                                <span class="text-xl font-bold text-emerald-700 ltr-nums">{{ money(grandTotal) }}</span>
                            </div>
                        </div>

                        <!-- sale type -->
                        <div class="mt-3">
                            <SelectButton v-model="saleMode" :options="saleModeOptions" optionLabel="label" optionValue="value"
                                :allowEmpty="false" class="w-full [&>*]:flex-1" size="small">
                                <template #option="{ option }">
                                    <i :class="option.icon" class="text-xs" /> <span>{{ option.label }}</span>
                                </template>
                            </SelectButton>
                            <p v-if="!isInstallment" class="text-[11px] text-slate-500 mt-1">{{ t("pos_screen.partial_hint") }}</p>
                        </div>

                        <!-- payments -->
                        <div class="mt-3 space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-semibold text-slate-800">
                                    {{ isInstallment ? t("shared.terms.down_payment") : t("pos_screen.payments") }}
                                </span>
                                <Button icon="pi pi-plus" text size="small" :label="t('pos_screen.add_payment')" @click="addPaymentRow" />
                            </div>

                            <div v-for="(row, index) in payments" :key="row.id" class="border border-slate-200 rounded-xl p-3 space-y-2">
                                <div class="flex items-center gap-2">
                                    <Select v-model="row.payment_method_id" :options="paymentMethods" optionLabel="name" optionValue="id"
                                        :placeholder="t('shared.terms.method')" filter class="flex-1 min-w-0" size="small" />
                                    <InputNumber v-model="row.amount" :min="0" :maxFractionDigits="2" class="w-28 shrink-0" inputClass="!w-full"
                                        size="small" :aria-label="t('shared.terms.amount')" />
                                    <Button icon="pi pi-times" text severity="danger" size="small" :aria-label="t('common.delete')" @click="removePaymentRow(index)" />
                                </div>
                                <div class="flex gap-2">
                                    <InputText v-model="row.transaction_ref" :placeholder="t('pos_screen.txn_ref')" class="flex-1 min-w-0 text-xs" size="small" />
                                    <Button v-if="due > 0" :label="t('pos_screen.fill_due')" text size="small" class="!px-2 !text-xs shrink-0" @click="fillDue(row)" />
                                </div>
                                <InputText v-model="row.notes" :placeholder="t('pos_screen.notes_optional')" class="w-full text-xs" size="small" />

                                <EmiPaymentFields v-if="isEmiRow(row) && row.emi" v-model="row.emi" :banks="emiBanks" :amount="n(row.amount)" />

                                <div v-if="isBankRow(row) && row.meta" class="p-3 rounded-xl bg-slate-50 border border-slate-200 space-y-2">
                                    <div class="text-xs font-semibold text-slate-700 flex items-center gap-2">
                                        <i class="pi pi-building-columns" /> {{ t("pos_screen.bank_details") }}
                                    </div>
                                    <InputText v-model="row.meta.customer_bank_name" :placeholder="t('pos_screen.customer_bank_name')" class="w-full text-xs" size="small" />
                                    <InputText v-model="row.meta.customer_account_no" :placeholder="t('pos_screen.customer_account_no')" class="w-full text-xs" size="small" />
                                    <InputNumber v-model="row.meta.received_to_bank_account_id" :min="1" :placeholder="t('pos_screen.received_bank_account')"
                                        class="w-full" inputClass="!text-xs" size="small" :useGrouping="false" />
                                    <InputText v-model="row.meta.txn_ref" :placeholder="t('pos_screen.bank_txn_ref')" class="w-full text-xs" size="small" />
                                </div>
                            </div>

                            <div class="rounded-lg px-3 py-2 space-y-1 text-xs">
                                <div class="flex justify-between text-slate-600"><span>{{ t("pos_screen.payment_total") }}</span><span class="font-semibold text-slate-800 ltr-nums">{{ money(totalPaid) }}</span></div>
                                <div class="flex justify-between text-slate-600"><span>{{ t("pos.change") }}</span><span class="font-semibold text-slate-800 ltr-nums">{{ money(change) }}</span></div>
                                <div class="flex justify-between text-slate-600">
                                    <span>{{ isInstallment ? t("pos_screen.remaining_installments") : t("shared.terms.due") }}</span>
                                    <span class="font-semibold ltr-nums" :class="due > 0 ? 'text-rose-600' : 'text-slate-800'">{{ money(due) }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- installment plan -->
                        <div v-if="isInstallment" class="mt-3 p-3 rounded-xl border border-violet-200">
                            <div class="text-sm font-semibold text-slate-800 mb-2 flex items-center gap-2">
                                <i class="pi pi-calendar-clock text-violet-600" /> {{ t("pos_screen.installment_plan") }}
                            </div>
                            <p v-if="!selectedCustomer" class="text-[11px] text-rose-600 mb-2">{{ t("pos_screen.customer_required_installment") }}</p>
                            <InstallmentConfigurator v-model="installmentConfig" :saleAmount="grandTotal" :downPayment="totalPaid"
                                :plans="installmentPlans" compact @preview="installmentPreview = $event" />
                        </div>

                        <!-- actions -->
                        <div class="grid grid-cols-2 gap-2 mt-4">
                            <Button :label="t('pos_screen.save_draft')" icon="pi pi-save" severity="secondary" outlined
                                :disabled="!canSubmit" @click="submitOrder('draft')" />
                            <Button :label="isInstallment ? t('pos_screen.complete_installment') : t('pos_screen.complete')" icon="pi pi-check"
                                severity="info" :disabled="!canSubmit" @click="submitOrder('complete')" />
                            <Button :label="t('pos_screen.complete_print')" icon="pi pi-print" class="col-span-2" size="large"
                                :loading="submitting" :disabled="!canSubmit" @click="submitOrder('complete_print')" />
                        </div>
                    </aside>
                </div>
            </main>
        </div>

        <!-- Mobile bottom bar: switch between products and cart -->
        <div class="lg:hidden fixed bottom-0 inset-x-0 z-[900] bg-white dark:bg-surface-900 border-t border-slate-200 px-3 py-2 flex items-center gap-2 shadow-[0_-4px_12px_rgba(0,0,0,0.06)]">
            <Button v-if="mobileView === 'cart'" :label="t('pos_screen.view_products')" icon="pi pi-th-large" severity="secondary" outlined
                class="flex-1" @click="mobileView = 'products'" />
            <Button v-else class="flex-1 !justify-between" @click="mobileView = 'cart'">
                <span class="flex items-center gap-2"><i class="pi pi-shopping-cart" /> {{ t("pos_screen.view_cart") }}
                    <Badge :value="cartQuantity" severity="contrast" /></span>
                <span class="font-bold ltr-nums">{{ money(grandTotal) }}</span>
            </Button>
        </div>

        <!-- Variation Dialog -->
        <Dialog v-model:visible="showVariationDialog" modal :header="t('pos_screen.select_variation')" :style="{ width: '440px' }" :breakpoints="dialogBreakpoints">
            <div v-if="dialogProduct" class="space-y-3">
                <div class="text-sm font-semibold text-slate-800">{{ dialogProduct.name }}</div>
                <div class="text-xs text-slate-500">{{ t("pos_screen.variation_hint") }}</div>

                <Select v-model="selectedVariationId" :options="dialogVariations" optionLabel="label" optionValue="value"
                    :placeholder="t('pos_screen.variation_placeholder')" class="w-full" showClear filter>
                    <template #option="{ option }">
                        <div class="flex justify-between items-center w-full gap-2">
                            <span class="ltr-nums">{{ option.label }}</span>
                            <div class="flex items-center gap-2">
                                <Badge :value="totalStock(dialogProduct, option.value)" :severity="totalStock(dialogProduct, option.value) > 0 ? 'success' : 'danger'" />
                                <Button icon="pi pi-pencil" text severity="secondary" size="small" class="!w-6 !h-6" :aria-label="t('pos_screen.quick_adjust')"
                                    @click.stop="openAdjustStock(dialogProduct, option.raw)" />
                            </div>
                        </div>
                    </template>
                </Select>

                <div v-if="selectedVariation" class="p-3 rounded-xl border border-slate-200 bg-slate-50">
                    <div class="text-xs text-slate-500">{{ t("common.price") }}</div>
                    <div class="flex items-end gap-2 ltr-nums">
                        <div class="text-lg font-bold text-emerald-700">
                            {{ money(getVariationDiscountPrice(selectedVariation) ?? getVariationUnitPrice(selectedVariation)) }}
                        </div>
                        <template v-if="getVariationDiscountPrice(selectedVariation)">
                            <div class="text-sm text-slate-400 line-through">{{ money(getVariationUnitPrice(selectedVariation)) }}</div>
                            <div class="text-xs text-rose-600 font-semibold">-{{ variationDiscountPercent(selectedVariation) }}%</div>
                        </template>
                    </div>

                    <div class="mt-2 pt-2 border-t border-slate-200">
                        <div class="text-[10px] uppercase tracking-wider text-slate-500 font-bold mb-1">{{ t("pos_screen.stock_by_warehouse") }}</div>
                        <div class="grid grid-cols-2 gap-1">
                            <div v-for="s in stockRows(dialogProduct, selectedVariation.id)" :key="s.id"
                                class="flex items-center justify-between bg-white p-1.5 rounded-lg border border-slate-100">
                                <span class="text-[11px] text-slate-600 truncate">{{ s.warehouse?.name }}</span>
                                <span class="text-xs font-bold text-slate-800">{{ n(s.quantity).toFixed(0) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <template #footer>
                <Button :label="t('shared.terms.close')" icon="pi pi-times" text severity="secondary" @click="showVariationDialog = false" />
                <Button :label="t('pos_screen.add_to_cart')" icon="pi pi-cart-plus" :disabled="!selectedVariationId" @click="confirmAddVariation" />
            </template>
        </Dialog>

        <!-- Quick Stock Adjust Dialog -->
        <Dialog v-model:visible="showAdjustStockDialog" modal :header="t('pos_screen.adjust_title')" :style="{ width: '420px' }" :breakpoints="dialogBreakpoints">
            <div class="space-y-3">
                <div class="text-xs text-slate-500">
                    {{ adjustProduct?.name }}
                    <span v-if="adjustVariation" class="font-semibold text-slate-600 ltr-nums">({{ adjustVariation.sku }})</span>
                </div>
                <p class="text-[11px] text-slate-500">{{ t("pos_screen.adjust_hint") }}</p>
                <div v-for="wh in props.warehouses" :key="wh.id"
                    class="flex items-center justify-between gap-2 p-3 bg-slate-50 border border-slate-200 rounded-xl">
                    <span class="text-sm font-medium text-slate-700 truncate">{{ wh.name }}</span>
                    <div class="flex items-center gap-2 shrink-0">
                        <InputNumber v-model="adjustmentForm[wh.id]" :min="0" inputClass="!w-20 !text-sm !text-center" size="small" />
                        <Button icon="pi pi-save" severity="success" size="small" :aria-label="t('shared.terms.save')" @click="submitQuickAdjust(wh.id)" />
                    </div>
                </div>
            </div>
            <template #footer>
                <Button :label="t('pos_screen.done')" icon="pi pi-check" @click="showAdjustStockDialog = false" />
            </template>
        </Dialog>

        <!-- Warranty Template Dialog -->
        <Dialog v-model:visible="showWarrantyDialog" modal :header="t('pos_screen.warranty_dialog')" :style="{ width: '480px' }" :breakpoints="dialogBreakpoints">
            <div class="space-y-3">
                <p class="text-xs text-slate-500 italic">{{ t("pos_screen.warranty_suggest") }}</p>
                <button v-for="template in suggestedTemplates" :key="template.id" type="button"
                    class="w-full text-start p-3 border border-slate-200 rounded-xl hover:bg-slate-50 transition flex flex-col gap-1"
                    @click="selectWarrantyTemplate(template)">
                    <span class="flex items-center justify-between gap-2">
                        <span class="text-sm font-bold text-slate-800">{{ template.name }}</span>
                        <Badge v-if="isRelevantTemplate(template)" :value="t('pos_screen.relevant')" severity="info" class="!text-[10px]" />
                    </span>
                    <span class="text-xs text-slate-600">{{ template.description }}</span>
                </button>
                <div v-if="!suggestedTemplates.length" class="text-center text-sm text-slate-500 py-6">{{ t("pos_screen.no_templates") }}</div>
            </div>
            <template #footer>
                <Button :label="t('shared.terms.cancel')" icon="pi pi-times" text severity="secondary" @click="showWarrantyDialog = false" />
            </template>
        </Dialog>
    </AuthenticatedLayout>
</template>
