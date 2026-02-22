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
import Dropdown from "primevue/dropdown";
import InputNumber from "primevue/inputnumber";
import InputText from "primevue/inputtext";
import Tag from "primevue/tag";
import Tooltip from 'primevue/tooltip';
import TabMenu from 'primevue/tabmenu';
import SplitButton from 'primevue/splitbutton';
import { useToast } from "primevue/usetoast";
import { v4 as uuidv4 } from 'uuid';

const vTooltip = Tooltip;

const toast = useToast();

const props = defineProps({
    orders: Object,
    filters: Object,
    paymentMethods: { type: Array, default: () => [] },
    categories: { type: Array, default: () => [] },
    brands: { type: Array, default: () => [] },
    available_products: { type: Array, default: () => [] },
    insights: { type: Object, default: () => ({}) },
});

/* ---------------- Filters ---------------- */
const search = ref(props.filters?.search || "");
const status = ref(props.filters?.status || "");
const paymentStatus = ref(props.filters?.payment_status || "");
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
            date_from: dateFrom.value ? fmtDate(dateFrom.value) : undefined,
            date_to: dateTo.value ? fmtDate(dateTo.value) : undefined,
            category_id: categoryId.value || undefined,
            brand_id: brandId.value || undefined,
            product_id: productId.value || undefined,
            trashed: trashed.value ? 1 : undefined,
        },
        { preserveState: true, replace: true }
    );
}

function resetFilter() {
    search.value = "";
    status.value = "";
    paymentStatus.value = "";
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
    };
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
function openCompleteModal(order) {
    completeTarget.value = order;
    showCompleteModal.value = true;
    completeRows.value = [newPaymentRow()];
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
        meta: cleanMeta(p.meta),
    }));

    return payload;
}

function completeDraft() {
    if (!completeTarget.value) return;

    const valid = validatePaymentsOrToast(completeRows);
    if (!valid) return;

    completeLoading.value = true;
    router.post(
        route("pos.orders.complete", completeTarget.value.id),
        { payments: valid },
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
            onError: () => {
                toast.add({
                    severity: "error",
                    summary: "Failed",
                    detail: "Could not complete draft",
                    life: 2500,
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
                        <InputText v-model="search" placeholder="Invoice / customer / cashier" class="w-full"
                            @keyup.enter="applyFilter" />
                    </InputGroup>

                    <Dropdown v-model="status" :options="statusOptions" optionLabel="label" optionValue="value"
                        placeholder="Order Status" class="w-1/2 md:w-44" />
                    <Dropdown v-model="paymentStatus" :options="paymentStatusOptions" optionLabel="label"
                        optionValue="value" placeholder="Payment Status" class="w-1/2 md:w-44" />

                    <Calendar v-model="dateFrom" placeholder="From" dateFormat="yy-mm-dd" showIcon
                        class="w-1/2 md:w-44" />
                    <Calendar v-model="dateTo" placeholder="To" dateFormat="yy-mm-dd" showIcon class="w-1/2 md:w-44" />
                </div>

                <div class="flex flex-wrap gap-3 items-center pt-2 border-t border-slate-100">
                    <Dropdown v-model="categoryId" :options="categories" optionLabel="name" optionValue="id"
                        placeholder="All Categories" class="w-1/2 md:w-56" showClear filter />
                    <Dropdown v-model="brandId" :options="brands" optionLabel="name" optionValue="id"
                        placeholder="All Brands" class="w-1/2 md:w-56" showClear />
                    <Dropdown v-model="productId" :options="available_products" optionLabel="name" optionValue="id"
                        placeholder="Filter by Product" class="w-full md:w-72" filter showClear />

                    <div class="flex gap-2 ml-auto">
                        <Button label="Apply" icon="pi pi-filter" class="p-button-outlined" @click="applyFilter" />
                        <Button label="Reset" icon="pi pi-refresh" class="p-button-text" @click="resetFilter" />

                        <Divider layout="vertical" class="hidden md:block" />

                        <Button icon="pi pi-file-pdf" class="p-button-outlined p-button-danger"
                            v-tooltip.top="'Export PDF'" @click="exportReport('pdf')" />
                        <Button icon="pi pi-file-excel" class="p-button-outlined p-button-success"
                            v-tooltip.top="'Export Excel'" @click="exportReport('excel')" />
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
                        <template #body="{ data }">{{
                            data.customer?.name || "Walk-in"
                            }}</template>
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

                    <Column header="Total">
                        <template #body="{ data }">
                            <span class="font-semibold text-emerald-600">{{
                                Number(data.total_amount || 0).toFixed(2)
                                }}</span>
                        </template>
                    </Column>

                    <Column header="Payment">
                        <template #body="{ data }">
                            <Tag :value="data.payment_status" :severity="paymentSeverity(data.payment_status)" />
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

                <div class="flex items-center justify-between">
                    <div class="font-semibold">Payments</div>
                    <Button label="Add payment" icon="pi pi-plus" class="p-button-text" @click="addRow(completeRows)" />
                </div>

                <div class="space-y-3">
                    <div v-for="(row, idx) in completeRows" :key="row.id" class="border rounded-xl p-3 bg-white">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3 items-end">
                            <div>
                                <div class="text-xs text-slate-500 mb-1">
                                    Method <span class="text-red-600">*</span>
                                </div>
                                <Dropdown v-model="row.payment_method_id" :options="paymentMethods" optionLabel="name"
                                    optionValue="id" placeholder="Select method" class="w-full" />
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
                                <Dropdown v-model="row.payment_method_id" :options="paymentMethods" optionLabel="name"
                                    optionValue="id" placeholder="Select method" class="w-full" />
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
    </AuthenticatedLayout>
</template>
