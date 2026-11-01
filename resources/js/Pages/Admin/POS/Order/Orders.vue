<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, router } from "@inertiajs/vue3";
import { computed, ref, watch } from "vue";
import { useI18n } from "vue-i18n";

import AutoComplete from "primevue/autocomplete";
import Button from "primevue/button";
import Column from "primevue/column";
import ConfirmDialog from "primevue/confirmdialog";
import DataTable from "primevue/datatable";
import DatePicker from "primevue/datepicker";
import Dialog from "primevue/dialog";
import InputGroup from "primevue/inputgroup";
import InputGroupAddon from "primevue/inputgroupaddon";
import InputNumber from "primevue/inputnumber";
import InputText from "primevue/inputtext";
import Menu from "primevue/menu";
import Paginator from "primevue/paginator";
import Select from "primevue/select";
import SelectButton from "primevue/selectbutton";
import SplitButton from "primevue/splitbutton";
import Tag from "primevue/tag";
import Tooltip from "primevue/tooltip";
import { useConfirm } from "primevue/useconfirm";
import { useToast } from "primevue/usetoast";
import { v4 as uuidv4 } from "uuid";

import EmiPaymentFields from "@/Components/Sales/EmiPaymentFields.vue";
import InstallmentConfigurator from "@/Components/Sales/InstallmentConfigurator.vue";
import { useFlashToast } from "@/Composables/useFlashToast";
import { cleanParams, firstError, money, ymd, ymdhms } from "@/Helpers/format";
import { defaultInstallmentConfig, installmentPayload } from "@/Helpers/installment";

const vTooltip = Tooltip;

const { t } = useI18n();
const toast = useToast();
const confirm = useConfirm();
useFlashToast();

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

/* ---------------- Helpers ---------------- */
const warn = (detail) => toast.add({ severity: "warn", summary: t("common.warning"), detail, life: 2800 });
const fail = (errors, fallback) =>
    toast.add({ severity: "error", summary: t("shared.terms.error"), detail: firstError(errors) || fallback, life: 3500 });
const statusLabel = (v) => (v ? t(`shared.status.${v}`) : "-");
const saleTypeLabel = (v) => t(`shared.sale_type.${v || "regular"}`);

/** Server dates arrive as "2026-10-24 09:46 AM UTC" (order cast) — parse that reliably on every browser. */
function parseServerDate(v) {
    if (!v) return null;
    if (v instanceof Date) return v;
    const m = String(v).match(/^(\d{4})-(\d{2})-(\d{2})[ T](\d{2}):(\d{2})(?::(\d{2}))?\s*(AM|PM)?\s*(UTC|GMT)?/i);
    if (!m) {
        const d = new Date(v);
        return isNaN(d) ? null : d;
    }
    let [, y, mo, d, h, mi, s, ap, tz] = m;
    h = Number(h);
    if (ap) {
        if (ap.toUpperCase() === "PM" && h < 12) h += 12;
        if (ap.toUpperCase() === "AM" && h === 12) h = 0;
    }
    const args = [Number(y), Number(mo) - 1, Number(d), h, Number(mi), Number(s || 0)];
    return tz ? new Date(Date.UTC(...args)) : new Date(...args);
}

function fmtDateTime(v) {
    const d = parseServerDate(v);
    if (!d) return v || "-";
    return d.toLocaleString(undefined, { day: "2-digit", month: "short", year: "numeric", hour: "2-digit", minute: "2-digit" });
}

/* ---------------- Filters ---------------- */
const search = ref(props.filters?.search || "");
const status = ref(props.filters?.status || "");
const paymentStatus = ref(props.filters?.payment_status || "");
const saleType = ref(props.filters?.sale_type || "");
const dateFrom = ref(props.filters?.date_from ? new Date(props.filters.date_from) : null);
const dateTo = ref(props.filters?.date_to ? new Date(props.filters.date_to) : null);
const trashed = ref(props.filters?.trashed || false);
const categoryId = ref(props.filters?.category_id || null);
const brandId = ref(props.filters?.brand_id || null);
const productId = ref(props.filters?.product_id || null);

const showInsights = ref(false);
const showMoreFilters = ref(!!(props.filters?.sale_type || props.filters?.category_id || props.filters?.brand_id || props.filters?.product_id));

const statusOptions = computed(() => [
    { label: t("common.all"), value: "" },
    { label: t("shared.status.completed"), value: "completed" },
    { label: t("shared.status.draft"), value: "draft" },
    { label: t("shared.status.void"), value: "void" },
]);
const paymentStatusOptions = computed(() => [
    { label: t("common.all"), value: "" },
    { label: t("shared.status.paid"), value: "paid" },
    { label: t("shared.status.partial"), value: "partial" },
    { label: t("shared.status.unpaid"), value: "unpaid" },
]);
const saleTypeOptions = computed(() => [
    { label: t("orders_page.all_types"), value: "" },
    { label: t("shared.sale_type.regular"), value: "regular" },
    { label: t("shared.sale_type.installment"), value: "installment" },
    { label: t("shared.sale_type.emi"), value: "emi" },
]);

const viewOptions = computed(() => [
    { label: t("orders_page.active_orders"), value: false, icon: "pi pi-receipt" },
    { label: t("orders_page.trash"), value: true, icon: "pi pi-trash" },
]);

function filterParams(extra = {}) {
    return cleanParams({
        search: search.value?.trim(),
        status: status.value,
        payment_status: paymentStatus.value,
        sale_type: saleType.value,
        date_from: dateFrom.value ? ymd(dateFrom.value) : undefined,
        date_to: dateTo.value ? ymd(dateTo.value) : undefined,
        category_id: categoryId.value,
        brand_id: brandId.value,
        product_id: productId.value,
        trashed: trashed.value ? 1 : undefined,
        all_branches: props.filters?.all_branches ? 1 : undefined,
        per_page: props.orders?.per_page && Number(props.orders.per_page) !== 15 ? props.orders.per_page : undefined,
        ...extra,
    });
}

function applyFilter(extra = {}) {
    router.get(route("pos.orders.index"), filterParams(extra), { preserveState: true, replace: true, preserveScroll: true });
}

function setTrashed(v) {
    trashed.value = v;
    selectedOrders.value = [];
    applyFilter();
}

function toggleAllBranches() {
    router.get(route("pos.orders.index"), { all_branches: props.filters?.all_branches ? undefined : 1 }, { preserveState: true });
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
    router.get(route("pos.orders.index"), {}, { replace: true });
}

function exportReport(format) {
    window.open(route("pos.orders.index", { ...cleanParams(props.filters || {}), export: format }), "_blank");
}

const exportItems = computed(() => [
    { label: "PDF", icon: "pi pi-file-pdf", command: () => exportReport("pdf") },
    { label: "Excel", icon: "pi pi-file-excel", command: () => exportReport("excel") },
    { label: "CSV", icon: "pi pi-file", command: () => exportReport("csv") },
]);

/* ---------------- Pagination (server driven) ---------------- */
const firstRow = computed(() => Math.max(0, (props.orders?.from || 1) - 1));
function onPage(e) {
    applyFilter({ page: e.page + 1, per_page: e.rows !== 15 ? e.rows : undefined });
}

/* ---------------- Data ---------------- */
const rows = computed(() => props.orders?.data || []);
const pageTotal = computed(() => rows.value.reduce((s, o) => s + Number(o.total_amount || 0), 0));
const pageDue = computed(() => rows.value.reduce((s, o) => s + orderDue(o), 0));

function invoiceLabel(o) {
    if (!o) return "";
    return o.invoice_no || (o.status === "draft" ? `DRAFT-${o.id}` : `#${o.id}`);
}

function openInvoicePage(order) {
    window.open(route("pos.orders.invoice", order.id), "_blank");
}
function printInvoice(order) {
    window.open(route("pos.orders.invoice", order.id) + "?autoprint=1", "_blank");
}

function getOrderCategories(order) {
    if (!order.items) return "-";
    const cats = order.items.map((it) => it.product?.category?.name).filter((c, i, a) => c && a.indexOf(c) === i);
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
    return "danger";
}

function orderDue(order) {
    if (!order || order.status !== "completed") return 0;
    return Math.max(0, Number(order.total_amount || 0) - Number(order.paid_amount || 0));
}

/* ---------------- Confirm helper ---------------- */
function ask({ message, header, danger = false, accept }) {
    confirm.require({
        message,
        header: header || t("shared.terms.confirm_title"),
        icon: danger ? "pi pi-exclamation-triangle" : "pi pi-question-circle",
        rejectProps: { label: t("shared.terms.cancel"), severity: "secondary", outlined: true },
        acceptProps: { label: t("shared.terms.yes"), severity: danger ? "danger" : "primary" },
        accept,
    });
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
        meta: { customer_bank_name: "", customer_account_no: "", received_to_bank_account_id: null, txn_ref: "" },
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

function cleanMeta(meta) {
    if (!meta) return null;
    const m = {
        customer_bank_name: meta.customer_bank_name || null,
        customer_account_no: meta.customer_account_no || null,
        received_to_bank_account_id: meta.received_to_bank_account_id || null,
        txn_ref: meta.txn_ref || null,
    };
    return Object.values(m).some((v) => v !== null) ? m : null;
}

function validatePaymentsOrToast(rowsRef) {
    const validRows = rowsRef.value.filter((p) => p.payment_method_id && Number(p.amount || 0) > 0);

    if (!validRows.length) {
        warn(t("orders_page.payment_required"));
        return null;
    }

    for (const row of validRows) {
        if (isEmiRow(row) && !row.emi?.emi_bank_tenure_id) {
            warn(t("orders_page.emi_bank_required"));
            return null;
        }
        if (needsBankMeta(row)) {
            const bank = row.meta || {};
            if (!bank.customer_bank_name || !bank.customer_account_no) {
                warn(t("orders_page.bank_info_required"));
                return null;
            }
        }
    }

    return validRows.map((p) => ({
        payment_method_id: p.payment_method_id,
        amount: Number(p.amount),
        transaction_ref: p.transaction_ref?.trim() || null,
        notes: p.notes?.trim() || null,
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
}

/* ---------------- Void ---------------- */
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
    router.post(route("pos.orders.void", voidTarget.value.id), {}, {
        preserveScroll: true,
        onSuccess: () => {
            showVoidModal.value = false;
            voidTarget.value = null;
        },
        onError: (e) => fail(e, t("orders_page.void_failed")),
        onFinish: () => (voidLoading.value = false),
    });
}

/* ---------------- Transfer ---------------- */
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
        ids: selectedOrders.value.map((o) => o.id),
        target_branch_id: targetBranchId.value,
    }, {
        preserveScroll: true,
        onSuccess: () => {
            selectedOrders.value = [];
            showTransferModal.value = false;
            targetBranchId.value = null;
        },
        onError: (e) => fail(e, t("orders_page.transfer_failed")),
        onFinish: () => (transferLoading.value = false),
    });
}

/* ---------------- Trash / Restore / Force Delete ---------------- */
function trashOrder(order) {
    ask({
        message: t("orders_page.confirm_trash", { invoice: invoiceLabel(order) }),
        accept: () => router.delete(route("pos.orders.destroy", order.id), { preserveScroll: true }),
    });
}

function restoreOrder(order) {
    ask({
        message: t("orders_page.confirm_restore", { invoice: invoiceLabel(order) }),
        accept: () => router.post(route("pos.orders.restore", order.id), {}, { preserveScroll: true }),
    });
}

function forceDeleteOrder(order) {
    ask({
        danger: true,
        message: t("orders_page.confirm_force_delete", { invoice: invoiceLabel(order) }),
        accept: () => router.delete(route("pos.orders.force-delete", order.id), { preserveScroll: true }),
    });
}

/* ---------------- Bulk Actions ---------------- */
const selectedOrders = ref([]);

const bulkActions = computed(() =>
    trashed.value
        ? [
            { label: t("orders_page.restore_selected"), icon: "pi pi-refresh", command: () => bulkSubmit("restore") },
            { label: t("orders_page.force_delete_selected"), icon: "pi pi-times", command: () => bulkSubmit("force_delete") },
        ]
        : [
            { label: t("orders_page.transfer_to_branch"), icon: "pi pi-directions", command: () => openTransferModal() },
            { label: t("orders_page.trash_selected"), icon: "pi pi-trash", command: () => bulkSubmit("trash") },
        ]
);

function bulkSubmit(action) {
    if (!selectedOrders.value.length) return;
    ask({
        danger: action !== "restore",
        message: t(`orders_page.confirm_bulk_${action}`, { count: selectedOrders.value.length }),
        accept: () =>
            router.post(route("pos.orders.bulk-action"), {
                action,
                ids: selectedOrders.value.map((o) => o.id),
            }, {
                preserveScroll: true,
                onSuccess: () => (selectedOrders.value = []),
            }),
    });
}

/* ---------------- Complete Draft ---------------- */
const showCompleteModal = ref(false);
const completeTarget = ref(null);
const completeLoading = ref(false);
const completeRows = ref([newPaymentRow()]);
const completeMode = ref("regular");
const completeInstallment = ref(defaultInstallmentConfig());
const completePreview = ref(null);

const completeModeOptions = computed(() => [
    { label: t("orders_page.regular_or_due"), value: "regular" },
    { label: t("shared.sale_type.installment"), value: "installment" },
]);

function canCompleteDraft(order) {
    return order?.status === "draft";
}

function openCompleteModal(order) {
    completeTarget.value = order;
    showCompleteModal.value = true;
    completeRows.value = [newPaymentRow()];
    completeMode.value = "regular";
    completeInstallment.value = defaultInstallmentConfig();
}

const draftTotal = computed(() => Number(completeTarget.value?.total_amount || 0));
const draftPayTotal = computed(() => completeRows.value.reduce((s, r) => s + Number(r.amount || 0), 0));
const draftDue = computed(() => Math.max(0, draftTotal.value - draftPayTotal.value));
const draftChange = computed(() => Math.max(0, draftPayTotal.value - draftTotal.value));

function completeDraft() {
    if (!completeTarget.value) return;

    const installmentOn = completeMode.value === "installment";
    const hasPayments = completeRows.value.some((p) => p.payment_method_id && Number(p.amount || 0) > 0);
    const valid = installmentOn && !hasPayments ? [] : validatePaymentsOrToast(completeRows);
    if (!valid) return;
    if (installmentOn && !completeTarget.value.customer_id) {
        warn(t("orders_page.draft_needs_customer"));
        return;
    }

    completeLoading.value = true;
    router.post(
        route("pos.orders.complete", completeTarget.value.id),
        { payments: valid, installment: installmentOn ? installmentPayload(completeInstallment.value) : null },
        {
            preserveScroll: true,
            onSuccess: () => {
                showCompleteModal.value = false;
                completeTarget.value = null;
            },
            onError: (e) => fail(e, t("orders_page.complete_failed")),
            onFinish: () => (completeLoading.value = false),
        }
    );
}

/* ---------------- Pay Due ---------------- */
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
        (order.payment_status === "partial" || order.payment_status === "unpaid")
    );
}

function openPayDueModal(order) {
    payDueTarget.value = order;
    showPayDueModal.value = true;
    payDueRows.value = [newPaymentRow()];
}

const orderTotal = computed(() => Number(payDueTarget.value?.total_amount || 0));
const alreadyPaid = computed(() => Number(payDueTarget.value?.paid_amount || 0));
const currentDue = computed(() => Math.max(0, orderTotal.value - alreadyPaid.value));
const payNowTotal = computed(() => payDueRows.value.reduce((s, r) => s + Number(r.amount || 0), 0));
const dueAfterPay = computed(() => Math.max(0, currentDue.value - payNowTotal.value));
const changeAfterPay = computed(() => Math.max(0, payNowTotal.value - currentDue.value));

function fillFullDue() {
    const methodId = payDueRows.value[0]?.payment_method_id || null;
    payDueRows.value = [{ ...newPaymentRow(), amount: currentDue.value, payment_method_id: methodId }];
}

function submitPayDue() {
    if (!payDueTarget.value) return;
    const valid = validatePaymentsOrToast(payDueRows);
    if (!valid) return;

    payDueLoading.value = true;
    router.post(route("pos.orders.payments.store", payDueTarget.value.id), { payments: valid }, {
        preserveScroll: true,
        onSuccess: () => {
            showPayDueModal.value = false;
            payDueTarget.value = null;
        },
        onError: (e) => fail(e, t("orders_page.payment_failed")),
        onFinish: () => (payDueLoading.value = false),
    });
}

/* ---------------- Convert to Installment ---------------- */
const showConvertModal = ref(false);
const convertTarget = ref(null);
const convertLoading = ref(false);
const convertConfig = ref(defaultInstallmentConfig());
const convertPreview = ref(null);
const convertCustomer = ref(null);
const customerSuggestions = ref([]);

function canConvert(order) {
    return order && !order.deleted_at && order.status === "completed" && !order.installment_sale && orderDue(order) > 0.009;
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
    try {
        const res = await fetch(route("pos.customers.search", { q }), { headers: { Accept: "application/json" } });
        customerSuggestions.value = (await res.json()).data || [];
    } catch {
        customerSuggestions.value = [];
    }
}

function submitConvert() {
    if (!convertTarget.value) return;
    if (!convertCustomer.value?.id) {
        warn(t("orders_page.select_agreement_customer"));
        return;
    }
    convertLoading.value = true;
    router.post(route("installments.store"), {
        order_id: convertTarget.value.id,
        customer_id: convertCustomer.value.id,
        installment: installmentPayload(convertConfig.value),
    }, {
        onSuccess: () => (showConvertModal.value = false),
        onError: (e) => fail(e, t("orders_page.convert_failed")),
        onFinish: () => (convertLoading.value = false),
    });
}

function editDraft(order) {
    if (order) router.visit(route("pos.orders.edit", order.id));
}

/* ---------------- Edit Date (admin) ---------------- */
const showEditDateModal = ref(false);
const editDateTarget = ref(null);
const editDateLoading = ref(false);
const newSaleDate = ref(null);

function openEditDateModal(order) {
    editDateTarget.value = order;
    newSaleDate.value = parseServerDate(order.created_at) || new Date();
    showEditDateModal.value = true;
}

function updateOrderDate() {
    if (!editDateTarget.value || !newSaleDate.value) return;
    editDateLoading.value = true;
    // local time — toISOString() would shift the date to UTC
    router.put(route("pos.orders.update-date", editDateTarget.value.id), { sale_date: ymdhms(newSaleDate.value) }, {
        preserveScroll: true,
        onSuccess: () => {
            showEditDateModal.value = false;
            editDateTarget.value = null;
        },
        onError: (e) => fail(e, t("orders_page.date_failed")),
        onFinish: () => (editDateLoading.value = false),
    });
}

/* ---------------- Row actions (buttons on desktop, ⋮ menu on phones) ---------------- */
function rowActions(o) {
    if (o.deleted_at) {
        return [
            { key: "restore", label: t("orders_page.restore"), icon: "pi pi-refresh", severity: "success", command: () => restoreOrder(o) },
            { key: "force", label: t("orders_page.force_delete"), icon: "pi pi-times", severity: "danger", command: () => forceDeleteOrder(o) },
        ];
    }
    const list = [
        { key: "view", label: t("orders_page.preview"), icon: "pi pi-eye", command: () => openInvoiceModal(o) },
        { key: "open", label: t("orders_page.open_invoice"), icon: "pi pi-external-link", command: () => openInvoicePage(o) },
    ];
    if (o.status !== "void") list.push({ key: "print", label: t("shared.terms.print"), icon: "pi pi-print", command: () => printInvoice(o) });
    if (o.installment_sale?.status === "active") {
        list.push({ key: "collect", label: t("orders_page.collect_installment"), icon: "pi pi-calendar-clock", severity: "help", command: () => router.visit(route("installments.show", o.installment_sale.id)) });
    }
    if (canPayDue(o)) list.push({ key: "paydue", label: t("orders_page.pay_due"), icon: "pi pi-wallet", severity: "warn", command: () => openPayDueModal(o) });
    if (canConvert(o)) list.push({ key: "convert", label: t("orders_page.convert_installment"), icon: "pi pi-calendar-plus", severity: "help", command: () => openConvertModal(o) });
    if (canCompleteDraft(o)) {
        list.push({ key: "complete", label: t("orders_page.complete_draft"), icon: "pi pi-check", severity: "success", command: () => openCompleteModal(o) });
        list.push({ key: "edit", label: t("common.edit"), icon: "pi pi-file-edit", severity: "info", command: () => editDraft(o) });
    }
    if (props.isAdmin && o.status === "completed") {
        list.push({ key: "date", label: t("orders_page.edit_date"), icon: "pi pi-calendar", severity: "help", command: () => openEditDateModal(o) });
    }
    if (canVoid(o)) list.push({ key: "void", label: t("orders_page.void"), icon: "pi pi-ban", severity: "danger", command: () => openVoidModal(o) });
    list.push({ key: "trash", label: t("orders_page.move_to_trash"), icon: "pi pi-trash", severity: "danger", command: () => trashOrder(o) });
    return list;
}

const rowMenu = ref();
const rowMenuItems = ref([]);
function openRowMenu(event, order) {
    rowMenuItems.value = rowActions(order).map(({ label, icon, command }) => ({ label, icon, command }));
    rowMenu.value.toggle(event);
}

const dialogBreakpoints = { "960px": "75vw", "640px": "95vw" };

// clear selection when the page of data changes
watch(() => props.orders?.current_page, () => (selectedOrders.value = []));
</script>

<template>
    <AuthenticatedLayout>
        <Head :title="t('orders_page.title')" />
        <ConfirmDialog :breakpoints="dialogBreakpoints" :style="{ width: '420px' }" />
        <Menu ref="rowMenu" :model="rowMenuItems" popup />

        <div class="p-2 md:p-4 space-y-4">
            <!-- HEADER -->
            <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-3">
                <div class="min-w-0">
                    <h1 class="text-xl md:text-2xl font-semibold text-slate-800 flex items-center gap-2">
                        <i class="pi pi-receipt text-emerald-600" />
                        {{ t("orders_page.title") }}
                    </h1>
                    <p class="text-sm text-slate-500">{{ t("orders_page.subtitle") }}</p>
                </div>

                <div class="flex flex-wrap items-center gap-2">
                    <SelectButton :modelValue="trashed" @update:modelValue="setTrashed" :options="viewOptions"
                        optionLabel="label" optionValue="value" :allowEmpty="false" size="small">
                        <template #option="{ option }">
                            <i :class="option.icon" class="text-sm" />
                            <span class="hidden sm:inline">{{ option.label }}</span>
                        </template>
                    </SelectButton>

                    <Button v-if="isAdmin" size="small"
                        :label="props.filters?.all_branches ? t('orders_page.current_branch') : t('orders_page.all_branches')"
                        :icon="props.filters?.all_branches ? 'pi pi-building' : 'pi pi-globe'"
                        :severity="props.filters?.all_branches ? 'warn' : 'secondary'" outlined @click="toggleAllBranches" />

                    <Button size="small" text :label="showInsights ? t('orders_page.hide_insights') : t('orders_page.show_insights')"
                        :icon="showInsights ? 'pi pi-chart-bar' : 'pi pi-chart-line'" @click="showInsights = !showInsights" />

                    <Button size="small" icon="pi pi-desktop" :label="t('orders_page.new_sale')"
                        @click="router.visit(route('pos.index'))" />
                </div>
            </div>

            <!-- PAGE SUMMARY -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                <div class="bg-white rounded-xl border border-slate-200 p-3">
                    <div class="text-xs text-slate-500">{{ t("orders_page.orders_found") }}</div>
                    <div class="text-lg font-bold text-slate-800 tabular-nums">{{ orders?.total ?? 0 }}</div>
                </div>
                <div class="bg-white rounded-xl border border-slate-200 p-3">
                    <div class="text-xs text-slate-500">{{ t("orders_page.page_total") }}</div>
                    <div class="text-lg font-bold text-emerald-600 tabular-nums">{{ money(pageTotal) }}</div>
                </div>
                <div class="bg-white rounded-xl border border-slate-200 p-3">
                    <div class="text-xs text-slate-500">{{ t("orders_page.page_due") }}</div>
                    <div class="text-lg font-bold text-rose-600 tabular-nums">{{ money(pageDue) }}</div>
                </div>
                <div class="bg-white rounded-xl border border-slate-200 p-3">
                    <div class="text-xs text-slate-500">{{ t("orders_page.selected") }}</div>
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <span class="text-lg font-bold text-slate-800 tabular-nums">{{ selectedOrders.length }}</span>
                        <SplitButton v-if="selectedOrders.length" :label="t('orders_page.bulk_actions')" :model="bulkActions"
                            severity="danger" size="small" @click="bulkActions[0].command()" />
                    </div>
                </div>
            </div>

            <!-- INSIGHTS -->
            <div v-if="showInsights && insights" class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                <div class="bg-white rounded-xl border border-slate-200 p-4">
                    <h3 class="font-semibold text-slate-700 flex items-center gap-2 mb-3">
                        <i class="pi pi-star-fill text-amber-500" /> {{ t("orders_page.top_selling") }}
                    </h3>
                    <div class="space-y-2">
                        <div v-for="item in insights.top_products" :key="item.product_id"
                            class="flex items-center justify-between gap-3 p-2 rounded-lg bg-slate-50 border border-slate-100">
                            <div class="flex flex-col min-w-0">
                                <span class="text-sm font-semibold text-slate-800 truncate">{{ item.name }}</span>
                                <span class="text-xs text-slate-500">{{ t("orders_page.units_sold", { qty: item.total_qty }) }}</span>
                            </div>
                            <div class="text-sm font-bold text-emerald-600 tabular-nums shrink-0">{{ money(item.total_amount) }}</div>
                        </div>
                        <div v-if="!insights.top_products?.length" class="text-center py-4 text-slate-400 text-sm">{{ t("common.no_data") }}</div>
                    </div>
                </div>

                <div class="bg-white rounded-xl border border-slate-200 p-4">
                    <h3 class="font-semibold text-slate-700 flex items-center gap-2 mb-3">
                        <i class="pi pi-tags text-emerald-600" /> {{ t("orders_page.sales_by_brand") }}
                    </h3>
                    <div class="space-y-2">
                        <div v-for="item in insights.brand_sales" :key="item.name"
                            class="flex items-center justify-between gap-3 p-2 rounded-lg bg-slate-50 border border-slate-100">
                            <div class="flex flex-col min-w-0">
                                <span class="text-sm font-semibold text-slate-800 truncate">{{ item.name }}</span>
                                <span class="text-xs text-slate-500">{{ t("orders_page.items_count", { qty: item.total_qty }) }}</span>
                            </div>
                            <div class="text-sm font-bold text-emerald-600 tabular-nums shrink-0">{{ money(item.total_amount) }}</div>
                        </div>
                        <div v-if="!insights.brand_sales?.length" class="text-center py-4 text-slate-400 text-sm">{{ t("common.no_data") }}</div>
                    </div>
                </div>
            </div>

            <!-- FILTER BAR -->
            <div class="bg-white rounded-xl border border-slate-200 p-3 md:p-4 space-y-3">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-2">
                    <InputGroup class="sm:col-span-2">
                        <InputGroupAddon><i class="pi pi-search" /></InputGroupAddon>
                        <InputText v-model="search" :placeholder="t('orders_page.search_placeholder')" class="w-full"
                            v-tooltip.bottom="t('shared.terms.search_hint')" @keyup.enter="applyFilter()" />
                    </InputGroup>
                    <Select v-model="status" :options="statusOptions" optionLabel="label" optionValue="value"
                        :placeholder="t('orders.order_status')" class="w-full" />
                    <Select v-model="paymentStatus" :options="paymentStatusOptions" optionLabel="label"
                        optionValue="value" :placeholder="t('orders.payment_status')" class="w-full" />
                    <DatePicker v-model="dateFrom" :placeholder="t('shared.terms.from')" dateFormat="yy-mm-dd" showIcon class="w-full" />
                    <DatePicker v-model="dateTo" :placeholder="t('shared.terms.to')" dateFormat="yy-mm-dd" showIcon class="w-full" />
                </div>

                <div v-if="showMoreFilters" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-2 pt-3 border-t border-slate-100">
                    <Select v-model="saleType" :options="saleTypeOptions" optionLabel="label" optionValue="value"
                        :placeholder="t('orders_page.sale_type')" class="w-full" />
                    <Select v-model="categoryId" :options="categories" optionLabel="name" optionValue="id"
                        :placeholder="t('orders_page.all_categories')" class="w-full" showClear filter />
                    <Select v-model="brandId" :options="brands" optionLabel="name" optionValue="id"
                        :placeholder="t('orders_page.all_brands')" class="w-full" showClear filter />
                    <Select v-model="productId" :options="available_products" optionLabel="name" optionValue="id"
                        :placeholder="t('orders_page.filter_product')" class="w-full" filter showClear />
                </div>

                <div class="flex flex-wrap items-center gap-2">
                    <Button text size="small" :icon="showMoreFilters ? 'pi pi-chevron-up' : 'pi pi-sliders-h'"
                        :label="showMoreFilters ? t('orders_page.less_filters') : t('orders_page.more_filters')"
                        @click="showMoreFilters = !showMoreFilters" />
                    <div class="flex flex-wrap gap-2 ms-auto">
                        <Button :label="t('shared.terms.apply')" icon="pi pi-filter" size="small" @click="applyFilter()" />
                        <Button :label="t('shared.terms.reset')" icon="pi pi-refresh" size="small" severity="secondary" text @click="resetFilter" />
                        <SplitButton :label="t('shared.terms.export')" icon="pi pi-download" :model="exportItems" size="small"
                            severity="secondary" outlined @click="exportReport('excel')" />
                    </div>
                </div>
            </div>

            <!-- TABLE -->
            <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
                <DataTable :value="rows" v-model:selection="selectedOrders" dataKey="id" scrollable size="small"
                    stripedRows class="text-sm">
                    <template #empty>
                        <div class="py-10 text-center text-slate-400">
                            <i class="pi pi-inbox text-3xl mb-2 block" />
                            {{ trashed ? t("orders_page.trash_empty") : t("orders_page.no_orders") }}
                        </div>
                    </template>

                    <Column selectionMode="multiple" headerStyle="width: 3rem" />

                    <Column :header="t('shared.terms.invoice')" style="min-width: 11rem">
                        <template #body="{ data }">
                            <button type="button" class="flex flex-col items-start text-start" @click="openInvoiceModal(data)">
                                <span class="font-semibold text-slate-800 hover:text-emerald-700 ltr-nums">{{ invoiceLabel(data) }}</span>
                                <span class="text-xs text-slate-500">{{ fmtDateTime(data.created_at) }}</span>
                            </button>
                        </template>
                    </Column>

                    <Column :header="t('shared.terms.customer')" style="min-width: 10rem">
                        <template #body="{ data }">
                            <div class="flex flex-col">
                                <span class="text-slate-800">{{ data.customer?.name || t("shared.terms.walk_in") }}</span>
                                <span v-if="data.customer?.phone" class="text-xs text-slate-500 ltr-nums">{{ data.customer.phone }}</span>
                            </div>
                        </template>
                    </Column>

                    <Column :header="t('orders_page.categories')" bodyClass="hidden xl:table-cell" headerClass="hidden xl:table-cell" style="min-width: 9rem">
                        <template #body="{ data }">
                            <span class="text-xs text-slate-600">{{ getOrderCategories(data) }}</span>
                        </template>
                    </Column>

                    <Column :header="t('shared.terms.cashier')" bodyClass="hidden lg:table-cell" headerClass="hidden lg:table-cell">
                        <template #body="{ data }">{{ data.user?.name || "-" }}</template>
                    </Column>

                    <Column :header="t('shared.terms.branch')" bodyClass="hidden lg:table-cell" headerClass="hidden lg:table-cell">
                        <template #body="{ data }">{{ data.branch?.name || "-" }}</template>
                    </Column>

                    <Column :header="t('shared.terms.total')" bodyClass="text-end">
                        <template #body="{ data }">
                            <span class="font-semibold text-emerald-600 tabular-nums">{{ money(data.total_amount) }}</span>
                        </template>
                    </Column>

                    <Column :header="t('shared.terms.due')" bodyClass="text-end">
                        <template #body="{ data }">
                            <span v-if="orderDue(data) > 0" class="font-semibold text-rose-600 tabular-nums">{{ money(orderDue(data)) }}</span>
                            <span v-else class="text-slate-400">-</span>
                        </template>
                    </Column>

                    <Column :header="t('orders_page.payment')">
                        <template #body="{ data }">
                            <div class="flex flex-col gap-1 items-start">
                                <Tag :value="statusLabel(data.payment_status)" :severity="paymentSeverity(data.payment_status)" />
                                <a v-if="data.installment_sale" :href="route('installments.show', data.installment_sale.id)"
                                    class="text-[11px] text-violet-700 hover:underline whitespace-nowrap">
                                    <i class="pi pi-calendar-clock !text-[10px]" /> {{ data.installment_sale.agreement_no }}
                                </a>
                                <Tag v-else-if="data.sale_type === 'emi'" :value="saleTypeLabel('emi')" severity="info" class="!text-[10px]" />
                            </div>
                        </template>
                    </Column>

                    <Column :header="t('common.status')">
                        <template #body="{ data }">
                            <Tag :value="statusLabel(data.status)" :severity="statusSeverity(data.status)" />
                        </template>
                    </Column>

                    <Column :header="t('common.actions')" bodyClass="text-end" style="min-width: 4rem">
                        <template #body="{ data }">
                            <!-- desktop: icon buttons -->
                            <div class="hidden md:flex gap-0.5 justify-end flex-wrap">
                                <Button v-for="a in rowActions(data)" :key="a.key" :icon="a.icon" text rounded size="small"
                                    :severity="a.severity || 'secondary'" :aria-label="a.label" v-tooltip.top="a.label" @click="a.command()" />
                            </div>
                            <!-- phones: single menu -->
                            <Button class="md:!hidden" icon="pi pi-ellipsis-v" text rounded size="small" severity="secondary"
                                :aria-label="t('common.actions')" @click="openRowMenu($event, data)" />
                        </template>
                    </Column>
                </DataTable>

                <Paginator v-if="orders?.total > 0" :rows="Number(orders.per_page)" :totalRecords="orders.total" :first="firstRow"
                    :rowsPerPageOptions="[15, 30, 50, 100]" @page="onPage"
                    template="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink RowsPerPageDropdown CurrentPageReport"
                    :currentPageReportTemplate="t('shared.terms.showing', { from: orders.from, to: orders.to, total: orders.total })"
                    class="border-t border-slate-100" />
            </div>
        </div>

        <!-- INVOICE PREVIEW -->
        <Dialog v-model:visible="showInvoiceModal" modal :header="t('orders_page.invoice_preview')" :style="{ width: '760px' }"
            :breakpoints="dialogBreakpoints">
            <div v-if="selectedOrder" class="space-y-4">
                <div class="flex justify-between gap-3 flex-wrap">
                    <div>
                        <div class="text-xs text-slate-500">{{ t("shared.terms.invoice") }}</div>
                        <div class="text-xl font-bold text-slate-800 ltr-nums">{{ invoiceLabel(selectedOrder) }}</div>
                        <div class="text-xs text-slate-500">{{ fmtDateTime(selectedOrder.created_at) }}</div>
                        <div class="flex flex-wrap gap-1 mt-2">
                            <Tag :value="statusLabel(selectedOrder.status)" :severity="statusSeverity(selectedOrder.status)" />
                            <Tag :value="statusLabel(selectedOrder.payment_status)" :severity="paymentSeverity(selectedOrder.payment_status)" />
                            <Tag :value="saleTypeLabel(selectedOrder.sale_type)" severity="secondary" />
                        </div>
                    </div>
                    <div class="flex gap-2 items-start">
                        <Button :label="t('orders_page.open_invoice')" icon="pi pi-external-link" severity="secondary" outlined size="small"
                            @click="openInvoicePage(selectedOrder)" />
                        <Button v-if="selectedOrder.status !== 'void'" :label="t('shared.terms.print')" icon="pi pi-print" size="small"
                            @click="printInvoice(selectedOrder)" />
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-sm">
                    <div class="p-3 border border-slate-200 rounded-lg">
                        <div class="text-xs text-slate-500 font-semibold">{{ t("shared.terms.customer") }}</div>
                        <div class="font-medium text-slate-800">{{ selectedOrder.customer?.name || t("shared.terms.walk_in") }}</div>
                        <div v-if="selectedOrder.customer?.phone" class="text-xs text-slate-500 ltr-nums">{{ selectedOrder.customer.phone }}</div>
                    </div>
                    <div class="p-3 border border-slate-200 rounded-lg">
                        <div class="text-xs text-slate-500 font-semibold">{{ t("shared.terms.cashier") }} / {{ t("shared.terms.branch") }}</div>
                        <div class="text-slate-800">{{ selectedOrder.user?.name || "-" }}</div>
                        <div class="text-xs text-slate-500">{{ selectedOrder.branch?.name || "-" }}</div>
                    </div>
                    <div class="p-3 border border-slate-200 rounded-lg tabular-nums">
                        <div class="flex justify-between gap-2"><span class="text-slate-500">{{ t("shared.terms.total") }}</span><b>{{ money(selectedOrder.total_amount) }}</b></div>
                        <div class="flex justify-between gap-2"><span class="text-slate-500">{{ t("shared.terms.paid") }}</span><span>{{ money(selectedOrder.paid_amount) }}</span></div>
                        <div class="flex justify-between gap-2"><span class="text-slate-500">{{ t("shared.terms.due") }}</span><span class="text-rose-600 font-semibold">{{ money(orderDue(selectedOrder)) }}</span></div>
                    </div>
                </div>

                <div class="border border-slate-200 rounded-lg overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-slate-50 text-xs text-slate-500">
                            <tr>
                                <th class="text-start px-3 py-2">{{ t("orders.order_items") }}</th>
                                <th class="text-end px-3 py-2">{{ t("common.quantity") }}</th>
                                <th class="text-end px-3 py-2">{{ t("common.price") }}</th>
                                <th class="text-end px-3 py-2">{{ t("shared.terms.total") }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="it in selectedOrder.items || []" :key="it.id" class="border-t border-slate-100">
                                <td class="px-3 py-2">
                                    <div class="text-slate-800">{{ it.name }}</div>
                                    <div class="text-xs text-slate-400 ltr-nums">{{ it.sku }}</div>
                                </td>
                                <td class="px-3 py-2 text-end tabular-nums">{{ it.quantity }}</td>
                                <td class="px-3 py-2 text-end tabular-nums">{{ money(it.unit_price) }}</td>
                                <td class="px-3 py-2 text-end tabular-nums font-medium">{{ money(it.line_total) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </Dialog>

        <!-- VOID -->
        <Dialog v-model:visible="showVoidModal" modal :header="t('orders_page.void_order')" :style="{ width: '520px' }"
            :breakpoints="dialogBreakpoints">
            <div v-if="voidTarget" class="space-y-3">
                <div class="p-3 rounded-xl border bg-rose-50 border-rose-200 text-rose-800 text-sm">
                    <div class="font-bold flex items-center gap-2"><i class="pi pi-exclamation-triangle" /> {{ t("orders_page.void_warning") }}</div>
                    <div class="text-xs mt-1">{{ t("orders_page.void_hint") }}</div>
                </div>
                <div class="text-sm space-y-1">
                    <div><b>{{ t("shared.terms.invoice") }}:</b> <span class="ltr-nums">{{ invoiceLabel(voidTarget) }}</span></div>
                    <div><b>{{ t("shared.terms.total") }}:</b> {{ money(voidTarget.total_amount) }}</div>
                    <div><b>{{ t("common.status") }}:</b> {{ statusLabel(voidTarget.status) }}</div>
                </div>
            </div>
            <template #footer>
                <Button :label="t('shared.terms.cancel')" severity="secondary" text @click="showVoidModal = false" />
                <Button :label="t('orders_page.void_now')" icon="pi pi-ban" severity="danger" :loading="voidLoading" @click="voidOrder" />
            </template>
        </Dialog>

        <!-- COMPLETE DRAFT -->
        <Dialog v-model:visible="showCompleteModal" modal :header="t('orders_page.complete_draft')" :style="{ width: '820px' }"
            :breakpoints="dialogBreakpoints">
            <div v-if="completeTarget" class="space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-sm">
                    <div class="p-3 border border-slate-200 rounded-lg">
                        <div class="text-xs text-slate-500 font-semibold">{{ t("shared.terms.invoice") }}</div>
                        <div class="font-semibold ltr-nums">{{ invoiceLabel(completeTarget) }}</div>
                    </div>
                    <div class="p-3 border border-slate-200 rounded-lg">
                        <div class="text-xs text-slate-500 font-semibold">{{ t("shared.terms.total") }}</div>
                        <div class="font-semibold tabular-nums">{{ money(draftTotal) }}</div>
                    </div>
                    <div class="p-3 border border-slate-200 rounded-lg">
                        <div class="text-xs text-slate-500 font-semibold">{{ t("shared.terms.due") }} / {{ t("shared.terms.change") }}</div>
                        <div v-if="draftDue > 0" class="font-semibold text-rose-600 tabular-nums">{{ t("shared.terms.due") }}: {{ money(draftDue) }}</div>
                        <div v-if="draftChange > 0" class="font-semibold text-emerald-600 tabular-nums">{{ t("shared.terms.change") }}: {{ money(draftChange) }}</div>
                        <div v-if="draftDue === 0 && draftChange === 0" class="font-semibold text-slate-700">{{ t("orders_page.balanced") }}</div>
                    </div>
                </div>

                <SelectButton v-model="completeMode" :allowEmpty="false" optionLabel="label" optionValue="value" :options="completeModeOptions" />

                <div class="flex items-center justify-between">
                    <div class="font-semibold text-slate-800">
                        {{ completeMode === "installment" ? t("orders_page.down_payment_optional") : t("orders_page.payments") }}
                    </div>
                    <Button :label="t('orders_page.add_payment')" icon="pi pi-plus" text size="small" @click="addRow(completeRows)" />
                </div>

                <div class="space-y-3">
                    <div v-for="(row, idx) in completeRows" :key="row.id" class="border border-slate-200 rounded-xl p-3">
                        <div class="grid grid-cols-1 sm:grid-cols-[1fr_1fr_auto] gap-3 items-end">
                            <div>
                                <label class="text-xs text-slate-500 mb-1 block">{{ t("shared.terms.method") }} <span class="text-rose-600">*</span></label>
                                <Select v-model="row.payment_method_id" :options="paymentMethods" optionLabel="name" optionValue="id"
                                    :placeholder="t('orders_page.select_method')" filter class="w-full" />
                            </div>
                            <div>
                                <label class="text-xs text-slate-500 mb-1 block">{{ t("shared.terms.amount") }} <span class="text-rose-600">*</span></label>
                                <InputNumber v-model="row.amount" :min="0" :maxFractionDigits="2" class="w-full" inputClass="w-full" />
                            </div>
                            <Button icon="pi pi-trash" text severity="danger" :aria-label="t('common.delete')" @click="removeRow(completeRows, idx)" />
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mt-3">
                            <InputText v-model="row.transaction_ref" class="w-full" :placeholder="t('orders_page.txn_ref_optional')" />
                            <InputText v-model="row.notes" class="w-full" :placeholder="t('orders_page.note_optional')" />
                        </div>

                        <EmiPaymentFields v-if="isEmiRow(row)" v-model="row.emi" :banks="emiBanks" :amount="Number(row.amount || 0)" />

                        <div v-if="row.payment_method_id && needsBankMeta(row)" class="mt-3 rounded-lg border border-slate-200 bg-slate-50 p-3">
                            <div class="font-semibold text-sm mb-2 text-slate-700"><i class="pi pi-building me-1" /> {{ t("orders_page.bank_info") }}</div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <InputText v-model="row.meta.customer_bank_name" class="w-full" :placeholder="t('orders_page.customer_bank_name') + ' *'" />
                                <InputText v-model="row.meta.customer_account_no" class="w-full ltr-nums" :placeholder="t('orders_page.customer_account_no') + ' *'" />
                                <InputNumber v-model="row.meta.received_to_bank_account_id" class="w-full" inputClass="w-full"
                                    :placeholder="t('orders_page.received_account_id')" :useGrouping="false" />
                                <InputText v-model="row.meta.txn_ref" class="w-full" :placeholder="t('orders_page.cheque_no')" />
                            </div>
                        </div>
                    </div>
                </div>

                <div v-if="completeMode === 'installment'" class="rounded-xl border border-violet-200 p-3">
                    <div class="font-semibold mb-2 text-slate-800">{{ t("orders_page.installment_plan") }}</div>
                    <InstallmentConfigurator v-model="completeInstallment" :saleAmount="draftTotal" :downPayment="draftPayTotal"
                        :plans="installmentPlans" @preview="completePreview = $event" />
                </div>
            </div>
            <template #footer>
                <Button :label="t('shared.terms.cancel')" severity="secondary" text @click="showCompleteModal = false" />
                <Button :label="t('orders_page.complete_draft')" icon="pi pi-check" severity="success" :loading="completeLoading" @click="completeDraft" />
            </template>
        </Dialog>

        <!-- PAY DUE -->
        <Dialog v-model:visible="showPayDueModal" modal :header="t('orders_page.pay_due')" :style="{ width: '760px' }"
            :breakpoints="dialogBreakpoints">
            <div v-if="payDueTarget" class="space-y-4">
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-sm">
                    <div class="p-3 border border-slate-200 rounded-lg min-w-0">
                        <div class="text-xs text-slate-500 font-semibold">{{ t("shared.terms.invoice") }}</div>
                        <div class="font-semibold ltr-nums truncate">{{ invoiceLabel(payDueTarget) }}</div>
                    </div>
                    <div class="p-3 border border-slate-200 rounded-lg">
                        <div class="text-xs text-slate-500 font-semibold">{{ t("shared.terms.total") }}</div>
                        <div class="font-semibold tabular-nums">{{ money(orderTotal) }}</div>
                    </div>
                    <div class="p-3 border border-slate-200 rounded-lg">
                        <div class="text-xs text-slate-500 font-semibold">{{ t("orders_page.already_paid") }}</div>
                        <div class="font-semibold tabular-nums">{{ money(alreadyPaid) }}</div>
                    </div>
                    <div class="p-3 border border-amber-200 bg-amber-50 rounded-lg">
                        <div class="text-xs text-amber-800 font-semibold">{{ t("orders_page.current_due") }}</div>
                        <div class="font-bold text-amber-900 tabular-nums">{{ money(currentDue) }}</div>
                    </div>
                </div>

                <div class="flex flex-wrap items-center justify-between gap-2">
                    <div class="font-semibold text-slate-800">{{ t("orders_page.payments") }}</div>
                    <div class="flex gap-1">
                        <Button :label="t('orders_page.full_due')" icon="pi pi-check-circle" text size="small" @click="fillFullDue" />
                        <Button :label="t('orders_page.add_payment')" icon="pi pi-plus" text size="small" @click="addRow(payDueRows)" />
                    </div>
                </div>

                <div class="space-y-3">
                    <div v-for="(row, idx) in payDueRows" :key="row.id" class="border border-slate-200 rounded-xl p-3">
                        <div class="grid grid-cols-1 sm:grid-cols-[1fr_1fr_auto] gap-3 items-end">
                            <div>
                                <label class="text-xs text-slate-500 mb-1 block">{{ t("shared.terms.method") }} <span class="text-rose-600">*</span></label>
                                <Select v-model="row.payment_method_id" :options="paymentMethods" optionLabel="name" optionValue="id"
                                    :placeholder="t('orders_page.select_method')" filter class="w-full" />
                            </div>
                            <div>
                                <label class="text-xs text-slate-500 mb-1 block">{{ t("shared.terms.amount") }} <span class="text-rose-600">*</span></label>
                                <InputNumber v-model="row.amount" :min="0" :maxFractionDigits="2" class="w-full" inputClass="w-full" />
                            </div>
                            <Button icon="pi pi-trash" text severity="danger" :aria-label="t('common.delete')" @click="removeRow(payDueRows, idx)" />
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mt-3">
                            <InputText v-model="row.transaction_ref" class="w-full" :placeholder="t('orders_page.txn_ref_optional')" />
                            <InputText v-model="row.notes" class="w-full" :placeholder="t('orders_page.note_optional')" />
                        </div>

                        <EmiPaymentFields v-if="isEmiRow(row)" v-model="row.emi" :banks="emiBanks" :amount="Number(row.amount || 0)" />

                        <div v-if="row.payment_method_id && needsBankMeta(row)" class="mt-3 rounded-lg border border-slate-200 bg-slate-50 p-3">
                            <div class="font-semibold text-sm mb-2 text-slate-700"><i class="pi pi-building me-1" /> {{ t("orders_page.bank_info") }}</div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <InputText v-model="row.meta.customer_bank_name" class="w-full" :placeholder="t('orders_page.customer_bank_name') + ' *'" />
                                <InputText v-model="row.meta.customer_account_no" class="w-full ltr-nums" :placeholder="t('orders_page.customer_account_no') + ' *'" />
                                <InputNumber v-model="row.meta.received_to_bank_account_id" class="w-full" inputClass="w-full"
                                    :placeholder="t('orders_page.received_account_id')" :useGrouping="false" />
                                <InputText v-model="row.meta.txn_ref" class="w-full" :placeholder="t('orders_page.cheque_no')" />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="text-sm text-slate-600 flex flex-wrap gap-x-4 gap-y-1 tabular-nums">
                    <span>{{ t("orders_page.paying_now") }}: <b>{{ money(payNowTotal) }}</b></span>
                    <span>{{ t("orders_page.due_after") }}: <b class="text-rose-600">{{ money(dueAfterPay) }}</b></span>
                    <span v-if="changeAfterPay > 0">{{ t("shared.terms.change") }}: <b class="text-emerald-600">{{ money(changeAfterPay) }}</b></span>
                </div>
            </div>
            <template #footer>
                <Button :label="t('shared.terms.cancel')" severity="secondary" text @click="showPayDueModal = false" />
                <Button :label="t('orders_page.submit_payment')" icon="pi pi-check" severity="warn" :loading="payDueLoading" @click="submitPayDue" />
            </template>
        </Dialog>

        <!-- CONVERT TO INSTALLMENT -->
        <Dialog v-model:visible="showConvertModal" modal :header="t('orders_page.convert_installment')" :style="{ width: '820px' }"
            :breakpoints="dialogBreakpoints">
            <div v-if="convertTarget" class="space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-sm">
                    <div class="p-3 border border-slate-200 rounded-lg">
                        <div class="text-xs text-slate-500 font-semibold">{{ t("shared.terms.invoice") }}</div>
                        <div class="font-semibold ltr-nums">{{ invoiceLabel(convertTarget) }}</div>
                    </div>
                    <div class="p-3 border border-slate-200 rounded-lg">
                        <div class="text-xs text-slate-500 font-semibold">{{ t("shared.terms.total") }} / {{ t("shared.terms.paid") }}</div>
                        <div class="font-semibold tabular-nums">{{ money(convertTarget.total_amount) }} / {{ money(convertTarget.paid_amount) }}</div>
                    </div>
                    <div class="p-3 border border-slate-200 rounded-lg">
                        <div class="text-xs text-slate-500 font-semibold">{{ t("orders_page.due_to_finance") }}</div>
                        <div class="font-semibold text-rose-600 tabular-nums">{{ money(orderDue(convertTarget)) }}</div>
                    </div>
                </div>

                <div>
                    <label class="text-xs text-slate-500 mb-1 block">{{ t("shared.terms.customer") }} <span class="text-rose-600">*</span></label>
                    <AutoComplete v-model="convertCustomer" :suggestions="customerSuggestions" optionLabel="name" class="w-full"
                        inputClass="w-full" :placeholder="t('orders_page.search_customer')" @complete="searchCustomers">
                        <template #option="{ option }">
                            <div class="flex flex-col"><span>{{ option.name }}</span><span class="text-xs text-slate-500 ltr-nums">{{ option.phone }}</span></div>
                        </template>
                    </AutoComplete>
                </div>

                <InstallmentConfigurator v-model="convertConfig" :saleAmount="Number(convertTarget.total_amount)"
                    :downPayment="Number(convertTarget.paid_amount)" :plans="installmentPlans" @preview="convertPreview = $event" />
            </div>
            <template #footer>
                <Button :label="t('shared.terms.cancel')" severity="secondary" text @click="showConvertModal = false" />
                <Button :label="t('orders_page.create_agreement')" icon="pi pi-calendar-plus" :loading="convertLoading"
                    :disabled="!convertPreview || convertPreview.financed_amount <= 0" @click="submitConvert" />
            </template>
        </Dialog>

        <!-- TRANSFER -->
        <Dialog v-model:visible="showTransferModal" modal :header="t('orders_page.transfer_title')" :style="{ width: '520px' }"
            :breakpoints="dialogBreakpoints">
            <div class="space-y-4">
                <div class="p-3 rounded-xl border bg-sky-50 border-sky-200 text-sky-900 text-sm">
                    <div class="font-bold">{{ t("orders_page.transfer_info") }}</div>
                    <div class="text-xs mt-1">{{ t("orders_page.transfer_hint") }}</div>
                </div>
                <div class="text-sm"><b>{{ t("orders_page.selected_orders") }}:</b> {{ selectedOrders.length }}</div>
                <div>
                    <label class="text-sm font-semibold mb-2 block">{{ t("orders_page.target_branch") }} <span class="text-rose-600">*</span></label>
                    <Select v-model="targetBranchId" :options="branches" optionLabel="name" optionValue="id"
                        :placeholder="t('orders_page.select_branch')" filter class="w-full" />
                </div>
            </div>
            <template #footer>
                <Button :label="t('shared.terms.cancel')" severity="secondary" text @click="showTransferModal = false" />
                <Button :label="t('orders_page.transfer')" icon="pi pi-directions" :loading="transferLoading" :disabled="!targetBranchId"
                    @click="transferOrders" />
            </template>
        </Dialog>

        <!-- EDIT DATE (admin) -->
        <Dialog v-model:visible="showEditDateModal" modal :header="t('orders_page.edit_date')" :style="{ width: '520px' }"
            :breakpoints="dialogBreakpoints">
            <div class="space-y-4">
                <div class="p-3 rounded-xl border bg-amber-50 border-amber-200 text-amber-900 text-sm">
                    <div class="font-bold">{{ t("orders_page.edit_date_info") }}</div>
                    <div class="text-xs mt-1">{{ t("orders_page.edit_date_hint") }}</div>
                </div>
                <div class="text-sm space-y-1">
                    <div><b>{{ t("shared.terms.invoice") }}:</b> <span class="ltr-nums">{{ invoiceLabel(editDateTarget) }}</span></div>
                    <div><b>{{ t("orders_page.current_date") }}:</b> {{ fmtDateTime(editDateTarget?.created_at) }}</div>
                </div>
                <div>
                    <label class="text-sm font-semibold mb-2 block">{{ t("orders_page.new_sale_date") }} <span class="text-rose-600">*</span></label>
                    <DatePicker v-model="newSaleDate" showTime hourFormat="24" dateFormat="yy-mm-dd" showIcon class="w-full" />
                </div>
            </div>
            <template #footer>
                <Button :label="t('shared.terms.cancel')" severity="secondary" text @click="showEditDateModal = false" />
                <Button :label="t('orders_page.update_date')" icon="pi pi-calendar" :loading="editDateLoading" @click="updateOrderDate" />
            </template>
        </Dialog>
    </AuthenticatedLayout>
</template>
