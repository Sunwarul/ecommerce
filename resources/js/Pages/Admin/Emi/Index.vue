<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, router } from "@inertiajs/vue3";
import { computed, ref } from "vue";
import Button from "primevue/button";
import Chart from "primevue/chart";
import Column from "primevue/column";
import DataTable from "primevue/datatable";
import DatePicker from "primevue/datepicker";
import Dialog from "primevue/dialog";
import InputNumber from "primevue/inputnumber";
import InputText from "primevue/inputtext";
import Select from "primevue/select";
import Tab from "primevue/tab";
import TabList from "primevue/tablist";
import TabPanel from "primevue/tabpanel";
import TabPanels from "primevue/tabpanels";
import Tabs from "primevue/tabs";
import Tag from "primevue/tag";
import Textarea from "primevue/textarea";
import ToggleSwitch from "primevue/toggleswitch";
import { useToast } from "primevue/usetoast";
import StatCard from "@/Components/Sales/StatCard.vue";
import ExportButtons from "@/Components/Sales/ExportButtons.vue";
import { useFlashToast } from "@/Composables/useFlashToast";
import { cleanParams, date, firstError, money, ymd } from "@/Helpers/format";
import { barDataset, baseOptions } from "@/Helpers/chartTheme";
import Tooltip from "primevue/tooltip";
import { useI18n } from "vue-i18n";
import useAuth from "@/Composables/useAuth";
import { PERMISSIONS } from "@/constants/permissions";
import { useDarkMode, withRtl } from "@/Composables/useDarkMode";

useFlashToast({ error: false }); // errors are toasted by the layout
const toast = useToast();
const vTooltip = Tooltip;
const { t } = useI18n();
const { hasPermission } = useAuth();
const canManage = computed(() => hasPermission(PERMISSIONS.EMI.MANAGE));
const { isRtl, themeKey } = useDarkMode();

const props = defineProps({
    transactions: { type: Object, default: () => ({ data: [] }) },
    banks: { type: Array, default: () => [] },
    stats: { type: Object, default: () => ({}) },
    byBank: { type: Array, default: () => [] },
    filters: { type: Object, default: () => ({}) },
});

const activeTab = ref("transactions");

// ---------------- Filters ----------------
const parseDate = (d) => (d ? new Date(d + "T00:00:00") : null);
const f = ref({
    search: props.filters?.search || "",
    bank_id: props.filters?.bank_id ? Number(props.filters.bank_id) : null,
    settlement_status: props.filters?.settlement_status || null,
    months: props.filters?.months ? Number(props.filters.months) : null,
    date_from: parseDate(props.filters?.date_from),
    date_to: parseDate(props.filters?.date_to),
});

const params = computed(() => cleanParams({ ...f.value, date_from: ymd(f.value.date_from), date_to: ymd(f.value.date_to) }));

const statusOptions = computed(() => [
    { label: t("emi.all_statuses"), value: null },
    ...["pending", "settled", "cancelled"].map((v) => ({ label: t(`shared.status.${v}`), value: v })),
]);
const bankOptions = computed(() => [{ id: null, name: t("emi.all_banks") }, ...props.banks]);
const monthOptions = computed(() => {
    const set = new Set(props.banks.flatMap((b) => (b.tenures || []).map((tn) => tn.months)));
    return [{ label: t("emi.any_tenure"), value: null }, ...[...set].sort((a, b) => a - b).map((m) => ({ label: t("emi.months_count", { n: m }), value: m }))];
});

function apply() {
    router.get(route("emi.index"), params.value, { preserveState: true, replace: true });
}
function reset() {
    f.value = { search: "", bank_id: null, settlement_status: null, months: null, date_from: null, date_to: null };
    apply();
}
const goTo = (url) => url && router.get(url, {}, { preserveState: true });

const settleSeverity = (s) => ({ pending: "warn", settled: "success", cancelled: "secondary" }[s] || "secondary");

// ---------------- Settlement ----------------
const selected = ref([]);
const pendingSelected = computed(() => selected.value.filter((t) => t.settlement_status === "pending"));
const showSettle = ref(false);
const settleForm = ref({ settled_at: new Date(), settlement_ref: "", settled_amount: null });
const settling = ref(false);

function openSettle(rows = null) {
    if (rows) selected.value = rows;
    if (!pendingSelected.value.length) {
        toast.add({ severity: "warn", summary: t("emi.nothing_to_settle"), detail: t("emi.select_pending"), life: 2500 });
        return;
    }
    settleForm.value = {
        settled_at: new Date(),
        settlement_ref: "",
        settled_amount: pendingSelected.value.length === 1 ? Number(pendingSelected.value[0].net_receivable) : null,
    };
    showSettle.value = true;
}
const settleTotal = computed(() => pendingSelected.value.reduce((s, t) => s + Number(t.net_receivable || 0), 0));

function submitSettle() {
    settling.value = true;
    const payload = {
        ids: pendingSelected.value.map((t) => t.id),
        settled_at: ymd(settleForm.value.settled_at),
        settlement_ref: settleForm.value.settlement_ref || null,
    };
    if (pendingSelected.value.length === 1 && settleForm.value.settled_amount !== null) payload.settled_amount = settleForm.value.settled_amount;
    router.post(route("emi.settle"), payload, {
        preserveScroll: true,
        onSuccess: () => {
            showSettle.value = false;
            selected.value = [];
        },
        onError: (e) => toast.add({ severity: "error", summary: t("shared.terms.error"), detail: firstError(e), life: 4000 }),
        onFinish: () => (settling.value = false),
    });
}

function unsettle(tx) {
    if (!confirm(t("emi.confirm_revert"))) return;
    router.post(route("emi.unsettle", tx.id), {}, { preserveScroll: true });
}

// ---------------- Banks ----------------
const showBank = ref(false);
const bankForm = ref({});
const savingBank = ref(false);
const PRESETS = [3, 6, 9, 12, 18, 24, 36];

const blankTenure = (months = null) => ({ id: null, months, customer_charge_percent: 0, merchant_charge_percent: 0, min_amount: 0, is_active: true });

function openBank(bank = null) {
    bankForm.value = bank
        ? {
            id: bank.id,
            name: bank.name,
            short_name: bank.short_name || "",
            notes: bank.notes || "",
            is_active: !!bank.is_active,
            tenures: (bank.tenures || []).map((t) => ({
                id: t.id,
                months: t.months,
                customer_charge_percent: Number(t.customer_charge_percent),
                merchant_charge_percent: Number(t.merchant_charge_percent),
                min_amount: Number(t.min_amount),
                is_active: !!t.is_active,
            })),
        }
        : { id: null, name: "", short_name: "", notes: "", is_active: true, tenures: [] };
    showBank.value = true;
}

function addPreset(m) {
    if (bankForm.value.tenures.some((t) => Number(t.months) === m)) return;
    bankForm.value.tenures.push(blankTenure(m));
    bankForm.value.tenures.sort((a, b) => (a.months ?? 999) - (b.months ?? 999));
}

function saveBank() {
    const { id, ...payload } = bankForm.value;
    payload.tenures = payload.tenures.filter((t) => t.months);
    savingBank.value = true;
    const opts = {
        preserveScroll: true,
        onSuccess: () => (showBank.value = false),
        onError: (e) => toast.add({ severity: "error", summary: t("shared.terms.error"), detail: firstError(e), life: 4000 }),
        onFinish: () => (savingBank.value = false),
    };
    id ? router.put(route("emi.banks.update", id), payload, opts) : router.post(route("emi.banks.store"), payload, opts);
}

function deleteBank(bank) {
    if (!confirm(t("emi.confirm_remove_bank", { name: bank.name }))) return;
    router.delete(route("emi.banks.destroy", bank.id), { preserveScroll: true });
}

// ---------------- Summary chart ----------------
const chartData = computed(() => {
    themeKey.value; // rebuild on theme / direction change
    return {
        labels: props.byBank.map((b) => b.name),
        datasets: [barDataset(t("emi.card_amount"), props.byBank.map((b) => Number(b.card_amount || 0)), 0)],
    };
});
const chartOptions = computed(() => {
    themeKey.value;
    return withRtl(baseOptions({ horizontal: true, legend: false }), isRtl.value);
});
const chartHeight = computed(() => `${Math.max(180, props.byBank.length * 40 + 60)}px`);
</script>

<template>
    <Head :title="t('shared.sale_type.emi')" />
    <AuthenticatedLayout>
        <div class="p-4 md:p-6 space-y-4">
            <div class="flex flex-wrap justify-between items-start gap-3">
                <div>
                    <h1 class="text-xl font-semibold text-slate-800 flex items-center gap-2"><i class="pi pi-credit-card text-primary" /> {{ t('shared.sale_type.emi') }}</h1>
                    <p class="text-sm text-slate-500">{{ t('emi.subtitle') }}</p>
                </div>
                <Button v-if="canManage" :label="t('emi.add_bank')" icon="pi pi-plus" size="small" @click="openBank()" />
            </div>

            <div class="p-3 rounded-xl border border-sky-200 bg-sky-50 text-sm text-sky-900 flex gap-2">
                <i class="pi pi-info-circle mt-0.5" />
                <span>{{ t('emi.explainer') }}</span>
            </div>

            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
                <StatCard :label="t('emi.pending_settlements')" :value="stats.pending_count ?? 0" icon="pi pi-hourglass" tone="amber" />
                <StatCard :label="t('emi.pending_receivable')" :value="money(stats.pending_receivable)" icon="pi pi-wallet" tone="violet" />
                <StatCard :label="t('emi.month_card_sales')" :value="money(stats.month_card_amount)" icon="pi pi-credit-card" tone="sky" />
                <StatCard :label="t('emi.month_bank_charges')" :value="money(stats.month_bank_charges)" icon="pi pi-percentage" tone="rose" />
            </div>

            <Tabs v-model:value="activeTab" scrollable>
                <TabList>
                    <Tab value="transactions"><i class="pi pi-list me-2" />{{ t('emi.tab_transactions') }}</Tab>
                    <Tab value="banks"><i class="pi pi-building-columns me-2" />{{ t('emi.tab_banks') }}</Tab>
                    <Tab value="summary"><i class="pi pi-chart-bar me-2" />{{ t('emi.tab_summary') }}</Tab>
                </TabList>
                <TabPanels>
                    <!-- Transactions -->
                    <TabPanel value="transactions">
                        <div class="space-y-3">
                            <div class="flex flex-wrap gap-2 items-center">
                                <InputText v-model="f.search" type="search" :placeholder="t('emi.search_placeholder')" v-tooltip.bottom="t('shared.terms.search_hint')" class="w-full md:w-72" @keyup.enter="apply" />
                                <div class="grid grid-cols-2 gap-2 w-full md:w-auto md:flex md:flex-wrap">
                                    <Select v-model="f.bank_id" :options="bankOptions" optionLabel="name" optionValue="id" :placeholder="t('emi.fields.bank')" class="w-full md:w-44" filter />
                                    <Select v-model="f.settlement_status" :options="statusOptions" optionLabel="label" optionValue="value" :placeholder="t('common.status')" class="w-full md:w-40" />
                                    <Select v-model="f.months" :options="monthOptions" optionLabel="label" optionValue="value" :placeholder="t('emi.fields.tenure')" class="w-full md:w-36" />
                                    <DatePicker v-model="f.date_from" :placeholder="t('shared.terms.from')" dateFormat="dd-mm-yy" showIcon class="w-full md:w-40" />
                                    <DatePicker v-model="f.date_to" :placeholder="t('shared.terms.to')" dateFormat="dd-mm-yy" showIcon class="w-full md:w-40" />
                                </div>
                                <Button :label="t('shared.terms.apply')" icon="pi pi-filter" outlined size="small" @click="apply" />
                                <Button :label="t('shared.terms.reset')" icon="pi pi-refresh" text size="small" @click="reset" />
                                <div class="md:ms-auto"><ExportButtons routeName="emi.index" :params="params" /></div>
                            </div>

                            <div v-if="canManage && selected.length" class="flex flex-wrap items-center gap-2 p-2 rounded-lg bg-slate-50 border border-slate-200 text-sm">
                                <span>{{ t('emi.selected_summary', { n: selected.length, pending: pendingSelected.length }) }}</span>
                                <Button :label="t('emi.mark_settled')" icon="pi pi-check" size="small" severity="success" :disabled="!pendingSelected.length" @click="openSettle()" />
                                <Button :label="t('sms.send.clear')" text size="small" @click="selected = []" />
                            </div>

                            <DataTable :value="transactions.data" v-model:selection="selected" dataKey="id" size="small" showGridlines scrollable>
                                <template #empty><div class="text-center text-slate-400 py-6">{{ t('emi.no_transactions') }}</div></template>
                                <Column v-if="canManage" selectionMode="multiple" headerStyle="width: 3rem" />
                                <Column :header="t('shared.terms.date')"><template #body="{ data }"><span class="whitespace-nowrap">{{ date(data.created_at) }}</span></template></Column>
                                <Column :header="t('shared.terms.invoice')">
                                    <template #body="{ data }">
                                        <a :href="route('pos.orders.invoice', data.order_id)" target="_blank" class="text-primary font-medium whitespace-nowrap">{{ data.order?.invoice_no || `#${data.order_id}` }}</a>
                                    </template>
                                </Column>
                                <Column :header="t('shared.terms.customer')">
                                    <template #body="{ data }">
                                        <div>{{ data.order?.customer?.name || t('shared.terms.walk_in') }}</div>
                                        <div class="text-xs text-slate-500 ltr-nums text-start">{{ data.order?.customer?.phone }}</div>
                                    </template>
                                </Column>
                                <Column :header="t('emi.fields.bank')"><template #body="{ data }">{{ data.bank?.short_name || data.bank?.name }}</template></Column>
                                <Column :header="t('emi.months')" field="months" />
                                <Column :header="t('installments.principal')"><template #body="{ data }"><span class="tabular-nums">{{ money(data.principal_amount) }}</span></template></Column>
                                <Column :header="t('emi.customer_charge')"><template #body="{ data }"><span class="tabular-nums">{{ money(data.customer_charge_amount) }}</span></template></Column>
                                <Column :header="t('emi.card_amount')"><template #body="{ data }"><span class="tabular-nums font-medium">{{ money(data.card_amount) }}</span></template></Column>
                                <Column :header="t('emi.bank_charge')"><template #body="{ data }"><span class="tabular-nums text-rose-600">{{ money(data.merchant_charge_amount) }}</span></template></Column>
                                <Column :header="t('emi.receivable')"><template #body="{ data }"><span class="tabular-nums font-semibold">{{ money(data.net_receivable) }}</span></template></Column>
                                <Column :header="t('emi.approval')">
                                    <template #body="{ data }">
                                        <div class="text-xs">{{ data.approval_code || '-' }}</div>
                                        <div v-if="data.card_last4" class="text-xs text-slate-500 ltr-nums text-start">•••• {{ data.card_last4 }}</div>
                                    </template>
                                </Column>
                                <Column :header="t('emi.settlement')">
                                    <template #body="{ data }">
                                        <Tag :value="t('shared.status.' + data.settlement_status)" :severity="settleSeverity(data.settlement_status)" />
                                        <div v-if="data.settlement_status === 'settled'" class="text-[11px] text-slate-500 whitespace-nowrap">
                                            {{ money(data.settled_amount) }} · {{ date(data.settled_at) }}
                                        </div>
                                    </template>
                                </Column>
                                <Column header="">
                                    <template #body="{ data }">
                                        <template v-if="canManage">
                                            <Button v-if="data.settlement_status === 'pending'" icon="pi pi-check" text size="small" severity="success" v-tooltip.top="t('emi.mark_settled')" :aria-label="t('emi.mark_settled')" @click="openSettle([data])" />
                                            <Button v-else-if="data.settlement_status === 'settled'" icon="pi pi-undo" text size="small" severity="secondary" v-tooltip.top="t('emi.revert')" :aria-label="t('emi.revert')" @click="unsettle(data)" />
                                        </template>
                                    </template>
                                </Column>
                            </DataTable>

                            <div class="flex justify-between items-center text-sm text-slate-500">
                                <span>{{ t('shared.terms.showing', { from: transactions.from || 0, to: transactions.to || 0, total: transactions.total || 0 }) }}</span>
                                <div class="flex gap-1">
                                    <Button icon="pi pi-angle-left" text :aria-label="t('shared.terms.previous')" :disabled="!transactions.prev_page_url" @click="goTo(transactions.prev_page_url)" />
                                    <Button icon="pi pi-angle-right" text :aria-label="t('shared.terms.next')" :disabled="!transactions.next_page_url" @click="goTo(transactions.next_page_url)" />
                                </div>
                            </div>
                        </div>
                    </TabPanel>

                    <!-- Banks -->
                    <TabPanel value="banks">
                        <div v-if="!banks.length" class="text-center text-slate-400 py-8">
                            {{ t('emi.no_banks') }}
                        </div>
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                            <div v-for="bank in banks" :key="bank.id" class="border border-slate-200 rounded-xl p-4 bg-white min-w-0 overflow-x-auto">
                                <div class="flex justify-between items-start gap-2 mb-2">
                                    <div>
                                        <div class="font-semibold flex items-center gap-2">
                                            {{ bank.name }}
                                            <span v-if="bank.short_name" class="text-xs text-slate-500">({{ bank.short_name }})</span>
                                            <Tag v-if="!bank.is_active" :value="t('shared.status.inactive')" severity="secondary" />
                                        </div>
                                        <div v-if="bank.notes" class="text-xs text-slate-500">{{ bank.notes }}</div>
                                    </div>
                                    <div v-if="canManage" class="flex gap-1 shrink-0">
                                        <Button icon="pi pi-pencil" text size="small" :aria-label="t('common.edit')" @click="openBank(bank)" />
                                        <Button icon="pi pi-trash" text size="small" severity="danger" :aria-label="t('common.delete')" @click="deleteBank(bank)" />
                                    </div>
                                </div>
                                <table class="w-full text-sm tabular-nums">
                                    <thead>
                                        <tr class="text-xs text-slate-500 border-b text-start">
                                            <th class="py-1 text-start">{{ t('emi.months') }}</th>
                                            <th class="py-1 text-end">{{ t('emi.customer_percent') }}</th>
                                            <th class="py-1 text-end">{{ t('emi.bank_percent') }}</th>
                                            <th class="py-1 text-end">{{ t('emi.min_amount') }}</th>
                                            <th class="py-1 text-end">{{ t('shared.status.active') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="tn in bank.tenures" :key="tn.id" class="border-b border-slate-100" :class="!tn.is_active ? 'text-slate-400' : ''">
                                            <td class="py-1">{{ tn.months }}</td>
                                            <td class="py-1 text-end">{{ Number(tn.customer_charge_percent) }}%</td>
                                            <td class="py-1 text-end">{{ Number(tn.merchant_charge_percent) }}%</td>
                                            <td class="py-1 text-end">{{ Number(tn.min_amount) > 0 ? money(tn.min_amount) : '-' }}</td>
                                            <td class="py-1 text-end"><i :class="tn.is_active ? 'pi pi-check text-emerald-600' : 'pi pi-times'" :aria-label="tn.is_active ? t('shared.status.active') : t('shared.status.inactive')" /></td>
                                        </tr>
                                        <tr v-if="!bank.tenures?.length"><td colspan="5" class="py-2 text-center text-slate-400">{{ t('emi.no_tenures') }}</td></tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </TabPanel>

                    <!-- Summary -->
                    <TabPanel value="summary">
                        <div v-if="!byBank.length" class="text-center text-slate-400 py-8">{{ t('emi.no_sales') }}</div>
                        <div v-else class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                            <div>
                                <h2 class="font-semibold text-sm text-slate-700 mb-2">{{ t('emi.sales_by_bank') }}</h2>
                                <Chart :key="themeKey" type="bar" :data="chartData" :options="chartOptions" :style="{ height: chartHeight }" />
                            </div>
                            <div class="overflow-x-auto">
                                <table class="w-full text-sm tabular-nums">
                                    <thead>
                                        <tr class="text-xs text-slate-500 border-b text-start">
                                            <th class="py-2 text-start">{{ t('emi.fields.bank') }}</th>
                                            <th class="py-2 text-end">{{ t('emi.tab_transactions') }}</th>
                                            <th class="py-2 text-end">{{ t('emi.card_amount') }}</th>
                                            <th class="py-2 text-end">{{ t('emi.receivable') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="b in byBank" :key="b.name" class="border-b border-slate-100">
                                            <td class="py-2">{{ b.name }}</td>
                                            <td class="py-2 text-end">{{ b.count }}</td>
                                            <td class="py-2 text-end">{{ money(b.card_amount) }}</td>
                                            <td class="py-2 text-end font-medium">{{ money(b.receivable) }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </TabPanel>
                </TabPanels>
            </Tabs>
        </div>

        <!-- Settle dialog -->
        <Dialog v-model:visible="showSettle" modal :header="t('emi.record_settlement')" :style="{ width: '460px' }" :breakpoints="{ '640px': '95vw' }">
            <div class="space-y-3">
                <div class="text-sm">
                    {{ t('emi.settle_summary', { n: pendingSelected.length }) }} <b class="ltr-nums">{{ money(settleTotal) }}</b>
                </div>
                <div>
                    <label class="text-xs text-slate-500">{{ t('emi.settlement_date') }} *</label>
                    <DatePicker v-model="settleForm.settled_at" dateFormat="dd-mm-yy" showIcon class="w-full" />
                </div>
                <div>
                    <label class="text-xs text-slate-500">{{ t('emi.bank_reference') }}</label>
                    <InputText v-model="settleForm.settlement_ref" class="w-full" :placeholder="t('emi.bank_reference_placeholder')" />
                </div>
                <div v-if="pendingSelected.length === 1">
                    <label class="text-xs text-slate-500">{{ t('emi.amount_received') }}</label>
                    <InputNumber v-model="settleForm.settled_amount" :min="0" :maxFractionDigits="2" class="w-full" inputClass="w-full" />
                </div>
                <div v-else class="text-xs text-slate-500">{{ t('emi.bulk_settle_note') }}</div>
            </div>
            <template #footer>
                <Button :label="t('shared.terms.cancel')" text @click="showSettle = false" />
                <Button :label="t('emi.mark_settled')" icon="pi pi-check" severity="success" :loading="settling" :disabled="!settleForm.settled_at" @click="submitSettle" />
            </template>
        </Dialog>

        <!-- Bank dialog -->
        <Dialog v-model:visible="showBank" modal :header="bankForm.id ? t('emi.edit_bank') : t('emi.add_bank')" :style="{ width: '760px' }" :breakpoints="{ '960px': '90vw', '640px': '98vw' }">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <div><label class="text-xs text-slate-500">{{ t('emi.bank_name') }} *</label><InputText v-model="bankForm.name" dir="auto" class="w-full" /></div>
                <div><label class="text-xs text-slate-500">{{ t('emi.short_name') }}</label><InputText v-model="bankForm.short_name" class="w-full" placeholder="CBL" /></div>
                <div class="md:col-span-2"><label class="text-xs text-slate-500">{{ t('shared.terms.notes') }}</label><Textarea v-model="bankForm.notes" rows="2" dir="auto" class="w-full" /></div>
                <div class="flex items-center gap-2"><ToggleSwitch v-model="bankForm.is_active" /><span class="text-sm">{{ t('shared.status.active') }}</span></div>
            </div>

            <div class="mt-4">
                <div class="flex flex-wrap items-center justify-between gap-2 mb-2">
                    <div class="font-semibold text-sm">{{ t('emi.tenures') }}</div>
                    <div class="flex flex-wrap gap-1">
                        <Button v-for="m in PRESETS" :key="m" :label="t('emi.months_short', { n: m })" size="small" outlined severity="secondary"
                            :disabled="bankForm.tenures?.some((t) => Number(t.months) === m)" @click="addPreset(m)" />
                        <Button :label="t('emi.custom')" icon="pi pi-plus" size="small" text @click="bankForm.tenures.push(blankTenure())" />
                    </div>
                </div>
                <div class="text-[11px] text-slate-500 mb-2">
                    {{ t('emi.rates_help') }}
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-xs text-slate-500 text-start border-b">
                                <th class="py-1 pe-2 text-start">{{ t('emi.months') }}</th>
                                <th class="py-1 pe-2 text-start">{{ t('emi.customer_percent') }}</th>
                                <th class="py-1 pe-2 text-start">{{ t('emi.bank_percent') }}</th>
                                <th class="py-1 pe-2 text-start">{{ t('emi.min_amount') }}</th>
                                <th class="py-1 pe-2 text-start">{{ t('shared.status.active') }}</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(tn, i) in bankForm.tenures" :key="i" class="border-b border-slate-100">
                                <td class="py-1 pe-2"><InputNumber v-model="tn.months" :min="1" :max="120" inputClass="!w-16" size="small" /></td>
                                <td class="py-1 pe-2"><InputNumber v-model="tn.customer_charge_percent" :min="0" :max="100" :maxFractionDigits="3" suffix="%" inputClass="!w-20" size="small" /></td>
                                <td class="py-1 pe-2"><InputNumber v-model="tn.merchant_charge_percent" :min="0" :max="100" :maxFractionDigits="3" suffix="%" inputClass="!w-20" size="small" /></td>
                                <td class="py-1 pe-2"><InputNumber v-model="tn.min_amount" :min="0" :maxFractionDigits="2" inputClass="!w-24" size="small" /></td>
                                <td class="py-1 pe-2"><ToggleSwitch v-model="tn.is_active" /></td>
                                <td class="py-1"><Button icon="pi pi-trash" text severity="danger" size="small" :aria-label="t('common.delete')" @click="bankForm.tenures.splice(i, 1)" /></td>
                            </tr>
                            <tr v-if="!bankForm.tenures?.length"><td colspan="6" class="py-3 text-center text-slate-400">{{ t('emi.add_tenures_hint') }}</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <template #footer>
                <Button :label="t('shared.terms.cancel')" text @click="showBank = false" />
                <Button :label="t('shared.terms.save')" icon="pi pi-check" :loading="savingBank" :disabled="!bankForm.name" @click="saveBank" />
            </template>
        </Dialog>
    </AuthenticatedLayout>
</template>
