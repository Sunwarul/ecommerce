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

useFlashToast({ error: false }); // errors are toasted by the layout
const toast = useToast();

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

const statusOptions = [
    { label: "All statuses", value: null },
    { label: "Pending", value: "pending" },
    { label: "Settled", value: "settled" },
    { label: "Cancelled", value: "cancelled" },
];
const bankOptions = computed(() => [{ id: null, name: "All banks" }, ...props.banks]);
const monthOptions = computed(() => {
    const set = new Set(props.banks.flatMap((b) => (b.tenures || []).map((t) => t.months)));
    return [{ label: "Any tenure", value: null }, ...[...set].sort((a, b) => a - b).map((m) => ({ label: `${m} months`, value: m }))];
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
        toast.add({ severity: "warn", summary: "Nothing to settle", detail: "Select pending transactions", life: 2500 });
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
        onError: (e) => toast.add({ severity: "error", summary: "Failed", detail: firstError(e), life: 4000 }),
        onFinish: () => (settling.value = false),
    });
}

function unsettle(t) {
    if (!confirm("Revert this settlement to pending?")) return;
    router.post(route("emi.unsettle", t.id), {}, { preserveScroll: true });
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
        onError: (e) => toast.add({ severity: "error", summary: "Failed", detail: firstError(e), life: 4000 }),
        onFinish: () => (savingBank.value = false),
    };
    id ? router.put(route("emi.banks.update", id), payload, opts) : router.post(route("emi.banks.store"), payload, opts);
}

function deleteBank(bank) {
    if (!confirm(`Remove ${bank.name}? Past EMI transactions are kept.`)) return;
    router.delete(route("emi.banks.destroy", bank.id), { preserveScroll: true });
}

// ---------------- Summary chart ----------------
const chartData = computed(() => ({
    labels: props.byBank.map((b) => b.name),
    datasets: [barDataset("Card amount", props.byBank.map((b) => Number(b.card_amount || 0)), 0)],
}));
const chartOptions = computed(() => baseOptions({ horizontal: true, legend: false }));
const chartHeight = computed(() => `${Math.max(180, props.byBank.length * 40 + 60)}px`);
</script>

<template>
    <Head title="Bank EMI" />
    <AuthenticatedLayout>
        <div class="p-4 md:p-6 space-y-4">
            <div class="flex flex-wrap justify-between items-start gap-3">
                <div>
                    <h2 class="text-xl font-semibold flex items-center gap-2"><i class="pi pi-credit-card text-primary" /> Bank EMI</h2>
                    <p class="text-sm text-slate-500">Card EMI sales, bank rates and settlements</p>
                </div>
                <Button label="Add bank" icon="pi pi-plus" size="small" @click="openBank()" />
            </div>

            <div class="p-3 rounded-xl border border-sky-200 bg-sky-50 text-sm text-sky-900 flex gap-2">
                <i class="pi pi-info-circle mt-0.5" />
                <span>Bank EMI is processed on the bank's card terminal. Record bank, tenure and approval code at POS; the bank pays you the card amount minus its merchant charge — track that here.</span>
            </div>

            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
                <StatCard label="Pending settlements" :value="stats.pending_count ?? 0" icon="pi pi-hourglass" tone="amber" />
                <StatCard label="Pending receivable" :value="money(stats.pending_receivable)" icon="pi pi-wallet" tone="violet" />
                <StatCard label="EMI card sales (month)" :value="money(stats.month_card_amount)" icon="pi pi-credit-card" tone="sky" />
                <StatCard label="Bank charges (month)" :value="money(stats.month_bank_charges)" icon="pi pi-percentage" tone="rose" />
            </div>

            <Tabs v-model:value="activeTab">
                <TabList>
                    <Tab value="transactions">Transactions</Tab>
                    <Tab value="banks">Banks &amp; rates</Tab>
                    <Tab value="summary">Summary</Tab>
                </TabList>
                <TabPanels>
                    <!-- Transactions -->
                    <TabPanel value="transactions">
                        <div class="space-y-3">
                            <div class="flex flex-wrap gap-2 items-center">
                                <InputText v-model="f.search" placeholder="Invoice / customer / phone / approval / last 4" class="w-full md:w-72" @keyup.enter="apply" />
                                <Select v-model="f.bank_id" :options="bankOptions" optionLabel="name" optionValue="id" placeholder="Bank" class="w-[48%] md:w-44" filter />
                                <Select v-model="f.settlement_status" :options="statusOptions" optionLabel="label" optionValue="value" placeholder="Status" class="w-[48%] md:w-40" />
                                <Select v-model="f.months" :options="monthOptions" optionLabel="label" optionValue="value" placeholder="Tenure" class="w-[48%] md:w-36" />
                                <DatePicker v-model="f.date_from" placeholder="From" dateFormat="dd-mm-yy" showIcon class="w-[48%] md:w-40" />
                                <DatePicker v-model="f.date_to" placeholder="To" dateFormat="dd-mm-yy" showIcon class="w-[48%] md:w-40" />
                                <Button label="Apply" icon="pi pi-filter" outlined size="small" @click="apply" />
                                <Button label="Reset" icon="pi pi-refresh" text size="small" @click="reset" />
                                <div class="md:ml-auto"><ExportButtons routeName="emi.index" :params="params" /></div>
                            </div>

                            <div v-if="selected.length" class="flex items-center gap-2 p-2 rounded-lg bg-slate-50 border border-slate-200 text-sm">
                                <span>{{ selected.length }} selected ({{ pendingSelected.length }} pending)</span>
                                <Button label="Mark settled" icon="pi pi-check" size="small" severity="success" :disabled="!pendingSelected.length" @click="openSettle()" />
                                <Button label="Clear" text size="small" @click="selected = []" />
                            </div>

                            <DataTable :value="transactions.data" v-model:selection="selected" dataKey="id" size="small" showGridlines scrollable>
                                <template #empty><div class="text-center text-slate-400 py-6">No EMI transactions</div></template>
                                <Column selectionMode="multiple" headerStyle="width: 3rem" />
                                <Column header="Date"><template #body="{ data }"><span class="whitespace-nowrap">{{ date(data.created_at) }}</span></template></Column>
                                <Column header="Invoice">
                                    <template #body="{ data }">
                                        <a :href="route('pos.orders.invoice', data.order_id)" target="_blank" class="text-primary font-medium whitespace-nowrap">{{ data.order?.invoice_no || `#${data.order_id}` }}</a>
                                    </template>
                                </Column>
                                <Column header="Customer">
                                    <template #body="{ data }">
                                        <div>{{ data.order?.customer?.name || 'Walk-in' }}</div>
                                        <div class="text-xs text-slate-500">{{ data.order?.customer?.phone }}</div>
                                    </template>
                                </Column>
                                <Column header="Bank"><template #body="{ data }">{{ data.bank?.short_name || data.bank?.name }}</template></Column>
                                <Column header="Months" field="months" />
                                <Column header="Principal"><template #body="{ data }"><span class="tabular-nums">{{ money(data.principal_amount) }}</span></template></Column>
                                <Column header="Cust. charge"><template #body="{ data }"><span class="tabular-nums">{{ money(data.customer_charge_amount) }}</span></template></Column>
                                <Column header="Card amount"><template #body="{ data }"><span class="tabular-nums font-medium">{{ money(data.card_amount) }}</span></template></Column>
                                <Column header="Bank charge"><template #body="{ data }"><span class="tabular-nums text-rose-600">{{ money(data.merchant_charge_amount) }}</span></template></Column>
                                <Column header="Receivable"><template #body="{ data }"><span class="tabular-nums font-semibold">{{ money(data.net_receivable) }}</span></template></Column>
                                <Column header="Approval">
                                    <template #body="{ data }">
                                        <div class="text-xs">{{ data.approval_code || '-' }}</div>
                                        <div v-if="data.card_last4" class="text-xs text-slate-500">•••• {{ data.card_last4 }}</div>
                                    </template>
                                </Column>
                                <Column header="Settlement">
                                    <template #body="{ data }">
                                        <Tag :value="data.settlement_status" :severity="settleSeverity(data.settlement_status)" />
                                        <div v-if="data.settlement_status === 'settled'" class="text-[11px] text-slate-500 whitespace-nowrap">
                                            {{ money(data.settled_amount) }} · {{ date(data.settled_at) }}
                                        </div>
                                    </template>
                                </Column>
                                <Column header="">
                                    <template #body="{ data }">
                                        <Button v-if="data.settlement_status === 'pending'" icon="pi pi-check" text size="small" severity="success" v-tooltip.top="'Mark settled'" @click="openSettle([data])" />
                                        <Button v-else-if="data.settlement_status === 'settled'" icon="pi pi-undo" text size="small" severity="secondary" v-tooltip.top="'Revert'" @click="unsettle(data)" />
                                    </template>
                                </Column>
                            </DataTable>

                            <div class="flex justify-between items-center text-sm text-slate-500">
                                <span>Showing {{ transactions.from || 0 }}–{{ transactions.to || 0 }} of {{ transactions.total || 0 }}</span>
                                <div class="flex gap-1">
                                    <Button icon="pi pi-angle-left" text :disabled="!transactions.prev_page_url" @click="goTo(transactions.prev_page_url)" />
                                    <Button icon="pi pi-angle-right" text :disabled="!transactions.next_page_url" @click="goTo(transactions.next_page_url)" />
                                </div>
                            </div>
                        </div>
                    </TabPanel>

                    <!-- Banks -->
                    <TabPanel value="banks">
                        <div v-if="!banks.length" class="text-center text-slate-400 py-8">
                            No banks yet. Add the banks you have EMI agreements with and their tenure rates.
                        </div>
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                            <div v-for="bank in banks" :key="bank.id" class="border border-slate-200 rounded-xl p-4 bg-white">
                                <div class="flex justify-between items-start gap-2 mb-2">
                                    <div>
                                        <div class="font-semibold flex items-center gap-2">
                                            {{ bank.name }}
                                            <span v-if="bank.short_name" class="text-xs text-slate-500">({{ bank.short_name }})</span>
                                            <Tag v-if="!bank.is_active" value="Inactive" severity="secondary" />
                                        </div>
                                        <div v-if="bank.notes" class="text-xs text-slate-500">{{ bank.notes }}</div>
                                    </div>
                                    <div class="flex gap-1">
                                        <Button icon="pi pi-pencil" text size="small" @click="openBank(bank)" />
                                        <Button icon="pi pi-trash" text size="small" severity="danger" @click="deleteBank(bank)" />
                                    </div>
                                </div>
                                <table class="w-full text-sm tabular-nums">
                                    <thead>
                                        <tr class="text-xs text-slate-500 border-b text-left">
                                            <th class="py-1">Months</th>
                                            <th class="py-1 text-right">Customer %</th>
                                            <th class="py-1 text-right">Bank %</th>
                                            <th class="py-1 text-right">Min amount</th>
                                            <th class="py-1 text-right">Active</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="t in bank.tenures" :key="t.id" class="border-b border-slate-100" :class="!t.is_active ? 'text-slate-400' : ''">
                                            <td class="py-1">{{ t.months }}</td>
                                            <td class="py-1 text-right">{{ Number(t.customer_charge_percent) }}%</td>
                                            <td class="py-1 text-right">{{ Number(t.merchant_charge_percent) }}%</td>
                                            <td class="py-1 text-right">{{ Number(t.min_amount) > 0 ? money(t.min_amount) : '-' }}</td>
                                            <td class="py-1 text-right"><i :class="t.is_active ? 'pi pi-check text-emerald-600' : 'pi pi-times'" /></td>
                                        </tr>
                                        <tr v-if="!bank.tenures?.length"><td colspan="5" class="py-2 text-center text-slate-400">No tenures</td></tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </TabPanel>

                    <!-- Summary -->
                    <TabPanel value="summary">
                        <div v-if="!byBank.length" class="text-center text-slate-400 py-8">No EMI sales yet</div>
                        <div v-else class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                            <div>
                                <h3 class="font-semibold text-sm mb-2">Card EMI sales by bank</h3>
                                <Chart type="bar" :data="chartData" :options="chartOptions" :style="{ height: chartHeight }" />
                            </div>
                            <div class="overflow-x-auto">
                                <table class="w-full text-sm tabular-nums">
                                    <thead>
                                        <tr class="text-xs text-slate-500 border-b text-left">
                                            <th class="py-2">Bank</th>
                                            <th class="py-2 text-right">Transactions</th>
                                            <th class="py-2 text-right">Card amount</th>
                                            <th class="py-2 text-right">Receivable</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="b in byBank" :key="b.name" class="border-b border-slate-100">
                                            <td class="py-2">{{ b.name }}</td>
                                            <td class="py-2 text-right">{{ b.count }}</td>
                                            <td class="py-2 text-right">{{ money(b.card_amount) }}</td>
                                            <td class="py-2 text-right font-medium">{{ money(b.receivable) }}</td>
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
        <Dialog v-model:visible="showSettle" modal header="Record bank settlement" :style="{ width: '460px' }" :breakpoints="{ '640px': '95vw' }">
            <div class="space-y-3">
                <div class="text-sm">
                    {{ pendingSelected.length }} transaction(s) · expected <b>{{ money(settleTotal) }}</b>
                </div>
                <div>
                    <label class="text-xs text-slate-500">Settlement date *</label>
                    <DatePicker v-model="settleForm.settled_at" dateFormat="dd-mm-yy" showIcon class="w-full" />
                </div>
                <div>
                    <label class="text-xs text-slate-500">Bank reference</label>
                    <InputText v-model="settleForm.settlement_ref" class="w-full" placeholder="Statement / batch ref" />
                </div>
                <div v-if="pendingSelected.length === 1">
                    <label class="text-xs text-slate-500">Amount received</label>
                    <InputNumber v-model="settleForm.settled_amount" :min="0" :maxFractionDigits="2" class="w-full" inputClass="w-full" />
                </div>
                <div v-else class="text-xs text-slate-500">Each transaction is recorded at its expected receivable.</div>
            </div>
            <template #footer>
                <Button label="Cancel" text @click="showSettle = false" />
                <Button label="Mark settled" icon="pi pi-check" severity="success" :loading="settling" :disabled="!settleForm.settled_at" @click="submitSettle" />
            </template>
        </Dialog>

        <!-- Bank dialog -->
        <Dialog v-model:visible="showBank" modal :header="bankForm.id ? 'Edit bank' : 'Add bank'" :style="{ width: '760px' }" :breakpoints="{ '960px': '90vw', '640px': '98vw' }">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <div><label class="text-xs text-slate-500">Bank name *</label><InputText v-model="bankForm.name" class="w-full" /></div>
                <div><label class="text-xs text-slate-500">Short name</label><InputText v-model="bankForm.short_name" class="w-full" placeholder="e.g. CBL" /></div>
                <div class="md:col-span-2"><label class="text-xs text-slate-500">Notes</label><Textarea v-model="bankForm.notes" rows="2" class="w-full" /></div>
                <div class="flex items-center gap-2"><ToggleSwitch v-model="bankForm.is_active" /><span class="text-sm">Active</span></div>
            </div>

            <div class="mt-4">
                <div class="flex flex-wrap items-center justify-between gap-2 mb-2">
                    <div class="font-semibold text-sm">Tenures</div>
                    <div class="flex flex-wrap gap-1">
                        <Button v-for="m in PRESETS" :key="m" :label="`${m}m`" size="small" outlined severity="secondary"
                            :disabled="bankForm.tenures?.some((t) => Number(t.months) === m)" @click="addPreset(m)" />
                        <Button label="Custom" icon="pi pi-plus" size="small" text @click="bankForm.tenures.push(blankTenure())" />
                    </div>
                </div>
                <div class="text-[11px] text-slate-500 mb-2">
                    Customer % is added to the customer's bill (0 for "0% EMI"). Bank % is the merchant charge the bank deducts at settlement.
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-xs text-slate-500 text-left border-b">
                                <th class="py-1 pr-2">Months</th>
                                <th class="py-1 pr-2">Customer %</th>
                                <th class="py-1 pr-2">Bank %</th>
                                <th class="py-1 pr-2">Min amount</th>
                                <th class="py-1 pr-2">Active</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(t, i) in bankForm.tenures" :key="i" class="border-b border-slate-100">
                                <td class="py-1 pr-2"><InputNumber v-model="t.months" :min="1" :max="120" inputClass="!w-16" size="small" /></td>
                                <td class="py-1 pr-2"><InputNumber v-model="t.customer_charge_percent" :min="0" :max="100" :maxFractionDigits="3" suffix="%" inputClass="!w-20" size="small" /></td>
                                <td class="py-1 pr-2"><InputNumber v-model="t.merchant_charge_percent" :min="0" :max="100" :maxFractionDigits="3" suffix="%" inputClass="!w-20" size="small" /></td>
                                <td class="py-1 pr-2"><InputNumber v-model="t.min_amount" :min="0" :maxFractionDigits="2" inputClass="!w-24" size="small" /></td>
                                <td class="py-1 pr-2"><ToggleSwitch v-model="t.is_active" /></td>
                                <td class="py-1"><Button icon="pi pi-trash" text severity="danger" size="small" @click="bankForm.tenures.splice(i, 1)" /></td>
                            </tr>
                            <tr v-if="!bankForm.tenures?.length"><td colspan="6" class="py-3 text-center text-slate-400">Add tenures with the buttons above</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <template #footer>
                <Button label="Cancel" text @click="showBank = false" />
                <Button label="Save" icon="pi pi-check" :loading="savingBank" :disabled="!bankForm.name" @click="saveBank" />
            </template>
        </Dialog>
    </AuthenticatedLayout>
</template>
