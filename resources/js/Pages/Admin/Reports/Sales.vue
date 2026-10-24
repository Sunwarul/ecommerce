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
import Tooltip from "primevue/tooltip";

const vTooltip = Tooltip;

import StatCard from "@/Components/Sales/StatCard.vue";
import { useFlashToast } from "@/Composables/useFlashToast";
import { baseOptions, barDataset, doughnutOptions, foldOther, ink, lineDataset, series } from "@/Helpers/chartTheme";
import { cleanParams, compact, date, dateTime, money, ymd } from "@/Helpers/format";
import { useI18n } from "vue-i18n";
import useAuth from "@/Composables/useAuth";
import { PERMISSIONS } from "@/constants/permissions";
import { useDarkMode, withRtl } from "@/Composables/useDarkMode";

useFlashToast();
const { t } = useI18n();
const { hasPermission } = useAuth();
const canExport = computed(() => hasPermission(PERMISSIONS.REPORT.EXPORT));
const { isRtl, themeKey } = useDarkMode();

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
const PRESET_KEYS = ["today", "yesterday", "last_7_days", "this_week", "last_week", "last_30_days", "this_month", "last_month", "this_quarter", "this_year", "last_year", "custom"];
const presets = computed(() => PRESET_KEYS.map((v) => ({ label: t(`reports.presets.${v}`), value: v })));
const quickPresets = ["today", "this_week", "this_month", "this_year"];
const groupOptions = computed(() => ["auto", "day", "week", "month"].map((v) => ({ label: t(`reports.group.${v}`), value: v })));
const saleTypeOptions = computed(() => ["regular", "installment", "emi"].map((v) => ({ label: t(`shared.sale_type.${v}`), value: v })));
const paymentStatusOptions = computed(() => ["paid", "partial", "unpaid"].map((v) => ({ label: t(`shared.status.${v}`), value: v })));

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

const branchOptions = computed(() => [{ id: "all", name: t("reports.all_branches") }, ...(props.options.branches || [])]);

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
const SECTION_KEYS = ["orders", "summary", "products", "categories", "customers", "payments", "cashiers"];
const formats = [
    { key: "pdf", label: "PDF", icon: "pi pi-file-pdf" },
    { key: "csv", label: "CSV", icon: "pi pi-file" },
    { key: "excel", label: "Excel", icon: "pi pi-file-excel" },
];
function download(section, format) {
    window.open(route("reports.sales", params({ export: format, section })), "_blank");
}
const exportItems = computed(() => SECTION_KEYS.map((key) => ({
    label: t(`reports.sections.${key}`),
    items: formats.map((fm) => ({ label: fm.label, icon: fm.icon, command: () => download(key, fm.key) })),
})));

/* ---------------- Tabs & table toggles ---------------- */
const activeTab = ref("overview");
const asTable = reactive({ trend: false, orders: false, payments: false, hourly: false, products: false, categories: false, brands: false });

/* ---------------- KPIs ---------------- */
const periodLabel = computed(() => `${date(props.filters.date_from)} – ${date(props.filters.date_to)}`);
const vsPrev = computed(() => t("reports.vs_previous"));

/* ---------------- Charts ---------------- */
// Chart computeds read themeKey so colours / RTL rebuild when the theme or direction changes
const rtlOpts = (o) => withRtl(o, isRtl.value);

const trendData = computed(() => {
    themeKey.value;
    return {
        labels: props.trend.map((r) => r.label),
        datasets: [
            lineDataset(t("reports.net_sales"), props.trend.map((r) => r.net), 0),
            lineDataset(t("reports.collected"), props.trend.map((r) => r.collected), 1),
        ],
    };
});
const trendOptions = computed(() => (themeKey.value, rtlOpts(baseOptions())));

const ordersData = computed(() => {
    themeKey.value;
    return {
        labels: props.trend.map((r) => r.label),
        datasets: [barDataset(t("reports.orders"), props.trend.map((r) => r.orders), 0)],
    };
});
const ordersOptions = computed(() => (themeKey.value, rtlOpts(baseOptions({ legend: false, money: false }))));

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

const otherLabel = (rows) => rows.map((r) => (r.label === "Other" ? { ...r, label: t("reports.other") } : r));
const paymentData = computed(() => (themeKey.value, doughnut(otherLabel(props.paymentMethods), "label", "amount")));
const categoryData = computed(() => (themeKey.value, doughnut(otherLabel(props.categories), "label", "amount")));
const pieOptions = computed(() => (themeKey.value, rtlOpts(doughnutOptions())));

const hourlyData = computed(() => {
    themeKey.value;
    return {
        labels: props.hourly.map((h) => h.label),
        datasets: [barDataset(t("reports.net_sales"), props.hourly.map((h) => h.net), 0)],
    };
});
const hourlyOptions = computed(() => (themeKey.value, rtlOpts(baseOptions({ legend: false }))));
const hasHourly = computed(() => props.hourly.some((h) => h.orders > 0));

const productsData = computed(() => {
    themeKey.value;
    return {
        labels: props.topProducts.map((p) => p.label),
        datasets: [barDataset(t("shared.terms.amount"), props.topProducts.map((p) => p.amount), 0)],
    };
});
const brandsData = computed(() => {
    themeKey.value;
    return {
        labels: props.brands.map((b) => b.label),
        datasets: [barDataset(t("shared.terms.amount"), props.brands.map((b) => b.amount), 0)],
    };
});
const hBarOptions = computed(() => (themeKey.value, rtlOpts(baseOptions({ horizontal: true, legend: false }))));

/* ---------------- Totals ---------------- */
const sum = (rows, key) => rows.reduce((s, r) => s + Number(r[key] || 0), 0);
const pct = (v, rows, key) => {
    const total = sum(rows, key);
    return total ? ((Number(v || 0) / total) * 100).toFixed(1) : "0.0";
};

/* ---------------- Orders table ---------------- */
const perPageOptions = computed(() => [20, 50, 100].map((v) => ({ label: t("reports.per_page", { n: v }), value: v })));
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
        <Head :title="t('reports.title')" />

        <div class="p-3 md:p-6 space-y-4">
            <!-- HEADER -->
            <div class="flex flex-wrap items-start justify-between gap-3">
                <div>
                    <h1 class="text-xl font-semibold text-slate-800 flex items-center gap-2">
                        <i class="pi pi-chart-bar text-primary" /> {{ t('reports.title') }}
                    </h1>
                    <p class="text-sm text-slate-500">
                        <span class="ltr-nums">{{ periodLabel }}</span> · {{ t('reports.grouped_by', { group: t('reports.group.' + filters.resolved_group_by) }) }}
                    </p>
                </div>
                <div v-if="canExport" class="flex items-center gap-2">
                    <Button :label="t('shared.terms.export')" icon="pi pi-download" severity="secondary" outlined size="small"
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
                        <DatePicker v-model="f.date_from" dateFormat="dd-mm-yy" :placeholder="t('shared.terms.from')" showIcon size="small"
                            class="w-[calc(50%-0.25rem)] sm:w-40" />
                        <DatePicker v-model="f.date_to" dateFormat="dd-mm-yy" :placeholder="t('shared.terms.to')" showIcon size="small"
                            class="w-[calc(50%-0.25rem)] sm:w-40" />
                    </template>
                    <Select v-model="f.group_by" :options="groupOptions" optionLabel="label" optionValue="value"
                        class="w-full sm:w-32" size="small" />
                    <InputText v-model="f.search" type="search" size="small" class="w-full sm:flex-1 sm:min-w-[16rem]"
                        :placeholder="t('reports.search_placeholder')" v-tooltip.bottom="t('shared.terms.search_hint')" @keyup.enter="apply()" />
                    <div class="flex gap-1 ms-auto">
                        <Button :label="activeMoreFilters ? `${t('reports.more_filters')} (${activeMoreFilters})` : t('reports.more_filters')"
                            :icon="showMore ? 'pi pi-chevron-up' : 'pi pi-sliders-h'" text size="small"
                            @click="showMore = !showMore" />
                        <Button :label="t('shared.terms.apply')" icon="pi pi-filter" size="small" @click="apply()" />
                        <Button :label="t('shared.terms.reset')" icon="pi pi-refresh" text size="small" @click="reset" />
                    </div>
                </div>

                <div v-if="showMore" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-2 pt-3 border-t border-slate-100">
                    <Select v-if="options.isAdmin" v-model="f.branch_id" :options="branchOptions" optionLabel="name"
                        optionValue="id" :placeholder="t('reports.current_branch')" showClear filter size="small" class="w-full" />
                    <Select v-model="f.user_id" :options="options.users || []" optionLabel="name" optionValue="id"
                        :placeholder="t('reports.all_cashiers')" showClear filter size="small" class="w-full" />
                    <Select v-model="f.sale_type" :options="saleTypeOptions" optionLabel="label" optionValue="value"
                        :placeholder="t('reports.all_sale_types')" showClear size="small" class="w-full" />
                    <Select v-model="f.payment_status" :options="paymentStatusOptions" optionLabel="label"
                        optionValue="value" :placeholder="t('reports.any_payment_status')" showClear size="small" class="w-full" />
                    <Select v-model="f.payment_method_id" :options="options.paymentMethods || []" optionLabel="name"
                        optionValue="id" :placeholder="t('reports.any_payment_method')" showClear filter size="small" class="w-full" />
                    <Select v-model="f.category_id" :options="options.categories || []" optionLabel="name"
                        optionValue="id" :placeholder="t('reports.all_categories')" showClear filter size="small" class="w-full" />
                    <Select v-model="f.brand_id" :options="options.brands || []" optionLabel="name" optionValue="id"
                        :placeholder="t('reports.all_brands')" showClear filter size="small" class="w-full" />
                    <Select v-model="f.product_id" :options="options.products || []" optionLabel="name"
                        optionValue="id" :placeholder="t('reports.all_products')" showClear filter size="small" class="w-full" />
                </div>
            </div>

            <!-- KPIs -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                <StatCard :label="t('reports.net_sales')" :value="money(summary.net)" :growth="growth.net" :hint="vsPrev" icon="pi pi-wallet" tone="emerald" />
                <StatCard :label="t('reports.orders')" :value="summary.orders ?? 0" :growth="growth.orders" :hint="vsPrev" icon="pi pi-receipt" tone="sky" />
                <StatCard :label="t('reports.collected')" :value="money(summary.collected)" :growth="growth.collected" :hint="vsPrev" icon="pi pi-money-bill" tone="violet" />
                <StatCard :label="t('shared.terms.due')" :value="money(summary.due)" :hint="t('reports.prev_value', { amount: money(previous.due) })" icon="pi pi-clock" tone="rose" />
                <StatCard :label="t('reports.avg_order')" :value="money(summary.avg_order)" :growth="growth.avg_order" :hint="vsPrev" icon="pi pi-chart-line" tone="slate" />
                <StatCard :label="t('reports.discount')" :value="money(summary.discount)" :hint="t('reports.tax_value', { amount: money(summary.tax) })" icon="pi pi-percentage" tone="amber" />
                <StatCard :label="t('reports.items_sold')" :value="compact(summary.items)" :hint="t('reports.charges_value', { amount: money(summary.charges) })" icon="pi pi-box" tone="slate" />
                <StatCard :label="t('reports.customers')" :value="summary.customers ?? 0" :hint="t('reports.unique_buyers')" icon="pi pi-users" tone="sky" />
            </div>

            <Tabs v-model:value="activeTab" scrollable>
                <TabList>
                    <Tab value="overview"><i class="pi pi-chart-line me-2" />{{ t('reports.tabs.overview') }}</Tab>
                    <Tab value="products"><i class="pi pi-box me-2" />{{ t('reports.tabs.products') }}</Tab>
                    <Tab value="people"><i class="pi pi-users me-2" />{{ t('reports.tabs.people') }}</Tab>
                    <Tab value="orders"><i class="pi pi-list me-2" />{{ t('reports.orders') }} ({{ orders.total ?? 0 }})</Tab>
                </TabList>

                <TabPanels class="!px-0 !bg-transparent">
                    <!-- ================= OVERVIEW ================= -->
                    <TabPanel value="overview">
                        <div class="space-y-4">
                            <!-- Trend -->
                            <div class="card !p-4">
                                <div class="flex items-center justify-between mb-2">
                                    <h2 class="font-semibold text-slate-700">{{ t('reports.sales_vs_collected') }}</h2>
                                    <Button :label="asTable.trend ? t('shared.terms.chart') : t('shared.terms.table')" :icon="asTable.trend ? 'pi pi-chart-line' : 'pi pi-table'"
                                        text size="small" @click="asTable.trend = !asTable.trend" />
                                </div>
                                <div v-if="!summary.orders" class="py-12 text-center text-slate-400 text-sm">{{ t('reports.empty.sales') }}</div>
                                <Chart :key="themeKey" v-else-if="!asTable.trend" type="line" :data="trendData" :options="trendOptions" class="h-72" />
                                <div v-else class="overflow-x-auto">
                                    <table class="w-full text-sm tabular-nums">
                                        <thead>
                                            <tr class="text-slate-500 text-xs border-b">
                                                <th class="text-start py-2 px-2">{{ t('reports.period') }}</th>
                                                <th class="text-end py-2 px-2">{{ t('reports.orders') }}</th>
                                                <th class="text-end py-2 px-2">{{ t('reports.net_sales') }}</th>
                                                <th class="text-end py-2 px-2">{{ t('reports.collected') }}</th>
                                                <th class="text-end py-2 px-2">{{ t('shared.terms.due') }}</th>
                                                <th class="text-end py-2 px-2">{{ t('reports.discount') }}</th>
                                                <th class="text-end py-2 px-2">{{ t('reports.avg_order') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="r in trend" :key="r.key" class="border-b border-slate-100">
                                                <td class="py-1.5 px-2 whitespace-nowrap">{{ r.label }}</td>
                                                <td class="py-1.5 px-2 text-end">{{ r.orders }}</td>
                                                <td class="py-1.5 px-2 text-end">{{ money(r.net) }}</td>
                                                <td class="py-1.5 px-2 text-end">{{ money(r.collected) }}</td>
                                                <td class="py-1.5 px-2 text-end">{{ money(r.due) }}</td>
                                                <td class="py-1.5 px-2 text-end">{{ money(r.discount) }}</td>
                                                <td class="py-1.5 px-2 text-end">{{ money(r.avg_order) }}</td>
                                            </tr>
                                        </tbody>
                                        <tfoot>
                                            <tr class="font-semibold bg-slate-50">
                                                <td class="py-2 px-2">{{ t('shared.terms.total') }}</td>
                                                <td class="py-2 px-2 text-end">{{ sum(trend, 'orders') }}</td>
                                                <td class="py-2 px-2 text-end">{{ money(sum(trend, 'net')) }}</td>
                                                <td class="py-2 px-2 text-end">{{ money(sum(trend, 'collected')) }}</td>
                                                <td class="py-2 px-2 text-end">{{ money(sum(trend, 'due')) }}</td>
                                                <td class="py-2 px-2 text-end">{{ money(sum(trend, 'discount')) }}</td>
                                                <td class="py-2 px-2 text-end">{{ money(summary.avg_order) }}</td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                                <!-- Orders count -->
                                <div class="card !p-4">
                                    <div class="flex items-center justify-between mb-2">
                                        <h2 class="font-semibold text-slate-700">{{ t('reports.orders') }}</h2>
                                        <Button :label="asTable.orders ? t('shared.terms.chart') : t('shared.terms.table')" :icon="asTable.orders ? 'pi pi-chart-bar' : 'pi pi-table'"
                                            text size="small" @click="asTable.orders = !asTable.orders" />
                                    </div>
                                    <div v-if="!summary.orders" class="py-12 text-center text-slate-400 text-sm">{{ t('reports.empty.orders') }}</div>
                                    <Chart :key="themeKey" v-else-if="!asTable.orders" type="bar" :data="ordersData" :options="ordersOptions" class="h-56" />
                                    <div v-else class="overflow-x-auto max-h-56">
                                        <table class="w-full text-sm tabular-nums">
                                            <tbody>
                                                <tr v-for="r in trend" :key="r.key" class="border-b border-slate-100">
                                                    <td class="py-1 px-2">{{ r.label }}</td>
                                                    <td class="py-1 px-2 text-end">{{ r.orders }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <!-- Hourly -->
                                <div class="card !p-4">
                                    <div class="flex items-center justify-between mb-2">
                                        <h2 class="font-semibold text-slate-700">{{ t('reports.by_hour') }}</h2>
                                        <Button :label="asTable.hourly ? t('shared.terms.chart') : t('shared.terms.table')" :icon="asTable.hourly ? 'pi pi-chart-bar' : 'pi pi-table'"
                                            text size="small" @click="asTable.hourly = !asTable.hourly" />
                                    </div>
                                    <div v-if="!hasHourly" class="py-12 text-center text-slate-400 text-sm">{{ t('shared.terms.no_data') }}</div>
                                    <Chart :key="themeKey" v-else-if="!asTable.hourly" type="bar" :data="hourlyData" :options="hourlyOptions" class="h-56" />
                                    <div v-else class="overflow-x-auto max-h-56">
                                        <table class="w-full text-sm tabular-nums">
                                            <thead>
                                                <tr class="text-slate-500 text-xs border-b">
                                                    <th class="text-start py-1 px-2">{{ t('reports.hour') }}</th>
                                                    <th class="text-end py-1 px-2">{{ t('reports.orders') }}</th>
                                                    <th class="text-end py-1 px-2">{{ t('reports.net_sales') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr v-for="h in hourly.filter((x) => x.orders > 0)" :key="h.hour" class="border-b border-slate-100">
                                                    <td class="py-1 px-2">{{ h.label }}</td>
                                                    <td class="py-1 px-2 text-end">{{ h.orders }}</td>
                                                    <td class="py-1 px-2 text-end">{{ money(h.net) }}</td>
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
                                        <h2 class="font-semibold text-slate-700">{{ t('reports.payment_methods') }}</h2>
                                        <Button :label="asTable.payments ? t('shared.terms.chart') : t('shared.terms.table')" :icon="asTable.payments ? 'pi pi-chart-pie' : 'pi pi-table'"
                                            text size="small" @click="asTable.payments = !asTable.payments" />
                                    </div>
                                    <div v-if="!paymentMethods.length" class="py-12 text-center text-slate-400 text-sm">{{ t('reports.empty.payments') }}</div>
                                    <template v-else>
                                        <Chart :key="themeKey" v-if="!asTable.payments" type="doughnut" :data="paymentData" :options="pieOptions" class="h-56" />
                                        <div class="overflow-x-auto" :class="asTable.payments ? '' : 'mt-3'">
                                            <table class="w-full text-sm tabular-nums">
                                                <thead>
                                                    <tr class="text-slate-500 text-xs border-b">
                                                        <th class="text-start py-1 px-2">{{ t('shared.terms.method') }}</th>
                                                        <th class="text-end py-1 px-2">{{ t('reports.payments') }}</th>
                                                        <th class="text-end py-1 px-2">{{ t('shared.terms.amount') }}</th>
                                                        <th class="text-end py-1 px-2">{{ t('reports.share') }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr v-for="(p, i) in paymentMethods" :key="p.label" class="border-b border-slate-100">
                                                        <td class="py-1 px-2">
                                                            <span class="inline-block w-2.5 h-2.5 rounded-full me-2 align-middle"
                                                                :style="{ background: i < 7 || paymentMethods.length <= 8 ? series(i) : series(7) }" />
                                                            {{ p.label }}
                                                        </td>
                                                        <td class="py-1 px-2 text-end">{{ p.count }}</td>
                                                        <td class="py-1 px-2 text-end">{{ money(p.amount) }}</td>
                                                        <td class="py-1 px-2 text-end text-slate-500">{{ pct(p.amount, paymentMethods, 'amount') }}%</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </template>
                                </div>

                                <!-- Collections in period -->
                                <div class="card !p-4">
                                    <h2 class="font-semibold text-slate-700">{{ t('reports.collections') }}</h2>
                                    <p class="text-xs text-slate-500 mb-3">{{ t('reports.collections_help') }}</p>
                                    <div v-if="!collections.length" class="py-12 text-center text-slate-400 text-sm">{{ t('reports.empty.collections') }}</div>
                                    <div v-else class="overflow-x-auto">
                                        <table class="w-full text-sm tabular-nums">
                                            <thead>
                                                <tr class="text-slate-500 text-xs border-b">
                                                    <th class="text-start py-1 px-2">{{ t('shared.terms.method') }}</th>
                                                    <th class="text-end py-1 px-2">{{ t('reports.payments') }}</th>
                                                    <th class="text-end py-1 px-2">{{ t('shared.terms.amount') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr v-for="c in collections" :key="c.label" class="border-b border-slate-100">
                                                    <td class="py-1 px-2">{{ c.label }}</td>
                                                    <td class="py-1 px-2 text-end">{{ c.count }}</td>
                                                    <td class="py-1 px-2 text-end">{{ money(c.amount) }}</td>
                                                </tr>
                                            </tbody>
                                            <tfoot>
                                                <tr class="font-semibold bg-slate-50">
                                                    <td class="py-1.5 px-2">{{ t('shared.terms.total') }}</td>
                                                    <td class="py-1.5 px-2 text-end">{{ sum(collections, 'count') }}</td>
                                                    <td class="py-1.5 px-2 text-end">{{ money(sum(collections, 'amount')) }}</td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- Sale types -->
                            <div class="card !p-4">
                                <h2 class="font-semibold text-slate-700 mb-3">{{ t('reports.by_sale_type') }}</h2>
                                <div v-if="!saleTypes.length" class="py-6 text-center text-slate-400 text-sm">{{ t('shared.terms.no_data') }}</div>
                                <div v-else class="overflow-x-auto">
                                    <table class="w-full text-sm tabular-nums">
                                        <thead>
                                            <tr class="text-slate-500 text-xs border-b">
                                                <th class="text-start py-1 px-2">{{ t('reports.type') }}</th>
                                                <th class="text-end py-1 px-2">{{ t('reports.orders') }}</th>
                                                <th class="text-end py-1 px-2">{{ t('shared.terms.amount') }}</th>
                                                <th class="text-end py-1 px-2">{{ t('shared.terms.due') }}</th>
                                                <th class="text-end py-1 px-2">{{ t('reports.share') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="s in saleTypes" :key="s.label" class="border-b border-slate-100">
                                                <td class="py-1 px-2"><Tag :value="t('shared.sale_type.' + (s.label || 'regular'))" :severity="typeSeverity(s.label)" /></td>
                                                <td class="py-1 px-2 text-end">{{ s.orders }}</td>
                                                <td class="py-1 px-2 text-end">{{ money(s.amount) }}</td>
                                                <td class="py-1 px-2 text-end">{{ money(s.due) }}</td>
                                                <td class="py-1 px-2 text-end text-slate-500">{{ pct(s.amount, saleTypes, 'amount') }}%</td>
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
                                    <h2 class="font-semibold text-slate-700">{{ t('reports.top_products') }}</h2>
                                    <Button :label="asTable.products ? t('shared.terms.chart') : t('shared.terms.table')" :icon="asTable.products ? 'pi pi-chart-bar' : 'pi pi-table'"
                                        text size="small" @click="asTable.products = !asTable.products" />
                                </div>
                                <div v-if="!topProducts.length" class="py-12 text-center text-slate-400 text-sm">{{ t('reports.empty.products') }}</div>
                                <Chart :key="themeKey" v-else-if="!asTable.products" type="bar" :data="productsData" :options="hBarOptions"
                                    :style="{ height: Math.max(220, topProducts.length * 34) + 'px' }" />
                                <div v-else class="overflow-x-auto">
                                    <table class="w-full text-sm tabular-nums">
                                        <thead>
                                            <tr class="text-slate-500 text-xs border-b">
                                                <th class="text-start py-1 px-2">#</th>
                                                <th class="text-start py-1 px-2">{{ t('reports.product') }}</th>
                                                <th class="text-end py-1 px-2">{{ t('reports.qty') }}</th>
                                                <th class="text-end py-1 px-2">{{ t('reports.orders') }}</th>
                                                <th class="text-end py-1 px-2">{{ t('shared.terms.amount') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(p, i) in topProducts" :key="p.product_id ?? p.label" class="border-b border-slate-100">
                                                <td class="py-1 px-2 text-slate-400">{{ i + 1 }}</td>
                                                <td class="py-1 px-2">{{ p.label }}</td>
                                                <td class="py-1 px-2 text-end">{{ p.qty }}</td>
                                                <td class="py-1 px-2 text-end">{{ p.orders }}</td>
                                                <td class="py-1 px-2 text-end">{{ money(p.amount) }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                                <div class="card !p-4">
                                    <div class="flex items-center justify-between mb-2">
                                        <h2 class="font-semibold text-slate-700">{{ t('reports.by_category') }}</h2>
                                        <Button :label="asTable.categories ? t('shared.terms.chart') : t('shared.terms.table')" :icon="asTable.categories ? 'pi pi-chart-pie' : 'pi pi-table'"
                                            text size="small" @click="asTable.categories = !asTable.categories" />
                                    </div>
                                    <div v-if="!categories.length" class="py-12 text-center text-slate-400 text-sm">{{ t('shared.terms.no_data') }}</div>
                                    <Chart :key="themeKey" v-else-if="!asTable.categories" type="doughnut" :data="categoryData" :options="pieOptions" class="h-64" />
                                    <div v-else class="overflow-x-auto">
                                        <table class="w-full text-sm tabular-nums">
                                            <thead>
                                                <tr class="text-slate-500 text-xs border-b">
                                                    <th class="text-start py-1 px-2">{{ t('reports.category') }}</th>
                                                    <th class="text-end py-1 px-2">{{ t('reports.qty') }}</th>
                                                    <th class="text-end py-1 px-2">{{ t('shared.terms.amount') }}</th>
                                                    <th class="text-end py-1 px-2">{{ t('reports.share') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr v-for="c in categories" :key="c.label" class="border-b border-slate-100">
                                                    <td class="py-1 px-2">{{ c.label }}</td>
                                                    <td class="py-1 px-2 text-end">{{ c.qty }}</td>
                                                    <td class="py-1 px-2 text-end">{{ money(c.amount) }}</td>
                                                    <td class="py-1 px-2 text-end text-slate-500">{{ pct(c.amount, categories, 'amount') }}%</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="card !p-4">
                                    <div class="flex items-center justify-between mb-2">
                                        <h2 class="font-semibold text-slate-700">{{ t('reports.by_brand') }}</h2>
                                        <Button :label="asTable.brands ? t('shared.terms.chart') : t('shared.terms.table')" :icon="asTable.brands ? 'pi pi-chart-bar' : 'pi pi-table'"
                                            text size="small" @click="asTable.brands = !asTable.brands" />
                                    </div>
                                    <div v-if="!brands.length" class="py-12 text-center text-slate-400 text-sm">{{ t('shared.terms.no_data') }}</div>
                                    <Chart :key="themeKey" v-else-if="!asTable.brands" type="bar" :data="brandsData" :options="hBarOptions"
                                        :style="{ height: Math.max(220, brands.length * 30) + 'px' }" />
                                    <div v-else class="overflow-x-auto">
                                        <table class="w-full text-sm tabular-nums">
                                            <thead>
                                                <tr class="text-slate-500 text-xs border-b">
                                                    <th class="text-start py-1 px-2">{{ t('reports.brand') }}</th>
                                                    <th class="text-end py-1 px-2">{{ t('reports.qty') }}</th>
                                                    <th class="text-end py-1 px-2">{{ t('shared.terms.amount') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr v-for="b in brands" :key="b.label" class="border-b border-slate-100">
                                                    <td class="py-1 px-2">{{ b.label }}</td>
                                                    <td class="py-1 px-2 text-end">{{ b.qty }}</td>
                                                    <td class="py-1 px-2 text-end">{{ money(b.amount) }}</td>
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
                                <h2 class="font-semibold text-slate-700">{{ t('reports.top_customers') }}</h2>
                                <p class="text-xs text-slate-500 mb-3">{{ t('reports.click_customer') }}</p>
                                <div v-if="!topCustomers.length" class="py-12 text-center text-slate-400 text-sm">{{ t('reports.empty.customers') }}</div>
                                <div v-else class="overflow-x-auto">
                                    <table class="w-full text-sm tabular-nums">
                                        <thead>
                                            <tr class="text-slate-500 text-xs border-b">
                                                <th class="text-start py-1 px-2">{{ t('shared.terms.customer') }}</th>
                                                <th class="text-start py-1 px-2">{{ t('shared.terms.phone') }}</th>
                                                <th class="text-end py-1 px-2">{{ t('reports.orders') }}</th>
                                                <th class="text-end py-1 px-2">{{ t('shared.terms.amount') }}</th>
                                                <th class="text-end py-1 px-2">{{ t('shared.terms.due') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="c in topCustomers" :key="c.id"
                                                class="border-b border-slate-100 hover:bg-slate-50 cursor-pointer"
                                                @click="searchCustomer(c.phone, c.label)">
                                                <td class="py-1.5 px-2 font-medium text-slate-800">{{ c.label }}</td>
                                                <td class="py-1.5 px-2 text-slate-500 whitespace-nowrap ltr-nums">{{ c.phone || '-' }}</td>
                                                <td class="py-1.5 px-2 text-end">{{ c.orders }}</td>
                                                <td class="py-1.5 px-2 text-end">{{ money(c.amount) }}</td>
                                                <td class="py-1.5 px-2 text-end" :class="c.due > 0 ? 'text-rose-700 font-medium' : 'text-slate-400'">{{ money(c.due) }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="card !p-4">
                                <h2 class="font-semibold text-slate-700 mb-3">{{ t('reports.by_cashier') }}</h2>
                                <div v-if="!cashiers.length" class="py-12 text-center text-slate-400 text-sm">{{ t('shared.terms.no_data') }}</div>
                                <div v-else class="overflow-x-auto">
                                    <table class="w-full text-sm tabular-nums">
                                        <thead>
                                            <tr class="text-slate-500 text-xs border-b">
                                                <th class="text-start py-1 px-2">{{ t('shared.terms.cashier') }}</th>
                                                <th class="text-end py-1 px-2">{{ t('reports.orders') }}</th>
                                                <th class="text-end py-1 px-2">{{ t('shared.terms.amount') }}</th>
                                                <th class="text-end py-1 px-2">{{ t('reports.share') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="c in cashiers" :key="c.label" class="border-b border-slate-100">
                                                <td class="py-1.5 px-2">{{ c.label }}</td>
                                                <td class="py-1.5 px-2 text-end">{{ c.orders }}</td>
                                                <td class="py-1.5 px-2 text-end">{{ money(c.amount) }}</td>
                                                <td class="py-1.5 px-2 text-end text-slate-500">{{ pct(c.amount, cashiers, 'amount') }}%</td>
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
                                    <div class="py-8 text-center text-slate-400 text-sm">{{ t('reports.empty.filtered') }}</div>
                                </template>
                                <Column :header="t('shared.terms.invoice')">
                                    <template #body="{ data }">
                                        <a href="#" class="font-semibold text-primary hover:underline whitespace-nowrap"
                                            @click.prevent="openInvoice(data)">{{ data.invoice_no || `#${data.id}` }}</a>
                                    </template>
                                </Column>
                                <Column :header="t('shared.terms.date')">
                                    <template #body="{ data }"><span class="whitespace-nowrap text-xs">{{ dateTime(data.created_at) }}</span></template>
                                </Column>
                                <Column :header="t('shared.terms.customer')">
                                    <template #body="{ data }">
                                        <div class="flex flex-col">
                                            <span>{{ data.customer?.name || t('shared.terms.walk_in') }}</span>
                                            <span v-if="data.customer?.phone" class="text-xs text-slate-500 ltr-nums text-start">{{ data.customer.phone }}</span>
                                        </div>
                                    </template>
                                </Column>
                                <Column :header="t('shared.terms.cashier')">
                                    <template #body="{ data }">{{ data.user?.name || '-' }}</template>
                                </Column>
                                <Column :header="t('shared.terms.branch')">
                                    <template #body="{ data }">{{ data.branch?.name || '-' }}</template>
                                </Column>
                                <Column :header="t('reports.type')">
                                    <template #body="{ data }"><Tag :value="t('shared.sale_type.' + (data.sale_type || 'regular'))" :severity="typeSeverity(data.sale_type)" /></template>
                                </Column>
                                <Column :header="t('shared.terms.total')" class="text-end">
                                    <template #body="{ data }"><span class="tabular-nums font-semibold">{{ money(data.total_amount) }}</span></template>
                                </Column>
                                <Column :header="t('shared.terms.paid')" class="text-end">
                                    <template #body="{ data }"><span class="tabular-nums">{{ money(data.paid_amount) }}</span></template>
                                </Column>
                                <Column :header="t('shared.terms.due')" class="text-end">
                                    <template #body="{ data }">
                                        <span class="tabular-nums" :class="due(data) > 0 ? 'text-rose-700 font-medium' : 'text-slate-400'">{{ money(due(data)) }}</span>
                                    </template>
                                </Column>
                                <Column :header="t('reports.payment')">
                                    <template #body="{ data }"><Tag :value="t('shared.status.' + data.payment_status)" :severity="paymentSeverity(data.payment_status)" /></template>
                                </Column>
                            </DataTable>

                            <div class="flex flex-wrap justify-between items-center gap-2 mt-3 text-sm text-slate-500">
                                <span>{{ t('shared.terms.showing', { from: orders.from || 0, to: orders.to || 0, total: orders.total || 0 }) }}</span>
                                <div class="flex items-center gap-2">
                                    <Select :modelValue="perPage" :options="perPageOptions" optionLabel="label" optionValue="value"
                                        size="small" class="w-32" @update:modelValue="changePerPage" />
                                    <Button icon="pi pi-angle-left" text :aria-label="t('shared.terms.previous')" :disabled="!orders.prev_page_url" @click="goToPage(orders.prev_page_url)" />
                                    <span>{{ orders.current_page || 1 }} / {{ orders.last_page || 1 }}</span>
                                    <Button icon="pi pi-angle-right" text :aria-label="t('shared.terms.next')" :disabled="!orders.next_page_url" @click="goToPage(orders.next_page_url)" />
                                </div>
                            </div>
                        </div>
                    </TabPanel>
                </TabPanels>
            </Tabs>
        </div>
    </AuthenticatedLayout>
</template>
