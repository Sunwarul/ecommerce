<script setup>
import { computed } from "vue";
import { Head, Link, usePage } from "@inertiajs/vue3";
import { useI18n } from "vue-i18n";
import Button from "primevue/button";
import Chart from "primevue/chart";
import Tag from "primevue/tag";

import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import StatCard from "@/Components/Sales/StatCard.vue";
import useAuth from "@/Composables/useAuth";
import { useFlashToast } from "@/Composables/useFlashToast";
import { PERMISSIONS } from "@/constants/permissions";
import { baseOptions, barDataset } from "@/Helpers/chartTheme";
import { money } from "@/Helpers/format";

const { t, locale } = useI18n();
const page = usePage();
const { hasPermission } = useAuth();
useFlashToast();

const props = defineProps({
    stats: { type: Object, default: () => ({}) },
    chart: { type: Object, default: () => ({ labels: [], data: [] }) },
    top_products: { type: Array, default: () => [] },
    low_stock: { type: Array, default: () => [] },
    recent_orders: { type: Array, default: () => [] },
});

const currencySymbol = computed(() => page.props.configs?.currency_symbol || "");
const cash = (v) => `${currencySymbol.value}${money(v)}`;
const userName = computed(() => page.props.auth?.user?.name || "");

const greeting = computed(() => {
    const h = new Date().getHours();
    return h < 12 ? t("dashboard_page.good_morning") : h < 17 ? t("dashboard_page.good_afternoon") : t("dashboard_page.good_evening");
});

const todayLabel = computed(() =>
    new Date().toLocaleDateString(locale.value, { weekday: "long", day: "numeric", month: "long", year: "numeric" })
);

/* ---------------- KPIs ---------------- */
const kpis = computed(() => [
    { label: t("dashboard_page.today"), value: cash(props.stats.daily_sales), hint: t("dashboard_page.orders_count", { count: props.stats.daily_orders || 0 }), icon: "pi pi-sun", tone: "emerald" },
    { label: t("dashboard_page.this_week"), value: cash(props.stats.weekly_sales), hint: t("dashboard_page.orders_count", { count: props.stats.weekly_orders || 0 }), icon: "pi pi-calendar", tone: "sky" },
    { label: t("dashboard_page.this_month"), value: cash(props.stats.monthly_sales), hint: t("dashboard_page.orders_count", { count: props.stats.monthly_orders || 0 }), icon: "pi pi-chart-line", tone: "violet" },
    {
        label: t("dashboard_page.best_seller"),
        value: props.top_products[0]?.name || "—",
        hint: props.top_products[0] ? t("dashboard_page.units_sold", { qty: props.top_products[0].total_qty }) : t("dashboard_page.no_sales_yet"),
        icon: "pi pi-star",
        tone: "amber",
    },
]);

/* ---------------- Chart: last 7 days (localized day names) ---------------- */
const chartLabels = computed(() => {
    const data = props.chart?.data || [];
    if (data.length !== 7) return props.chart?.labels || [];
    return data.map((_, i) => {
        const d = new Date();
        d.setDate(d.getDate() - (6 - i));
        return d.toLocaleDateString(locale.value, { weekday: "short" });
    });
});
const chartData = computed(() => ({
    labels: chartLabels.value,
    datasets: [barDataset(t("dashboard_page.net_sales"), (props.chart?.data || []).map(Number), 0)],
}));
const chartOptions = computed(() => baseOptions({ legend: false }));
const weekTotal = computed(() => (props.chart?.data || []).reduce((s, v) => s + Number(v || 0), 0));
const hasChartData = computed(() => weekTotal.value > 0);

/* ---------------- Quick actions (permission aware) ---------------- */
const quickActions = computed(() =>
    [
        { label: t("dashboard_page.new_sale"), icon: "pi pi-desktop", href: route("pos.index"), perm: PERMISSIONS.POS.INDEX, tone: "bg-emerald-50 text-emerald-700" },
        { label: t("menu.orders"), icon: "pi pi-receipt", href: route("pos.orders.index"), perm: PERMISSIONS.ORDER.INDEX, tone: "bg-sky-50 text-sky-700" },
        { label: t("dashboard_page.due_today"), icon: "pi pi-calendar-clock", href: route("installments.index", { tab: "due_today" }), perm: PERMISSIONS.INSTALLMENT?.INDEX, tone: "bg-violet-50 text-violet-700" },
        { label: t("menu.customers"), icon: "pi pi-users", href: route("customers.index"), perm: PERMISSIONS.CUSTOMER.INDEX, tone: "bg-amber-50 text-amber-700" },
        { label: t("products.add_product"), icon: "pi pi-plus", href: route("products.create"), perm: PERMISSIONS.PRODUCT.STORE, tone: "bg-slate-100 text-slate-700" },
        { label: t("menu.sales_report"), icon: "pi pi-chart-bar", href: route("reports.sales"), perm: PERMISSIONS.REPORT?.INDEX, tone: "bg-rose-50 text-rose-700" },
    ].filter((a) => !a.perm || hasPermission(a.perm))
);

/* ---------------- Tables ---------------- */
const statusLabel = (v) => (v ? t(`shared.status.${v}`) : "-");
function statusSeverity(v) {
    if (v === "completed" || v === "paid") return "success";
    if (v === "draft") return "info";
    if (v === "partial" || v === "pending") return "warn";
    return "danger";
}
function stockSeverity(q) {
    return Number(q) <= 0 ? "danger" : "warn";
}
</script>

<template>
    <Head :title="t('menu.dashboard')" />

    <AuthenticatedLayout>
        <div class="p-2 md:p-4 space-y-5">
            <!-- Header -->
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                <div class="min-w-0">
                    <h1 class="text-xl md:text-2xl font-semibold text-slate-800">
                        {{ greeting }}<span v-if="userName">, {{ userName }}</span>
                    </h1>
                    <p class="text-sm text-slate-500">{{ todayLabel }}</p>
                </div>
                <div class="flex flex-wrap gap-2">
                    <Link v-if="hasPermission(PERMISSIONS.POS.INDEX)" :href="route('pos.index')">
                        <Button icon="pi pi-desktop" :label="t('dashboard_page.open_pos')" />
                    </Link>
                    <Link v-if="hasPermission(PERMISSIONS.REPORT?.INDEX)" :href="route('reports.sales')">
                        <Button icon="pi pi-chart-bar" :label="t('menu.sales_report')" severity="secondary" outlined />
                    </Link>
                </div>
            </div>

            <!-- KPIs -->
            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-3">
                <StatCard v-for="k in kpis" :key="k.label" v-bind="k" />
            </div>

            <!-- Chart + quick actions -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                <div class="lg:col-span-2 bg-white rounded-xl border border-slate-200 p-4 min-w-0">
                    <div class="flex flex-wrap items-baseline justify-between gap-2 mb-3">
                        <h2 class="text-base font-semibold text-slate-800">{{ t("dashboard_page.last_7_days") }}</h2>
                        <span class="text-sm text-slate-500">{{ t("dashboard_page.week_total") }}: <b class="text-slate-800 tabular-nums">{{ cash(weekTotal) }}</b></span>
                    </div>
                    <Chart v-if="hasChartData" type="bar" :data="chartData" :options="chartOptions" class="h-[280px]" />
                    <div v-else class="h-[280px] flex flex-col items-center justify-center text-slate-400 text-sm gap-2">
                        <i class="pi pi-chart-bar text-3xl" />
                        {{ t("dashboard_page.no_sales_week") }}
                    </div>
                </div>

                <div class="bg-white rounded-xl border border-slate-200 p-4">
                    <h2 class="text-base font-semibold text-slate-800 mb-3">{{ t("dashboard_page.quick_actions") }}</h2>
                    <div class="grid grid-cols-2 lg:grid-cols-1 gap-2">
                        <Link v-for="a in quickActions" :key="a.label" :href="a.href"
                            class="p-3 rounded-lg border border-slate-100 hover:bg-slate-50 transition flex items-center gap-3 min-w-0">
                            <span class="w-9 h-9 rounded-lg flex items-center justify-center shrink-0" :class="a.tone">
                                <i :class="a.icon" />
                            </span>
                            <span class="font-medium text-sm text-slate-700 truncate">{{ a.label }}</span>
                        </Link>
                    </div>
                </div>
            </div>

            <!-- Tables -->
            <div class="grid grid-cols-1 xl:grid-cols-3 gap-4">
                <!-- Recent orders -->
                <div class="xl:col-span-2 bg-white rounded-xl border border-slate-200 p-4 min-w-0">
                    <div class="flex items-center justify-between gap-2 mb-3">
                        <h2 class="text-base font-semibold text-slate-800">{{ t("dashboard.recent_orders") }}</h2>
                        <Link :href="route('pos.orders.index')" class="text-sm text-emerald-700 hover:underline">{{ t("dashboard_page.view_all") }}</Link>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="text-xs text-slate-500 bg-slate-50">
                                <tr>
                                    <th class="px-3 py-2 text-start">{{ t("shared.terms.invoice") }}</th>
                                    <th class="px-3 py-2 text-start hidden sm:table-cell">{{ t("shared.terms.cashier") }}</th>
                                    <th class="px-3 py-2 text-end">{{ t("shared.terms.total") }}</th>
                                    <th class="px-3 py-2 text-center">{{ t("common.status") }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="order in recent_orders" :key="order.id" class="border-t border-slate-100 hover:bg-slate-50">
                                    <td class="px-3 py-2">
                                        <a :href="route('pos.orders.invoice', order.id)" target="_blank"
                                            class="font-medium text-emerald-700 hover:underline ltr-nums">{{ order.invoice_no || `DRAFT-${order.id}` }}</a>
                                    </td>
                                    <td class="px-3 py-2 text-slate-600 hidden sm:table-cell">{{ order.user?.name || "-" }}</td>
                                    <td class="px-3 py-2 text-end tabular-nums font-medium text-slate-800">{{ money(order.total_amount) }}</td>
                                    <td class="px-3 py-2 text-center">
                                        <div class="flex flex-wrap justify-center gap-1">
                                            <Tag :value="statusLabel(order.status)" :severity="statusSeverity(order.status)" class="!text-[11px]" />
                                            <Tag v-if="order.status === 'completed' && order.payment_status !== 'paid'"
                                                :value="statusLabel(order.payment_status)" :severity="statusSeverity(order.payment_status)" class="!text-[11px]" />
                                        </div>
                                    </td>
                                </tr>
                                <tr v-if="!recent_orders.length">
                                    <td colspan="4" class="px-3 py-6 text-center text-slate-400">{{ t("dashboard_page.no_orders_yet") }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Low stock -->
                <div class="bg-white rounded-xl border border-slate-200 p-4 min-w-0">
                    <div class="flex items-center justify-between gap-2 mb-1">
                        <h2 class="text-base font-semibold text-slate-800 flex items-center gap-2">
                            <i class="pi pi-exclamation-triangle text-amber-500" /> {{ t("dashboard_page.low_stock") }}
                        </h2>
                        <Link v-if="hasPermission(PERMISSIONS.PRODUCT.INDEX)" :href="route('products.index')" class="text-sm text-emerald-700 hover:underline">
                            {{ t("dashboard_page.view_all") }}
                        </Link>
                    </div>
                    <p class="text-xs text-slate-500 mb-2">{{ t("dashboard_page.low_stock_hint") }}</p>
                    <ul class="divide-y divide-slate-100">
                        <li v-for="stock in low_stock" :key="stock.id" class="py-2 flex items-center justify-between gap-3">
                            <div class="min-w-0">
                                <Link v-if="stock.product" :href="route('products.show', stock.product_id)"
                                    class="text-sm font-medium text-slate-800 hover:text-emerald-700 truncate block">{{ stock.product.name }}</Link>
                                <span v-else class="text-sm text-slate-500">{{ t("dashboard_page.unknown_product") }}</span>
                                <span class="text-xs text-slate-400">{{ stock.branch?.name || "-" }}<span v-if="stock.product?.sku"> · <span class="ltr-nums">{{ stock.product.sku }}</span></span></span>
                            </div>
                            <Tag :value="Number(stock.quantity).toFixed(0)" :severity="stockSeverity(stock.quantity)" class="tabular-nums shrink-0" />
                        </li>
                        <li v-if="!low_stock.length" class="py-6 text-center text-sm text-emerald-600">
                            <i class="pi pi-check-circle me-1" />{{ t("dashboard_page.stock_ok") }}
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Top products -->
            <div class="bg-white rounded-xl border border-slate-200 p-4">
                <h2 class="text-base font-semibold text-slate-800 mb-3">{{ t("dashboard.top_products") }}</h2>
                <div v-if="top_products.length" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-2">
                    <div v-for="(product, i) in top_products" :key="product.product_id"
                        class="flex items-center gap-3 p-3 rounded-lg bg-slate-50 border border-slate-100 min-w-0">
                        <span class="w-7 h-7 rounded-full bg-white border border-slate-200 text-xs font-bold text-slate-600 flex items-center justify-center shrink-0">{{ i + 1 }}</span>
                        <div class="min-w-0">
                            <div class="text-sm font-medium text-slate-800 truncate" :title="product.name">{{ product.name }}</div>
                            <div class="text-xs text-slate-500">{{ t("dashboard_page.units_sold", { qty: product.total_qty }) }}</div>
                        </div>
                    </div>
                </div>
                <div v-else class="py-6 text-center text-sm text-slate-400">{{ t("dashboard_page.no_sales_yet") }}</div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
