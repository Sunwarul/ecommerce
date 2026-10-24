<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { router } from "@inertiajs/vue3";
import { computed, ref, watch } from "vue";

import Button from "primevue/button";
import Calendar from "primevue/calendar";
import Column from "primevue/column";
import DataTable from "primevue/datatable";
import Dialog from "primevue/dialog";
import Divider from "primevue/divider";
import Select from "primevue/select";
import InputNumber from "primevue/inputnumber";
import InputText from "primevue/inputtext";
import Tag from "primevue/tag";
import Tooltip from 'primevue/tooltip';
import TabMenu from 'primevue/tabmenu';
import SplitButton from 'primevue/splitbutton';
import { useToast } from "primevue/usetoast";
import { v4 as uuidv4 } from 'uuid';
import AutoComplete from "primevue/autocomplete";
import SelectButton from "primevue/selectbutton";
import InstallmentConfigurator from "@/Components/Sales/InstallmentConfigurator.vue";
import EmiPaymentFields from "@/Components/Sales/EmiPaymentFields.vue";
import { firstError, ymdhms } from "@/Helpers/format";
import { defaultInstallmentConfig, installmentPayload } from "@/Helpers/installment";

const vTooltip = Tooltip;

const toast = useToast();

const props = defineProps({
    orders: Object,
    filters: Object,
    paymentMethods: { type: Array, default: () => [] },
    categories: { type: Array, default: () => [] },
    brands: { type: Array, default: () => [] },
    available_products: { type: Array, default: () => [] },
    branches: { type: Array, default: () => [] },
    currentBranchId: { type: Number, default: null },
    isAdmin: { type: Boolean, default: false },
    insights: { type: Object, default: () => ({}) },
    installmentPlans: { type: Array, default: () => [] },
    emiBanks: { type: Array, default: () => [] },
    emiPaymentMethodId: { type: Number, default: null },
});

/* ---------------- Filters ---------------- */
const search = ref(props.filters?.search || "");
const status = ref(props.filters?.status || "");
const paymentStatus = ref(props.filters?.payment_status || "");
const saleType = ref(props.filters?.sale_type || "");
const saleTypeOptions = [
    { label: "All types", value: "" },
    { label: "Regular", value: "regular" },
    { label: "Installment", value: "installment" },
    { label: "Bank EMI", value: "emi" },
];
const dateFrom = ref(
    props.filters?.date_from ? new Date(props.filters.date_from) : null
);
const dateTo = ref(
    props.filters?.date_to ? new Date(props.filters.date_to) : null
);
const trashed = ref(props.filters?.trashed || false);

// Advanced Filters
const categoryId = ref(props.filters?.category_id || null);
const brandId = ref(props.filters?.brand_id || null);
const productId = ref(props.filters?.product_id || null);

const showInsights = ref(false);

const statusOptions = [
    { label: "All", value: "" },
    { label: "Completed", value: "completed" },
    { label: "Draft", value: "draft" },
    { label: "Void", value: "void" },
];

const paymentStatusOptions = [
    { label: "All", value: "" },
    { label: "Paid", value: "paid" },
    { label: "Partial", value: "partial" },
    { label: "Unpaid", value: "unpaid" },
];

const activeTab = ref(props.filters?.trashed ? 1 : 0);
const tabItems = [
    { label: 'Active Orders', icon: 'pi pi-receipt' },
    { label: 'Trash', icon: 'pi pi-trash' }
];

watch(activeTab, (val) => {
    trashed.value = (val === 1);
    applyFilter();
});

function fmtDate(d) {
    if (!d) return undefined;
    const yyyy = d.getFullYear();
    const mm = String(d.getMonth() + 1).padStart(2, "0");
    const dd = String(d.getDate()).padStart(2, "0");
    return `${yyyy}-${mm}-${dd}`;
}

function applyFilter() {
    router.get(
        route("pos.orders.index"),
        {
            search: search.value || undefined,
            status: status.value || undefined,
            payment_status: paymentStatus.value || undefined,
            sale_type: saleType.value || undefined,
            date_from: dateFrom.value ? fmtDate(dateFrom.value) : undefined,
            date_to: dateTo.value ? fmtDate(dateTo.value) : undefined,
            category_id: categoryId.value || undefined,
            brand_id: brandId.value || undefined,
            product_id: productId.value || undefined,
            trashed: trashed.value ? 1 : undefined,
            all_branches: props.filters?.all_branches ? 1 : undefined,
        },
        { preserveState: true, replace: true }
    );
}

function resetFilter() {
    search.value = "";
    status.value = "";
    paymentStatus.value = "";
    saleType.value = "";
    dateFrom.value = null;
    dateTo.value = null;
    categoryId.value = null;
    brandId.value = null;
    productId.value = null;
    trashed.value = false;
    activeTab.value = 0;
    router.get(route("pos.orders.index"), {}, { replace: true });
}

function exportReport(format) {
    const params = {
        ...props.filters,
        export: format
    };

    const url = route('pos.orders.index', params);
    window.open(url, '_blank');
}

/* ---------------- Pagination ---------------- */
function goToPage(url) {
    if (!url) return;
    router.get(url, {}, { preserveState: true });
}

/* ---------------- Data ---------------- */
const rows = computed(() => props.orders?.data || []);
const pageTotal = computed(() =>
    rows.value.reduce((s, o) => s + Number(o.total_amount || 0), 0)
);

function invoiceLabel(o) {
    if (!o) return "";
    // If invoice_no is null for draft, show DRAFT-ID
    return (
        o.invoice_no || (o.status === "draft" ? `DRAFT-${o.id}` : `#${o.id}`)
    );
}

function openInvoicePage(order) {
    window.open(route("pos.orders.invoice", order.id), "_blank");
}
function printInvoice(order) {
    window.open(
        route("pos.orders.invoice", order.id) + "?autoprint=1",
        "_blank"
    );
}

/* ---------------- Tag helpers ---------------- */
function getOrderCategories(order) {
    if (!order.items) return "-";
    const cats = order.items
        .map((it) => it.product?.category?.name)
        .filter((c, i, a) => c && a.indexOf(c) === i);
    return cats.length ? cats.join(", ") : "-";
}

function paymentSeverity(v) {
    if (v === "paid") return "success";
    if (v === "partial") return "warn";
    return "danger";
}
function statusSeverity(v) {
    if (v === "completed") return "success";
    if (v === "draft") return "info";
    return "danger"; // void
}

/* ---------------- Invoice Preview Modal ---------------- */
const showInvoiceModal = ref(false);
const selectedOrder = ref(null);

function openInvoiceModal(order) {
    selectedOrder.value = order;
    showInvoiceModal.value = true;
}

/* ---------------- Shared: Payment Row Factory ---------------- */
function newPaymentRow() {
    return {
        id: uuidv4(),
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
        emi: { emi_bank_id: null, emi_bank_tenure_id: null, card_last4: "", approval_code: "", card_holder_name: "" },
    };
}

function isEmiRow(row) {
    return !!props.emiPaymentMethodId && row?.payment_method_id === props.emiPaymentMethodId;
}

function addRow(arrRef) {
    arrRef.value.push(newPaymentRow());
}

function removeRow(arrRef, i) {
    if (arrRef.value.length === 1) {
        arrRef.value = [newPaymentRow()];
        return;
    }
    arrRef.value.splice(i, 1);
}

function needsBankMeta(row) {
    if (isEmiRow(row)) return false;
    const pm = props.paymentMethods.find((x) => x.id === row.payment_method_id);
    const name = (pm?.name || "").toLowerCase();
    return name.includes("bank") || name.includes("transfer");
}

/* ---------------- Void Modal ---------------- */
const showVoidModal = ref(false);
const voidTarget = ref(null);
const voidLoading = ref(false);

function canVoid(order) {
    return order?.status !== "void";
}
function openVoidModal(order) {
    voidTarget.value = order;
    showVoidModal.value = true;
}
function voidOrder() {
    if (!voidTarget.value) return;

    voidLoading.value = true;
    router.post(
        route("pos.orders.void", voidTarget.value.id),
        {},
        {
            preserveScroll: true,
            onSuccess: () => {
                toast.add({
                    severity: "success",
                    summary: "Voided",
                    detail: "Order has been voided",
                    life: 2200,
                });
                showVoidModal.value = false;
                voidTarget.value = null;
            },
            onError: () => {
                toast.add({
                    severity: "error",
                    summary: "Failed",
                    detail: "Could not void order",
                    life: 2500,
                });
            },
            onFinish: () => (voidLoading.value = false),
        }
    );
}

/* ---------------- Transfer Modal ---------------- */
const showTransferModal = ref(false);
const transferLoading = ref(false);
const targetBranchId = ref(null);

function openTransferModal() {
    if (!selectedOrders.value.length) return;
    targetBranchId.value = null;
    showTransferModal.value = true;
}

function transferOrders() {
    if (!targetBranchId.value || !selectedOrders.value.length) return;

    transferLoading.value = true;
    router.post(route("pos.orders.transfer"), {
        ids: selectedOrders.value.map(o => o.id),
        target_branch_id: targetBranchId.value
    }, {
        preserveScroll: true,
        onSuccess: () => {
            selectedOrders.value = [];
            showTransferModal.value = false;
            targetBranchId.value = null;
            toast.add({ severity: 'success', summary: 'Transferred', detail: 'Orders transferred to selected branch', life: 2000 });
        },
        onError: () => {
            toast.add({ severity: 'error', summary: 'Failed', detail: 'Could not transfer orders', life: 2500 });
        },
        onFinish: () => (transferLoading.value = false),
    });
}

/* ---------------- Trash / Restore / Force Delete ---------------- */
function trashOrder(order) {
    if (!confirm("Move this order to trash?")) return;
    router.delete(route("pos.orders.destroy", order.id), {
        preserveScroll: true,
        onSuccess: () => toast.add({ severity: 'success', summary: 'Trashed', detail: 'Order moved to trash', life: 2000 })
    });
}

function restoreOrder(order) {
    if (!confirm("Restore this order?")) return;
    router.post(route("pos.orders.restore", order.id), {}, {
        preserveScroll: true,
        onSuccess: () => toast.add({ severity: 'success', summary: 'Restored', detail: 'Order restored successfully', life: 2000 })
    });
}

function forceDeleteOrder(order) {
    if (!confirm("PERMANENTLY delete this order? THIS CANNOT BE UNDONE.")) return;
    router.delete(route("pos.orders.force-delete", order.id), {
        preserveScroll: true,
        onSuccess: () => toast.add({ severity: 'success', summary: 'Deleted', detail: 'Order permanently deleted', life: 2000 })
    });
}

/* ---------------- Bulk Actions ---------------- */
const selectedOrders = ref([]);

const bulkActions = computed(() => {
    const actions = [];
    if (trashed.value) {
        actions.push({
            label: "Restore Selected",
            icon: "pi pi-refresh",
            command: () => bulkSubmit('restore'),
        });
        actions.push({
            label: "Force Delete Selected",
            icon: "pi pi-times",
            class: "p-button-danger",
            command: () => bulkSubmit('force_delete'),
        });
    } else {
        actions.push({
            label: "Transfer to Branch",
            icon: "pi pi-directions",
            command: () => openTransferModal(),
        });
        actions.push({
            label: "Trash Selected",
            icon: "pi pi-trash",
            command: () => bulkSubmit('trash'),
        });
    }
    return actions;
});

function bulkSubmit(action) {
    if (!selectedOrders.value.length) return;

    let msg = `Are you sure you want to ${action.replace('_', ' ')} ${selectedOrders.value.length} orders?`;
    if (action === 'force_delete') msg += " THIS CANNOT BE UNDONE.";

    if (!confirm(msg)) return;

    router.post(route("pos.orders.bulk-action"), {
        action: action,
        ids: selectedOrders.value.map(o => o.id)
    }, {
        onSuccess: () => {
            selectedOrders.value = [];
            toast.add({ severity: 'success', summary: 'Success', detail: 'Bulk action completed', life: 2000 });
        }
    });
}

/* ---------------- Complete Draft Modal ---------------- */
const showCompleteModal = ref(false);
const completeTarget = ref(null);
const completeLoading = ref(false);

const completeRows = ref([newPaymentRow()]);

function canCompleteDraft(order) {
    return order?.status === "draft";
}
const completeMode = ref("regular");
const completeInstallment = ref(defaultInstallmentConfig());
const completePreview = ref(null);

function openCompleteModal(order) {
    completeTarget.value = order;
    showCompleteModal.value = true;
    completeRows.value = [newPaymentRow()];
    completeMode.value = "regular";
    completeInstallment.value = defaultInstallmentConfig();
}

const draftTotal = computed(() =>
    Number(completeTarget.value?.total_amount || 0)
);
const draftPayTotal = computed(() =>
    completeRows.value.reduce((s, r) => s + Number(r.amount || 0), 0)
);
const draftDue = computed(() =>
    Math.max(0, draftTotal.value - draftPayTotal.value)
);
const draftChange = computed(() =>
    Math.max(0, draftPayTotal.value - draftTotal.value)
);

function cleanMeta(meta) {
    if (!meta) return null;

    // remove empty strings / nulls
    const m = {
        customer_bank_name: meta.customer_bank_name || null,
        customer_account_no: meta.customer_account_no || null,
        received_to_bank_account_id: meta.received_to_bank_account_id || null,
        txn_ref: meta.txn_ref || null,
    };

    // if everything is null, return null
    const hasAny = Object.values(m).some((v) => v !== null);
    return hasAny ? m : null;
}

function validatePaymentsOrToast(rowsRef) {
    // 1) basic required checks
    const validRows = rowsRef.value.filter(
        (p) => p.payment_method_id && Number(p.amount || 0) > 0
    );

    if (!validRows.length) {
        toast.add({
            severity: "warn",
            summary: "Payment required",
            detail: "Add at least one payment",
            life: 2200,
        });
        return null;
    }

    // 2) bank meta required fields (only if bank selected)
    for (const row of validRows) {
        if (isEmiRow(row) && !row.emi?.emi_bank_tenure_id) {
            toast.add({ severity: "warn", summary: "Bank EMI", detail: "Select the bank and tenure for the EMI payment", life: 2500 });
            return null;
        }
        if (needsBankMeta(row)) {
            const bank = row.meta || {};
            if (!bank.customer_bank_name || !bank.customer_account_no) {
                toast.add({
                    severity: "warn",
                    summary: "Bank info required",
                    detail: "Customer bank name & account no are required for bank payments",
                    life: 2500,
                });
                return null;
            }
        }
    }

    // 3) build payload exactly like backend expects
    const payload = validRows.map((p) => ({
        payment_method_id: p.payment_method_id,
        amount: Number(p.amount),
        transaction_ref: p.transaction_ref?.trim() || null,
        notes: p.notes?.trim() || null,

        // only send meta if it has values (matches nullable array)
        meta: isEmiRow(p) ? null : cleanMeta(p.meta),
        emi: isEmiRow(p)
            ? {
                emi_bank_tenure_id: p.emi.emi_bank_tenure_id,
                card_last4: p.emi.card_last4 || null,
                approval_code: p.emi.approval_code || null,
                card_holder_name: p.emi.card_holder_name || null,
            }
            : null,
    }));

    return payload;
}

function completeDraft() {
    if (!completeTarget.value) return;

    const installmentOn = completeMode.value === "installment";
    const hasPayments = completeRows.value.some((p) => p.payment_method_id && Number(p.amount || 0) > 0);
    const valid = installmentOn && !hasPayments ? [] : validatePaymentsOrToast(completeRows);
    if (!valid) return;
    if (installmentOn && !completeTarget.value.customer_id) {
        toast.add({ severity: "warn", summary: "Customer required", detail: "This draft has no customer. Use Edit to add one first.", life: 3000 });
        return;
    }

    completeLoading.value = true;
    router.post(
        route("pos.orders.complete", completeTarget.value.id),
        { payments: valid, installment: installmentOn ? installmentPayload(completeInstallment.value) : null },
        {
            preserveScroll: true,
            onSuccess: () => {
                toast.add({
                    severity: "success",
                    summary: "Completed",
                    detail: "Draft order completed",
                    life: 2200,
                });
                showCompleteModal.value = false;
                completeTarget.value = null;
            },
            onError: (errors) => {
                toast.add({
                    severity: "error",
                    summary: "Failed",
                    detail: firstError(errors) || "Could not complete draft",
                    life: 3500,
                });
            },
            onFinish: () => (completeLoading.value = false),
        }
    );
}

/* ---------------- Pay Due Modal (Partial/Unpaid Completed Orders) ---------------- */
const showPayDueModal = ref(false);
const payDueTarget = ref(null);
const payDueLoading = ref(false);

const payDueRows = ref([newPaymentRow()]);

function canPayDue(order) {
    return (
        order &&
        !(order.installment_sale && order.installment_sale.status === "active") &&
        order.status !== "void" &&
        order.status !== "draft" &&
        (order.payment_status === "partial" ||
            order.payment_status === "unpaid")
    );
}

function openPayDueModal(order) {
    payDueTarget.value = order;
    showPayDueModal.value = true;
    payDueRows.value = [newPaymentRow()];
}

const orderTotal = computed(() =>
    Number(payDueTarget.value?.total_amount || 0)
);
const alreadyPaid = computed(() =>
    Number(payDueTarget.value?.paid_amount || 0)
);
const currentDue = computed(() =>
    Math.max(0, orderTotal.value - alreadyPaid.value)
);

/* ---------------- Convert to Installment ---------------- */
const showConvertModal = ref(false);
const convertTarget = ref(null);
const convertLoading = ref(false);
const convertConfig = ref(defaultInstallmentConfig());
const convertPreview = ref(null);
const convertCustomer = ref(null);
const customerSuggestions = ref([]);

function canConvert(order) {
    return order && !order.deleted_at && order.status === "completed" && !order.installment_sale
        && Number(order.total_amount || 0) - Number(order.paid_amount || 0) > 0.009;
}

function openConvertModal(order) {
    convertTarget.value = order;
    convertConfig.value = defaultInstallmentConfig();
    convertCustomer.value = order.customer || null;
    showConvertModal.value = true;
}

async function searchCustomers(event) {
    const q = (event.query || "").trim();
    if (!q) return (customerSuggestions.value = []);
    const res = await fetch(route("pos.customers.search", { q }), { headers: { Accept: "application/json" } });
    customerSuggestions.value = (await res.json()).data || [];
}

function submitConvert() {
    if (!convertTarget.value) return;
    if (!convertCustomer.value?.id) {
        toast.add({ severity: "warn", summary: "Customer required", detail: "Select the customer for this agreement", life: 2500 });
        return;
    }
    convertLoading.value = true;
    router.post(route("installments.store"), {
        order_id: convertTarget.value.id,
        customer_id: convertCustomer.value.id,
        installment: installmentPayload(convertConfig.value),
    }, {
        onSuccess: () => (showConvertModal.value = false),
        onError: (errors) => toast.add({ severity: "error", summary: "Failed", detail: firstError(errors), life: 3500 }),
        onFinish: () => (convertLoading.value = false),
    });
}

function orderDue(order) {
    if (order.status !== "completed") return 0;
    return Math.max(0, Number(order.total_amount || 0) - Number(order.paid_amount || 0));
}

const payNowTotal = computed(() =>
    payDueRows.value.reduce((s, r) => s + Number(r.amount || 0), 0)
);
const dueAfterPay = computed(() =>
    Math.max(0, currentDue.value - payNowTotal.value)
);
const changeAfterPay = computed(() =>
    Math.max(0, payNowTotal.value - currentDue.value)
);

function submitPayDue() {
    if (!payDueTarget.value) return;

    const valid = validatePaymentsOrToast(payDueRows);
    if (!valid) return;

    payDueLoading.value = true;
    router.post(
        route("pos.orders.payments.store", payDueTarget.value.id),
        { payments: valid },
        {
            preserveScroll: true,
            onSuccess: () => {
                toast.add({
                    severity: "success",
                    summary: "Payment added",
                    detail: "Due updated successfully",
                    life: 2200,
                });
                showPayDueModal.value = false;
                payDueTarget.value = null;
            },
            onError: () => {
                toast.add({
                    severity: "error",
                    summary: "Failed",
                    detail: "Could not add payment",
                    life: 2500,
                });
            },
            onFinish: () => (payDueLoading.value = false),
        }
    );
}

function editDraft(order) {
    if (!order) return;
    router.visit(route("pos.orders.edit", order.id));
}

/* ---------------- Edit Date Modal (Admin Only) ---------------- */
const showEditDateModal = ref(false);
const editDateTarget = ref(null);
const editDateLoading = ref(false);
const newSaleDate = ref(null);

function openEditDateModal(order) {
    editDateTarget.value = order;
    newSaleDate.value = order.created_at ? new Date(order.created_at) : new Date();
    showEditDateModal.value = true;
}

function updateOrderDate() {
    if (!editDateTarget.value || !newSaleDate.value) return;

    editDateLoading.value = true;
    // local time — toISOString() would shift the date to UTC
    const dateStr = ymdhms(newSaleDate.value);
    
    router.put(route("pos.orders.update-date", editDateTarget.value.id), {
        sale_date: dateStr
    }, {
        preserveScroll: true,
        onSuccess: () => {
            toast.add({
                severity: "success",
                summary: "Updated",
                detail: "Order date updated successfully",
                life: 2200,
            });
            showEditDateModal.value = false;
            editDateTarget.value = null;
        },
        onError: (errors) => {
            toast.add({
                severity: "error",
                summary: "Failed",
                detail: errors.message || "Could not update order date",
                life: 2500,
            });
        },
        onFinish: () => (editDateLoading.value = false),
    });
}
</script>

<template>
    <AuthenticatedLayout>
        <div class="p-4 md:p-6 space-y-4">
            <!-- HEADER -->
            <div class="flex justify-between items-start flex-wrap gap-3">
                <div>
                    <h2 class="text-xl font-semibold flex items-center gap-2">
                        <i class="pi pi-receipt text-primary"></i>
                        POS Sales
                    </h2>
                    <p class="text-sm text-gray-500">
                        View and manage Point of Sale orders with rich insights
                    </p>
                </div>

                <div class="flex items-center gap-2">
                    <Button :label="showInsights ? 'Hide Insights' : 'Show Insights'"
                        :icon="showInsights ? 'pi pi-chart-bar' : 'pi pi-chart-line'" class="p-button-text p-button-sm"
                        @click="showInsights = !showInsights" />

                    <Button v-if="isAdmin" :label="props.filters?.all_branches ? 'Current Branch' : 'All Branches'"
                        :icon="props.filters?.all_branches ? 'pi pi-building' : 'pi pi-globe'" 
                        :class="props.filters?.all_branches ? 'p-button-warning' : 'p-button-outlined'"
                        class="p-button-sm"
                        @click="router.get(route('pos.orders.index', { all_branches: !props.filters?.all_branches ? 1 : undefined }), {}, { preserveState: true })" 
                    />

                    <div v-if="selectedOrders.length" class="flex items-center gap-2">
                        <SplitButton :label="selectedOrders.length + ' Selected'" :model="bulkActions" severity="danger"
                            size="small"></SplitButton>
                    </div>

                    <TabMenu :model="tabItems" v-model:activeIndex="activeTab" class="ml-2" />

                    <div
                        class="px-4 py-2 rounded-xl bg-emerald-50 text-emerald-700 text-sm font-semibold border border-emerald-100">
                        Page Total: {{ pageTotal.toFixed(2) }}
                    </div>
                </div>
            </div>

            <!-- INSIGHTS SECTION -->
            <div v-if="showInsights && insights" class="grid grid-cols-1 lg:grid-cols-2 gap-4 animate-fadein">
                <!-- Top Products -->
                <div class="card p-4 border-l-4 border-primary">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="font-bold text-slate-700 flex items-center gap-2">
                            <i class="pi pi-star-fill text-amber-400"></i>
                            Top Selling Products
                        </h3>
                    </div>
                    <div class="space-y-3">
                        <div v-for="item in insights.top_products" :key="item.product_id"
                            class="flex items-center justify-between p-2 rounded-lg bg-slate-50 border border-slate-100">
                            <div class="flex flex-col">
                                <span class="text-sm font-semibold text-slate-800">{{ item.name }}</span>
                                <span class="text-xs text-slate-500">Sold: {{ item.total_qty }} units</span>
                            </div>
                            <div class="text-right">
                                <div class="text-sm font-bold text-emerald-600">{{ Number(item.total_amount).toFixed(2)
                                    }}</div>
                            </div>
                        </div>
                        <div v-if="!insights.top_products?.length"
                            class="text-center py-4 text-slate-400 text-sm italic">
                            No data available
                        </div>
                    </div>
                </div>

                <!-- Sales by Brand -->
                <div class="card p-4 border-l-4 border-emerald-500">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="font-bold text-slate-700 flex items-center gap-2">
                            <i class="pi pi-tags text-emerald-500"></i>
                            Sales by Brand
                        </h3>
                    </div>
                    <div class="space-y-3">
                        <div v-for="item in insights.brand_sales" :key="item.name"
                            class="flex items-center justify-between p-2 rounded-lg bg-slate-50 border border-slate-100">
                            <div class="flex flex-col">
                                <span class="text-sm font-semibold text-slate-800">{{ item.name }}</span>
                                <span class="text-xs text-slate-500">Items: {{ item.total_qty }}</span>
                            </div>
                            <div class="text-right">
                                <div class="text-sm font-bold text-emerald-600">{{ Number(item.total_amount).toFixed(2)
                                    }}</div>
                            </div>
                        </div>
                        <div v-if="!insights.brand_sales?.length"
                            class="text-center py-4 text-slate-400 text-sm italic">
                            No data available
                        </div>
                    </div>
                </div>
            </div>

            <!-- FILTER BAR -->
            <div class="card p-4 space-y-4">
                <div class="flex flex-wrap gap-3 items-center">
                    <InputGroup class="max-w-sm">
                        <InputGroupAddon><i class="pi pi-search"></i></InputGroupAddon>
                        <InputText v-model="search" placeholder="Invoice, customer name or phone (partial, * wildcard)" class="w-full"
                            v-tooltip.bottom="'Partial match: 1712345 finds 01712-345678. Use * as wildcard, e.g. 017*678. Several words must all match.'"
                            @keyup.enter="applyFilter" />
                    </InputGroup>

                    <Select v-model="status" :options="statusOptions" optionLabel="label" optionValue="value"
                        placeholder="Order Status" class="w-1/2 md:w-44" />
                    <Select v-model="paymentStatus" :options="paymentStatusOptions" optionLabel="label"
                        optionValue="value" placeholder="Payment Status" class="w-1/2 md:w-44" />
                    <Select v-model="saleType" :options="saleTypeOptions" optionLabel="label"
                        optionValue="value" placeholder="Sale type" class="w-1/2 md:w-44" />

                    <Calendar v-model="dateFrom" placeholder="From" dateFormat="yy-mm-dd" showIcon
                        class="w-1/2 md:w-44" />
                    <Calendar v-model="dateTo" placeholder="To" dateFormat="yy-mm-dd" showIcon class="w-1/2 md:w-44" />
                </div>

                <div class="flex flex-wrap gap-3 items-center pt-2 border-t border-slate-100">
                    <Select v-model="categoryId" :options="categories" optionLabel="name" optionValue="id"
                        placeholder="All Categories" class="w-1/2 md:w-56" showClear filter />
                    <Select v-model="brandId" :options="brands" optionLabel="name" optionValue="id"
                        placeholder="All Brands" class="w-1/2 md:w-56" showClear filter />
                    <Select v-model="productId" :options="available_products" optionLabel="name" optionValue="id"
                        placeholder="Filter by Product" class="w-full md:w-72" filter showClear />

                    <div class="flex gap-2 ml-auto">
                        <Button label="Apply" icon="pi pi-filter" class="p-button-outlined" @click="applyFilter" />
                        <Button label="Reset" icon="pi pi-refresh" class="p-button-text" @click="resetFilter" />

                        <Divider layout="vertical" class="hidden md:block" />

                        <Button icon="pi pi-file-pdf" class="p-button-outlined p-button-danger"
                            v-tooltip.top="'Export PDF'" @click="exportReport('pdf')" />
                        <Button icon="pi pi-file-excel" class="p-button-outlined p-button-success"
                            v-tooltip.top="'Export Excel'" @click="exportReport('excel')" />
                        <Button icon="pi pi-file" class="p-button-outlined p-button-secondary"
                            v-tooltip.top="'Export CSV'" @click="exportReport('csv')" />
                    </div>
                </div>
            </div>

            <!-- TABLE -->
            <div class="card">
                <DataTable :value="rows" v-model:selection="selectedOrders" dataKey="id" responsiveLayout="scroll"
                    size="small" showGridlines>
                    <Column selectionMode="multiple" headerStyle="width: 3rem"></Column>
                    <Column header="Invoice">
                        <template #body="{ data }">
                            <div class="flex flex-col">
                                <span class="font-semibold text-slate-800">{{
                                    invoiceLabel(data)
                                    }}</span>
                                <span class="text-xs text-slate-500">{{
                                    new Date(data.created_at).toLocaleString()
                                    }}</span>
                            </div>
                        </template>
                    </Column>

                    <Column header="Customer">
                        <template #body="{ data }">
                            <div class="flex flex-col">
                                <span>{{ data.customer?.name || "Walk-in" }}</span>
                                <span v-if="data.customer?.phone" class="text-xs text-slate-500">{{ data.customer.phone }}</span>
                            </div>
                        </template>
                    </Column>

                    <Column header="Categories">
                        <template #body="{ data }">
                            <span class="text-xs text-slate-600">{{ getOrderCategories(data) }}</span>
                        </template>
                    </Column>

                    <Column header="Cashier">
                        <template #body="{ data }">{{
                            data.user?.name || "-"
                            }}</template>
                    </Column>

                    <Column header="Branch">
                        <template #body="{ data }">{{
                            data.branch?.name || "-"
                            }}</template>
                    </Column>

                    <Column header="Total">
                        <template #body="{ data }">
                            <span class="font-semibold text-emerald-600">{{
                                Number(data.total_amount || 0).toFixed(2)
                                }}</span>
                        </template>
                    </Column>

                    <Column header="Due">
                        <template #body="{ data }">
                            <span v-if="orderDue(data) > 0" class="font-semibold text-rose-600">{{ orderDue(data).toFixed(2) }}</span>
                            <span v-else class="text-slate-400">-</span>
                        </template>
                    </Column>

                    <Column header="Payment">
                        <template #body="{ data }">
                            <div class="flex flex-col gap-1 items-start">
                                <Tag :value="data.payment_status" :severity="paymentSeverity(data.payment_status)" />
                                <a v-if="data.installment_sale" :href="route('installments.show', data.installment_sale.id)"
                                    class="text-[11px] text-violet-700 hover:underline">
                                    <i class="pi pi-calendar-clock !text-[10px]" /> {{ data.installment_sale.agreement_no }}
                                </a>
                                <Tag v-else-if="data.sale_type === 'emi'" value="Bank EMI" severity="info" class="!text-[10px]" />
                            </div>
                        </template>
                    </Column>

                    <Column header="Status">
                        <template #body="{ data }">
                            <Tag :value="data.status" :severity="statusSeverity(data.status)" />
                        </template>
                    </Column>

                    <Column header="Actions">
                        <template #body="{ data }">
                            <div class="flex gap-1 flex-wrap">
                                <Button icon="pi pi-eye" class="p-button-text p-button-sm"
                                    @click="openInvoiceModal(data)" v-tooltip.top="'View'" />

                                <Button v-if="isAdmin && data.status === 'completed'" icon="pi pi-calendar"
                                    class="p-button-text p-button-sm p-button-help"
                                    @click="openEditDateModal(data)" v-tooltip.top="'Edit Date'" />

                                <template v-if="!data.deleted_at">
                                    <Button icon="pi pi-external-link" class="p-button-text p-button-sm"
                                        @click="openInvoicePage(data)" v-tooltip.top="'Open'" />

                                    <Button v-if="data.status !== 'void'" icon="pi pi-print"
                                        class="p-button-text p-button-sm" @click="printInvoice(data)"
                                        v-tooltip.top="'Print'" />

                                    <!-- ✅ Completed but Partial/Unpaid => Pay Due -->
                                    <Button v-if="canPayDue(data)" icon="pi pi-wallet"
                                        class="p-button-text p-button-sm p-button-warning"
                                        @click="openPayDueModal(data)" v-tooltip.top="'Pay Due'" />

                                    <!-- Installment orders collect on the agreement page -->
                                    <Button v-if="data.installment_sale?.status === 'active'" icon="pi pi-calendar-clock"
                                        class="p-button-text p-button-sm p-button-help"
                                        @click="router.visit(route('installments.show', data.installment_sale.id))" v-tooltip.top="'Collect installment'" />

                                    <!-- Completed with due => convert to installment plan -->
                                    <Button v-if="canConvert(data)" icon="pi pi-calendar-plus"
                                        class="p-button-text p-button-sm p-button-help"
                                        @click="openConvertModal(data)" v-tooltip.top="'Convert due to installments'" />

                                    <!-- ✅ Draft => Complete Draft -->
                                    <Button v-if="canCompleteDraft(data)" icon="pi pi-check"
                                        class="p-button-text p-button-sm p-button-success"
                                        @click="openCompleteModal(data)" v-tooltip.top="'Complete'" />

                                    <!-- ✅ Draft => Edit (New) -->
                                    <Button v-if="data.status === 'draft'" icon="pi pi-file-edit"
                                        class="p-button-text p-button-sm p-button-info" @click="editDraft(data)"
                                        v-tooltip.top="'Edit'" />

                                    <!-- ✅ Void (draft or completed, not void) -->
                                    <Button v-if="canVoid(data)" icon="pi pi-ban"
                                        class="p-button-text p-button-sm p-button-danger" @click="openVoidModal(data)"
                                        v-tooltip.top="'Void'" />

                                    <!-- ✅ Trash -->
                                    <Button icon="pi pi-trash" class="p-button-text p-button-sm p-button-danger"
                                        @click="trashOrder(data)" v-tooltip.top="'Move to Trash'" />
                                </template>

                                <template v-else>
                                    <Button icon="pi pi-refresh" class="p-button-text p-button-sm p-button-success"
                                        @click="restoreOrder(data)" v-tooltip.top="'Restore'" />
                                    <Button icon="pi pi-times" class="p-button-text p-button-sm p-button-danger"
                                        @click="forceDeleteOrder(data)" v-tooltip.top="'Force Delete'" />
                                </template>
                            </div>
                        </template>
                    </Column>
                </DataTable>

                <!-- PAGINATION -->
                <div class="flex justify-between items-center mt-3 text-sm text-gray-500">
                    <span>Showing {{ orders.from }}–{{ orders.to }} of
                        {{ orders.total }}</span>
                    <div class="flex gap-1">
                        <Button icon="pi pi-angle-left" class="p-button-text" :disabled="!orders.prev_page_url"
                            @click="goToPage(orders.prev_page_url)" />
                        <Button icon="pi pi-angle-right" class="p-button-text" :disabled="!orders.next_page_url"
                            @click="goToPage(orders.next_page_url)" />
                    </div>
                </div>
            </div>
        </div>

        <!-- INVOICE PREVIEW MODAL -->
        <Dialog v-model:visible="showInvoiceModal" modal header="Invoice Preview" :style="{ width: '760px' }"
            :breakpoints="{ '960px': '75vw', '640px': '95vw' }">
            <div v-if="selectedOrder" class="space-y-3">
                <div class="flex justify-between gap-3 flex-wrap">
                    <div>
                        <div class="text-sm text-gray-500">Invoice</div>
                        <div class="text-xl font-bold">
                            {{ invoiceLabel(selectedOrder) }}
                        </div>
                        <div class="text-xs text-gray-500">
                            {{
                                new Date(
                                    selectedOrder.created_at
                                ).toLocaleString()
                            }}
                        </div>
                    </div>

                    <div class="flex gap-2">
                        <Button label="Open" icon="pi pi-external-link" severity="secondary"
                            @click="openInvoicePage(selectedOrder)" />
                        <Button v-if="selectedOrder.status !== 'void'" label="Print" icon="pi pi-print"
                            class="p-button-success" @click="printInvoice(selectedOrder)" />
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div class="p-3 border rounded-lg">
                        <div class="text-xs text-gray-500 font-semibold">
                            Customer
                        </div>
                        <div class="font-medium">
                            {{ selectedOrder.customer?.name || "Walk-in" }}
                        </div>
                    </div>

                    <div class="p-3 border rounded-lg">
                        <div class="text-xs text-gray-500 font-semibold">
                            Summary
                        </div>
                        <div>
                            Total:
                            {{
                                Number(selectedOrder.total_amount || 0).toFixed(
                                    2
                                )
                            }}
                        </div>
                        <div class="capitalize">
                            Payment: {{ selectedOrder.payment_status }}
                        </div>
                        <div class="capitalize">
                            Status: {{ selectedOrder.status }}
                        </div>
                    </div>
                </div>
            </div>
        </Dialog>

        <!-- VOID MODAL -->
        <Dialog v-model:visible="showVoidModal" modal header="Void Order" :style="{ width: '520px' }"
            :breakpoints="{ '960px': '75vw', '640px': '95vw' }">
            <div v-if="voidTarget" class="space-y-3">
                <div class="p-3 rounded-xl border bg-red-50 border-red-200 text-red-800 text-sm">
                    <div class="font-bold">This will void the order.</div>
                    <div class="text-xs mt-1">
                        If order is <b>completed</b>, stock will be restored
                        (your backend logic).
                    </div>
                </div>

                <div class="text-sm">
                    <div><b>Invoice:</b> {{ invoiceLabel(voidTarget) }}</div>
                    <div>
                        <b>Total:</b>
                        {{ Number(voidTarget.total_amount || 0).toFixed(2) }}
                    </div>
                    <div><b>Status:</b> {{ voidTarget.status }}</div>
                </div>

                <div class="flex justify-end gap-2">
                    <Button label="Cancel" class="p-button-text" @click="showVoidModal = false" />
                    <Button label="Void Now" icon="pi pi-ban" class="p-button-danger" :loading="voidLoading"
                        @click="voidOrder" />
                </div>
            </div>
        </Dialog>

        <!-- COMPLETE DRAFT MODAL -->
        <Dialog v-model:visible="showCompleteModal" modal header="Complete Draft" :style="{ width: '760px' }"
            :breakpoints="{ '960px': '75vw', '640px': '95vw' }">
            <div v-if="completeTarget" class="space-y-4">
                <div class="p-3 rounded-xl border bg-emerald-50 border-emerald-200 text-emerald-800 text-sm">
                    <div class="font-bold">Complete this draft order?</div>
                    <div class="text-xs mt-1">
                        Add payments and bank info (if bank method).
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-3 text-sm">
                    <div class="p-3 border rounded-lg">
                        <div class="text-xs text-gray-500 font-semibold">
                            Invoice
                        </div>
                        <div class="font-semibold">
                            {{ invoiceLabel(completeTarget) }}
                        </div>
                    </div>
                    <div class="p-3 border rounded-lg">
                        <div class="text-xs text-gray-500 font-semibold">
                            Total
                        </div>
                        <div class="font-semibold">
                            {{ draftTotal.toFixed(2) }}
                        </div>
                    </div>
                    <div class="p-3 border rounded-lg">
                        <div class="text-xs text-gray-500 font-semibold">
                            Due / Change
                        </div>
                        <div class="font-semibold text-rose-600" v-if="draftDue > 0">
                            Due: {{ draftDue.toFixed(2) }}
                        </div>
                        <div class="font-semibold text-emerald-600" v-if="draftChange > 0">
                            Change: {{ draftChange.toFixed(2) }}
                        </div>
                        <div class="font-semibold text-slate-700" v-if="draftDue === 0 && draftChange === 0">
                            Balanced
                        </div>
                    </div>
                </div>

                <Divider />

                <SelectButton v-model="completeMode" :allowEmpty="false" optionLabel="label" optionValue="value"
                    :options="[{ label: 'Regular / due', value: 'regular' }, { label: 'Installment', value: 'installment' }]" />

                <div class="flex items-center justify-between">
                    <div class="font-semibold">{{ completeMode === 'installment' ? 'Down payment (optional)' : 'Payments' }}</div>
                    <Button label="Add payment" icon="pi pi-plus" class="p-button-text" @click="addRow(completeRows)" />
                </div>

                <div class="space-y-3">
                    <div v-for="(row, idx) in completeRows" :key="row.id" class="border rounded-xl p-3 bg-white">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3 items-end">
                            <div>
                                <div class="text-xs text-slate-500 mb-1">
                                    Method <span class="text-red-600">*</span>
                                </div>
                                <Select v-model="row.payment_method_id" :options="paymentMethods" optionLabel="name"
                                    optionValue="id" placeholder="Select method" filter class="w-full" />
                            </div>

                            <div>
                                <div class="text-xs text-slate-500 mb-1">
                                    Amount <span class="text-red-600">*</span>
                                </div>
                                <InputNumber v-model="row.amount" :min="0" class="w-full" inputClass="w-full" />
                            </div>

                            <div class="flex gap-2 justify-end">
                                <Button icon="pi pi-trash" class="p-button-text p-button-danger"
                                    @click="removeRow(completeRows, idx)" />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mt-3">
                            <div>
                                <div class="text-xs text-slate-500 mb-1">
                                    Txn Ref
                                </div>
                                <InputText v-model="row.transaction_ref" class="w-full"
                                    placeholder="Gateway / Slip ref (optional)" />
                            </div>
                            <div>
                                <div class="text-xs text-slate-500 mb-1">
                                    Notes
                                </div>
                                <InputText v-model="row.notes" class="w-full" placeholder="Optional note" />
                            </div>
                        </div>

                        <EmiPaymentFields v-if="isEmiRow(row)" v-model="row.emi" :banks="emiBanks" :amount="Number(row.amount || 0)" />

                        <div v-if="row.payment_method_id && needsBankMeta(row)"
                            class="mt-3 rounded-lg border bg-slate-50 p-3">
                            <div class="font-semibold text-sm mb-2">
                                Bank Info (Required for bank)
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                <div>
                                    <div class="text-xs text-slate-500 mb-1">
                                        Customer Bank Name
                                        <span class="text-red-600">*</span>
                                    </div>
                                    <InputText v-model="row.meta.customer_bank_name" class="w-full" />
                                </div>

                                <div>
                                    <div class="text-xs text-slate-500 mb-1">
                                        Customer Account No
                                        <span class="text-red-600">*</span>
                                    </div>
                                    <InputText v-model="row.meta.customer_account_no" class="w-full" />
                                </div>

                                <div>
                                    <div class="text-xs text-slate-500 mb-1">
                                        Received To Bank Account ID
                                    </div>
                                    <InputNumber v-model="row.meta.received_to_bank_account_id
                                        " class="w-full" inputClass="w-full" />
                                </div>

                                <div>
                                    <div class="text-xs text-slate-500 mb-1">
                                        Bank Txn / Cheque No
                                    </div>
                                    <InputText v-model="row.meta.txn_ref" class="w-full" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div v-if="completeMode === 'installment'" class="rounded-xl border border-violet-200 p-3">
                    <div class="font-semibold mb-2">Installment plan</div>
                    <InstallmentConfigurator v-model="completeInstallment" :saleAmount="draftTotal" :downPayment="draftPayTotal"
                        :plans="installmentPlans" @preview="completePreview = $event" />
                </div>

                <Divider />

                <div class="flex justify-end gap-2">
                    <Button label="Cancel" class="p-button-text" @click="showCompleteModal = false" />
                    <Button label="Complete Draft" icon="pi pi-check" class="p-button-success"
                        :loading="completeLoading" @click="completeDraft" />
                </div>
            </div>
        </Dialog>

        <!-- PAY DUE MODAL -->
        <Dialog v-model:visible="showPayDueModal" modal header="Pay Due" :style="{ width: '760px' }"
            :breakpoints="{ '960px': '75vw', '640px': '95vw' }">
            <div v-if="payDueTarget" class="space-y-4">
                <div class="p-3 rounded-xl border bg-amber-50 border-amber-200 text-amber-900 text-sm">
                    <div class="font-bold">Add payment for this sale</div>
                    <div class="text-xs mt-1">
                        Current Due: <b>{{ currentDue.toFixed(2) }}</b> • After
                        Pay: <b>{{ dueAfterPay.toFixed(2) }}</b>
                        <span v-if="changeAfterPay > 0">
                            • Change:
                            <b>{{ changeAfterPay.toFixed(2) }}</b></span>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-3 text-sm">
                    <div class="p-3 border rounded-lg">
                        <div class="text-xs text-gray-500 font-semibold">
                            Invoice
                        </div>
                        <div class="font-semibold">
                            {{ invoiceLabel(payDueTarget) }}
                        </div>
                    </div>
                    <div class="p-3 border rounded-lg">
                        <div class="text-xs text-gray-500 font-semibold">
                            Total
                        </div>
                        <div class="font-semibold">
                            {{ orderTotal.toFixed(2) }}
                        </div>
                    </div>
                    <div class="p-3 border rounded-lg">
                        <div class="text-xs text-gray-500 font-semibold">
                            Already Paid
                        </div>
                        <div class="font-semibold">
                            {{ alreadyPaid.toFixed(2) }}
                        </div>
                    </div>
                </div>

                <Divider />

                <div class="flex items-center justify-between">
                    <div class="font-semibold">Payments</div>
                    <Button label="Add payment" icon="pi pi-plus" class="p-button-text" @click="addRow(payDueRows)" />
                </div>

                <div class="space-y-3">
                    <div v-for="(row, idx) in payDueRows" :key="row.id" class="border rounded-xl p-3 bg-white">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3 items-end">
                            <div>
                                <div class="text-xs text-slate-500 mb-1">
                                    Method <span class="text-red-600">*</span>
                                </div>
                                <Select v-model="row.payment_method_id" :options="paymentMethods" optionLabel="name"
                                    optionValue="id" placeholder="Select method" filter class="w-full" />
                            </div>

                            <div>
                                <div class="text-xs text-slate-500 mb-1">
                                    Amount <span class="text-red-600">*</span>
                                </div>
                                <InputNumber v-model="row.amount" :min="0" class="w-full" inputClass="w-full" />
                            </div>

                            <div class="flex gap-2 justify-end">
                                <Button icon="pi pi-trash" class="p-button-text p-button-danger"
                                    @click="removeRow(payDueRows, idx)" />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mt-3">
                            <div>
                                <div class="text-xs text-slate-500 mb-1">
                                    Txn Ref
                                </div>
                                <InputText v-model="row.transaction_ref" class="w-full"
                                    placeholder="Gateway / Slip ref (optional)" />
                            </div>
                            <div>
                                <div class="text-xs text-slate-500 mb-1">
                                    Notes
                                </div>
                                <InputText v-model="row.notes" class="w-full" placeholder="Optional note" />
                            </div>
                        </div>

                        <EmiPaymentFields v-if="isEmiRow(row)" v-model="row.emi" :banks="emiBanks" :amount="Number(row.amount || 0)" />

                        <div v-if="row.payment_method_id && needsBankMeta(row)"
                            class="mt-3 rounded-lg border bg-slate-50 p-3">
                            <div class="font-semibold text-sm mb-2">
                                Bank Info (Required for bank)
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                <div>
                                    <div class="text-xs text-slate-500 mb-1">
                                        Customer Bank Name
                                        <span class="text-red-600">*</span>
                                    </div>
                                    <InputText v-model="row.meta.customer_bank_name" class="w-full" />
                                </div>

                                <div>
                                    <div class="text-xs text-slate-500 mb-1">
                                        Customer Account No
                                        <span class="text-red-600">*</span>
                                    </div>
                                    <InputText v-model="row.meta.customer_account_no" class="w-full" />
                                </div>

                                <div>
                                    <div class="text-xs text-slate-500 mb-1">
                                        Received To Bank Account ID
                                    </div>
                                    <InputNumber v-model="row.meta.received_to_bank_account_id
                                        " class="w-full" inputClass="w-full" />
                                </div>

                                <div>
                                    <div class="text-xs text-slate-500 mb-1">
                                        Bank Txn / Cheque No
                                    </div>
                                    <InputText v-model="row.meta.txn_ref" class="w-full" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <Divider />

                <div class="flex justify-end gap-2">
                    <Button label="Cancel" class="p-button-text" @click="showPayDueModal = false" />
                    <Button label="Submit Payment" icon="pi pi-check" class="p-button-warning" :loading="payDueLoading"
                        @click="submitPayDue" />
                </div>
            </div>
        </Dialog>

        <!-- CONVERT TO INSTALLMENT MODAL -->
        <Dialog v-model:visible="showConvertModal" modal header="Convert due to installments" :style="{ width: '820px' }"
            :breakpoints="{ '960px': '85vw', '640px': '95vw' }">
            <div v-if="convertTarget" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3 text-sm">
                    <div class="p-3 border rounded-lg">
                        <div class="text-xs text-gray-500 font-semibold">Invoice</div>
                        <div class="font-semibold">{{ invoiceLabel(convertTarget) }}</div>
                    </div>
                    <div class="p-3 border rounded-lg">
                        <div class="text-xs text-gray-500 font-semibold">Total / Paid</div>
                        <div class="font-semibold">{{ Number(convertTarget.total_amount).toFixed(2) }} / {{ Number(convertTarget.paid_amount).toFixed(2) }}</div>
                    </div>
                    <div class="p-3 border rounded-lg">
                        <div class="text-xs text-gray-500 font-semibold">Due to finance</div>
                        <div class="font-semibold text-rose-600">{{ orderDue(convertTarget).toFixed(2) }}</div>
                    </div>
                </div>

                <div>
                    <div class="text-xs text-slate-500 mb-1">Customer <span class="text-red-600">*</span></div>
                    <AutoComplete v-model="convertCustomer" :suggestions="customerSuggestions" optionLabel="name" class="w-full"
                        inputClass="w-full" placeholder="Search by name or phone" @complete="searchCustomers">
                        <template #option="{ option }">
                            <div class="flex flex-col"><span>{{ option.name }}</span><span class="text-xs text-slate-500">{{ option.phone }}</span></div>
                        </template>
                    </AutoComplete>
                </div>

                <InstallmentConfigurator v-model="convertConfig" :saleAmount="Number(convertTarget.total_amount)"
                    :downPayment="Number(convertTarget.paid_amount)" :plans="installmentPlans" @preview="convertPreview = $event" />

                <div class="flex justify-end gap-2">
                    <Button label="Cancel" class="p-button-text" @click="showConvertModal = false" />
                    <Button label="Create agreement" icon="pi pi-calendar-plus" :loading="convertLoading"
                        :disabled="!convertPreview || convertPreview.financed_amount <= 0" @click="submitConvert" />
                </div>
            </div>
        </Dialog>

        <!-- TRANSFER MODAL -->
        <Dialog v-model:visible="showTransferModal" modal header="Transfer Orders to Branch" :style="{ width: '520px' }"
            :breakpoints="{ '960px': '75vw', '640px': '95vw' }">
            <div class="space-y-4">
                <div class="p-3 rounded-xl border bg-blue-50 border-blue-200 text-blue-800 text-sm">
                    <div class="font-bold">Transfer completed orders to another branch</div>
                    <div class="text-xs mt-1">
                        Only completed orders will be transferred. Draft orders will be skipped.
                    </div>
                </div>

                <div class="text-sm">
                    <div><b>Selected Orders:</b> {{ selectedOrders.length }}</div>
                </div>

                <div>
                    <div class="text-sm font-semibold mb-2">Target Branch <span class="text-red-600">*</span></div>
                    <Select v-model="targetBranchId" :options="branches" optionLabel="name" optionValue="id"
                        placeholder="Select branch" filter class="w-full" />
                </div>

                <div class="flex justify-end gap-2">
                    <Button label="Cancel" class="p-button-text" @click="showTransferModal = false" />
                    <Button label="Transfer" icon="pi pi-directions" :loading="transferLoading"
                        :disabled="!targetBranchId" @click="transferOrders" />
                </div>
            </div>
        </Dialog>

        <!-- EDIT DATE MODAL (Admin Only) -->
        <Dialog v-model:visible="showEditDateModal" modal header="Edit Order Date" :style="{ width: '520px' }"
            :breakpoints="{ '960px': '75vw', '640px': '95vw' }">
            <div class="space-y-4">
                <div class="p-3 rounded-xl border bg-amber-50 border-amber-200 text-amber-800 text-sm">
                    <div class="font-bold">Change the sale date for this order</div>
                    <div class="text-xs mt-1">
                        This will update when the sale appears to have been made. Admin only.
                    </div>
                </div>

                <div class="text-sm">
                    <div><b>Invoice:</b> {{ invoiceLabel(editDateTarget) }}</div>
                    <div><b>Current Date:</b> {{
                        editDateTarget?.created_at ? new Date(editDateTarget.created_at).toLocaleString() : '-'
                        }}</div>
                </div>

                <div>
                    <div class="text-sm font-semibold mb-2">New Sale Date <span class="text-red-600">*</span></div>
                    <Calendar v-model="newSaleDate" showTime hourFormat="24" dateFormat="yy-mm-dd" showIcon
                        class="w-full" />
                </div>

                <div class="flex justify-end gap-2">
                    <Button label="Cancel" class="p-button-text" @click="showEditDateModal = false" />
                    <Button label="Update Date" icon="pi pi-calendar" :loading="editDateLoading"
                        @click="updateOrderDate" />
                </div>
            </div>
        </Dialog>
    </AuthenticatedLayout>
</template>
