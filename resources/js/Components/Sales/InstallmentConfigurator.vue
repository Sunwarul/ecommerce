<script setup>
import axios from "axios";
import { computed, ref, watch } from "vue";
import Button from "primevue/button";
import DatePicker from "primevue/datepicker";
import InputNumber from "primevue/inputnumber";
import InputText from "primevue/inputtext";
import Select from "primevue/select";
import Textarea from "primevue/textarea";
import { money, date } from "@/Helpers/format";
import { applyPlan, FEE_TYPES, FREQUENCIES, INTEREST_TYPES, installmentPayload } from "@/Helpers/installment";

const props = defineProps({
    saleAmount: { type: Number, default: 0 },
    downPayment: { type: Number, default: 0 },
    plans: { type: Array, default: () => [] },
    compact: { type: Boolean, default: false }, // POS side panel
});

const config = defineModel({ type: Object, required: true });
const emit = defineEmits(["preview"]);

const preview = ref(null);
const loading = ref(false);
const error = ref("");
const showSchedule = ref(!props.compact);
const showKyc = ref(false);

const planOptions = computed(() => [{ id: null, name: "Custom terms" }, ...props.plans]);

function onPlanChange(id) {
    const plan = props.plans.find((p) => p.id === id);
    config.value = plan ? applyPlan(config.value, plan) : { ...config.value, installment_plan_id: null };
}

let timer = null;
let seq = 0;
async function refresh() {
    if (props.saleAmount <= 0) {
        preview.value = null;
        emit("preview", null);
        return;
    }
    const mine = ++seq;
    loading.value = true;
    error.value = "";
    try {
        const { data } = await axios.post(route("installments.preview"), {
            sale_amount: props.saleAmount,
            down_payment: props.downPayment,
            installment: installmentPayload(config.value),
        });
        if (mine !== seq) return;
        preview.value = data;
        emit("preview", data);
    } catch (e) {
        if (mine !== seq) return;
        const errs = e?.response?.data?.errors;
        error.value = errs ? Object.values(errs)[0][0] : "Could not calculate schedule";
        preview.value = null;
        emit("preview", null);
    } finally {
        if (mine === seq) loading.value = false;
    }
}

watch(
    () => [props.saleAmount, props.downPayment, JSON.stringify(installmentPayload(config.value))],
    () => {
        clearTimeout(timer);
        timer = setTimeout(refresh, 350);
    },
    { immediate: true }
);

const belowMin = computed(() => preview.value && preview.value.min_down_payment > 0 && props.downPayment + 0.009 < preview.value.min_down_payment);
const labelCls = "text-[11px] text-slate-500 mb-1 block";
const grid = computed(() => (props.compact ? "grid grid-cols-2 gap-2" : "grid grid-cols-2 md:grid-cols-4 gap-3"));
</script>

<template>
    <div class="space-y-3">
        <div :class="grid">
            <div class="col-span-2">
                <label :class="labelCls">Plan</label>
                <Select :modelValue="config.installment_plan_id" @update:modelValue="onPlanChange" :options="planOptions"
                    optionLabel="name" optionValue="id" class="w-full" size="small" />
            </div>
            <div>
                <label :class="labelCls">Installments</label>
                <InputNumber v-model="config.number_of_installments" :min="1" :max="120" class="w-full" inputClass="w-full" size="small" />
            </div>
            <div>
                <label :class="labelCls">Frequency</label>
                <Select v-model="config.frequency" :options="FREQUENCIES" optionLabel="label" optionValue="value" class="w-full" size="small" />
            </div>
            <div v-if="config.frequency === 'custom'">
                <label :class="labelCls">Every (days)</label>
                <InputNumber v-model="config.interval_days" :min="1" :max="365" class="w-full" inputClass="w-full" size="small" />
            </div>
            <div>
                <label :class="labelCls">First due date</label>
                <DatePicker v-model="config.first_due_date" dateFormat="dd-mm-yy" class="w-full" size="small" showIcon />
            </div>
            <div :class="compact ? 'col-span-2' : ''">
                <label :class="labelCls">Interest</label>
                <Select v-model="config.interest_type" :options="INTEREST_TYPES" optionLabel="label" optionValue="value" class="w-full" size="small" />
            </div>
            <div v-if="config.interest_type !== 'none'">
                <label :class="labelCls">Rate %</label>
                <InputNumber v-model="config.interest_rate" :min="0" :max="100" :maxFractionDigits="4" suffix="%" class="w-full" inputClass="w-full" size="small" />
            </div>
            <div>
                <label :class="labelCls">Processing fee</label>
                <div class="flex gap-1">
                    <Select v-model="config.processing_fee_type" :options="FEE_TYPES" optionLabel="label" optionValue="value" class="w-24 shrink-0" size="small" />
                    <InputNumber v-model="config.processing_fee" :min="0" :maxFractionDigits="2" class="flex-1 min-w-0" inputClass="w-full" size="small" />
                </div>
            </div>
            <div>
                <label :class="labelCls">Late fee</label>
                <div class="flex gap-1">
                    <Select v-model="config.late_fee_type" :options="FEE_TYPES" optionLabel="label" optionValue="value" class="w-24 shrink-0" size="small" />
                    <InputNumber v-model="config.late_fee_amount" :min="0" :maxFractionDigits="2" class="flex-1 min-w-0" inputClass="w-full" size="small" />
                </div>
            </div>
            <div>
                <label :class="labelCls">Grace days</label>
                <InputNumber v-model="config.grace_days" :min="0" :max="90" class="w-full" inputClass="w-full" size="small" />
            </div>
        </div>

        <!-- Summary -->
        <div class="rounded-xl border p-3 text-xs space-y-1" :class="error ? 'border-rose-200 bg-rose-50' : 'border-violet-200 bg-violet-50'">
            <div v-if="error" class="text-rose-700">{{ error }}</div>
            <div v-else-if="!preview" class="text-slate-500">
                <i v-if="loading" class="pi pi-spin pi-spinner mr-1" />{{ loading ? "Calculating…" : "Add items to see the schedule" }}
            </div>
            <template v-else>
                <div class="flex justify-between"><span class="text-slate-600">Sale amount</span><span>{{ money(preview.sale_amount) }}</span></div>
                <div class="flex justify-between"><span class="text-slate-600">Down payment (paid now)</span><span>- {{ money(preview.down_payment) }}</span></div>
                <div v-if="preview.processing_fee > 0" class="flex justify-between"><span class="text-slate-600">Processing fee</span><span>+ {{ money(preview.processing_fee) }}</span></div>
                <div class="flex justify-between"><span class="text-slate-600">Financed</span><span class="font-semibold">{{ money(preview.financed_amount) }}</span></div>
                <div v-if="preview.interest_amount > 0" class="flex justify-between"><span class="text-slate-600">Interest</span><span>+ {{ money(preview.interest_amount) }}</span></div>
                <div class="flex justify-between border-t border-violet-200 pt-1 mt-1 text-sm">
                    <span class="font-semibold text-violet-900">{{ preview.schedules.length }} × {{ money(preview.installment_amount) }}</span>
                    <span class="font-bold text-violet-900">{{ money(preview.total_payable) }}</span>
                </div>
                <div class="text-slate-500">Customer pays in total: <b>{{ money(preview.grand_total) }}</b></div>
                <div v-if="belowMin" class="text-rose-700 font-medium">
                    <i class="pi pi-exclamation-triangle mr-1" />Plan requires a minimum down payment of {{ money(preview.min_down_payment) }}
                </div>
                <div v-if="preview.financed_amount <= 0" class="text-rose-700 font-medium">Nothing left to finance — reduce the payment.</div>
            </template>
        </div>

        <div v-if="preview?.schedules?.length">
            <Button :label="showSchedule ? 'Hide schedule' : 'Show schedule'" :icon="showSchedule ? 'pi pi-chevron-up' : 'pi pi-chevron-down'"
                text size="small" class="!px-1 !py-0.5 !text-xs" @click="showSchedule = !showSchedule" />
            <div v-if="showSchedule" class="max-h-56 overflow-y-auto border border-slate-200 rounded-lg mt-1">
                <table class="w-full text-xs tabular-nums">
                    <thead class="bg-slate-50 sticky top-0">
                        <tr class="text-slate-500">
                            <th class="text-left px-2 py-1">#</th>
                            <th class="text-left px-2 py-1">Due date</th>
                            <th v-if="!compact" class="text-right px-2 py-1">Principal</th>
                            <th v-if="!compact" class="text-right px-2 py-1">Interest</th>
                            <th class="text-right px-2 py-1">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="s in preview.schedules" :key="s.installment_no" class="border-t border-slate-100">
                            <td class="px-2 py-1">{{ s.installment_no }}</td>
                            <td class="px-2 py-1">{{ date(s.due_date) }}</td>
                            <td v-if="!compact" class="px-2 py-1 text-right">{{ money(s.principal) }}</td>
                            <td v-if="!compact" class="px-2 py-1 text-right">{{ money(s.interest) }}</td>
                            <td class="px-2 py-1 text-right font-medium">{{ money(s.amount) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- KYC / guarantor -->
        <div>
            <Button :label="showKyc ? 'Hide NID & guarantor' : 'NID & guarantor (optional)'" icon="pi pi-id-card" text size="small"
                class="!px-1 !py-0.5 !text-xs" @click="showKyc = !showKyc" />
            <div v-if="showKyc" :class="grid" class="mt-2">
                <div :class="compact ? 'col-span-2' : ''">
                    <label :class="labelCls">Customer NID</label>
                    <InputText v-model="config.customer_nid" class="w-full" size="small" />
                </div>
                <div>
                    <label :class="labelCls">Guarantor name</label>
                    <InputText v-model="config.guarantor_name" class="w-full" size="small" />
                </div>
                <div>
                    <label :class="labelCls">Guarantor phone</label>
                    <InputText v-model="config.guarantor_phone" class="w-full" size="small" />
                </div>
                <div>
                    <label :class="labelCls">Guarantor NID</label>
                    <InputText v-model="config.guarantor_nid" class="w-full" size="small" />
                </div>
                <div class="col-span-2">
                    <label :class="labelCls">Guarantor address</label>
                    <InputText v-model="config.guarantor_address" class="w-full" size="small" />
                </div>
                <div class="col-span-2 md:col-span-4" :class="compact ? '!col-span-2' : ''">
                    <label :class="labelCls">Agreement notes</label>
                    <Textarea v-model="config.notes" rows="2" class="w-full text-xs" />
                </div>
            </div>
        </div>
    </div>
</template>
