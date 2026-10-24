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
import { useI18n } from "vue-i18n";
import { firstError, money } from "@/Helpers/format";
import { feeTypeOptions, frequencyOptions, interestTypeOptions } from "@/Helpers/installment";

useFlashToast({ error: false }); // errors are toasted by the layout
const toast = useToast();
const { t } = useI18n();
const FREQUENCIES = computed(() => frequencyOptions(t));
const INTEREST_TYPES = computed(() => interestTypeOptions(t));
const FEE_TYPES = computed(() => feeTypeOptions(t));

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
        onError: (e) => toast.add({ severity: "error", summary: t("shared.terms.error"), detail: firstError(e), life: 4000 }),
        onFinish: () => (saving.value = false),
    };
    id ? router.put(route("installment-plans.update", id), payload, opts) : router.post(route("installment-plans.store"), payload, opts);
}

function destroy(plan) {
    if (!confirm(t("installments.plans.confirm_delete", { name: plan.name }))) return;
    router.delete(route("installment-plans.destroy", plan.id), { preserveScroll: true });
}

const freqLabel = (p) => (p.frequency === "custom" ? t("installments.plans.every_n_days", { n: p.interval_days }) : t(`shared.frequency.${p.frequency}`));
const interestLabel = (p) =>
    p.interest_type === "none"
        ? t("shared.interest.none")
        : p.interest_type === "flat"
            ? t("installments.show.flat_rate", { rate: Number(p.interest_rate) })
            : t("installments.show.reducing_rate", { rate: Number(p.interest_rate) });
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
    <Head :title="t('installments.plans.title')" />
    <AuthenticatedLayout>
        <div class="p-4 md:p-6 space-y-4">
            <div class="flex flex-wrap justify-between items-start gap-3">
                <div>
                    <h1 class="text-xl font-semibold text-slate-800 flex items-center gap-2"><i class="pi pi-sliders-h text-primary" /> {{ t('installments.plans.title') }}</h1>
                    <p class="text-sm text-slate-500">{{ t('installments.plans.subtitle') }}</p>
                </div>
                <div class="flex gap-2">
                    <Link :href="route('installments.index')"><Button :label="t('installments.index.agreements')" icon="pi pi-list" severity="secondary" outlined size="small" /></Link>
                    <Button :label="t('installments.plans.new_plan')" icon="pi pi-plus" size="small" @click="openCreate" />
                </div>
            </div>

            <div class="card">
                <DataTable :value="plans" dataKey="id" size="small" showGridlines scrollable>
                    <template #empty><div class="text-center text-slate-400 py-6">{{ t('installments.plans.empty') }}</div></template>
                    <Column :header="t('common.name')">
                        <template #body="{ data }">
                            <div class="font-semibold">{{ data.name }}</div>
                            <div v-if="data.description" class="text-xs text-slate-500 max-w-xs truncate">{{ data.description }}</div>
                        </template>
                    </Column>
                    <Column :header="t('installments.config.installments')"><template #body="{ data }">{{ data.number_of_installments }} × {{ freqLabel(data) }}</template></Column>
                    <Column :header="t('installments.config.interest')"><template #body="{ data }">{{ interestLabel(data) }}</template></Column>
                    <Column :header="t('installments.plans.min_down')"><template #body="{ data }">{{ Number(data.min_down_payment_percent) > 0 ? `${Number(data.min_down_payment_percent)}%` : '-' }}</template></Column>
                    <Column :header="t('installments.config.processing_fee')"><template #body="{ data }">{{ feeLabel(data.processing_fee_type, data.processing_fee) }}</template></Column>
                    <Column :header="t('installments.config.late_fee')">
                        <template #body="{ data }">{{ feeLabel(data.late_fee_type, data.late_fee_amount) }}<span v-if="Number(data.late_fee_amount) > 0" class="text-xs text-slate-500"> · {{ t('installments.plans.grace_short', { n: data.grace_days }) }}</span></template>
                    </Column>
                    <Column :header="t('common.status')"><template #body="{ data }"><Tag :value="data.is_active ? t('shared.status.active') : t('shared.status.inactive')" :severity="data.is_active ? 'success' : 'secondary'" /></template></Column>
                    <Column header="">
                        <template #body="{ data }">
                            <div class="flex gap-1">
                                <Button icon="pi pi-pencil" text size="small" :aria-label="t('common.edit')" @click="openEdit(data)" />
                                <Button icon="pi pi-trash" text size="small" severity="danger" :aria-label="t('common.delete')" @click="destroy(data)" />
                            </div>
                        </template>
                    </Column>
                </DataTable>
            </div>
        </div>

        <Dialog v-model:visible="show" modal :header="form.id ? t('installments.plans.edit_plan') : t('installments.plans.new_plan')" :style="{ width: '720px' }" :breakpoints="{ '960px': '85vw', '640px': '95vw' }">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <div class="md:col-span-2"><label :class="label">{{ t('common.name') }} *</label><InputText v-model="form.name" dir="auto" class="w-full" :placeholder="t('installments.plans.name_placeholder')" /></div>
                <div class="md:col-span-2"><label :class="label">{{ t('common.description') }}</label><Textarea v-model="form.description" rows="2" dir="auto" class="w-full" /></div>
                <div><label :class="label">{{ t('installments.plans.number_of_installments') }} *</label><InputNumber v-model="form.number_of_installments" :min="1" :max="120" class="w-full" inputClass="w-full" /></div>
                <div><label :class="label">{{ t('installments.config.frequency') }} *</label><Select v-model="form.frequency" :options="FREQUENCIES" optionLabel="label" optionValue="value" class="w-full" /></div>
                <div v-if="form.frequency === 'custom'"><label :class="label">{{ t('installments.config.every_days') }} *</label><InputNumber v-model="form.interval_days" :min="1" :max="365" class="w-full" inputClass="w-full" /></div>
                <div><label :class="label">{{ t('installments.plans.interest_type') }} *</label><Select v-model="form.interest_type" :options="INTEREST_TYPES" optionLabel="label" optionValue="value" class="w-full" /></div>
                <div v-if="form.interest_type !== 'none'">
                    <label :class="label">{{ form.interest_type === 'flat' ? t('installments.plans.rate_whole_term') : t('installments.plans.annual_rate') }}</label>
                    <InputNumber v-model="form.interest_rate" :min="0" :max="100" :maxFractionDigits="4" suffix="%" class="w-full" inputClass="w-full" />
                </div>
                <div><label :class="label">{{ t('installments.plans.min_down_percent') }}</label><InputNumber v-model="form.min_down_payment_percent" :min="0" :max="100" :maxFractionDigits="2" suffix="%" class="w-full" inputClass="w-full" /></div>
                <div>
                    <label :class="label">{{ t('installments.config.processing_fee') }}</label>
                    <div class="flex gap-2">
                        <Select v-model="form.processing_fee_type" :options="FEE_TYPES" optionLabel="label" optionValue="value" class="w-32 shrink-0" />
                        <InputNumber v-model="form.processing_fee" :min="0" :maxFractionDigits="2" class="flex-1 min-w-0" inputClass="w-full" />
                    </div>
                </div>
                <div>
                    <label :class="label">{{ t('installments.plans.late_fee_per_missed') }}</label>
                    <div class="flex gap-2">
                        <Select v-model="form.late_fee_type" :options="FEE_TYPES" optionLabel="label" optionValue="value" class="w-32 shrink-0" />
                        <InputNumber v-model="form.late_fee_amount" :min="0" :maxFractionDigits="2" class="flex-1 min-w-0" inputClass="w-full" />
                    </div>
                </div>
                <div><label :class="label">{{ t('installments.plans.grace_days_before') }}</label><InputNumber v-model="form.grace_days" :min="0" :max="90" class="w-full" inputClass="w-full" /></div>
                <div class="flex items-center gap-2 md:col-span-2"><ToggleSwitch v-model="form.is_active" /><span class="text-sm">{{ t('installments.plans.active_at_pos') }}</span></div>
            </div>

            <div class="mt-4 p-3 rounded-lg bg-violet-50 border border-violet-200 text-sm text-violet-900">
                <div class="font-semibold mb-1">{{ t('installments.plans.example_title') }}</div>
                <span class="ltr-nums">{{ t('installments.config.financed') }} {{ money(example.financed) }}<span v-if="example.interest > 0"> + {{ t('installments.config.interest') }} {{ money(example.interest) }}</span>
                = {{ money(example.total) }} → <b>{{ example.n }} × {{ money(example.each) }}</b></span>
                <div v-if="example.minDown > 0" class="text-xs mt-1">{{ t('installments.plans.example_min_down', { amount: money(example.minDown) }) }}</div>
            </div>

            <template #footer>
                <Button :label="t('shared.terms.cancel')" text @click="show = false" />
                <Button :label="t('shared.terms.save')" icon="pi pi-check" :loading="saving" :disabled="!form.name" @click="save" />
            </template>
        </Dialog>
    </AuthenticatedLayout>
</template>
