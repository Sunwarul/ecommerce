<script setup>
import { computed, watch } from "vue";
import InputText from "primevue/inputtext";
import Select from "primevue/select";
import { money } from "@/Helpers/format";

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
    label: `${t.months} months${Number(t.customer_charge_percent) > 0 ? ` (+${Number(t.customer_charge_percent)}%)` : " (0%)"}`,
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
        <div class="text-xs font-semibold text-sky-900 flex items-center gap-1.5"><i class="pi pi-credit-card" /> Bank EMI</div>
        <div v-if="!banks.length" class="text-[11px] text-slate-600">No EMI banks configured. Add them under Sales → Bank EMI.</div>
        <template v-else>
            <div class="grid grid-cols-2 gap-2">
                <Select v-model="emi.emi_bank_id" :options="banks" optionLabel="name" optionValue="id" placeholder="Bank" filter class="w-full text-xs" size="small" />
                <Select v-model="emi.emi_bank_tenure_id" :options="tenures" optionLabel="label" optionValue="id" placeholder="Tenure" :disabled="!bank" class="w-full text-xs" size="small" />
                <InputText v-model="emi.card_last4" placeholder="Card last 4" maxlength="4" class="w-full text-xs" size="small" />
                <InputText v-model="emi.approval_code" placeholder="Approval code" class="w-full text-xs" size="small" />
                <InputText v-model="emi.card_holder_name" placeholder="Card holder name" class="w-full text-xs col-span-2" size="small" />
            </div>
            <div v-if="quote" class="text-[11px] text-sky-900 space-y-0.5">
                <div v-if="quote.customerCharge > 0" class="flex justify-between"><span>Bank EMI charge</span><span>+ {{ money(quote.customerCharge) }}</span></div>
                <div class="flex justify-between font-semibold"><span>Swipe on card</span><span>{{ money(quote.card) }}</span></div>
                <div class="flex justify-between"><span>Customer pays bank</span><span>{{ tenure.months }} × {{ money(quote.monthly) }}</span></div>
                <div v-if="quote.belowMin" class="text-rose-700 font-medium">Minimum for this tenure is {{ money(tenure.min_amount) }}</div>
            </div>
        </template>
    </div>
</template>
