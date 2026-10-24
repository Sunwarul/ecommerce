<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, router } from "@inertiajs/vue3";
import { computed, ref } from "vue";
import Button from "primevue/button";
import Column from "primevue/column";
import DataTable from "primevue/datatable";
import DatePicker from "primevue/datepicker";
import InputText from "primevue/inputtext";
import ProgressBar from "primevue/progressbar";
import Select from "primevue/select";
import SelectButton from "primevue/selectbutton";
import Tag from "primevue/tag";
import Tooltip from "primevue/tooltip";

const vTooltip = Tooltip;
import StatCard from "@/Components/Sales/StatCard.vue";
import ExportButtons from "@/Components/Sales/ExportButtons.vue";
import { useFlashToast } from "@/Composables/useFlashToast";
import { useI18n } from "vue-i18n";
import useAuth from "@/Composables/useAuth";
import { PERMISSIONS } from "@/constants/permissions";
import { cleanParams, date, money, ymd } from "@/Helpers/format";

const { t } = useI18n();
const { hasPermission } = useAuth();

useFlashToast({ error: false }); // errors are toasted by the layout

const props = defineProps({
    mode: { type: String, default: "agreements" },
    sales: { type: Object, default: null },
    schedules: { type: Object, default: null },
    stats: { type: Object, default: () => ({}) },
    filters: { type: Object, default: () => ({}) },
});

const modeOptions = computed(() => [
    { label: t("installments.index.agreements"), value: "agreements" },
    { label: t("installments.index.collection_sheet"), value: "schedules" },
]);

const tabs = computed(() => [
    { label: t("shared.status.active"), value: "active", icon: "pi pi-file" },
    { label: t("installments.index.due_today"), value: "due_today", icon: "pi pi-clock" },
    { label: t("installments.index.upcoming"), value: "upcoming", icon: "pi pi-calendar" },
    { label: t("shared.status.overdue"), value: "overdue", icon: "pi pi-exclamation-triangle" },
    { label: t("shared.status.completed"), value: "completed", icon: "pi pi-check-circle" },
    { label: t("shared.status.defaulted"), value: "defaulted", icon: "pi pi-ban" },
    { label: t("shared.status.cancelled"), value: "cancelled", icon: "pi pi-times-circle" },
    { label: t("common.all"), value: "all", icon: "pi pi-list" },
]);

const scheduleStatuses = computed(() => [
    { label: t("installments.index.all_statuses"), value: null },
    ...["pending", "partial", "paid", "overdue"].map((v) => ({ label: t(`shared.status.${v}`), value: v })),
]);

const parseDate = (d) => (d ? new Date(d + "T00:00:00") : null);
const monthStart = () => { const d = new Date(); return new Date(d.getFullYear(), d.getMonth(), 1); };
const monthEnd = () => { const d = new Date(); return new Date(d.getFullYear(), d.getMonth() + 1, 0); };

const search = ref(props.filters?.search || "");
const tab = ref(props.filters?.tab || "active");
const dateFrom = ref(parseDate(props.filters?.date_from) || (props.mode === "schedules" ? monthStart() : null));
const dateTo = ref(parseDate(props.filters?.date_to) || (props.mode === "schedules" ? monthEnd() : null));
const scheduleStatus = ref(props.filters?.schedule_status || null);

const params = computed(() => cleanParams({
    mode: props.mode,
    tab: props.mode === "agreements" ? tab.value : undefined,
    search: search.value,
    date_from: ymd(dateFrom.value),
    date_to: ymd(dateTo.value),
    schedule_status: props.mode === "schedules" ? scheduleStatus.value : undefined,
}));

function apply(extra = {}) {
    router.get(route("installments.index"), { ...params.value, ...extra }, { preserveState: true, replace: true });
}

function switchMode(m) {
    if (!m || m === props.mode) return;
    router.get(route("installments.index"), cleanParams({ mode: m, search: search.value }), { replace: true });
}

function setTab(v) {
    tab.value = v;
    apply();
}

function reset() {
    search.value = "";
    scheduleStatus.value = null;
    dateFrom.value = props.mode === "schedules" ? monthStart() : null;
    dateTo.value = props.mode === "schedules" ? monthEnd() : null;
    apply();
}

const pager = computed(() => (props.mode === "agreements" ? props.sales : props.schedules) || { data: [] });

function goTo(url) {
    if (url) router.get(url, {}, { preserveState: true });
}

const today = ymd(new Date());
const isOverdueDate = (d) => d && String(d).slice(0, 10) < today;

function statusSeverity(s) {
    return { active: "info", completed: "success", defaulted: "danger", cancelled: "secondary", paid: "success", partial: "warn", pending: "warn" }[s] || "secondary";
}

function progress(row) {
    const payable = Number(row.total_payable || 0);
    return payable > 0 ? Math.min(100, Math.round((Number(row.paid_amount || 0) / payable) * 100)) : 0;
}

const openShow = (id) => router.visit(route("installments.show", id));
</script>

<template>
    <Head :title="t('installments.title')" />
    <AuthenticatedLayout>
        <div class="p-4 md:p-6 space-y-4">
            <div class="flex flex-wrap justify-between items-start gap-3">
                <div>
                    <h1 class="text-xl font-semibold text-slate-800 flex items-center gap-2"><i class="pi pi-calendar-clock text-primary" /> {{ t('installments.title') }}</h1>
                    <p class="text-sm text-slate-500">{{ t('installments.index.subtitle') }}</p>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <SelectButton :modelValue="mode" @update:modelValue="switchMode" :options="modeOptions" optionLabel="label" optionValue="value" :allowEmpty="false" size="small" />
                    <Link v-if="hasPermission(PERMISSIONS.INSTALLMENT.PLAN_MANAGE)" :href="route('installment-plans.index')">
                        <Button :label="t('installments.plans.title')" icon="pi pi-sliders-h" severity="secondary" outlined size="small" />
                    </Link>
                </div>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-6 gap-3">
                <StatCard :label="t('shared.status.active')" :value="stats.active_count ?? 0" icon="pi pi-file" tone="sky" />
                <StatCard :label="t('installments.index.outstanding')" :value="money(stats.outstanding)" icon="pi pi-wallet" tone="violet" />
                <StatCard :label="t('shared.status.overdue')" :value="money(stats.overdue_amount)" :hint="t('installments.index.n_agreements', { n: stats.overdue_count ?? 0 })" icon="pi pi-exclamation-triangle" tone="rose" />
                <StatCard :label="t('installments.index.due_today')" :value="money(stats.due_today_amount)" icon="pi pi-clock" tone="amber" />
                <StatCard :label="t('installments.index.collected_month')" :value="money(stats.collected_this_month)" icon="pi pi-check-circle" tone="emerald" />
                <StatCard :label="t('installments.index.showing')" :value="pager.total ?? 0" :hint="mode === 'agreements' ? t('installments.index.agreements') : t('installments.index.installments')" icon="pi pi-list" />
            </div>

            <div class="card p-4 space-y-3">
                <div v-if="mode === 'agreements'" class="flex flex-nowrap md:flex-wrap gap-1 border-b border-slate-100 pb-3 overflow-x-auto -mx-1 px-1" role="tablist">
                    <Button v-for="tb in tabs" :key="tb.value" :label="tb.label" :icon="tb.icon" size="small" class="shrink-0"
                        role="tab" :aria-selected="tab === tb.value"
                        :severity="tab === tb.value ? (tb.value === 'overdue' ? 'danger' : 'primary') : 'secondary'"
                        :text="tab !== tb.value" @click="setTab(tb.value)" />
                </div>

                <div class="flex flex-wrap gap-2 items-center">
                    <InputText v-model="search" type="search" :placeholder="t('installments.index.search_placeholder')" v-tooltip.bottom="t('shared.terms.search_hint')" class="w-full md:w-72" @keyup.enter="apply()" />
                    <div class="grid grid-cols-2 gap-2 w-full md:w-auto md:flex">
                        <DatePicker v-model="dateFrom" :placeholder="mode === 'schedules' ? t('installments.index.due_from') : t('installments.index.created_from')" dateFormat="dd-mm-yy" showIcon class="w-full md:w-44" />
                        <DatePicker v-model="dateTo" :placeholder="mode === 'schedules' ? t('installments.index.due_to') : t('installments.index.created_to')" dateFormat="dd-mm-yy" showIcon class="w-full md:w-44" />
                    </div>
                    <Select v-if="mode === 'schedules'" v-model="scheduleStatus" :options="scheduleStatuses" optionLabel="label" optionValue="value" :placeholder="t('common.status')" class="w-full md:w-44" />
                    <Button :label="t('shared.terms.apply')" icon="pi pi-filter" outlined size="small" @click="apply()" />
                    <Button :label="t('shared.terms.reset')" icon="pi pi-refresh" text size="small" @click="reset" />
                    <div class="md:ms-auto">
                        <ExportButtons routeName="installments.index" :params="params" />
                    </div>
                </div>
            </div>

            <div class="card">
                <DataTable v-if="mode === 'agreements'" :value="pager.data" dataKey="id" size="small" showGridlines scrollable
                    rowHover class="cursor-pointer" @row-click="(e) => openShow(e.data.id)">
                    <template #empty><div class="text-center text-slate-400 py-6">{{ t('installments.index.no_agreements') }}</div></template>
                    <Column :header="t('installments.agreement')">
                        <template #body="{ data }">
                            <Link :href="route('installments.show', data.id)" class="font-semibold text-primary" @click.stop>{{ data.agreement_no }}</Link>
                            <div class="text-xs text-slate-500">{{ date(data.created_at) }}</div>
                        </template>
                    </Column>
                    <Column :header="t('shared.terms.customer')">
                        <template #body="{ data }">
                            <div class="font-medium">{{ data.customer?.name || '-' }}</div>
                            <div class="text-xs text-slate-500 ltr-nums text-start">{{ data.customer?.phone }}</div>
                        </template>
                    </Column>
                    <Column :header="t('shared.terms.invoice')">
                        <template #body="{ data }"><span class="text-xs">{{ data.order?.invoice_no || '-' }}</span></template>
                    </Column>
                    <Column :header="t('installments.config.plan')">
                        <template #body="{ data }">
                            <span class="text-sm whitespace-nowrap">{{ data.number_of_installments }} × {{ money(data.installment_amount) }}</span>
                            <div class="text-xs text-slate-500">{{ t('shared.frequency.' + data.frequency) }}</div>
                        </template>
                    </Column>
                    <Column :header="t('installments.index.paid_payable')" style="min-width: 160px">
                        <template #body="{ data }">
                            <div class="text-xs mb-1 tabular-nums">{{ money(data.paid_amount) }} / {{ money(data.total_payable) }}</div>
                            <ProgressBar :value="progress(data)" :showValue="false" style="height: 6px" />
                        </template>
                    </Column>
                    <Column :header="t('shared.terms.balance')">
                        <template #body="{ data }"><span class="font-semibold tabular-nums" :class="data.balance > 0 ? 'text-rose-600' : 'text-emerald-600'">{{ money(data.balance) }}</span></template>
                    </Column>
                    <Column :header="t('installments.next_due')">
                        <template #body="{ data }">
                            <template v-if="data.next_schedule">
                                <div :class="isOverdueDate(data.next_schedule.due_date) ? 'text-rose-600 font-semibold' : ''">
                                    <i v-if="isOverdueDate(data.next_schedule.due_date)" class="pi pi-exclamation-circle text-xs me-1" />{{ date(data.next_schedule.due_date) }}
                                </div>
                                <div class="text-xs text-slate-500">#{{ data.next_schedule.installment_no }} · {{ money(data.next_schedule.remaining) }}</div>
                            </template>
                            <span v-else class="text-slate-400">-</span>
                        </template>
                    </Column>
                    <Column :header="t('common.status')">
                        <template #body="{ data }"><Tag :value="t('shared.status.' + data.status)" :severity="statusSeverity(data.status)" /></template>
                    </Column>
                    <Column header="">
                        <template #body="{ data }">
                            <Button v-if="data.status === 'active' && hasPermission(PERMISSIONS.INSTALLMENT.COLLECT)" :label="t('installments.collect')" icon="pi pi-wallet" size="small" severity="success" outlined @click.stop="openShow(data.id)" />
                            <Button v-else icon="pi pi-eye" text size="small" :aria-label="t('common.view')" @click.stop="openShow(data.id)" />
                        </template>
                    </Column>
                </DataTable>

                <DataTable v-else :value="pager.data" dataKey="id" size="small" showGridlines scrollable>
                    <template #empty><div class="text-center text-slate-400 py-6">{{ t('installments.index.no_schedules') }}</div></template>
                    <Column :header="t('installments.due_date')">
                        <template #body="{ data }"><span :class="data.is_overdue ? 'text-rose-600 font-semibold' : ''">{{ date(data.due_date) }}</span></template>
                    </Column>
                    <Column :header="t('installments.agreement')">
                        <template #body="{ data }">
                            <Link v-if="data.sale" :href="route('installments.show', data.sale.id)" class="font-semibold text-primary">{{ data.sale.agreement_no }}</Link>
                        </template>
                    </Column>
                    <Column :header="t('shared.terms.customer')">
                        <template #body="{ data }">
                            <div class="font-medium">{{ data.sale?.customer?.name || '-' }}</div>
                            <div class="text-xs text-slate-500 ltr-nums text-start">{{ data.sale?.customer?.phone }}</div>
                        </template>
                    </Column>
                    <Column field="installment_no" header="#" />
                    <Column :header="t('shared.terms.amount')"><template #body="{ data }"><span class="tabular-nums">{{ money(data.amount) }}</span></template></Column>
                    <Column :header="t('installments.config.late_fee')"><template #body="{ data }"><span class="tabular-nums">{{ money(data.late_fee) }}</span></template></Column>
                    <Column :header="t('shared.terms.paid')"><template #body="{ data }"><span class="tabular-nums">{{ money(Number(data.paid_amount) + Number(data.late_fee_paid)) }}</span></template></Column>
                    <Column :header="t('installments.remaining')"><template #body="{ data }"><span class="font-semibold tabular-nums">{{ money(data.remaining) }}</span></template></Column>
                    <Column :header="t('common.status')">
                        <template #body="{ data }">
                            <Tag v-if="data.is_overdue" :value="t('shared.status.overdue')" severity="danger" icon="pi pi-exclamation-triangle" />
                            <Tag v-else :value="t('shared.status.' + data.status)" :severity="statusSeverity(data.status)" />
                        </template>
                    </Column>
                </DataTable>

                <div class="flex justify-between items-center mt-3 text-sm text-slate-500">
                    <span>{{ t('shared.terms.showing', { from: pager.from || 0, to: pager.to || 0, total: pager.total || 0 }) }}</span>
                    <div class="flex gap-1">
                        <Button icon="pi pi-angle-left" text :aria-label="t('shared.terms.previous')" :disabled="!pager.prev_page_url" @click="goTo(pager.prev_page_url)" />
                        <Button icon="pi pi-angle-right" text :aria-label="t('shared.terms.next')" :disabled="!pager.next_page_url" @click="goTo(pager.next_page_url)" />
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
