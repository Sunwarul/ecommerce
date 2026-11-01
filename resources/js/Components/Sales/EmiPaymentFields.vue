<script setup>
import { computed, watch } from "vue";
import InputText from "primevue/inputtext";
import Select from "primevue/select";
import { useI18n } from "vue-i18n";
import { money } from "@/Helpers/format";

const { t } = useI18n();
const tr = t;

/**
 * Bank card EMI details for one payment row. The row "amount" is the part of the bill put on EMI;
 * the bank's customer charge (if any) is added on top and swiped on the card.
 */
const props = defineProps({
    banks: { type: Array, default: () => [] },
    amount: { type: Number, default: 0 },
});

const emi = defineModel({ type: Object, required: true });

const bank = computed(() => props.banks.find((b) => b.id === emi.value.emi_bank_id) || null);
const tenures = computed(() => (bank.value?.tenures || []).map((t) => ({
    ...t,
    label: `${tr("emi.months_count", { n: t.months })} (${Number(t.customer_charge_percent) > 0 ? "+" + Number(t.customer_charge_percent) : 0}%)`,
})));
const tenure = computed(() => tenures.value.find((t) => t.id === emi.value.emi_bank_tenure_id) || null);

watch(() => emi.value.emi_bank_id, () => {
    if (tenure.value?.emi_bank_id !== emi.value.emi_bank_id) emi.value.emi_bank_tenure_id = null;
});

// mirrors EmiService::quote()
const quote = computed(() => {
    if (!tenure.value) return null;
    const principal = Number(props.amount || 0);
    const customerCharge = Math.round(principal * Number(tenure.value.customer_charge_percent) / 100 * 100) / 100;
    const card = principal + customerCharge;
    return {
        customerCharge,
        card,
        monthly: card / tenure.value.months,
        belowMin: principal < Number(tenure.value.min_amount || 0),
    };
});

defineExpose({ quote });
</script>

<template>
    <div class="mt-2 p-2.5 rounded-xl bg-sky-50 border border-sky-200 space-y-2">
        <div class="text-xs font-semibold text-sky-900 flex items-center gap-1.5"><i class="pi pi-credit-card" /> {{ t('shared.sale_type.emi') }}</div>
        <div v-if="!banks.length" class="text-[11px] text-slate-600">{{ t('emi.fields.no_banks') }}</div>
        <template v-else>
            <div class="grid grid-cols-2 gap-2">
                <Select v-model="emi.emi_bank_id" :options="banks" optionLabel="name" optionValue="id" :placeholder="t('emi.fields.bank')" filter class="w-full text-xs" size="small" />
                <Select v-model="emi.emi_bank_tenure_id" :options="tenures" optionLabel="label" optionValue="id" :placeholder="t('emi.fields.tenure')" :disabled="!bank" class="w-full text-xs" size="small" />
                <InputText v-model="emi.card_last4" :placeholder="t('emi.fields.card_last4')" maxlength="4" inputmode="numeric" class="w-full text-xs" size="small" />
                <InputText v-model="emi.approval_code" :placeholder="t('emi.fields.approval_code')" class="w-full text-xs" size="small" />
                <InputText v-model="emi.card_holder_name" :placeholder="t('emi.fields.card_holder')" class="w-full text-xs col-span-2" size="small" />
            </div>
            <div v-if="quote" class="text-[11px] text-sky-900 space-y-0.5">
                <div v-if="quote.customerCharge > 0" class="flex justify-between"><span>{{ t('emi.fields.bank_charge') }}</span><span class="ltr-nums">+ {{ money(quote.customerCharge) }}</span></div>
                <div class="flex justify-between font-semibold"><span>{{ t('emi.fields.swipe_on_card') }}</span><span class="ltr-nums">{{ money(quote.card) }}</span></div>
                <div class="flex justify-between"><span>{{ t('emi.fields.customer_pays_bank') }}</span><span class="ltr-nums">{{ tenure.months }} × {{ money(quote.monthly) }}</span></div>
                <div v-if="quote.belowMin" class="text-rose-700 font-medium">{{ t('emi.fields.min_for_tenure', { amount: money(tenure.min_amount) }) }}</div>
            </div>
        </template>
    </div>
</template>
