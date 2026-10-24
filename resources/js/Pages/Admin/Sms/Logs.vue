<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, router } from "@inertiajs/vue3";
import { computed, ref } from "vue";
import Button from "primevue/button";
import Column from "primevue/column";
import DataTable from "primevue/datatable";
import DatePicker from "primevue/datepicker";
import InputText from "primevue/inputtext";
import Select from "primevue/select";
import Tag from "primevue/tag";
import Tooltip from "primevue/tooltip";

const vTooltip = Tooltip;
import ExportButtons from "@/Components/Sales/ExportButtons.vue";
import { useFlashToast } from "@/Composables/useFlashToast";
import { cleanParams, dateTime, titleCase, ymd } from "@/Helpers/format";

useFlashToast();

const props = defineProps({
    logs: { type: Object, default: () => ({ data: [] }) },
    filters: { type: Object, default: () => ({}) },
    gateways: { type: Array, default: () => [] },
    campaign: { type: Object, default: null },
    events: { type: Array, default: () => [] },
});

const toDate = (s) => (s ? new Date(s + "T00:00:00") : null);
const f = ref({
    search: props.filters.search || "",
    status: props.filters.status || null,
    type: props.filters.type || null,
    event: props.filters.event || null,
    gateway_id: props.filters.gateway_id ? Number(props.filters.gateway_id) : null,
    campaign_id: props.filters.campaign_id || null,
    date_from: toDate(props.filters.date_from),
    date_to: toDate(props.filters.date_to),
});

const statusOptions = [
    { label: "Sent", value: "sent" },
    { label: "Failed", value: "failed" },
    { label: "Queued", value: "queued" },
];
const typeOptions = [
    { label: "Transactional", value: "transactional" },
    { label: "Promotional", value: "promotional" },
    { label: "Test", value: "test" },
];

const params = computed(() =>
    cleanParams({ ...f.value, date_from: ymd(f.value.date_from), date_to: ymd(f.value.date_to) })
);

function apply() {
    router.get(route("sms.logs"), params.value, { preserveState: true, replace: true });
}
function reset() {
    router.get(route("sms.logs"), {}, { replace: true });
}
function goToPage(url) {
    if (url) router.get(url, {}, { preserveState: true });
}

const rows = computed(() => props.logs?.data || []);
const selected = ref([]);
const selectedFailed = computed(() => selected.value.filter((l) => l.status === "failed"));

function resend(ids) {
    router.post(route("sms.resend"), { ids }, { preserveScroll: true, onSuccess: () => (selected.value = []) });
}
function retryStuck() {
    router.post(route("sms.retry-stuck"), {}, { preserveScroll: true });
}

function statusSeverity(s) {
    return s === "sent" ? "success" : s === "failed" ? "danger" : "warn";
}
const expanded = ref({});
</script>

<template>
    <Head title="SMS History" />
    <AuthenticatedLayout>
        <div class="p-4 md:p-6 space-y-4">
            <div class="flex flex-wrap justify-between items-start gap-3">
                <div>
                    <h2 class="text-xl font-semibold text-slate-800 flex items-center gap-2"><i class="pi pi-history text-primary" /> SMS History</h2>
                    <p class="text-sm text-slate-500">Every message sent, with delivery status</p>
                </div>
                <div class="flex flex-wrap gap-2">
                    <Button v-if="selectedFailed.length" :label="`Resend ${selectedFailed.length} failed`" icon="pi pi-replay" size="small" severity="warn" @click="resend(selectedFailed.map((l) => l.id))" />
                    <Button label="Retry stuck queued" icon="pi pi-refresh" size="small" severity="secondary" outlined @click="retryStuck" />
                    <Link :href="route('sms.compose')"><Button label="Send SMS" icon="pi pi-send" size="small" /></Link>
                </div>
            </div>

            <div v-if="campaign" class="p-3 rounded-xl border bg-sky-50 border-sky-200 text-sm text-slate-700 flex flex-wrap items-center gap-x-4 gap-y-1">
                <span class="font-semibold text-slate-800"><i class="pi pi-megaphone mr-1" />{{ campaign.name }}</span>
                <span>Recipients: <b>{{ campaign.recipients_count }}</b></span>
                <span>Sent: <b>{{ campaign.sent_count }}</b></span>
                <span>Failed: <b>{{ campaign.failed_count }}</b></span>
                <Tag :value="campaign.status" :severity="campaign.status === 'completed' ? 'success' : 'warn'" />
                <Link :href="route('sms.logs')" class="ml-auto underline text-xs">Show all messages</Link>
            </div>

            <div class="card p-4 space-y-3">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                    <InputText v-model="f.search" placeholder="Phone (partial) or message text" class="w-full" @keyup.enter="apply" />
                    <Select v-model="f.status" :options="statusOptions" optionLabel="label" optionValue="value" placeholder="Status" showClear class="w-full" />
                    <Select v-model="f.type" :options="typeOptions" optionLabel="label" optionValue="value" placeholder="Type" showClear class="w-full" />
                    <Select v-model="f.event" :options="events" optionLabel="label" optionValue="value" placeholder="Event" showClear class="w-full" />
                    <Select v-model="f.gateway_id" :options="gateways" optionLabel="name" optionValue="id" placeholder="Gateway" showClear class="w-full" />
                    <DatePicker v-model="f.date_from" placeholder="From" dateFormat="dd-mm-yy" showIcon class="w-full" />
                    <DatePicker v-model="f.date_to" placeholder="To" dateFormat="dd-mm-yy" showIcon class="w-full" />
                    <div class="flex gap-2">
                        <Button label="Apply" icon="pi pi-filter" outlined @click="apply" />
                        <Button label="Reset" icon="pi pi-refresh" text @click="reset" />
                    </div>
                </div>
                <div class="flex justify-end"><ExportButtons routeName="sms.logs" :params="params" /></div>
            </div>

            <div class="card">
                <DataTable :value="rows" v-model:selection="selected" dataKey="id" size="small" showGridlines scrollable>
                    <Column selectionMode="multiple" headerStyle="width: 3rem" />
                    <Column header="Date">
                        <template #body="{ data }"><span class="text-xs text-slate-600 whitespace-nowrap">{{ dateTime(data.created_at) }}</span></template>
                    </Column>
                    <Column header="Phone">
                        <template #body="{ data }"><span class="font-medium text-slate-800 whitespace-nowrap">{{ data.phone }}</span></template>
                    </Column>
                    <Column header="Customer">
                        <template #body="{ data }">{{ data.customer?.name || "-" }}</template>
                    </Column>
                    <Column header="Type">
                        <template #body="{ data }">
                            <div class="flex flex-col gap-1 items-start">
                                <Tag :value="titleCase(data.event || data.type)" severity="secondary" />
                                <span v-if="data.campaign" class="text-[11px] text-slate-500">{{ data.campaign.name }}</span>
                            </div>
                        </template>
                    </Column>
                    <Column header="Gateway">
                        <template #body="{ data }">{{ data.gateway?.name || "-" }}</template>
                    </Column>
                    <Column header="Parts">
                        <template #body="{ data }">{{ data.segments }}</template>
                    </Column>
                    <Column header="Status">
                        <template #body="{ data }">
                            <Tag :value="data.status" :severity="statusSeverity(data.status)" />
                            <div v-if="data.status === 'failed' && data.error" class="text-[11px] text-rose-700 mt-1 max-w-[220px] break-words">{{ data.error }}</div>
                        </template>
                    </Column>
                    <Column header="Message">
                        <template #body="{ data }">
                            <div class="text-xs text-slate-700 max-w-[360px] cursor-pointer" :class="expanded[data.id] ? 'whitespace-pre-wrap' : 'truncate'"
                                @click="expanded[data.id] = !expanded[data.id]" :title="data.message">{{ data.message }}</div>
                        </template>
                    </Column>
                    <Column header="">
                        <template #body="{ data }">
                            <Button v-if="data.status === 'failed'" icon="pi pi-replay" text size="small" severity="warn" v-tooltip.top="'Resend'" @click="resend([data.id])" />
                        </template>
                    </Column>
                    <template #empty><div class="text-center text-slate-400 py-6">No messages found</div></template>
                </DataTable>

                <div class="flex justify-between items-center mt-3 text-sm text-slate-500">
                    <span>Showing {{ logs.from || 0 }}–{{ logs.to || 0 }} of {{ logs.total || 0 }}</span>
                    <div class="flex gap-1">
                        <Button icon="pi pi-angle-left" text :disabled="!logs.prev_page_url" @click="goToPage(logs.prev_page_url)" />
                        <Button icon="pi pi-angle-right" text :disabled="!logs.next_page_url" @click="goToPage(logs.next_page_url)" />
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
