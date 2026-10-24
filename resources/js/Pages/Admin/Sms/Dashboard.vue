<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, router } from "@inertiajs/vue3";
import { computed, ref } from "vue";
import Button from "primevue/button";
import Chart from "primevue/chart";
import DatePicker from "primevue/datepicker";
import Tag from "primevue/tag";
import StatCard from "@/Components/Sales/StatCard.vue";
import ExportButtons from "@/Components/Sales/ExportButtons.vue";
import { useFlashToast } from "@/Composables/useFlashToast";
import { baseOptions, barDataset, STATUS } from "@/Helpers/chartTheme";
import { dateTime, money, titleCase, ymd } from "@/Helpers/format";

useFlashToast();

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
    const sent = barDataset("Sent", props.chart.sent || [], 0);
    sent.backgroundColor = STATUS.good;
    const failed = barDataset("Failed", props.chart.failed || [], 1);
    failed.backgroundColor = STATUS.critical;
    return { labels: props.chart.labels || [], datasets: [sent, failed] };
});
const chartOptions = computed(() => baseOptions({ money: false, stacked: true }));

const checking = ref(null);
function checkBalance(g) {
    checking.value = g.id;
    router.post(route("sms.gateways.balance", g.id), {}, { preserveScroll: true, onFinish: () => (checking.value = null) });
}

function rate(row) {
    return row.total ? ((row.sent / row.total) * 100).toFixed(1) + "%" : "-";
}
</script>

<template>
    <Head title="SMS Dashboard" />
    <AuthenticatedLayout>
        <div class="p-4 md:p-6 space-y-4">
            <div class="flex flex-wrap justify-between items-start gap-3">
                <div>
                    <h2 class="text-xl font-semibold text-slate-800 flex items-center gap-2"><i class="pi pi-envelope text-primary" /> SMS Dashboard</h2>
                    <p class="text-sm text-slate-500">Delivery reports, balances and campaigns</p>
                </div>
                <div class="flex flex-wrap gap-2">
                    <Link :href="route('sms.compose')"><Button label="Send SMS" icon="pi pi-send" size="small" /></Link>
                    <Link :href="route('sms.logs')"><Button label="History" icon="pi pi-history" size="small" severity="secondary" outlined /></Link>
                    <Link :href="route('sms.settings')"><Button label="Settings" icon="pi pi-cog" size="small" severity="secondary" outlined /></Link>
                </div>
            </div>

            <div v-if="!enabled" class="p-3 rounded-xl border bg-amber-50 border-amber-200 text-amber-900 text-sm flex flex-wrap items-center gap-2">
                <i class="pi pi-exclamation-triangle" />
                SMS sending is turned off. No messages will be sent.
                <Link :href="route('sms.settings')" class="font-semibold underline">Enable in SMS Settings</Link>
            </div>

            <div class="card p-4 flex flex-wrap items-end gap-3">
                <div>
                    <label class="text-xs text-slate-500 block mb-1">From</label>
                    <DatePicker v-model="dateFrom" dateFormat="dd-mm-yy" showIcon class="w-44" />
                </div>
                <div>
                    <label class="text-xs text-slate-500 block mb-1">To</label>
                    <DatePicker v-model="dateTo" dateFormat="dd-mm-yy" showIcon class="w-44" />
                </div>
                <Button label="Apply" icon="pi pi-filter" outlined @click="apply" />
                <div class="ml-auto"><ExportButtons routeName="sms.dashboard" :params="exportParams" /></div>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3">
                <StatCard label="Total" :value="totals.total ?? 0" icon="pi pi-envelope" tone="slate" />
                <StatCard label="Sent" :value="totals.sent ?? 0" icon="pi pi-check" tone="emerald" />
                <StatCard label="Failed" :value="totals.failed ?? 0" icon="pi pi-times" tone="rose" />
                <StatCard label="Queued" :value="totals.queued ?? 0" icon="pi pi-clock" tone="amber" />
                <StatCard label="Parts used" :value="totals.segments ?? 0" icon="pi pi-th-large" tone="sky" hint="sent segments" />
                <StatCard label="Success rate" :value="(totals.success_rate ?? 0) + '%'" icon="pi pi-percentage" tone="violet" />
            </div>

            <div class="card p-4">
                <h3 class="font-semibold text-slate-700 mb-3">Daily delivery</h3>
                <div class="h-72"><Chart type="bar" :data="chartData" :options="chartOptions" class="h-full" /></div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                <div class="card p-4 overflow-x-auto">
                    <h3 class="font-semibold text-slate-700 mb-3">By type / event</h3>
                    <table class="w-full text-sm tabular-nums">
                        <thead><tr class="text-slate-500 text-xs text-left"><th class="py-1">Type</th><th class="text-right">Total</th><th class="text-right">Sent</th><th class="text-right">Failed</th><th class="text-right">Rate</th></tr></thead>
                        <tbody>
                            <tr v-for="r in byType" :key="r.label" class="border-t border-slate-100">
                                <td class="py-1.5 text-slate-700">{{ titleCase(r.label) }}</td>
                                <td class="text-right">{{ r.total }}</td><td class="text-right">{{ r.sent }}</td><td class="text-right">{{ r.failed }}</td><td class="text-right text-slate-500">{{ rate(r) }}</td>
                            </tr>
                            <tr v-if="!byType.length"><td colspan="5" class="py-4 text-center text-slate-400">No data</td></tr>
                        </tbody>
                    </table>
                </div>
                <div class="card p-4 overflow-x-auto">
                    <h3 class="font-semibold text-slate-700 mb-3">By gateway</h3>
                    <table class="w-full text-sm tabular-nums">
                        <thead><tr class="text-slate-500 text-xs text-left"><th class="py-1">Gateway</th><th class="text-right">Total</th><th class="text-right">Sent</th><th class="text-right">Failed</th><th class="text-right">Parts</th></tr></thead>
                        <tbody>
                            <tr v-for="r in byGateway" :key="r.label" class="border-t border-slate-100">
                                <td class="py-1.5 text-slate-700">{{ r.label }}</td>
                                <td class="text-right">{{ r.total }}</td><td class="text-right">{{ r.sent }}</td><td class="text-right">{{ r.failed }}</td><td class="text-right">{{ r.segments }}</td>
                            </tr>
                            <tr v-if="!byGateway.length"><td colspan="5" class="py-4 text-center text-slate-400">No data</td></tr>
                        </tbody>
                    </table>
                </div>
                <div class="card p-4">
                    <h3 class="font-semibold text-slate-700 mb-3">Top failure reasons</h3>
                    <div v-for="e in topErrors" :key="e.error" class="flex justify-between gap-2 py-1.5 border-t border-slate-100 text-sm">
                        <span class="text-slate-700 break-all">{{ e.error }}</span>
                        <span class="font-semibold text-slate-800 shrink-0">{{ e.total }}</span>
                    </div>
                    <div v-if="!topErrors.length" class="py-4 text-center text-slate-400 text-sm">No failures</div>
                </div>
            </div>

            <div class="card p-4">
                <h3 class="font-semibold text-slate-700 mb-3">Gateways &amp; balance</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                    <div v-for="g in gateways" :key="g.id" class="border border-slate-200 rounded-xl p-3 flex flex-col gap-2">
                        <div class="flex items-center justify-between gap-2">
                            <span class="font-semibold text-slate-800 truncate">{{ g.name }}</span>
                            <div class="flex gap-1">
                                <Tag v-if="g.is_default" value="Default" severity="info" />
                                <Tag :value="g.is_active ? 'Active' : 'Inactive'" :severity="g.is_active ? 'success' : 'secondary'" />
                            </div>
                        </div>
                        <div class="text-xs text-slate-500">{{ g.driver }}</div>
                        <div class="text-lg font-bold text-slate-800">{{ g.balance !== null && g.balance !== undefined ? money(g.balance) : "—" }}</div>
                        <div class="text-xs text-slate-500">{{ g.balance_checked_at ? "Checked " + dateTime(g.balance_checked_at) : "Never checked" }}</div>
                        <Button label="Check balance" icon="pi pi-refresh" size="small" outlined :loading="checking === g.id" @click="checkBalance(g)" />
                    </div>
                    <div v-if="!gateways.length" class="text-sm text-slate-500">
                        No gateway configured. <Link :href="route('sms.settings')" class="underline font-semibold">Add one</Link>.
                    </div>
                </div>
            </div>

            <div class="card p-4 overflow-x-auto">
                <h3 class="font-semibold text-slate-700 mb-3">Recent campaigns</h3>
                <table class="w-full text-sm tabular-nums min-w-[560px]">
                    <thead><tr class="text-slate-500 text-xs text-left"><th class="py-1">Name</th><th>Date</th><th>By</th><th class="text-right">Recipients</th><th class="text-right">Sent</th><th class="text-right">Failed</th><th>Status</th></tr></thead>
                    <tbody>
                        <tr v-for="c in campaigns" :key="c.id" class="border-t border-slate-100">
                            <td class="py-1.5"><Link :href="route('sms.logs', { campaign_id: c.id })" class="text-primary font-medium hover:underline">{{ c.name }}</Link></td>
                            <td class="text-slate-500">{{ dateTime(c.created_at) }}</td>
                            <td class="text-slate-600">{{ c.creator?.name || "-" }}</td>
                            <td class="text-right">{{ c.recipients_count }}</td>
                            <td class="text-right">{{ c.sent_count }}</td>
                            <td class="text-right">{{ c.failed_count }}</td>
                            <td><Tag :value="c.status" :severity="c.status === 'completed' ? 'success' : 'warn'" /></td>
                        </tr>
                        <tr v-if="!campaigns.length"><td colspan="7" class="py-4 text-center text-slate-400">No campaigns yet</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
