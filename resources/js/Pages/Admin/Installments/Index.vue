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
import StatCard from "@/Components/Sales/StatCard.vue";
import ExportButtons from "@/Components/Sales/ExportButtons.vue";
import { useFlashToast } from "@/Composables/useFlashToast";
import { cleanParams, date, money, titleCase, ymd } from "@/Helpers/format";

useFlashToast({ error: false }); // errors are toasted by the layout

const props = defineProps({
    mode: { type: String, default: "agreements" },
    sales: { type: Object, default: null },
    schedules: { type: Object, default: null },
    stats: { type: Object, default: () => ({}) },
    filters: { type: Object, default: () => ({}) },
});

const modeOptions = [
    { label: "Agreements", value: "agreements" },
    { label: "Collection sheet", value: "schedules" },
];

const tabs = [
    { label: "Active", value: "active" },
    { label: "Due today", value: "due_today" },
    { label: "Upcoming 7 days", value: "upcoming" },
    { label: "Overdue", value: "overdue" },
    { label: "Completed", value: "completed" },
    { label: "Defaulted", value: "defaulted" },
    { label: "Cancelled", value: "cancelled" },
    { label: "All", value: "all" },
];

const scheduleStatuses = [
    { label: "All statuses", value: null },
    { label: "Pending", value: "pending" },
    { label: "Partial", value: "partial" },
    { label: "Paid", value: "paid" },
    { label: "Overdue", value: "overdue" },
];

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
    return { active: "info", completed: "success", defaulted: "danger", cancelled: "secondary", paid: "success", partial: "warn", pending: "info" }[s] || "secondary";
}

function progress(row) {
    const payable = Number(row.total_payable || 0);
    return payable > 0 ? Math.min(100, Math.round((Number(row.paid_amount || 0) / payable) * 100)) : 0;
}

const openShow = (id) => router.visit(route("installments.show", id));
</script>

<template>
    <Head title="Installments" />
    <AuthenticatedLayout>
        <div class="p-4 md:p-6 space-y-4">
            <div class="flex flex-wrap justify-between items-start gap-3">
                <div>
                    <h2 class="text-xl font-semibold flex items-center gap-2"><i class="pi pi-calendar-clock text-primary" /> Installments</h2>
                    <p class="text-sm text-slate-500">Hire-purchase agreements, dues and collections</p>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <SelectButton :modelValue="mode" @update:modelValue="switchMode" :options="modeOptions" optionLabel="label" optionValue="value" :allowEmpty="false" size="small" />
                    <Link :href="route('installment-plans.index')">
                        <Button label="Plans" icon="pi pi-sliders-h" severity="secondary" outlined size="small" />
                    </Link>
                </div>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-6 gap-3">
                <StatCard label="Active" :value="stats.active_count ?? 0" icon="pi pi-file" tone="sky" />
                <StatCard label="Outstanding" :value="money(stats.outstanding)" icon="pi pi-wallet" tone="violet" />
                <StatCard label="Overdue" :value="money(stats.overdue_amount)" :hint="`${stats.overdue_count ?? 0} agreements`" icon="pi pi-exclamation-triangle" tone="rose" />
                <StatCard label="Due today" :value="money(stats.due_today_amount)" icon="pi pi-clock" tone="amber" />
                <StatCard label="Collected this month" :value="money(stats.collected_this_month)" icon="pi pi-check-circle" tone="emerald" />
                <StatCard label="Showing" :value="pager.total ?? 0" :hint="mode === 'agreements' ? 'agreements' : 'installments'" icon="pi pi-list" />
            </div>

            <div class="card p-4 space-y-3">
                <div v-if="mode === 'agreements'" class="flex flex-wrap gap-1 border-b border-slate-100 pb-3">
                    <Button v-for="t in tabs" :key="t.value" :label="t.label" size="small"
                        :severity="tab === t.value ? (t.value === 'overdue' ? 'danger' : 'primary') : 'secondary'"
                        :text="tab !== t.value" @click="setTab(t.value)" />
                </div>

                <div class="flex flex-wrap gap-2 items-center">
                    <InputText v-model="search" placeholder="Agreement / invoice / customer / phone" class="w-full md:w-72" @keyup.enter="apply()" />
                    <DatePicker v-model="dateFrom" :placeholder="mode === 'schedules' ? 'Due from' : 'Created from'" dateFormat="dd-mm-yy" showIcon class="w-[48%] md:w-44" />
                    <DatePicker v-model="dateTo" :placeholder="mode === 'schedules' ? 'Due to' : 'Created to'" dateFormat="dd-mm-yy" showIcon class="w-[48%] md:w-44" />
                    <Select v-if="mode === 'schedules'" v-model="scheduleStatus" :options="scheduleStatuses" optionLabel="label" optionValue="value" placeholder="Status" class="w-full md:w-44" />
                    <Button label="Apply" icon="pi pi-filter" outlined size="small" @click="apply()" />
                    <Button label="Reset" icon="pi pi-refresh" text size="small" @click="reset" />
                    <div class="md:ml-auto">
                        <ExportButtons routeName="installments.index" :params="params" />
                    </div>
                </div>
            </div>

            <div class="card">
                <DataTable v-if="mode === 'agreements'" :value="pager.data" dataKey="id" size="small" showGridlines scrollable
                    rowHover class="cursor-pointer" @row-click="(e) => openShow(e.data.id)">
                    <template #empty><div class="text-center text-slate-400 py-6">No agreements found</div></template>
                    <Column header="Agreement">
                        <template #body="{ data }">
                            <Link :href="route('installments.show', data.id)" class="font-semibold text-primary" @click.stop>{{ data.agreement_no }}</Link>
                            <div class="text-xs text-slate-500">{{ date(data.created_at) }}</div>
                        </template>
                    </Column>
                    <Column header="Customer">
                        <template #body="{ data }">
                            <div class="font-medium">{{ data.customer?.name || '-' }}</div>
                            <div class="text-xs text-slate-500">{{ data.customer?.phone }}</div>
                        </template>
                    </Column>
                    <Column header="Invoice">
                        <template #body="{ data }"><span class="text-xs">{{ data.order?.invoice_no || '-' }}</span></template>
                    </Column>
                    <Column header="Plan">
                        <template #body="{ data }">
                            <span class="text-sm whitespace-nowrap">{{ data.number_of_installments }} × {{ money(data.installment_amount) }}</span>
                            <div class="text-xs text-slate-500">{{ titleCase(data.frequency) }}</div>
                        </template>
                    </Column>
                    <Column header="Paid / Payable" style="min-width: 160px">
                        <template #body="{ data }">
                            <div class="text-xs mb-1 tabular-nums">{{ money(data.paid_amount) }} / {{ money(data.total_payable) }}</div>
                            <ProgressBar :value="progress(data)" :showValue="false" style="height: 6px" />
                        </template>
                    </Column>
                    <Column header="Balance">
                        <template #body="{ data }"><span class="font-semibold tabular-nums" :class="data.balance > 0 ? 'text-rose-600' : 'text-emerald-600'">{{ money(data.balance) }}</span></template>
                    </Column>
                    <Column header="Next due">
                        <template #body="{ data }">
                            <template v-if="data.next_schedule">
                                <div :class="isOverdueDate(data.next_schedule.due_date) ? 'text-rose-600 font-semibold' : ''">
                                    <i v-if="isOverdueDate(data.next_schedule.due_date)" class="pi pi-exclamation-circle text-xs mr-1" />{{ date(data.next_schedule.due_date) }}
                                </div>
                                <div class="text-xs text-slate-500">#{{ data.next_schedule.installment_no }} · {{ money(data.next_schedule.remaining) }}</div>
                            </template>
                            <span v-else class="text-slate-400">-</span>
                        </template>
                    </Column>
                    <Column header="Status">
                        <template #body="{ data }"><Tag :value="data.status" :severity="statusSeverity(data.status)" /></template>
                    </Column>
                    <Column header="">
                        <template #body="{ data }">
                            <Button v-if="data.status === 'active'" label="Collect" icon="pi pi-wallet" size="small" severity="success" outlined @click.stop="openShow(data.id)" />
                            <Button v-else icon="pi pi-eye" text size="small" @click.stop="openShow(data.id)" />
                        </template>
                    </Column>
                </DataTable>

                <DataTable v-else :value="pager.data" dataKey="id" size="small" showGridlines scrollable>
                    <template #empty><div class="text-center text-slate-400 py-6">No installments due in this period</div></template>
                    <Column header="Due date">
                        <template #body="{ data }"><span :class="data.is_overdue ? 'text-rose-600 font-semibold' : ''">{{ date(data.due_date) }}</span></template>
                    </Column>
                    <Column header="Agreement">
                        <template #body="{ data }">
                            <Link v-if="data.sale" :href="route('installments.show', data.sale.id)" class="font-semibold text-primary">{{ data.sale.agreement_no }}</Link>
                        </template>
                    </Column>
                    <Column header="Customer">
                        <template #body="{ data }">
                            <div class="font-medium">{{ data.sale?.customer?.name || '-' }}</div>
                            <div class="text-xs text-slate-500">{{ data.sale?.customer?.phone }}</div>
                        </template>
                    </Column>
                    <Column field="installment_no" header="#" />
                    <Column header="Amount"><template #body="{ data }"><span class="tabular-nums">{{ money(data.amount) }}</span></template></Column>
                    <Column header="Late fee"><template #body="{ data }"><span class="tabular-nums">{{ money(data.late_fee) }}</span></template></Column>
                    <Column header="Paid"><template #body="{ data }"><span class="tabular-nums">{{ money(Number(data.paid_amount) + Number(data.late_fee_paid)) }}</span></template></Column>
                    <Column header="Remaining"><template #body="{ data }"><span class="font-semibold tabular-nums">{{ money(data.remaining) }}</span></template></Column>
                    <Column header="Status">
                        <template #body="{ data }">
                            <Tag v-if="data.is_overdue" value="Overdue" severity="danger" icon="pi pi-exclamation-triangle" />
                            <Tag v-else :value="data.status" :severity="statusSeverity(data.status)" />
                        </template>
                    </Column>
                </DataTable>

                <div class="flex justify-between items-center mt-3 text-sm text-slate-500">
                    <span>Showing {{ pager.from || 0 }}–{{ pager.to || 0 }} of {{ pager.total || 0 }}</span>
                    <div class="flex gap-1">
                        <Button icon="pi pi-angle-left" text :disabled="!pager.prev_page_url" @click="goTo(pager.prev_page_url)" />
                        <Button icon="pi pi-angle-right" text :disabled="!pager.next_page_url" @click="goTo(pager.next_page_url)" />
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
