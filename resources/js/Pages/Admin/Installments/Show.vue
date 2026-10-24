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
import { date, dateTime, firstError, money, titleCase } from "@/Helpers/format";

useFlashToast({ error: false }); // errors are toasted by the layout
const toast = useToast();

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
const canCollect = computed(() => props.sale.status === "active" && balance.value > 0);

function statusSeverity(s) {
    return { active: "info", completed: "success", defaulted: "danger", cancelled: "secondary", paid: "success", partial: "warn", pending: "info" }[s] || "secondary";
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
        toast.add({ severity: "warn", summary: "Amount required", detail: "Enter a payment method and amount", life: 2500 });
        return;
    }
    if (collectTotal.value > balance.value + 0.009) {
        toast.add({ severity: "warn", summary: "Too much", detail: `Outstanding balance is ${money(balance.value)}`, life: 3000 });
        return;
    }
    collecting.value = true;
    router.post(route("installments.collect", props.sale.id), { payments }, {
        preserveScroll: true,
        onSuccess: () => (rows.value = [blankRow()]),
        onError: (e) => toast.add({ severity: "error", summary: "Failed", detail: firstError(e) || "Could not collect payment", life: 4000 }),
        onFinish: () => (collecting.value = false),
    });
}

function waive(s) {
    if (!confirm(`Waive late fee of ${money(n(s.late_fee) - n(s.late_fee_paid))} on installment #${s.installment_no}?`)) return;
    router.post(route("installments.waive-late-fee", s.id), {}, {
        preserveScroll: true,
        onError: (e) => toast.add({ severity: "error", summary: "Failed", detail: firstError(e), life: 3000 }),
    });
}

// ---------------- Status ----------------
function setStatus(status, msg) {
    if (!confirm(msg)) return;
    router.put(route("installments.status", props.sale.id), { status }, {
        preserveScroll: true,
        onError: (e) => toast.add({ severity: "error", summary: "Failed", detail: firstError(e), life: 3000 }),
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
        onError: (e) => toast.add({ severity: "error", summary: "Failed", detail: firstError(e), life: 3000 }),
        onFinish: () => (saving.value = false),
    });
}

const printPage = () => window.print();

const summary = computed(() => [
    ["Sale amount", props.sale.sale_amount],
    ["Down payment", props.sale.down_payment],
    ["Processing fee", props.sale.processing_fee],
    ["Financed", props.sale.financed_amount],
    ["Interest", props.sale.interest_amount],
    ["Total payable", props.sale.total_payable],
    ["Paid", props.sale.paid_amount],
    ["Late fees (charged / paid)", null, `${money(props.sale.late_fee_charged)} / ${money(props.sale.late_fee_paid)}`],
]);

const interestLabel = computed(() => {
    const s = props.sale;
    if (s.interest_type === "none") return "No interest";
    return s.interest_type === "flat" ? `Flat ${n(s.interest_rate)}%` : `Reducing ${n(s.interest_rate)}% p.a.`;
});
</script>

<template>
    <Head :title="`Installment ${sale.agreement_no}`" />
    <AuthenticatedLayout>
        <div class="p-4 md:p-6 space-y-4">
            <!-- Actions -->
            <div class="flex flex-wrap justify-between gap-2 print:hidden">
                <Link :href="route('installments.index')"><Button label="Back" icon="pi pi-arrow-left" severity="secondary" size="small" /></Link>
                <div class="flex flex-wrap gap-2">
                    <Button label="Print" icon="pi pi-print" severity="secondary" outlined size="small" @click="printPage" />
                    <Button v-if="sale.status === 'active'" label="Mark defaulted" icon="pi pi-flag" severity="warn" outlined size="small"
                        @click="setStatus('defaulted', 'Mark this agreement as defaulted?')" />
                    <Button v-if="sale.status === 'defaulted' || sale.status === 'cancelled'" label="Reactivate" icon="pi pi-replay" severity="info" outlined size="small"
                        @click="setStatus('active', 'Reactivate this agreement?')" />
                    <Button v-if="sale.status === 'active' || sale.status === 'defaulted'" label="Cancel agreement" icon="pi pi-ban" severity="danger" outlined size="small"
                        @click="setStatus('cancelled', 'Cancel this agreement? Unpaid installments will be cancelled.')" />
                </div>
            </div>

            <div id="print-area" class="space-y-4">
                <!-- Header -->
                <div class="card p-4 flex flex-wrap justify-between gap-4">
                    <div>
                        <div class="flex items-center gap-2">
                            <h2 class="text-xl font-bold">{{ sale.agreement_no }}</h2>
                            <Tag :value="sale.status" :severity="statusSeverity(sale.status)" />
                        </div>
                        <div class="text-sm text-slate-500 mt-1">
                            Created {{ dateTime(sale.created_at) }}<span v-if="sale.creator"> by {{ sale.creator.name }}</span>
                        </div>
                        <div class="text-sm mt-1">
                            Invoice:
                            <a :href="route('pos.orders.invoice', sale.order_id)" target="_blank" class="text-primary font-medium">{{ sale.order?.invoice_no || `#${sale.order_id}` }}</a>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="text-xs text-slate-500 uppercase">Customer</div>
                        <div class="font-semibold">{{ sale.customer?.name }}</div>
                        <div class="text-sm text-slate-600">{{ sale.customer?.phone }}</div>
                        <div v-if="sale.customer?.address" class="text-xs text-slate-500 max-w-xs">{{ sale.customer.address }}</div>
                    </div>
                </div>

                <!-- Summary -->
                <div class="card p-4">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                        <div v-for="[label, value, text] in summary" :key="label" class="p-3 rounded-lg bg-slate-50 border border-slate-100">
                            <div class="text-xs text-slate-500">{{ label }}</div>
                            <div class="font-semibold tabular-nums">{{ text ?? money(value) }}</div>
                        </div>
                    </div>
                    <div class="mt-4 flex flex-wrap items-center gap-4">
                        <div class="flex-1 min-w-[200px]">
                            <div class="flex justify-between text-xs text-slate-500 mb-1"><span>Repaid</span><span>{{ progress }}%</span></div>
                            <ProgressBar :value="progress" :showValue="false" style="height: 8px" />
                        </div>
                        <div class="text-right">
                            <div class="text-xs text-slate-500">Balance</div>
                            <div class="text-2xl font-bold" :class="balance > 0 ? 'text-rose-600' : 'text-emerald-600'">{{ money(balance) }}</div>
                        </div>
                    </div>
                    <div class="mt-3 text-xs text-slate-500">
                        {{ sale.number_of_installments }} × {{ money(sale.installment_amount) }} · {{ titleCase(sale.frequency) }}<span v-if="sale.frequency === 'custom'"> ({{ sale.interval_days }} days)</span>
                        · {{ interestLabel }}
                        <span v-if="n(sale.late_fee_amount) > 0"> · Late fee {{ sale.late_fee_type === 'percent' ? `${n(sale.late_fee_amount)}%` : money(sale.late_fee_amount) }} after {{ sale.grace_days }} grace days</span>
                        <span v-if="sale.plan"> · Plan: {{ sale.plan.name }}</span>
                    </div>
                </div>

                <div class="grid grid-cols-1 xl:grid-cols-3 gap-4">
                    <!-- Schedule -->
                    <div class="card p-4 xl:col-span-2 overflow-x-auto">
                        <h3 class="font-semibold mb-3">Schedule</h3>
                        <table class="w-full text-sm tabular-nums">
                            <thead>
                                <tr class="text-left text-xs text-slate-500 border-b">
                                    <th class="py-2 pr-2">#</th>
                                    <th class="py-2 pr-2">Due date</th>
                                    <th class="py-2 pr-2 text-right">Amount</th>
                                    <th class="py-2 pr-2 text-right">Late fee</th>
                                    <th class="py-2 pr-2 text-right">Paid</th>
                                    <th class="py-2 pr-2 text-right">Remaining</th>
                                    <th class="py-2 pr-2">Status</th>
                                    <th class="py-2 print:hidden"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="s in schedules" :key="s.id" class="border-b border-slate-100" :class="s.is_overdue ? 'bg-rose-50/50' : ''">
                                    <td class="py-2 pr-2">{{ s.installment_no }}</td>
                                    <td class="py-2 pr-2 whitespace-nowrap" :class="s.is_overdue ? 'text-rose-600 font-semibold' : ''">{{ date(s.due_date) }}</td>
                                    <td class="py-2 pr-2 text-right">{{ money(s.amount) }}</td>
                                    <td class="py-2 pr-2 text-right">{{ n(s.late_fee) > 0 ? money(s.late_fee) : '-' }}</td>
                                    <td class="py-2 pr-2 text-right">{{ money(n(s.paid_amount) + n(s.late_fee_paid)) }}</td>
                                    <td class="py-2 pr-2 text-right font-medium">{{ money(s.remaining) }}</td>
                                    <td class="py-2 pr-2 whitespace-nowrap">
                                        <Tag :value="s.status" :severity="statusSeverity(s.status)" class="!text-xs" />
                                        <Tag v-if="s.is_overdue" value="Overdue" severity="danger" class="!text-xs ml-1" />
                                        <div v-if="s.paid_at" class="text-[10px] text-slate-400">{{ date(s.paid_at) }}</div>
                                    </td>
                                    <td class="py-2 print:hidden">
                                        <Button v-if="n(s.late_fee) > n(s.late_fee_paid)" label="Waive fee" icon="pi pi-times-circle" text size="small" severity="warn" @click="waive(s)" />
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Collect -->
                    <div class="card p-4 space-y-3 print:hidden">
                        <h3 class="font-semibold">Collect payment</h3>
                        <div v-if="!canCollect" class="text-sm text-slate-500">
                            {{ sale.status === 'completed' || balance <= 0 ? 'This agreement is fully paid.' : `Agreement is ${sale.status}; reactivate it to collect.` }}
                        </div>
                        <template v-else>
                            <div class="flex flex-wrap gap-2">
                                <Button v-if="nextSchedule" :label="`Next installment (${money(nextSchedule.remaining)})`" size="small" outlined @click="fillNext" />
                                <Button :label="`Full balance (${money(balance)})`" size="small" outlined severity="secondary" @click="fillFull" />
                            </div>
                            <div v-for="(row, i) in rows" :key="row.id" class="border border-slate-200 rounded-lg p-2 space-y-2">
                                <div class="flex gap-2">
                                    <Select v-model="row.payment_method_id" :options="paymentMethods" optionLabel="name" optionValue="id" placeholder="Method" class="flex-1 min-w-0" size="small" />
                                    <InputNumber v-model="row.amount" :min="0" :maxFractionDigits="2" class="w-32" inputClass="w-full" size="small" />
                                    <Button icon="pi pi-times" text severity="danger" size="small" :disabled="rows.length === 1" @click="rows.splice(i, 1)" />
                                </div>
                                <div class="grid grid-cols-2 gap-2">
                                    <InputText v-model="row.transaction_ref" placeholder="Ref (optional)" class="w-full" size="small" />
                                    <InputText v-model="row.notes" placeholder="Notes" class="w-full" size="small" />
                                </div>
                            </div>
                            <Button label="Add method" icon="pi pi-plus" text size="small" @click="rows.push(blankRow())" />

                            <div v-if="collectTotal > 0" class="rounded-lg bg-slate-50 border border-slate-200 p-2 text-xs space-y-1">
                                <div class="font-semibold text-slate-700">Allocation</div>
                                <div v-for="a in allocation.rows" :key="a.no" class="flex justify-between">
                                    <span>#{{ a.no }} <span class="text-slate-400">{{ a.full ? '(clears)' : '(partial)' }}</span></span>
                                    <span class="tabular-nums">
                                        <span v-if="a.fee > 0">late fee {{ money(a.fee) }} + </span>{{ money(a.amt) }}
                                    </span>
                                </div>
                                <div v-if="allocation.excess > 0" class="text-rose-600">Exceeds balance by {{ money(allocation.excess) }}</div>
                                <div class="flex justify-between border-t pt-1 font-semibold"><span>Total</span><span>{{ money(collectTotal) }}</span></div>
                                <div class="flex justify-between text-slate-500"><span>Balance after</span><span>{{ money(Math.max(0, balance - collectTotal)) }}</span></div>
                            </div>

                            <Button label="Collect" icon="pi pi-check" severity="success" class="w-full" :loading="collecting" :disabled="collectTotal <= 0" @click="submitCollect" />
                        </template>
                    </div>
                </div>

                <div class="grid grid-cols-1 xl:grid-cols-3 gap-4">
                    <!-- Payment history -->
                    <div class="card p-4 xl:col-span-2 overflow-x-auto">
                        <h3 class="font-semibold mb-3">Payment history</h3>
                        <table class="w-full text-sm tabular-nums">
                            <thead>
                                <tr class="text-left text-xs text-slate-500 border-b">
                                    <th class="py-2 pr-2">Date</th>
                                    <th class="py-2 pr-2">Installment</th>
                                    <th class="py-2 pr-2 text-right">Amount</th>
                                    <th class="py-2 pr-2 text-right">Late fee</th>
                                    <th class="py-2 pr-2">Method</th>
                                    <th class="py-2">Received by</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="p in sale.payments" :key="p.id" class="border-b border-slate-100">
                                    <td class="py-2 pr-2 whitespace-nowrap">{{ dateTime(p.created_at) }}</td>
                                    <td class="py-2 pr-2">#{{ p.schedule?.installment_no ?? '-' }}</td>
                                    <td class="py-2 pr-2 text-right">{{ money(p.amount) }}</td>
                                    <td class="py-2 pr-2 text-right">{{ n(p.late_fee) > 0 ? money(p.late_fee) : '-' }}</td>
                                    <td class="py-2 pr-2">{{ p.pos_payment?.payment_method?.name || '-' }}<div v-if="p.pos_payment?.transaction_ref" class="text-xs text-slate-400">{{ p.pos_payment.transaction_ref }}</div></td>
                                    <td class="py-2">{{ p.receiver?.name || '-' }}</td>
                                </tr>
                                <tr v-if="!sale.payments?.length"><td colspan="6" class="py-4 text-center text-slate-400">No installment payments yet</td></tr>
                            </tbody>
                        </table>

                        <h3 class="font-semibold mt-6 mb-2">Items</h3>
                        <table class="w-full text-sm tabular-nums">
                            <tbody>
                                <tr v-for="it in sale.order?.items || []" :key="it.id" class="border-b border-slate-100">
                                    <td class="py-1.5 pr-2">{{ it.name }} <span class="text-xs text-slate-400">{{ it.sku }}</span></td>
                                    <td class="py-1.5 pr-2 text-right whitespace-nowrap">{{ it.quantity }} × {{ money(it.unit_price) }}</td>
                                    <td class="py-1.5 text-right font-medium">{{ money(it.line_total) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Agreement info -->
                    <div class="card p-4 space-y-2 text-sm">
                        <div class="flex justify-between items-center">
                            <h3 class="font-semibold">Agreement info</h3>
                            <Button label="Edit" icon="pi pi-pencil" text size="small" class="print:hidden" @click="openEdit" />
                        </div>
                        <div><span class="text-slate-500">Customer NID:</span> {{ sale.customer_nid || '-' }}</div>
                        <div class="pt-2 border-t border-slate-100 text-xs uppercase text-slate-500">Guarantor</div>
                        <div><span class="text-slate-500">Name:</span> {{ sale.guarantor_name || '-' }}</div>
                        <div><span class="text-slate-500">Phone:</span> {{ sale.guarantor_phone || '-' }}</div>
                        <div><span class="text-slate-500">NID:</span> {{ sale.guarantor_nid || '-' }}</div>
                        <div><span class="text-slate-500">Address:</span> {{ sale.guarantor_address || '-' }}</div>
                        <div v-if="sale.notes" class="pt-2 border-t border-slate-100 whitespace-pre-line">{{ sale.notes }}</div>
                        <div class="hidden print:grid grid-cols-2 gap-8 pt-16 text-xs text-center">
                            <div class="border-t border-slate-400 pt-1">Customer signature</div>
                            <div class="border-t border-slate-400 pt-1">Authorized signature</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <Dialog v-model:visible="showEdit" modal header="Edit agreement info" :style="{ width: '520px' }" :breakpoints="{ '640px': '95vw' }">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <div class="md:col-span-2"><label class="text-xs text-slate-500">Customer NID</label><InputText v-model="editForm.customer_nid" class="w-full" /></div>
                <div><label class="text-xs text-slate-500">Guarantor name</label><InputText v-model="editForm.guarantor_name" class="w-full" /></div>
                <div><label class="text-xs text-slate-500">Guarantor phone</label><InputText v-model="editForm.guarantor_phone" class="w-full" /></div>
                <div><label class="text-xs text-slate-500">Guarantor NID</label><InputText v-model="editForm.guarantor_nid" class="w-full" /></div>
                <div><label class="text-xs text-slate-500">Guarantor address</label><InputText v-model="editForm.guarantor_address" class="w-full" /></div>
                <div class="md:col-span-2"><label class="text-xs text-slate-500">Notes</label><Textarea v-model="editForm.notes" rows="3" class="w-full" /></div>
            </div>
            <template #footer>
                <Button label="Cancel" text @click="showEdit = false" />
                <Button label="Save" icon="pi pi-check" :loading="saving" @click="saveEdit" />
            </template>
        </Dialog>
    </AuthenticatedLayout>
</template>

<style>
@media print {
    body * { visibility: hidden !important; }
    #print-area, #print-area * { visibility: visible !important; }
    #print-area { position: absolute !important; left: 0 !important; top: 0 !important; width: 100% !important; }
}
</style>
