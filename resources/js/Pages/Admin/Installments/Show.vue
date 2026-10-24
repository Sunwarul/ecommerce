<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, router } from "@inertiajs/vue3";
import { computed, ref } from "vue";
import Button from "primevue/button";
import Dialog from "primevue/dialog";
import InputNumber from "primevue/inputnumber";
import InputText from "primevue/inputtext";
import ProgressBar from "primevue/progressbar";
import Select from "primevue/select";
import Tag from "primevue/tag";
import Textarea from "primevue/textarea";
import { useToast } from "primevue/usetoast";
import { useFlashToast } from "@/Composables/useFlashToast";
import { useI18n } from "vue-i18n";
import useAuth from "@/Composables/useAuth";
import { PERMISSIONS } from "@/constants/permissions";
import { date, dateTime, firstError, money } from "@/Helpers/format";

useFlashToast({ error: false }); // errors are toasted by the layout
const toast = useToast();
const { t } = useI18n();
const { hasPermission } = useAuth();
const canManageStatus = computed(() => hasPermission(PERMISSIONS.INSTALLMENT.CANCEL));
const canEditInfo = computed(() => hasPermission(PERMISSIONS.INSTALLMENT.CREATE));

const props = defineProps({
    sale: { type: Object, required: true },
    paymentMethods: { type: Array, default: () => [] },
});

const n = (v) => Number(v || 0);
const r2 = (v) => Math.round(v * 100) / 100;

const schedules = computed(() => props.sale.schedules || []);
const unpaid = computed(() => schedules.value.filter((s) => ["pending", "partial"].includes(s.status)));
const nextSchedule = computed(() => unpaid.value[0] || null);
const balance = computed(() => n(props.sale.balance));
const progress = computed(() => {
    const p = n(props.sale.total_payable);
    return p > 0 ? Math.min(100, Math.round((n(props.sale.paid_amount) / p) * 100)) : 0;
});
const canCollect = computed(() => props.sale.status === "active" && balance.value > 0 && hasPermission(PERMISSIONS.INSTALLMENT.COLLECT));

function statusSeverity(s) {
    return { active: "info", completed: "success", defaulted: "danger", cancelled: "secondary", paid: "success", partial: "warn", pending: "warn" }[s] || "secondary";
}

// ---------------- Collect ----------------
let rowId = 1;
const blankRow = () => ({ id: rowId++, payment_method_id: props.paymentMethods[0]?.id ?? null, amount: 0, transaction_ref: "", notes: "" });
const rows = ref([blankRow()]);
const collecting = ref(false);

const collectTotal = computed(() => rows.value.reduce((s, r) => s + n(r.amount), 0));

function setFirstAmount(v) {
    if (!rows.value.length) rows.value.push(blankRow());
    rows.value = [{ ...rows.value[0], amount: r2(v) }];
}
const fillNext = () => nextSchedule.value && setFirstAmount(n(nextSchedule.value.remaining));
const fillFull = () => setFirstAmount(balance.value);

// mirrors InstallmentService::collect(): oldest first, late fee before principal
const allocation = computed(() => {
    let remaining = r2(collectTotal.value);
    const out = [];
    for (const s of unpaid.value) {
        if (remaining <= 0) break;
        const feeDue = r2(n(s.late_fee) - n(s.late_fee_paid));
        const amtDue = r2(n(s.amount) - n(s.paid_amount));
        const fee = Math.min(feeDue, remaining);
        remaining = r2(remaining - fee);
        const amt = Math.min(amtDue, remaining);
        remaining = r2(remaining - amt);
        if (fee > 0 || amt > 0) out.push({ no: s.installment_no, fee, amt, full: fee >= feeDue && amt >= amtDue });
    }
    return { rows: out, excess: remaining };
});

function submitCollect() {
    const payments = rows.value
        .filter((r) => r.payment_method_id && n(r.amount) > 0)
        .map((r) => ({ payment_method_id: r.payment_method_id, amount: n(r.amount), transaction_ref: r.transaction_ref || null, notes: r.notes || null }));
    if (!payments.length) {
        toast.add({ severity: "warn", summary: t("installments.show.amount_required"), detail: t("installments.show.enter_method_amount"), life: 2500 });
        return;
    }
    if (collectTotal.value > balance.value + 0.009) {
        toast.add({ severity: "warn", summary: t("installments.show.too_much"), detail: t("installments.show.outstanding_is", { amount: money(balance.value) }), life: 3000 });
        return;
    }
    collecting.value = true;
    router.post(route("installments.collect", props.sale.id), { payments }, {
        preserveScroll: true,
        onSuccess: () => (rows.value = [blankRow()]),
        onError: (e) => toast.add({ severity: "error", summary: t("shared.terms.error"), detail: firstError(e) || t("installments.show.collect_failed"), life: 4000 }),
        onFinish: () => (collecting.value = false),
    });
}

function waive(s) {
    if (!confirm(t("installments.show.confirm_waive", { amount: money(n(s.late_fee) - n(s.late_fee_paid)), no: s.installment_no }))) return;
    router.post(route("installments.waive-late-fee", s.id), {}, {
        preserveScroll: true,
        onError: (e) => toast.add({ severity: "error", summary: t("shared.terms.error"), detail: firstError(e), life: 3000 }),
    });
}

// ---------------- Status ----------------
function setStatus(status, msg) {
    if (!confirm(msg)) return;
    router.put(route("installments.status", props.sale.id), { status }, {
        preserveScroll: true,
        onError: (e) => toast.add({ severity: "error", summary: t("shared.terms.error"), detail: firstError(e), life: 3000 }),
    });
}

// ---------------- Edit agreement info ----------------
const showEdit = ref(false);
const editForm = ref({});
const saving = ref(false);
function openEdit() {
    const s = props.sale;
    editForm.value = {
        customer_nid: s.customer_nid || "",
        guarantor_name: s.guarantor_name || "",
        guarantor_phone: s.guarantor_phone || "",
        guarantor_nid: s.guarantor_nid || "",
        guarantor_address: s.guarantor_address || "",
        notes: s.notes || "",
    };
    showEdit.value = true;
}
function saveEdit() {
    saving.value = true;
    router.put(route("installments.update", props.sale.id), editForm.value, {
        preserveScroll: true,
        onSuccess: () => (showEdit.value = false),
        onError: (e) => toast.add({ severity: "error", summary: t("shared.terms.error"), detail: firstError(e), life: 3000 }),
        onFinish: () => (saving.value = false),
    });
}

const printPage = () => window.print();

const summary = computed(() => [
    [t("installments.config.sale_amount"), props.sale.sale_amount],
    [t("shared.terms.down_payment"), props.sale.down_payment],
    [t("installments.config.processing_fee"), props.sale.processing_fee],
    [t("installments.config.financed"), props.sale.financed_amount],
    [t("installments.config.interest"), props.sale.interest_amount],
    [t("installments.total_payable"), props.sale.total_payable],
    [t("shared.terms.paid"), props.sale.paid_amount],
    [t("installments.show.late_fees_charged_paid"), null, `${money(props.sale.late_fee_charged)} / ${money(props.sale.late_fee_paid)}`],
]);

const interestLabel = computed(() => {
    const s = props.sale;
    if (s.interest_type === "none") return t("shared.interest.none");
    return s.interest_type === "flat"
        ? t("installments.show.flat_rate", { rate: n(s.interest_rate) })
        : t("installments.show.reducing_rate", { rate: n(s.interest_rate) });
});
</script>

<template>
    <Head :title="`${t('installments.agreement')} ${sale.agreement_no}`" />
    <AuthenticatedLayout>
        <div class="p-4 md:p-6 space-y-4">
            <!-- Actions -->
            <div class="flex flex-wrap justify-between gap-2 print:hidden">
                <Link :href="route('installments.index')"><Button :label="t('shared.terms.back')" icon="pi pi-arrow-left" severity="secondary" size="small" /></Link>
                <div class="flex flex-wrap gap-2">
                    <Button :label="t('shared.terms.print')" icon="pi pi-print" severity="secondary" outlined size="small" @click="printPage" />
                    <template v-if="canManageStatus">
                        <Button v-if="sale.status === 'active'" :label="t('installments.show.mark_defaulted')" icon="pi pi-flag" severity="warn" outlined size="small"
                            @click="setStatus('defaulted', t('installments.show.confirm_defaulted'))" />
                        <Button v-if="sale.status === 'defaulted' || sale.status === 'cancelled'" :label="t('installments.show.reactivate')" icon="pi pi-replay" severity="info" outlined size="small"
                            @click="setStatus('active', t('installments.show.confirm_reactivate'))" />
                        <Button v-if="sale.status === 'active' || sale.status === 'defaulted'" :label="t('installments.show.cancel_agreement')" icon="pi pi-ban" severity="danger" outlined size="small"
                            @click="setStatus('cancelled', t('installments.show.confirm_cancel'))" />
                    </template>
                </div>
            </div>

            <div id="print-area" class="space-y-4">
                <!-- Header -->
                <div class="card p-4 flex flex-wrap justify-between gap-4">
                    <div>
                        <div class="flex items-center gap-2">
                            <h1 class="text-xl font-bold text-slate-800 ltr-nums">{{ sale.agreement_no }}</h1>
                            <Tag :value="t('shared.status.' + sale.status)" :severity="statusSeverity(sale.status)" />
                        </div>
                        <div class="text-sm text-slate-500 mt-1">
                            {{ t('installments.show.created_at', { time: dateTime(sale.created_at) }) }}<span v-if="sale.creator"> · {{ sale.creator.name }}</span>
                        </div>
                        <div class="text-sm mt-1">
                            {{ t('shared.terms.invoice') }}:
                            <a :href="route('pos.orders.invoice', sale.order_id)" target="_blank" class="text-primary font-medium">{{ sale.order?.invoice_no || `#${sale.order_id}` }}</a>
                        </div>
                    </div>
                    <div class="text-end">
                        <div class="text-xs text-slate-500 uppercase">{{ t('shared.terms.customer') }}</div>
                        <div class="font-semibold">{{ sale.customer?.name }}</div>
                        <div class="text-sm text-slate-600 ltr-nums">{{ sale.customer?.phone }}</div>
                        <div v-if="sale.customer?.address" class="text-xs text-slate-500 max-w-xs">{{ sale.customer.address }}</div>
                    </div>
                </div>

                <!-- Summary -->
                <div class="card p-4">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                        <div v-for="[label, value, text] in summary" :key="label" class="p-3 rounded-lg bg-slate-50 border border-slate-100">
                            <div class="text-xs text-slate-500">{{ label }}</div>
                            <div class="font-semibold tabular-nums ltr-nums text-start">{{ text ?? money(value) }}</div>
                        </div>
                    </div>
                    <div class="mt-4 flex flex-wrap items-center gap-4">
                        <div class="flex-1 min-w-[200px]">
                            <div class="flex justify-between text-xs text-slate-500 mb-1"><span>{{ t('installments.show.repaid') }}</span><span>{{ progress }}%</span></div>
                            <ProgressBar :value="progress" :showValue="false" style="height: 8px" />
                        </div>
                        <div class="text-end">
                            <div class="text-xs text-slate-500">{{ t('shared.terms.balance') }}</div>
                            <div class="text-2xl font-bold ltr-nums" :class="balance > 0 ? 'text-rose-600' : 'text-emerald-600'">{{ money(balance) }}</div>
                        </div>
                    </div>
                    <div class="mt-3 text-xs text-slate-500">
                        {{ sale.number_of_installments }} × {{ money(sale.installment_amount) }} · {{ t('shared.frequency.' + sale.frequency) }}<span v-if="sale.frequency === 'custom'"> ({{ t('installments.show.n_days', { n: sale.interval_days }) }})</span>
                        · {{ interestLabel }}
                        <span v-if="n(sale.late_fee_amount) > 0"> · {{ t('installments.show.late_fee_rule', { fee: sale.late_fee_type === 'percent' ? `${n(sale.late_fee_amount)}%` : money(sale.late_fee_amount), days: sale.grace_days }) }}</span>
                        <span v-if="sale.plan"> · {{ t('installments.config.plan') }}: {{ sale.plan.name }}</span>
                    </div>
                </div>

                <div class="grid grid-cols-1 xl:grid-cols-3 gap-4">
                    <!-- Schedule -->
                    <div class="card p-4 xl:col-span-2 overflow-x-auto">
                        <h2 class="font-semibold text-slate-800 mb-3">{{ t('installments.show.schedule') }}</h2>
                        <table class="w-full text-sm tabular-nums">
                            <thead>
                                <tr class="text-start text-xs text-slate-500 border-b">
                                    <th class="py-2 pe-2 text-start">#</th>
                                    <th class="py-2 pe-2 text-start">{{ t('installments.due_date') }}</th>
                                    <th class="py-2 pe-2 text-end">{{ t('shared.terms.amount') }}</th>
                                    <th class="py-2 pe-2 text-end">{{ t('installments.config.late_fee') }}</th>
                                    <th class="py-2 pe-2 text-end">{{ t('shared.terms.paid') }}</th>
                                    <th class="py-2 pe-2 text-end">{{ t('installments.remaining') }}</th>
                                    <th class="py-2 pe-2 text-start">{{ t('common.status') }}</th>
                                    <th class="py-2 print:hidden"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="s in schedules" :key="s.id" class="border-b border-slate-100" :class="s.is_overdue ? 'bg-rose-50/50' : ''">
                                    <td class="py-2 pe-2">{{ s.installment_no }}</td>
                                    <td class="py-2 pe-2 whitespace-nowrap" :class="s.is_overdue ? 'text-rose-600 font-semibold' : ''">{{ date(s.due_date) }}</td>
                                    <td class="py-2 pe-2 text-end">{{ money(s.amount) }}</td>
                                    <td class="py-2 pe-2 text-end">{{ n(s.late_fee) > 0 ? money(s.late_fee) : '-' }}</td>
                                    <td class="py-2 pe-2 text-end">{{ money(n(s.paid_amount) + n(s.late_fee_paid)) }}</td>
                                    <td class="py-2 pe-2 text-end font-medium">{{ money(s.remaining) }}</td>
                                    <td class="py-2 pe-2 whitespace-nowrap">
                                        <Tag :value="t('shared.status.' + s.status)" :severity="statusSeverity(s.status)" class="!text-xs" />
                                        <Tag v-if="s.is_overdue" :value="t('shared.status.overdue')" severity="danger" class="!text-xs ms-1" />
                                        <div v-if="s.paid_at" class="text-[10px] text-slate-400">{{ date(s.paid_at) }}</div>
                                    </td>
                                    <td class="py-2 print:hidden">
                                        <Button v-if="n(s.late_fee) > n(s.late_fee_paid) && hasPermission(PERMISSIONS.INSTALLMENT.COLLECT)" :label="t('installments.show.waive_fee')" icon="pi pi-times-circle" text size="small" severity="warn" @click="waive(s)" />
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Collect -->
                    <div class="card p-4 space-y-3 print:hidden">
                        <h2 class="font-semibold text-slate-800">{{ t('installments.show.collect_payment') }}</h2>
                        <div v-if="!canCollect" class="text-sm text-slate-500">
                            {{ sale.status === 'completed' || balance <= 0 ? t('installments.show.fully_paid') : (sale.status === 'active' ? t('installments.show.no_collect_permission') : t('installments.show.not_active', { status: t('shared.status.' + sale.status) })) }}
                        </div>
                        <template v-else>
                            <div class="flex flex-wrap gap-2">
                                <Button v-if="nextSchedule" :label="t('installments.show.next_installment', { amount: money(nextSchedule.remaining) })" size="small" outlined @click="fillNext" />
                                <Button :label="t('installments.show.full_balance', { amount: money(balance) })" size="small" outlined severity="secondary" @click="fillFull" />
                            </div>
                            <div v-for="(row, i) in rows" :key="row.id" class="border border-slate-200 rounded-lg p-2 space-y-2">
                                <div class="flex flex-wrap sm:flex-nowrap gap-2">
                                    <Select v-model="row.payment_method_id" :options="paymentMethods" optionLabel="name" optionValue="id" :placeholder="t('shared.terms.method')" class="w-full sm:flex-1 min-w-0" size="small" />
                                    <InputNumber v-model="row.amount" :min="0" :maxFractionDigits="2" class="flex-1 sm:flex-none sm:w-32" inputClass="w-full" size="small" :aria-label="t('shared.terms.amount')" />
                                    <Button icon="pi pi-times" text severity="danger" size="small" :aria-label="t('common.delete')" :disabled="rows.length === 1" @click="rows.splice(i, 1)" />
                                </div>
                                <div class="grid grid-cols-2 gap-2">
                                    <InputText v-model="row.transaction_ref" :placeholder="t('installments.show.ref_optional')" class="w-full" size="small" />
                                    <InputText v-model="row.notes" :placeholder="t('shared.terms.notes')" dir="auto" class="w-full" size="small" />
                                </div>
                            </div>
                            <Button :label="t('installments.show.add_method')" icon="pi pi-plus" text size="small" @click="rows.push(blankRow())" />

                            <div v-if="collectTotal > 0" class="rounded-lg bg-slate-50 border border-slate-200 p-2 text-xs space-y-1">
                                <div class="font-semibold text-slate-700">{{ t('installments.show.allocation') }}</div>
                                <div v-for="a in allocation.rows" :key="a.no" class="flex justify-between">
                                    <span>#{{ a.no }} <span class="text-slate-400">({{ a.full ? t('installments.show.clears') : t('shared.status.partial') }})</span></span>
                                    <span class="tabular-nums">
                                        <span v-if="a.fee > 0">{{ t('installments.config.late_fee') }} {{ money(a.fee) }} + </span>{{ money(a.amt) }}
                                    </span>
                                </div>
                                <div v-if="allocation.excess > 0" class="text-rose-600">{{ t('installments.show.exceeds_by', { amount: money(allocation.excess) }) }}</div>
                                <div class="flex justify-between border-t pt-1 font-semibold"><span>{{ t('shared.terms.total') }}</span><span>{{ money(collectTotal) }}</span></div>
                                <div class="flex justify-between text-slate-500"><span>{{ t('installments.show.balance_after') }}</span><span>{{ money(Math.max(0, balance - collectTotal)) }}</span></div>
                            </div>

                            <Button :label="t('installments.collect')" icon="pi pi-check" severity="success" class="w-full" :loading="collecting" :disabled="collectTotal <= 0" @click="submitCollect" />
                        </template>
                    </div>
                </div>

                <div class="grid grid-cols-1 xl:grid-cols-3 gap-4">
                    <!-- Payment history -->
                    <div class="card p-4 xl:col-span-2 overflow-x-auto">
                        <h2 class="font-semibold text-slate-800 mb-3">{{ t('installments.show.payment_history') }}</h2>
                        <table class="w-full text-sm tabular-nums">
                            <thead>
                                <tr class="text-start text-xs text-slate-500 border-b">
                                    <th class="py-2 pe-2 text-start">{{ t('shared.terms.date') }}</th>
                                    <th class="py-2 pe-2 text-start">{{ t('installments.installment') }}</th>
                                    <th class="py-2 pe-2 text-end">{{ t('shared.terms.amount') }}</th>
                                    <th class="py-2 pe-2 text-end">{{ t('installments.config.late_fee') }}</th>
                                    <th class="py-2 pe-2 text-start">{{ t('shared.terms.method') }}</th>
                                    <th class="py-2 text-start">{{ t('installments.show.received_by') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="p in sale.payments" :key="p.id" class="border-b border-slate-100">
                                    <td class="py-2 pe-2 whitespace-nowrap">{{ dateTime(p.created_at) }}</td>
                                    <td class="py-2 pe-2">#{{ p.schedule?.installment_no ?? '-' }}</td>
                                    <td class="py-2 pe-2 text-end">{{ money(p.amount) }}</td>
                                    <td class="py-2 pe-2 text-end">{{ n(p.late_fee) > 0 ? money(p.late_fee) : '-' }}</td>
                                    <td class="py-2 pe-2">{{ p.pos_payment?.payment_method?.name || '-' }}<div v-if="p.pos_payment?.transaction_ref" class="text-xs text-slate-400">{{ p.pos_payment.transaction_ref }}</div></td>
                                    <td class="py-2">{{ p.receiver?.name || '-' }}</td>
                                </tr>
                                <tr v-if="!sale.payments?.length"><td colspan="6" class="py-4 text-center text-slate-400">{{ t('installments.show.no_payments') }}</td></tr>
                            </tbody>
                        </table>

                        <h2 class="font-semibold text-slate-800 mt-6 mb-2">{{ t('installments.show.items') }}</h2>
                        <table class="w-full text-sm tabular-nums">
                            <tbody>
                                <tr v-for="it in sale.order?.items || []" :key="it.id" class="border-b border-slate-100">
                                    <td class="py-1.5 pe-2">{{ it.name }} <span class="text-xs text-slate-400">{{ it.sku }}</span></td>
                                    <td class="py-1.5 pe-2 text-end whitespace-nowrap">{{ it.quantity }} × {{ money(it.unit_price) }}</td>
                                    <td class="py-1.5 text-end font-medium">{{ money(it.line_total) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Agreement info -->
                    <div class="card p-4 space-y-2 text-sm">
                        <div class="flex justify-between items-center">
                            <h2 class="font-semibold text-slate-800">{{ t('installments.show.agreement_info') }}</h2>
                            <Button v-if="canEditInfo" :label="t('common.edit')" icon="pi pi-pencil" text size="small" class="print:hidden" @click="openEdit" />
                        </div>
                        <div><span class="text-slate-500">{{ t('installments.customer_nid') }}:</span> {{ sale.customer_nid || '-' }}</div>
                        <div class="pt-2 border-t border-slate-100 text-xs uppercase text-slate-500">{{ t('installments.guarantor') }}</div>
                        <div><span class="text-slate-500">{{ t('common.name') }}:</span> {{ sale.guarantor_name || '-' }}</div>
                        <div><span class="text-slate-500">{{ t('shared.terms.phone') }}:</span> <span class="ltr-nums">{{ sale.guarantor_phone || '-' }}</span></div>
                        <div><span class="text-slate-500">NID:</span> {{ sale.guarantor_nid || '-' }}</div>
                        <div><span class="text-slate-500">{{ t('common.address') }}:</span> {{ sale.guarantor_address || '-' }}</div>
                        <div v-if="sale.notes" dir="auto" class="pt-2 border-t border-slate-100 whitespace-pre-line">{{ sale.notes }}</div>
                        <div class="hidden print:grid grid-cols-2 gap-8 pt-16 text-xs text-center">
                            <div class="border-t border-slate-400 pt-1">{{ t('installments.show.customer_signature') }}</div>
                            <div class="border-t border-slate-400 pt-1">{{ t('installments.show.authorized_signature') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <Dialog v-model:visible="showEdit" modal :header="t('installments.show.edit_info')" :style="{ width: '520px' }" :breakpoints="{ '640px': '95vw' }">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <div class="md:col-span-2"><label class="text-xs text-slate-500">{{ t('installments.customer_nid') }}</label><InputText v-model="editForm.customer_nid" class="w-full" /></div>
                <div><label class="text-xs text-slate-500">{{ t('installments.guarantor_name') }}</label><InputText v-model="editForm.guarantor_name" dir="auto" class="w-full" /></div>
                <div><label class="text-xs text-slate-500">{{ t('installments.guarantor_phone') }}</label><InputText v-model="editForm.guarantor_phone" type="tel" class="w-full" /></div>
                <div><label class="text-xs text-slate-500">{{ t('installments.guarantor_nid') }}</label><InputText v-model="editForm.guarantor_nid" class="w-full" /></div>
                <div><label class="text-xs text-slate-500">{{ t('installments.guarantor_address') }}</label><InputText v-model="editForm.guarantor_address" dir="auto" class="w-full" /></div>
                <div class="md:col-span-2"><label class="text-xs text-slate-500">{{ t('shared.terms.notes') }}</label><Textarea v-model="editForm.notes" rows="3" dir="auto" class="w-full" /></div>
            </div>
            <template #footer>
                <Button :label="t('shared.terms.cancel')" text @click="showEdit = false" />
                <Button :label="t('shared.terms.save')" icon="pi pi-check" :loading="saving" @click="saveEdit" />
            </template>
        </Dialog>
    </AuthenticatedLayout>
</template>

<style>
@media print {
    body * { visibility: hidden !important; }
    #print-area, #print-area * { visibility: visible !important; }
    #print-area { position: absolute !important; inset-inline-start: 0 !important; top: 0 !important; width: 100% !important; }
}
</style>
