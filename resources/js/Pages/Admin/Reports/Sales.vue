<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, router } from "@inertiajs/vue3";
import { computed, reactive, ref } from "vue";

import Button from "primevue/button";
import Chart from "primevue/chart";
import Column from "primevue/column";
import DataTable from "primevue/datatable";
import DatePicker from "primevue/datepicker";
import InputText from "primevue/inputtext";
import Menu from "primevue/menu";
import Select from "primevue/select";
import Tab from "primevue/tab";
import TabList from "primevue/tablist";
import TabPanel from "primevue/tabpanel";
import TabPanels from "primevue/tabpanels";
import Tabs from "primevue/tabs";
import Tag from "primevue/tag";

import StatCard from "@/Components/Sales/StatCard.vue";
import { useFlashToast } from "@/Composables/useFlashToast";
import { baseOptions, barDataset, doughnutOptions, foldOther, ink, lineDataset, series } from "@/Helpers/chartTheme";
import { cleanParams, compact, date, dateTime, money, titleCase, ymd } from "@/Helpers/format";

useFlashToast();

const props = defineProps({
    filters: { type: Object, default: () => ({}) },
    summary: { type: Object, default: () => ({}) },
    previous: { type: Object, default: () => ({}) },
    growth: { type: Object, default: () => ({}) },
    trend: { type: Array, default: () => [] },
    hourly: { type: Array, default: () => [] },
    paymentMethods: { type: Array, default: () => [] },
    collections: { type: Array, default: () => [] },
    topProducts: { type: Array, default: () => [] },
    categories: { type: Array, default: () => [] },
    brands: { type: Array, default: () => [] },
    cashiers: { type: Array, default: () => [] },
    saleTypes: { type: Array, default: () => [] },
    topCustomers: { type: Array, default: () => [] },
    orders: { type: Object, default: () => ({ data: [] }) },
    options: { type: Object, default: () => ({}) },
});

/* ---------------- Filters ---------------- */
const presets = [
    { label: "Today", value: "today" },
    { label: "Yesterday", value: "yesterday" },
    { label: "Last 7 days", value: "last_7_days" },
    { label: "This week", value: "this_week" },
    { label: "Last week", value: "last_week" },
    { label: "Last 30 days", value: "last_30_days" },
    { label: "This month", value: "this_month" },
    { label: "Last month", value: "last_month" },
    { label: "This quarter", value: "this_quarter" },
    { label: "This year", value: "this_year" },
    { label: "Last year", value: "last_year" },
    { label: "Custom range", value: "custom" },
];
const quickPresets = ["today", "this_week", "this_month", "this_year"];
const groupOptions = [
    { label: "Auto", value: "auto" },
    { label: "Daily", value: "day" },
    { label: "Weekly", value: "week" },
    { label: "Monthly", value: "month" },
];
const saleTypeOptions = [
    { label: "Regular", value: "regular" },
    { label: "Installment", value: "installment" },
    { label: "Bank EMI", value: "emi" },
];
const paymentStatusOptions = [
    { label: "Paid", value: "paid" },
    { label: "Partial", value: "partial" },
    { label: "Unpaid", value: "unpaid" },
];

const toDate = (s) => (s ? new Date(`${s}T00:00:00`) : null);
const numOrNull = (v) => (v === null || v === undefined || v === "" ? null : Number(v));

const f = reactive({
    preset: props.filters.preset || "this_month",
    date_from: toDate(props.filters.date_from),
    date_to: toDate(props.filters.date_to),
    group_by: props.filters.group_by || "auto",
    search: props.filters.search || "",
    branch_id: props.filters.branch_id === "all" ? "all" : numOrNull(props.filters.branch_id),
    user_id: numOrNull(props.filters.user_id),
    sale_type: props.filters.sale_type || null,
    payment_status: props.filters.payment_status || null,
    payment_method_id: numOrNull(props.filters.payment_method_id),
    category_id: numOrNull(props.filters.category_id),
    brand_id: numOrNull(props.filters.brand_id),
    product_id: numOrNull(props.filters.product_id),
});

const moreFilterKeys = ["branch_id", "user_id", "sale_type", "payment_status", "payment_method_id", "category_id", "brand_id", "product_id"];
const activeMoreFilters = computed(() => moreFilterKeys.filter((k) => f[k] !== null && f[k] !== "").length);
const showMore = ref(activeMoreFilters.value > 0);

const branchOptions = computed(() => [{ id: "all", name: "All branches" }, ...(props.options.branches || [])]);

function params(extra = {}) {
    return cleanParams({
        preset: f.preset,
        date_from: f.preset === "custom" ? ymd(f.date_from) : undefined,
        date_to: f.preset === "custom" ? ymd(f.date_to) : undefined,
        group_by: f.group_by !== "auto" ? f.group_by : undefined,
        search: f.search?.trim(),
        branch_id: f.branch_id,
        user_id: f.user_id,
        sale_type: f.sale_type,
        payment_status: f.payment_status,
        payment_method_id: f.payment_method_id,
        category_id: f.category_id,
        brand_id: f.brand_id,
        product_id: f.product_id,
        per_page: perPage.value !== 20 ? perPage.value : undefined,
        ...extra,
    });
}

function apply(extra = {}) {
    router.get(route("reports.sales"), params(extra), { preserveState: true, preserveScroll: true });
}

function setPreset(p) {
    f.preset = p;
    if (p !== "custom") apply();
}

function onPresetChange(p) {
    if (p !== "custom") apply();
}

function reset() {
    router.get(route("reports.sales"), {}, { preserveScroll: true });
}

function searchCustomer(phone, name) {
    f.search = phone || name || "";
    activeTab.value = "orders";
    apply();
}

/* ---------------- Export ---------------- */
const exportMenu = ref();
const sections = [
    { key: "orders", label: "Orders (detailed)" },
    { key: "summary", label: "Period summary" },
    { key: "products", label: "Products" },
    { key: "categories", label: "Categories" },
    { key: "customers", label: "Customers" },
    { key: "payments", label: "Payment methods" },
    { key: "cashiers", label: "Cashiers" },
];
const formats = [
    { key: "pdf", label: "PDF", icon: "pi pi-file-pdf" },
    { key: "csv", label: "CSV", icon: "pi pi-file" },
    { key: "excel", label: "Excel", icon: "pi pi-file-excel" },
];
function download(section, format) {
    window.open(route("reports.sales", params({ export: format, section })), "_blank");
}
const exportItems = sections.map((s) => ({
    label: s.label,
    items: formats.map((fm) => ({ label: fm.label, icon: fm.icon, command: () => download(s.key, fm.key) })),
}));

/* ---------------- Tabs & table toggles ---------------- */
const activeTab = ref("overview");
const asTable = reactive({ trend: false, orders: false, payments: false, hourly: false, products: false, categories: false, brands: false });

/* ---------------- KPIs ---------------- */
const periodLabel = computed(() => `${date(props.filters.date_from)} – ${date(props.filters.date_to)}`);
const vsPrev = "vs previous period";

/* ---------------- Charts ---------------- */
const trendData = computed(() => ({
    labels: props.trend.map((r) => r.label),
    datasets: [
        lineDataset("Net sales", props.trend.map((r) => r.net), 0),
        lineDataset("Collected", props.trend.map((r) => r.collected), 1),
    ],
}));
const trendOptions = computed(() => baseOptions());

const ordersData = computed(() => ({
    labels: props.trend.map((r) => r.label),
    datasets: [barDataset("Orders", props.trend.map((r) => r.orders), 0)],
}));
const ordersOptions = computed(() => baseOptions({ legend: false, money: false }));

function doughnut(rows, labelKey, valueKey) {
    const folded = foldOther(rows, labelKey, valueKey);
    return {
        labels: folded.map((r) => r[labelKey]),
        datasets: [{
            data: folded.map((r) => r[valueKey]),
            backgroundColor: folded.map((_, i) => series(i)),
            borderColor: ink().surface,
            borderWidth: 2,
            hoverOffset: 4,
        }],
    };
}

const paymentData = computed(() => doughnut(props.paymentMethods, "label", "amount"));
const categoryData = computed(() => doughnut(props.categories, "label", "amount"));
const pieOptions = computed(() => doughnutOptions());

const hourlyData = computed(() => ({
    labels: props.hourly.map((h) => h.label),
    datasets: [barDataset("Net sales", props.hourly.map((h) => h.net), 0)],
}));
const hourlyOptions = computed(() => baseOptions({ legend: false }));
const hasHourly = computed(() => props.hourly.some((h) => h.orders > 0));

const productsData = computed(() => ({
    labels: props.topProducts.map((p) => p.label),
    datasets: [barDataset("Amount", props.topProducts.map((p) => p.amount), 0)],
}));
const brandsData = computed(() => ({
    labels: props.brands.map((b) => b.label),
    datasets: [barDataset("Amount", props.brands.map((b) => b.amount), 0)],
}));
const hBarOptions = computed(() => baseOptions({ horizontal: true, legend: false }));

/* ---------------- Totals ---------------- */
const sum = (rows, key) => rows.reduce((s, r) => s + Number(r[key] || 0), 0);
const pct = (v, rows, key) => {
    const total = sum(rows, key);
    return total ? ((Number(v || 0) / total) * 100).toFixed(1) : "0.0";
};

/* ---------------- Orders table ---------------- */
const perPageOptions = [20, 50, 100].map((v) => ({ label: `${v} / page`, value: v }));
const perPage = ref(Number(props.filters?.per_page || props.orders?.per_page) || 20);
function changePerPage(v) {
    perPage.value = v;
    apply({ per_page: v });
}
function goToPage(url) {
    if (url) router.get(url, {}, { preserveState: true, preserveScroll: true });
}
function openInvoice(o) {
    window.open(route("pos.orders.invoice", o.id), "_blank");
}
const due = (o) => Math.max(0, Number(o.total_amount || 0) - Number(o.paid_amount || 0));
function paymentSeverity(v) {
    return v === "paid" ? "success" : v === "partial" ? "warn" : "danger";
}
function typeSeverity(v) {
    return v === "installment" ? "help" : v === "emi" ? "info" : "secondary";
}
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Sales Reports" />

        <div class="p-3 md:p-6 space-y-4">
            <!-- HEADER -->
            <div class="flex flex-wrap items-start justify-between gap-3">
                <div>
                    <h2 class="text-xl font-semibold flex items-center gap-2">
                        <i class="pi pi-chart-bar text-primary" /> Sales Reports
                    </h2>
                    <p class="text-sm text-slate-500">
                        {{ periodLabel }} · grouped {{ titleCase(filters.resolved_group_by) }}
                    </p>
                </div>
                <div class="flex items-center gap-2">
                    <Button label="Export" icon="pi pi-download" severity="secondary" outlined size="small"
                        @click="exportMenu.toggle($event)" aria-haspopup="true" aria-controls="export_menu" />
                    <Menu ref="exportMenu" id="export_menu" :model="exportItems" :popup="true" />
                </div>
            </div>

            <!-- FILTER BAR -->
            <div class="card !p-3 space-y-3">
                <div class="flex flex-wrap gap-2 items-center">
                    <div class="flex flex-wrap gap-1">
                        <Button v-for="p in quickPresets" :key="p" size="small"
                            :label="presets.find((x) => x.value === p).label"
                            :outlined="f.preset !== p" :severity="f.preset === p ? 'primary' : 'secondary'"
                            @click="setPreset(p)" />
                    </div>
                    <Select v-model="f.preset" :options="presets" optionLabel="label" optionValue="value"
                        class="w-full sm:w-44" size="small" @update:modelValue="onPresetChange" />
                    <template v-if="f.preset === 'custom'">
                        <DatePicker v-model="f.date_from" dateFormat="dd-mm-yy" placeholder="From" showIcon size="small"
                            class="w-[calc(50%-0.25rem)] sm:w-40" />
                        <DatePicker v-model="f.date_to" dateFormat="dd-mm-yy" placeholder="To" showIcon size="small"
                            class="w-[calc(50%-0.25rem)] sm:w-40" />
                    </template>
                    <Select v-model="f.group_by" :options="groupOptions" optionLabel="label" optionValue="value"
                        class="w-full sm:w-32" size="small" />
                    <InputText v-model="f.search" size="small" class="w-full sm:flex-1 sm:min-w-[16rem]"
                        placeholder="Invoice, customer name or phone (partial, * wildcard)" @keyup.enter="apply()" />
                    <div class="flex gap-1 ml-auto">
                        <Button :label="activeMoreFilters ? `More filters (${activeMoreFilters})` : 'More filters'"
                            :icon="showMore ? 'pi pi-chevron-up' : 'pi pi-sliders-h'" text size="small"
                            @click="showMore = !showMore" />
                        <Button label="Apply" icon="pi pi-filter" size="small" @click="apply()" />
                        <Button label="Reset" icon="pi pi-refresh" text size="small" @click="reset" />
                    </div>
                </div>

                <div v-if="showMore" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-2 pt-3 border-t border-slate-100">
                    <Select v-if="options.isAdmin" v-model="f.branch_id" :options="branchOptions" optionLabel="name"
                        optionValue="id" placeholder="Current branch" showClear filter size="small" class="w-full" />
                    <Select v-model="f.user_id" :options="options.users || []" optionLabel="name" optionValue="id"
                        placeholder="All cashiers" showClear filter size="small" class="w-full" />
                    <Select v-model="f.sale_type" :options="saleTypeOptions" optionLabel="label" optionValue="value"
                        placeholder="All sale types" showClear size="small" class="w-full" />
                    <Select v-model="f.payment_status" :options="paymentStatusOptions" optionLabel="label"
                        optionValue="value" placeholder="Any payment status" showClear size="small" class="w-full" />
                    <Select v-model="f.payment_method_id" :options="options.paymentMethods || []" optionLabel="name"
                        optionValue="id" placeholder="Any payment method" showClear filter size="small" class="w-full" />
                    <Select v-model="f.category_id" :options="options.categories || []" optionLabel="name"
                        optionValue="id" placeholder="All categories" showClear filter size="small" class="w-full" />
                    <Select v-model="f.brand_id" :options="options.brands || []" optionLabel="name" optionValue="id"
                        placeholder="All brands" showClear filter size="small" class="w-full" />
                    <Select v-model="f.product_id" :options="options.products || []" optionLabel="name"
                        optionValue="id" placeholder="All products" showClear filter size="small" class="w-full" />
                </div>
            </div>

            <!-- KPIs -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                <StatCard label="Net sales" :value="money(summary.net)" :growth="growth.net" :hint="vsPrev" icon="pi pi-wallet" tone="emerald" />
                <StatCard label="Orders" :value="summary.orders ?? 0" :growth="growth.orders" :hint="vsPrev" icon="pi pi-receipt" tone="sky" />
                <StatCard label="Collected" :value="money(summary.collected)" :growth="growth.collected" :hint="vsPrev" icon="pi pi-money-bill" tone="violet" />
                <StatCard label="Due" :value="money(summary.due)" :hint="`prev. ${money(previous.due)}`" icon="pi pi-clock" tone="rose" />
                <StatCard label="Avg. order" :value="money(summary.avg_order)" :growth="growth.avg_order" :hint="vsPrev" icon="pi pi-chart-line" tone="slate" />
                <StatCard label="Discount" :value="money(summary.discount)" :hint="`Tax ${money(summary.tax)}`" icon="pi pi-percentage" tone="amber" />
                <StatCard label="Items sold" :value="compact(summary.items)" :hint="`Charges ${money(summary.charges)}`" icon="pi pi-box" tone="slate" />
                <StatCard label="Customers" :value="summary.customers ?? 0" hint="unique buyers" icon="pi pi-users" tone="sky" />
            </div>

            <Tabs v-model:value="activeTab" scrollable>
                <TabList>
                    <Tab value="overview"><i class="pi pi-chart-line mr-2" />Overview</Tab>
                    <Tab value="products"><i class="pi pi-box mr-2" />Products &amp; categories</Tab>
                    <Tab value="people"><i class="pi pi-users mr-2" />Customers &amp; staff</Tab>
                    <Tab value="orders"><i class="pi pi-list mr-2" />Orders ({{ orders.total ?? 0 }})</Tab>
                </TabList>

                <TabPanels class="!px-0 !bg-transparent">
                    <!-- ================= OVERVIEW ================= -->
                    <TabPanel value="overview">
                        <div class="space-y-4">
                            <!-- Trend -->
                            <div class="card !p-4">
                                <div class="flex items-center justify-between mb-2">
                                    <h3 class="font-semibold text-slate-700">Sales vs collected</h3>
                                    <Button :label="asTable.trend ? 'Chart' : 'Table'" :icon="asTable.trend ? 'pi pi-chart-line' : 'pi pi-table'"
                                        text size="small" @click="asTable.trend = !asTable.trend" />
                                </div>
                                <div v-if="!summary.orders" class="py-12 text-center text-slate-400 text-sm">No sales in this period</div>
                                <Chart v-else-if="!asTable.trend" type="line" :data="trendData" :options="trendOptions" class="h-72" />
                                <div v-else class="overflow-x-auto">
                                    <table class="w-full text-sm tabular-nums">
                                        <thead>
                                            <tr class="text-slate-500 text-xs border-b">
                                                <th class="text-left py-2 px-2">Period</th>
                                                <th class="text-right py-2 px-2">Orders</th>
                                                <th class="text-right py-2 px-2">Net sales</th>
                                                <th class="text-right py-2 px-2">Collected</th>
                                                <th class="text-right py-2 px-2">Due</th>
                                                <th class="text-right py-2 px-2">Discount</th>
                                                <th class="text-right py-2 px-2">Avg. order</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="r in trend" :key="r.key" class="border-b border-slate-100">
                                                <td class="py-1.5 px-2 whitespace-nowrap">{{ r.label }}</td>
                                                <td class="py-1.5 px-2 text-right">{{ r.orders }}</td>
                                                <td class="py-1.5 px-2 text-right">{{ money(r.net) }}</td>
                                                <td class="py-1.5 px-2 text-right">{{ money(r.collected) }}</td>
                                                <td class="py-1.5 px-2 text-right">{{ money(r.due) }}</td>
                                                <td class="py-1.5 px-2 text-right">{{ money(r.discount) }}</td>
                                                <td class="py-1.5 px-2 text-right">{{ money(r.avg_order) }}</td>
                                            </tr>
                                        </tbody>
                                        <tfoot>
                                            <tr class="font-semibold bg-slate-50">
                                                <td class="py-2 px-2">Total</td>
                                                <td class="py-2 px-2 text-right">{{ sum(trend, 'orders') }}</td>
                                                <td class="py-2 px-2 text-right">{{ money(sum(trend, 'net')) }}</td>
                                                <td class="py-2 px-2 text-right">{{ money(sum(trend, 'collected')) }}</td>
                                                <td class="py-2 px-2 text-right">{{ money(sum(trend, 'due')) }}</td>
                                                <td class="py-2 px-2 text-right">{{ money(sum(trend, 'discount')) }}</td>
                                                <td class="py-2 px-2 text-right">{{ money(summary.avg_order) }}</td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                                <!-- Orders count -->
                                <div class="card !p-4">
                                    <div class="flex items-center justify-between mb-2">
                                        <h3 class="font-semibold text-slate-700">Orders</h3>
                                        <Button :label="asTable.orders ? 'Chart' : 'Table'" :icon="asTable.orders ? 'pi pi-chart-bar' : 'pi pi-table'"
                                            text size="small" @click="asTable.orders = !asTable.orders" />
                                    </div>
                                    <div v-if="!summary.orders" class="py-12 text-center text-slate-400 text-sm">No orders</div>
                                    <Chart v-else-if="!asTable.orders" type="bar" :data="ordersData" :options="ordersOptions" class="h-56" />
                                    <div v-else class="overflow-x-auto max-h-56">
                                        <table class="w-full text-sm tabular-nums">
                                            <tbody>
                                                <tr v-for="r in trend" :key="r.key" class="border-b border-slate-100">
                                                    <td class="py-1 px-2">{{ r.label }}</td>
                                                    <td class="py-1 px-2 text-right">{{ r.orders }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <!-- Hourly -->
                                <div class="card !p-4">
                                    <div class="flex items-center justify-between mb-2">
                                        <h3 class="font-semibold text-slate-700">Sales by hour of day</h3>
                                        <Button :label="asTable.hourly ? 'Chart' : 'Table'" :icon="asTable.hourly ? 'pi pi-chart-bar' : 'pi pi-table'"
                                            text size="small" @click="asTable.hourly = !asTable.hourly" />
                                    </div>
                                    <div v-if="!hasHourly" class="py-12 text-center text-slate-400 text-sm">No data</div>
                                    <Chart v-else-if="!asTable.hourly" type="bar" :data="hourlyData" :options="hourlyOptions" class="h-56" />
                                    <div v-else class="overflow-x-auto max-h-56">
                                        <table class="w-full text-sm tabular-nums">
                                            <thead>
                                                <tr class="text-slate-500 text-xs border-b">
                                                    <th class="text-left py-1 px-2">Hour</th>
                                                    <th class="text-right py-1 px-2">Orders</th>
                                                    <th class="text-right py-1 px-2">Net sales</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr v-for="h in hourly.filter((x) => x.orders > 0)" :key="h.hour" class="border-b border-slate-100">
                                                    <td class="py-1 px-2">{{ h.label }}</td>
                                                    <td class="py-1 px-2 text-right">{{ h.orders }}</td>
                                                    <td class="py-1 px-2 text-right">{{ money(h.net) }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                                <!-- Payment methods -->
                                <div class="card !p-4">
                                    <div class="flex items-center justify-between mb-2">
                                        <h3 class="font-semibold text-slate-700">Payment methods (these sales)</h3>
                                        <Button :label="asTable.payments ? 'Chart' : 'Table'" :icon="asTable.payments ? 'pi pi-chart-pie' : 'pi pi-table'"
                                            text size="small" @click="asTable.payments = !asTable.payments" />
                                    </div>
                                    <div v-if="!paymentMethods.length" class="py-12 text-center text-slate-400 text-sm">No payments</div>
                                    <template v-else>
                                        <Chart v-if="!asTable.payments" type="doughnut" :data="paymentData" :options="pieOptions" class="h-56" />
                                        <div class="overflow-x-auto" :class="asTable.payments ? '' : 'mt-3'">
                                            <table class="w-full text-sm tabular-nums">
                                                <thead>
                                                    <tr class="text-slate-500 text-xs border-b">
                                                        <th class="text-left py-1 px-2">Method</th>
                                                        <th class="text-right py-1 px-2">Payments</th>
                                                        <th class="text-right py-1 px-2">Amount</th>
                                                        <th class="text-right py-1 px-2">Share</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr v-for="(p, i) in paymentMethods" :key="p.label" class="border-b border-slate-100">
                                                        <td class="py-1 px-2">
                                                            <span class="inline-block w-2.5 h-2.5 rounded-full mr-2 align-middle"
                                                                :style="{ background: i < 7 || paymentMethods.length <= 8 ? series(i) : series(7) }" />
                                                            {{ p.label }}
                                                        </td>
                                                        <td class="py-1 px-2 text-right">{{ p.count }}</td>
                                                        <td class="py-1 px-2 text-right">{{ money(p.amount) }}</td>
                                                        <td class="py-1 px-2 text-right text-slate-500">{{ pct(p.amount, paymentMethods, 'amount') }}%</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </template>
                                </div>

                                <!-- Collections in period -->
                                <div class="card !p-4">
                                    <h3 class="font-semibold text-slate-700">Collections received in period</h3>
                                    <p class="text-xs text-slate-500 mb-3">Includes payments of old dues &amp; installments received between these dates.</p>
                                    <div v-if="!collections.length" class="py-12 text-center text-slate-400 text-sm">No collections</div>
                                    <div v-else class="overflow-x-auto">
                                        <table class="w-full text-sm tabular-nums">
                                            <thead>
                                                <tr class="text-slate-500 text-xs border-b">
                                                    <th class="text-left py-1 px-2">Method</th>
                                                    <th class="text-right py-1 px-2">Payments</th>
                                                    <th class="text-right py-1 px-2">Amount</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr v-for="c in collections" :key="c.label" class="border-b border-slate-100">
                                                    <td class="py-1 px-2">{{ c.label }}</td>
                                                    <td class="py-1 px-2 text-right">{{ c.count }}</td>
                                                    <td class="py-1 px-2 text-right">{{ money(c.amount) }}</td>
                                                </tr>
                                            </tbody>
                                            <tfoot>
                                                <tr class="font-semibold bg-slate-50">
                                                    <td class="py-1.5 px-2">Total</td>
                                                    <td class="py-1.5 px-2 text-right">{{ sum(collections, 'count') }}</td>
                                                    <td class="py-1.5 px-2 text-right">{{ money(sum(collections, 'amount')) }}</td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- Sale types -->
                            <div class="card !p-4">
                                <h3 class="font-semibold text-slate-700 mb-3">By sale type</h3>
                                <div v-if="!saleTypes.length" class="py-6 text-center text-slate-400 text-sm">No data</div>
                                <div v-else class="overflow-x-auto">
                                    <table class="w-full text-sm tabular-nums">
                                        <thead>
                                            <tr class="text-slate-500 text-xs border-b">
                                                <th class="text-left py-1 px-2">Type</th>
                                                <th class="text-right py-1 px-2">Orders</th>
                                                <th class="text-right py-1 px-2">Amount</th>
                                                <th class="text-right py-1 px-2">Due</th>
                                                <th class="text-right py-1 px-2">Share</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="s in saleTypes" :key="s.label" class="border-b border-slate-100">
                                                <td class="py-1 px-2"><Tag :value="titleCase(s.label)" :severity="typeSeverity(s.label)" /></td>
                                                <td class="py-1 px-2 text-right">{{ s.orders }}</td>
                                                <td class="py-1 px-2 text-right">{{ money(s.amount) }}</td>
                                                <td class="py-1 px-2 text-right">{{ money(s.due) }}</td>
                                                <td class="py-1 px-2 text-right text-slate-500">{{ pct(s.amount, saleTypes, 'amount') }}%</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </TabPanel>

                    <!-- ================= PRODUCTS ================= -->
                    <TabPanel value="products">
                        <div class="space-y-4">
                            <div class="card !p-4">
                                <div class="flex items-center justify-between mb-2">
                                    <h3 class="font-semibold text-slate-700">Top products by revenue</h3>
                                    <Button :label="asTable.products ? 'Chart' : 'Table'" :icon="asTable.products ? 'pi pi-chart-bar' : 'pi pi-table'"
                                        text size="small" @click="asTable.products = !asTable.products" />
                                </div>
                                <div v-if="!topProducts.length" class="py-12 text-center text-slate-400 text-sm">No products sold</div>
                                <Chart v-else-if="!asTable.products" type="bar" :data="productsData" :options="hBarOptions"
                                    :style="{ height: Math.max(220, topProducts.length * 34) + 'px' }" />
                                <div v-else class="overflow-x-auto">
                                    <table class="w-full text-sm tabular-nums">
                                        <thead>
                                            <tr class="text-slate-500 text-xs border-b">
                                                <th class="text-left py-1 px-2">#</th>
                                                <th class="text-left py-1 px-2">Product</th>
                                                <th class="text-right py-1 px-2">Qty</th>
                                                <th class="text-right py-1 px-2">Orders</th>
                                                <th class="text-right py-1 px-2">Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(p, i) in topProducts" :key="p.product_id ?? p.label" class="border-b border-slate-100">
                                                <td class="py-1 px-2 text-slate-400">{{ i + 1 }}</td>
                                                <td class="py-1 px-2">{{ p.label }}</td>
                                                <td class="py-1 px-2 text-right">{{ p.qty }}</td>
                                                <td class="py-1 px-2 text-right">{{ p.orders }}</td>
                                                <td class="py-1 px-2 text-right">{{ money(p.amount) }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                                <div class="card !p-4">
                                    <div class="flex items-center justify-between mb-2">
                                        <h3 class="font-semibold text-slate-700">Sales by category</h3>
                                        <Button :label="asTable.categories ? 'Chart' : 'Table'" :icon="asTable.categories ? 'pi pi-chart-pie' : 'pi pi-table'"
                                            text size="small" @click="asTable.categories = !asTable.categories" />
                                    </div>
                                    <div v-if="!categories.length" class="py-12 text-center text-slate-400 text-sm">No data</div>
                                    <Chart v-else-if="!asTable.categories" type="doughnut" :data="categoryData" :options="pieOptions" class="h-64" />
                                    <div v-else class="overflow-x-auto">
                                        <table class="w-full text-sm tabular-nums">
                                            <thead>
                                                <tr class="text-slate-500 text-xs border-b">
                                                    <th class="text-left py-1 px-2">Category</th>
                                                    <th class="text-right py-1 px-2">Qty</th>
                                                    <th class="text-right py-1 px-2">Amount</th>
                                                    <th class="text-right py-1 px-2">Share</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr v-for="c in categories" :key="c.label" class="border-b border-slate-100">
                                                    <td class="py-1 px-2">{{ c.label }}</td>
                                                    <td class="py-1 px-2 text-right">{{ c.qty }}</td>
                                                    <td class="py-1 px-2 text-right">{{ money(c.amount) }}</td>
                                                    <td class="py-1 px-2 text-right text-slate-500">{{ pct(c.amount, categories, 'amount') }}%</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="card !p-4">
                                    <div class="flex items-center justify-between mb-2">
                                        <h3 class="font-semibold text-slate-700">Sales by brand</h3>
                                        <Button :label="asTable.brands ? 'Chart' : 'Table'" :icon="asTable.brands ? 'pi pi-chart-bar' : 'pi pi-table'"
                                            text size="small" @click="asTable.brands = !asTable.brands" />
                                    </div>
                                    <div v-if="!brands.length" class="py-12 text-center text-slate-400 text-sm">No data</div>
                                    <Chart v-else-if="!asTable.brands" type="bar" :data="brandsData" :options="hBarOptions"
                                        :style="{ height: Math.max(220, brands.length * 30) + 'px' }" />
                                    <div v-else class="overflow-x-auto">
                                        <table class="w-full text-sm tabular-nums">
                                            <thead>
                                                <tr class="text-slate-500 text-xs border-b">
                                                    <th class="text-left py-1 px-2">Brand</th>
                                                    <th class="text-right py-1 px-2">Qty</th>
                                                    <th class="text-right py-1 px-2">Amount</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr v-for="b in brands" :key="b.label" class="border-b border-slate-100">
                                                    <td class="py-1 px-2">{{ b.label }}</td>
                                                    <td class="py-1 px-2 text-right">{{ b.qty }}</td>
                                                    <td class="py-1 px-2 text-right">{{ money(b.amount) }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </TabPanel>

                    <!-- ================= PEOPLE ================= -->
                    <TabPanel value="people">
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                            <div class="card !p-4">
                                <h3 class="font-semibold text-slate-700">Top customers</h3>
                                <p class="text-xs text-slate-500 mb-3">Click a customer to see their orders.</p>
                                <div v-if="!topCustomers.length" class="py-12 text-center text-slate-400 text-sm">No registered customers in this period</div>
                                <div v-else class="overflow-x-auto">
                                    <table class="w-full text-sm tabular-nums">
                                        <thead>
                                            <tr class="text-slate-500 text-xs border-b">
                                                <th class="text-left py-1 px-2">Customer</th>
                                                <th class="text-left py-1 px-2">Phone</th>
                                                <th class="text-right py-1 px-2">Orders</th>
                                                <th class="text-right py-1 px-2">Amount</th>
                                                <th class="text-right py-1 px-2">Due</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="c in topCustomers" :key="c.id"
                                                class="border-b border-slate-100 hover:bg-slate-50 cursor-pointer"
                                                @click="searchCustomer(c.phone, c.label)">
                                                <td class="py-1.5 px-2 font-medium text-slate-800">{{ c.label }}</td>
                                                <td class="py-1.5 px-2 text-slate-500 whitespace-nowrap">{{ c.phone || '-' }}</td>
                                                <td class="py-1.5 px-2 text-right">{{ c.orders }}</td>
                                                <td class="py-1.5 px-2 text-right">{{ money(c.amount) }}</td>
                                                <td class="py-1.5 px-2 text-right" :class="c.due > 0 ? 'text-rose-700 font-medium' : 'text-slate-400'">{{ money(c.due) }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="card !p-4">
                                <h3 class="font-semibold text-slate-700 mb-3">Sales by cashier</h3>
                                <div v-if="!cashiers.length" class="py-12 text-center text-slate-400 text-sm">No data</div>
                                <div v-else class="overflow-x-auto">
                                    <table class="w-full text-sm tabular-nums">
                                        <thead>
                                            <tr class="text-slate-500 text-xs border-b">
                                                <th class="text-left py-1 px-2">Cashier</th>
                                                <th class="text-right py-1 px-2">Orders</th>
                                                <th class="text-right py-1 px-2">Amount</th>
                                                <th class="text-right py-1 px-2">Share</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="c in cashiers" :key="c.label" class="border-b border-slate-100">
                                                <td class="py-1.5 px-2">{{ c.label }}</td>
                                                <td class="py-1.5 px-2 text-right">{{ c.orders }}</td>
                                                <td class="py-1.5 px-2 text-right">{{ money(c.amount) }}</td>
                                                <td class="py-1.5 px-2 text-right text-slate-500">{{ pct(c.amount, cashiers, 'amount') }}%</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </TabPanel>

                    <!-- ================= ORDERS ================= -->
                    <TabPanel value="orders">
                        <div class="card !p-3">
                            <DataTable :value="orders.data || []" dataKey="id" size="small" showGridlines scrollable
                                responsiveLayout="scroll">
                                <template #empty>
                                    <div class="py-8 text-center text-slate-400 text-sm">No orders match these filters</div>
                                </template>
                                <Column header="Invoice">
                                    <template #body="{ data }">
                                        <a href="#" class="font-semibold text-primary hover:underline whitespace-nowrap"
                                            @click.prevent="openInvoice(data)">{{ data.invoice_no || `#${data.id}` }}</a>
                                    </template>
                                </Column>
                                <Column header="Date">
                                    <template #body="{ data }"><span class="whitespace-nowrap text-xs">{{ dateTime(data.created_at) }}</span></template>
                                </Column>
                                <Column header="Customer">
                                    <template #body="{ data }">
                                        <div class="flex flex-col">
                                            <span>{{ data.customer?.name || 'Walk-in' }}</span>
                                            <span v-if="data.customer?.phone" class="text-xs text-slate-500">{{ data.customer.phone }}</span>
                                        </div>
                                    </template>
                                </Column>
                                <Column header="Cashier">
                                    <template #body="{ data }">{{ data.user?.name || '-' }}</template>
                                </Column>
                                <Column header="Branch">
                                    <template #body="{ data }">{{ data.branch?.name || '-' }}</template>
                                </Column>
                                <Column header="Type">
                                    <template #body="{ data }"><Tag :value="titleCase(data.sale_type || 'regular')" :severity="typeSeverity(data.sale_type)" /></template>
                                </Column>
                                <Column header="Total" class="text-right">
                                    <template #body="{ data }"><span class="tabular-nums font-semibold">{{ money(data.total_amount) }}</span></template>
                                </Column>
                                <Column header="Paid" class="text-right">
                                    <template #body="{ data }"><span class="tabular-nums">{{ money(data.paid_amount) }}</span></template>
                                </Column>
                                <Column header="Due" class="text-right">
                                    <template #body="{ data }">
                                        <span class="tabular-nums" :class="due(data) > 0 ? 'text-rose-700 font-medium' : 'text-slate-400'">{{ money(due(data)) }}</span>
                                    </template>
                                </Column>
                                <Column header="Payment">
                                    <template #body="{ data }"><Tag :value="data.payment_status" :severity="paymentSeverity(data.payment_status)" /></template>
                                </Column>
                            </DataTable>

                            <div class="flex flex-wrap justify-between items-center gap-2 mt-3 text-sm text-slate-500">
                                <span>Showing {{ orders.from || 0 }}–{{ orders.to || 0 }} of {{ orders.total || 0 }}</span>
                                <div class="flex items-center gap-2">
                                    <Select :modelValue="perPage" :options="perPageOptions" optionLabel="label" optionValue="value"
                                        size="small" class="w-32" @update:modelValue="changePerPage" />
                                    <Button icon="pi pi-angle-left" text :disabled="!orders.prev_page_url" @click="goToPage(orders.prev_page_url)" />
                                    <span>{{ orders.current_page || 1 }} / {{ orders.last_page || 1 }}</span>
                                    <Button icon="pi pi-angle-right" text :disabled="!orders.next_page_url" @click="goToPage(orders.next_page_url)" />
                                </div>
                            </div>
                        </div>
                    </TabPanel>
                </TabPanels>
            </Tabs>
        </div>
    </AuthenticatedLayout>
</template>
