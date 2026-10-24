<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, router } from "@inertiajs/vue3";
import { computed, ref } from "vue";
import { useI18n } from "vue-i18n";
import Button from "primevue/button";
import Chart from "primevue/chart";
import DatePicker from "primevue/datepicker";
import Tag from "primevue/tag";
import StatCard from "@/Components/Sales/StatCard.vue";
import ExportButtons from "@/Components/Sales/ExportButtons.vue";
import { useFlashToast } from "@/Composables/useFlashToast";
import { useDarkMode, withRtl } from "@/Composables/useDarkMode";
import useAuth from "@/Composables/useAuth";
import { PERMISSIONS } from "@/constants/permissions";
import { baseOptions, barDataset, STATUS } from "@/Helpers/chartTheme";
import { dateTime, money, ymd } from "@/Helpers/format";

useFlashToast();
const { t, te } = useI18n();
const { hasPermission } = useAuth();
const { isRtl, themeKey } = useDarkMode();

const props = defineProps({
    filters: { type: Object, default: () => ({}) },
    totals: { type: Object, default: () => ({}) },
    chart: { type: Object, default: () => ({ labels: [], sent: [], failed: [] }) },
    byType: { type: Array, default: () => [] },
    byGateway: { type: Array, default: () => [] },
    topErrors: { type: Array, default: () => [] },
    gateways: { type: Array, default: () => [] },
    campaigns: { type: Array, default: () => [] },
    enabled: { type: Boolean, default: false },
});

const dateFrom = ref(props.filters.date_from ? new Date(props.filters.date_from + "T00:00:00") : null);
const dateTo = ref(props.filters.date_to ? new Date(props.filters.date_to + "T00:00:00") : null);

const exportParams = computed(() => ({ date_from: props.filters.date_from, date_to: props.filters.date_to }));

function apply() {
    router.get(route("sms.dashboard"), { date_from: ymd(dateFrom.value), date_to: ymd(dateTo.value) }, { preserveState: true, preserveScroll: true });
}

const chartData = computed(() => {
    themeKey.value; // rebuild on theme / direction change
    const sent = barDataset(t("shared.status.sent"), props.chart.sent || [], 0);
    sent.backgroundColor = STATUS.good;
    const failed = barDataset(t("shared.status.failed"), props.chart.failed || [], 1);
    failed.backgroundColor = STATUS.critical;
    return { labels: props.chart.labels || [], datasets: [sent, failed] };
});
const chartOptions = computed(() => {
    themeKey.value;
    return withRtl(baseOptions({ money: false, stacked: true }), isRtl.value);
});

const checking = ref(null);
function checkBalance(g) {
    checking.value = g.id;
    router.post(route("sms.gateways.balance", g.id), {}, { preserveScroll: true, onFinish: () => (checking.value = null) });
}

function rate(row) {
    return row.total ? ((row.sent / row.total) * 100).toFixed(1) + "%" : "-";
}

function eventLabel(key) {
    return te(`sms.events.${key}`) ? t(`sms.events.${key}`) : key;
}
</script>

<template>
    <Head :title="t('sms.dashboard.title')" />
    <AuthenticatedLayout>
        <div class="p-4 md:p-6 space-y-4">
            <div class="flex flex-wrap justify-between items-start gap-3">
                <div class="min-w-0">
                    <h1 class="text-xl font-semibold text-slate-800 flex items-center gap-2"><i class="pi pi-chart-pie text-primary" /> {{ t('sms.dashboard.title') }}</h1>
                    <p class="text-sm text-slate-500">{{ t('sms.dashboard.subtitle') }}</p>
                </div>
                <div class="flex flex-wrap gap-2">
                    <Link v-if="hasPermission(PERMISSIONS.SMS.SEND)" :href="route('sms.compose')"><Button :label="t('sms.send_sms')" icon="pi pi-send" size="small" /></Link>
                    <Link :href="route('sms.logs')"><Button :label="t('sms.history')" icon="pi pi-history" size="small" severity="secondary" outlined /></Link>
                    <Link v-if="hasPermission(PERMISSIONS.SMS.SETTINGS)" :href="route('sms.settings')"><Button :label="t('sms.settings_title')" icon="pi pi-cog" size="small" severity="secondary" outlined /></Link>
                </div>
            </div>

            <div v-if="!enabled" class="p-3 rounded-xl border bg-amber-50 border-amber-200 text-amber-900 text-sm flex flex-wrap items-center gap-2">
                <i class="pi pi-exclamation-triangle" />
                {{ t('sms.disabled_banner') }}
                <Link v-if="hasPermission(PERMISSIONS.SMS.SETTINGS)" :href="route('sms.settings')" class="font-semibold underline">{{ t('sms.enable_in_settings') }}</Link>
            </div>

            <div class="card p-4 flex flex-wrap items-end gap-3">
                <div class="w-full sm:w-auto">
                    <label class="text-xs text-slate-500 block mb-1">{{ t('shared.terms.from') }}</label>
                    <DatePicker v-model="dateFrom" dateFormat="dd-mm-yy" showIcon class="w-full sm:w-44" />
                </div>
                <div class="w-full sm:w-auto">
                    <label class="text-xs text-slate-500 block mb-1">{{ t('shared.terms.to') }}</label>
                    <DatePicker v-model="dateTo" dateFormat="dd-mm-yy" showIcon class="w-full sm:w-44" />
                </div>
                <Button :label="t('shared.terms.apply')" icon="pi pi-filter" outlined @click="apply" />
                <div class="sm:ms-auto"><ExportButtons routeName="sms.dashboard" :params="exportParams" /></div>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3">
                <StatCard :label="t('shared.terms.total')" :value="totals.total ?? 0" icon="pi pi-envelope" tone="slate" />
                <StatCard :label="t('shared.status.sent')" :value="totals.sent ?? 0" icon="pi pi-check" tone="emerald" />
                <StatCard :label="t('shared.status.failed')" :value="totals.failed ?? 0" icon="pi pi-times" tone="rose" />
                <StatCard :label="t('shared.status.queued')" :value="totals.queued ?? 0" icon="pi pi-clock" tone="amber" />
                <StatCard :label="t('sms.parts_used')" :value="totals.segments ?? 0" icon="pi pi-th-large" tone="sky" :hint="t('sms.sent_segments')" />
                <StatCard :label="t('sms.success_rate')" :value="(totals.success_rate ?? 0) + '%'" icon="pi pi-percentage" tone="violet" />
            </div>

            <div class="card p-4">
                <h2 class="font-semibold text-slate-700 mb-3">{{ t('sms.dashboard.daily_delivery') }}</h2>
                <div class="h-72"><Chart :key="themeKey" type="bar" :data="chartData" :options="chartOptions" class="h-full" /></div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                <div class="card p-4 overflow-x-auto">
                    <h2 class="font-semibold text-slate-700 mb-3">{{ t('sms.dashboard.by_type') }}</h2>
                    <table class="w-full text-sm tabular-nums">
                        <thead><tr class="text-slate-500 text-xs text-start"><th class="py-1 text-start">{{ t('sms.type') }}</th><th class="text-end">{{ t('shared.terms.total') }}</th><th class="text-end">{{ t('shared.status.sent') }}</th><th class="text-end">{{ t('shared.status.failed') }}</th><th class="text-end">{{ t('sms.rate') }}</th></tr></thead>
                        <tbody>
                            <tr v-for="r in byType" :key="r.label" class="border-t border-slate-100">
                                <td class="py-1.5 text-slate-700">{{ eventLabel(r.label) }}</td>
                                <td class="text-end">{{ r.total }}</td><td class="text-end">{{ r.sent }}</td><td class="text-end">{{ r.failed }}</td><td class="text-end text-slate-500">{{ rate(r) }}</td>
                            </tr>
                            <tr v-if="!byType.length"><td colspan="5" class="py-4 text-center text-slate-400">{{ t('shared.terms.no_data') }}</td></tr>
                        </tbody>
                    </table>
                </div>
                <div class="card p-4 overflow-x-auto">
                    <h2 class="font-semibold text-slate-700 mb-3">{{ t('sms.dashboard.by_gateway') }}</h2>
                    <table class="w-full text-sm tabular-nums">
                        <thead><tr class="text-slate-500 text-xs text-start"><th class="py-1 text-start">{{ t('sms.gateway') }}</th><th class="text-end">{{ t('shared.terms.total') }}</th><th class="text-end">{{ t('shared.status.sent') }}</th><th class="text-end">{{ t('shared.status.failed') }}</th><th class="text-end">{{ t('sms.parts') }}</th></tr></thead>
                        <tbody>
                            <tr v-for="r in byGateway" :key="r.label" class="border-t border-slate-100">
                                <td class="py-1.5 text-slate-700">{{ r.label === 'None' ? t('sms.no_gateway_short') : r.label }}</td>
                                <td class="text-end">{{ r.total }}</td><td class="text-end">{{ r.sent }}</td><td class="text-end">{{ r.failed }}</td><td class="text-end">{{ r.segments }}</td>
                            </tr>
                            <tr v-if="!byGateway.length"><td colspan="5" class="py-4 text-center text-slate-400">{{ t('shared.terms.no_data') }}</td></tr>
                        </tbody>
                    </table>
                </div>
                <div class="card p-4">
                    <h2 class="font-semibold text-slate-700 mb-3">{{ t('sms.dashboard.top_errors') }}</h2>
                    <div v-for="e in topErrors" :key="e.error" class="flex justify-between gap-2 py-1.5 border-t border-slate-100 text-sm">
                        <span class="text-slate-700 break-all" dir="auto">{{ e.error }}</span>
                        <span class="font-semibold text-slate-800 shrink-0">{{ e.total }}</span>
                    </div>
                    <div v-if="!topErrors.length" class="py-4 text-center text-slate-400 text-sm">{{ t('sms.dashboard.no_failures') }}</div>
                </div>
            </div>

            <div class="card p-4">
                <h2 class="font-semibold text-slate-700 mb-3">{{ t('sms.dashboard.gateways_balance') }}</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                    <div v-for="g in gateways" :key="g.id" class="border border-slate-200 rounded-xl p-3 flex flex-col gap-2">
                        <div class="flex items-center justify-between gap-2">
                            <span class="font-semibold text-slate-800 truncate">{{ g.name }}</span>
                            <div class="flex gap-1 shrink-0">
                                <Tag v-if="g.is_default" :value="t('sms.default')" severity="info" />
                                <Tag :value="g.is_active ? t('shared.status.active') : t('shared.status.inactive')" :severity="g.is_active ? 'success' : 'secondary'" />
                            </div>
                        </div>
                        <div class="text-xs text-slate-500">{{ g.driver }}</div>
                        <div class="text-lg font-bold text-slate-800 ltr-nums text-start">{{ g.balance !== null && g.balance !== undefined ? money(g.balance) : "—" }}</div>
                        <div class="text-xs text-slate-500">{{ g.balance_checked_at ? t('sms.checked_at', { time: dateTime(g.balance_checked_at) }) : t('sms.never_checked') }}</div>
                        <Button v-if="hasPermission(PERMISSIONS.SMS.SETTINGS)" :label="t('sms.check_balance')" icon="pi pi-refresh" size="small" outlined :loading="checking === g.id" @click="checkBalance(g)" />
                    </div>
                    <div v-if="!gateways.length" class="text-sm text-slate-500">
                        {{ t('sms.no_gateway') }}
                        <Link v-if="hasPermission(PERMISSIONS.SMS.SETTINGS)" :href="route('sms.settings')" class="underline font-semibold">{{ t('sms.add_one') }}</Link>
                    </div>
                </div>
            </div>

            <div class="card p-4 overflow-x-auto">
                <h2 class="font-semibold text-slate-700 mb-3">{{ t('sms.dashboard.recent_campaigns') }}</h2>
                <table class="w-full text-sm tabular-nums min-w-[560px]">
                    <thead><tr class="text-slate-500 text-xs"><th class="py-1 text-start">{{ t('common.name') }}</th><th class="text-start">{{ t('shared.terms.date') }}</th><th class="text-start">{{ t('sms.by') }}</th><th class="text-end">{{ t('sms.recipients') }}</th><th class="text-end">{{ t('shared.status.sent') }}</th><th class="text-end">{{ t('shared.status.failed') }}</th><th class="text-start">{{ t('common.status') }}</th></tr></thead>
                    <tbody>
                        <tr v-for="c in campaigns" :key="c.id" class="border-t border-slate-100">
                            <td class="py-1.5"><Link :href="route('sms.logs', { campaign_id: c.id })" class="text-primary font-medium hover:underline">{{ c.name }}</Link></td>
                            <td class="text-slate-500">{{ dateTime(c.created_at) }}</td>
                            <td class="text-slate-600">{{ c.creator?.name || "-" }}</td>
                            <td class="text-end">{{ c.recipients_count }}</td>
                            <td class="text-end">{{ c.sent_count }}</td>
                            <td class="text-end">{{ c.failed_count }}</td>
                            <td><Tag :value="t('sms.campaign_status.' + c.status)" :severity="c.status === 'completed' ? 'success' : 'warn'" /></td>
                        </tr>
                        <tr v-if="!campaigns.length"><td colspan="7" class="py-4 text-center text-slate-400">{{ t('sms.dashboard.no_campaigns') }}</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
