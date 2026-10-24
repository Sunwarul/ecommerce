<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, router } from "@inertiajs/vue3";
import { computed, ref } from "vue";
import Button from "primevue/button";
import Column from "primevue/column";
import DataTable from "primevue/datatable";
import Dialog from "primevue/dialog";
import InputNumber from "primevue/inputnumber";
import InputText from "primevue/inputtext";
import Select from "primevue/select";
import Tag from "primevue/tag";
import Textarea from "primevue/textarea";
import ToggleSwitch from "primevue/toggleswitch";
import { useToast } from "primevue/usetoast";
import { useFlashToast } from "@/Composables/useFlashToast";
import { firstError, money, titleCase } from "@/Helpers/format";
import { FEE_TYPES, FREQUENCIES, INTEREST_TYPES } from "@/Helpers/installment";

useFlashToast({ error: false }); // errors are toasted by the layout
const toast = useToast();

defineProps({ plans: { type: Array, default: () => [] } });

const blank = () => ({
    id: null,
    name: "",
    description: "",
    number_of_installments: 6,
    frequency: "monthly",
    interval_days: null,
    interest_type: "none",
    interest_rate: 0,
    min_down_payment_percent: 0,
    processing_fee_type: "fixed",
    processing_fee: 0,
    late_fee_type: "fixed",
    late_fee_amount: 0,
    grace_days: 0,
    is_active: true,
});

const show = ref(false);
const form = ref(blank());
const saving = ref(false);

function openCreate() {
    form.value = blank();
    show.value = true;
}
function openEdit(plan) {
    form.value = {
        ...blank(),
        ...plan,
        interest_rate: Number(plan.interest_rate),
        min_down_payment_percent: Number(plan.min_down_payment_percent),
        processing_fee: Number(plan.processing_fee),
        late_fee_amount: Number(plan.late_fee_amount),
    };
    show.value = true;
}

function save() {
    const { id, ...payload } = form.value;
    if (payload.frequency !== "custom") payload.interval_days = null;
    saving.value = true;
    const opts = {
        preserveScroll: true,
        onSuccess: () => (show.value = false),
        onError: (e) => toast.add({ severity: "error", summary: "Failed", detail: firstError(e), life: 4000 }),
        onFinish: () => (saving.value = false),
    };
    id ? router.put(route("installment-plans.update", id), payload, opts) : router.post(route("installment-plans.store"), payload, opts);
}

function destroy(plan) {
    if (!confirm(`Delete plan "${plan.name}"? Existing agreements keep their terms.`)) return;
    router.delete(route("installment-plans.destroy", plan.id), { preserveScroll: true });
}

const freqLabel = (p) => (p.frequency === "custom" ? `every ${p.interval_days} days` : titleCase(p.frequency));
const interestLabel = (p) =>
    p.interest_type === "none" ? "None" : p.interest_type === "flat" ? `Flat ${Number(p.interest_rate)}%` : `Reducing ${Number(p.interest_rate)}% p.a.`;
const feeLabel = (type, v) => (Number(v) > 0 ? (type === "percent" ? `${Number(v)}%` : money(v)) : "-");

// Simple illustration on 50,000 with 10,000 down (flat / none; reducing approximated by its formula)
const example = computed(() => {
    const f = form.value;
    const sale = 50000;
    const down = 10000;
    const fee = f.processing_fee_type === "percent" ? (sale * Number(f.processing_fee || 0)) / 100 : Number(f.processing_fee || 0);
    const financed = sale + fee - down;
    const nInst = Math.max(1, Number(f.number_of_installments || 1));
    let interest = 0;
    if (f.interest_type === "flat") interest = (financed * Number(f.interest_rate || 0)) / 100;
    if (f.interest_type === "reducing" && Number(f.interest_rate) > 0) {
        const days = { weekly: 7, biweekly: 14, custom: Math.max(1, Number(f.interval_days || 30)) }[f.frequency] ?? 30;
        const r = f.frequency === "monthly" ? Number(f.interest_rate) / 100 / 12 : (Number(f.interest_rate) / 100) * days / 365;
        const emi = (financed * r * (1 + r) ** nInst) / ((1 + r) ** nInst - 1);
        interest = emi * nInst - financed;
    }
    const total = financed + interest;
    return { financed, interest, total, each: total / nInst, n: nInst, minDown: (sale * Number(f.min_down_payment_percent || 0)) / 100 };
});

const label = "text-xs text-slate-500 mb-1 block";
</script>

<template>
    <Head title="Installment Plans" />
    <AuthenticatedLayout>
        <div class="p-4 md:p-6 space-y-4">
            <div class="flex flex-wrap justify-between items-start gap-3">
                <div>
                    <h2 class="text-xl font-semibold flex items-center gap-2"><i class="pi pi-sliders-h text-primary" /> Installment Plans</h2>
                    <p class="text-sm text-slate-500">Reusable terms the cashier can pick at the POS (still editable per sale)</p>
                </div>
                <div class="flex gap-2">
                    <Link :href="route('installments.index')"><Button label="Agreements" icon="pi pi-list" severity="secondary" outlined size="small" /></Link>
                    <Button label="New plan" icon="pi pi-plus" size="small" @click="openCreate" />
                </div>
            </div>

            <div class="card">
                <DataTable :value="plans" dataKey="id" size="small" showGridlines scrollable>
                    <template #empty><div class="text-center text-slate-400 py-6">No plans yet. Create one, e.g. "6 months 0%" or "12 months 10% flat".</div></template>
                    <Column header="Name">
                        <template #body="{ data }">
                            <div class="font-semibold">{{ data.name }}</div>
                            <div v-if="data.description" class="text-xs text-slate-500 max-w-xs truncate">{{ data.description }}</div>
                        </template>
                    </Column>
                    <Column header="Installments"><template #body="{ data }">{{ data.number_of_installments }} × {{ freqLabel(data) }}</template></Column>
                    <Column header="Interest"><template #body="{ data }">{{ interestLabel(data) }}</template></Column>
                    <Column header="Min down"><template #body="{ data }">{{ Number(data.min_down_payment_percent) > 0 ? `${Number(data.min_down_payment_percent)}%` : '-' }}</template></Column>
                    <Column header="Processing fee"><template #body="{ data }">{{ feeLabel(data.processing_fee_type, data.processing_fee) }}</template></Column>
                    <Column header="Late fee">
                        <template #body="{ data }">{{ feeLabel(data.late_fee_type, data.late_fee_amount) }}<span v-if="Number(data.late_fee_amount) > 0" class="text-xs text-slate-500"> after {{ data.grace_days }}d</span></template>
                    </Column>
                    <Column header="Status"><template #body="{ data }"><Tag :value="data.is_active ? 'Active' : 'Inactive'" :severity="data.is_active ? 'success' : 'secondary'" /></template></Column>
                    <Column header="">
                        <template #body="{ data }">
                            <div class="flex gap-1">
                                <Button icon="pi pi-pencil" text size="small" @click="openEdit(data)" />
                                <Button icon="pi pi-trash" text size="small" severity="danger" @click="destroy(data)" />
                            </div>
                        </template>
                    </Column>
                </DataTable>
            </div>
        </div>

        <Dialog v-model:visible="show" modal :header="form.id ? 'Edit plan' : 'New plan'" :style="{ width: '720px' }" :breakpoints="{ '960px': '85vw', '640px': '95vw' }">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <div class="md:col-span-2"><label :class="label">Name *</label><InputText v-model="form.name" class="w-full" placeholder="e.g. 12 months 10% flat" /></div>
                <div class="md:col-span-2"><label :class="label">Description</label><Textarea v-model="form.description" rows="2" class="w-full" /></div>
                <div><label :class="label">Number of installments *</label><InputNumber v-model="form.number_of_installments" :min="1" :max="120" class="w-full" inputClass="w-full" /></div>
                <div><label :class="label">Frequency *</label><Select v-model="form.frequency" :options="FREQUENCIES" optionLabel="label" optionValue="value" class="w-full" /></div>
                <div v-if="form.frequency === 'custom'"><label :class="label">Every (days) *</label><InputNumber v-model="form.interval_days" :min="1" :max="365" class="w-full" inputClass="w-full" /></div>
                <div><label :class="label">Interest type *</label><Select v-model="form.interest_type" :options="INTEREST_TYPES" optionLabel="label" optionValue="value" class="w-full" /></div>
                <div v-if="form.interest_type !== 'none'">
                    <label :class="label">{{ form.interest_type === 'flat' ? 'Rate % (whole term)' : 'Annual rate %' }}</label>
                    <InputNumber v-model="form.interest_rate" :min="0" :max="100" :maxFractionDigits="4" suffix="%" class="w-full" inputClass="w-full" />
                </div>
                <div><label :class="label">Minimum down payment %</label><InputNumber v-model="form.min_down_payment_percent" :min="0" :max="100" :maxFractionDigits="2" suffix="%" class="w-full" inputClass="w-full" /></div>
                <div>
                    <label :class="label">Processing fee</label>
                    <div class="flex gap-2">
                        <Select v-model="form.processing_fee_type" :options="FEE_TYPES" optionLabel="label" optionValue="value" class="w-32 shrink-0" />
                        <InputNumber v-model="form.processing_fee" :min="0" :maxFractionDigits="2" class="flex-1 min-w-0" inputClass="w-full" />
                    </div>
                </div>
                <div>
                    <label :class="label">Late fee (per missed installment)</label>
                    <div class="flex gap-2">
                        <Select v-model="form.late_fee_type" :options="FEE_TYPES" optionLabel="label" optionValue="value" class="w-32 shrink-0" />
                        <InputNumber v-model="form.late_fee_amount" :min="0" :maxFractionDigits="2" class="flex-1 min-w-0" inputClass="w-full" />
                    </div>
                </div>
                <div><label :class="label">Grace days before late fee</label><InputNumber v-model="form.grace_days" :min="0" :max="90" class="w-full" inputClass="w-full" /></div>
                <div class="flex items-center gap-2 md:col-span-2"><ToggleSwitch v-model="form.is_active" /><span class="text-sm">Active (shown at POS)</span></div>
            </div>

            <div class="mt-4 p-3 rounded-lg bg-violet-50 border border-violet-200 text-sm text-violet-900">
                <div class="font-semibold mb-1">Example: 50,000 sale with 10,000 down</div>
                Financed {{ money(example.financed) }}<span v-if="example.interest > 0"> + interest {{ money(example.interest) }}</span>
                = {{ money(example.total) }} → <b>{{ example.n }} × {{ money(example.each) }}</b>
                <div v-if="example.minDown > 0" class="text-xs mt-1">Minimum down payment on 50,000: {{ money(example.minDown) }}</div>
            </div>

            <template #footer>
                <Button label="Cancel" text @click="show = false" />
                <Button label="Save" icon="pi pi-check" :loading="saving" @click="save" />
            </template>
        </Dialog>
    </AuthenticatedLayout>
</template>
