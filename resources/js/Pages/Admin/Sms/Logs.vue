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
import { useI18n } from "vue-i18n";
import useAuth from "@/Composables/useAuth";
import { PERMISSIONS } from "@/constants/permissions";
import { cleanParams, dateTime, ymd } from "@/Helpers/format";

useFlashToast();
const { t, te } = useI18n();
const { hasPermission } = useAuth();
const canSend = computed(() => hasPermission(PERMISSIONS.SMS.SEND));

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

const statusOptions = computed(() => ["sent", "failed", "queued"].map((v) => ({ label: t(`shared.status.${v}`), value: v })));
const typeOptions = computed(() => ["transactional", "promotional", "test"].map((v) => ({ label: t(`sms.events.${v}`), value: v })));
const eventOptions = computed(() => props.events.map((e) => ({ ...e, label: eventLabel(e.value) })));

function eventLabel(key) {
    return te(`sms.events.${key}`) ? t(`sms.events.${key}`) : key;
}

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
    <Head :title="t('sms.history')" />
    <AuthenticatedLayout>
        <div class="p-4 md:p-6 space-y-4">
            <div class="flex flex-wrap justify-between items-start gap-3">
                <div>
                    <h1 class="text-xl font-semibold text-slate-800 flex items-center gap-2"><i class="pi pi-history text-primary" /> {{ t('sms.history') }}</h1>
                    <p class="text-sm text-slate-500">{{ t('sms.logs.subtitle') }}</p>
                </div>
                <div class="flex flex-wrap gap-2">
                    <Button v-if="canSend && selectedFailed.length" :label="t('sms.logs.resend_failed', { n: selectedFailed.length })" icon="pi pi-replay" size="small" severity="warn" @click="resend(selectedFailed.map((l) => l.id))" />
                    <Button v-if="canSend" :label="t('sms.logs.retry_stuck')" icon="pi pi-refresh" size="small" severity="secondary" outlined @click="retryStuck" />
                    <Link v-if="canSend" :href="route('sms.compose')"><Button :label="t('sms.send_sms')" icon="pi pi-send" size="small" /></Link>
                </div>
            </div>

            <div v-if="campaign" class="p-3 rounded-xl border bg-sky-50 border-sky-200 text-sm text-slate-700 flex flex-wrap items-center gap-x-4 gap-y-1">
                <span class="font-semibold text-slate-800"><i class="pi pi-megaphone me-1" />{{ campaign.name }}</span>
                <span>{{ t('sms.recipients') }}: <b>{{ campaign.recipients_count }}</b></span>
                <span>{{ t('shared.status.sent') }}: <b>{{ campaign.sent_count }}</b></span>
                <span>{{ t('shared.status.failed') }}: <b>{{ campaign.failed_count }}</b></span>
                <Tag :value="t('sms.campaign_status.' + campaign.status)" :severity="campaign.status === 'completed' ? 'success' : 'warn'" />
                <Link :href="route('sms.logs')" class="ms-auto underline text-xs">{{ t('sms.logs.show_all') }}</Link>
            </div>

            <div class="card p-4 space-y-3">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                    <InputText v-model="f.search" type="search" :placeholder="t('sms.logs.search_placeholder')" v-tooltip.bottom="t('shared.terms.search_hint')" class="w-full" @keyup.enter="apply" />
                    <Select v-model="f.status" :options="statusOptions" optionLabel="label" optionValue="value" :placeholder="t('common.status')" showClear class="w-full" />
                    <Select v-model="f.type" :options="typeOptions" optionLabel="label" optionValue="value" :placeholder="t('sms.type')" showClear class="w-full" />
                    <Select v-model="f.event" :options="eventOptions" optionLabel="label" optionValue="value" :placeholder="t('sms.event')" showClear class="w-full" />
                    <Select v-model="f.gateway_id" :options="gateways" optionLabel="name" optionValue="id" :placeholder="t('sms.gateway')" showClear class="w-full" />
                    <DatePicker v-model="f.date_from" :placeholder="t('shared.terms.from')" dateFormat="dd-mm-yy" showIcon class="w-full" />
                    <DatePicker v-model="f.date_to" :placeholder="t('shared.terms.to')" dateFormat="dd-mm-yy" showIcon class="w-full" />
                    <div class="flex gap-2">
                        <Button :label="t('shared.terms.apply')" icon="pi pi-filter" outlined @click="apply" />
                        <Button :label="t('shared.terms.reset')" icon="pi pi-refresh" text @click="reset" />
                    </div>
                </div>
                <div class="flex justify-end"><ExportButtons routeName="sms.logs" :params="params" /></div>
            </div>

            <div class="card">
                <DataTable :value="rows" v-model:selection="selected" dataKey="id" size="small" showGridlines scrollable>
                    <Column v-if="canSend" selectionMode="multiple" headerStyle="width: 3rem" />
                    <Column :header="t('shared.terms.date')">
                        <template #body="{ data }"><span class="text-xs text-slate-600 whitespace-nowrap">{{ dateTime(data.created_at) }}</span></template>
                    </Column>
                    <Column :header="t('shared.terms.phone')">
                        <template #body="{ data }"><span class="font-medium text-slate-800 whitespace-nowrap ltr-nums">{{ data.phone }}</span></template>
                    </Column>
                    <Column :header="t('shared.terms.customer')">
                        <template #body="{ data }">{{ data.customer?.name || "-" }}</template>
                    </Column>
                    <Column :header="t('sms.type')">
                        <template #body="{ data }">
                            <div class="flex flex-col gap-1 items-start">
                                <Tag :value="eventLabel(data.event || data.type)" severity="secondary" />
                                <span v-if="data.campaign" class="text-[11px] text-slate-500">{{ data.campaign.name }}</span>
                            </div>
                        </template>
                    </Column>
                    <Column :header="t('sms.gateway')">
                        <template #body="{ data }">{{ data.gateway?.name || "-" }}</template>
                    </Column>
                    <Column :header="t('sms.parts')">
                        <template #body="{ data }">{{ data.segments }}</template>
                    </Column>
                    <Column :header="t('common.status')">
                        <template #body="{ data }">
                            <Tag :value="t('shared.status.' + data.status)" :severity="statusSeverity(data.status)" />
                            <div v-if="data.status === 'failed' && data.error" dir="auto" class="text-[11px] text-rose-700 mt-1 max-w-[220px] break-words">{{ data.error }}</div>
                        </template>
                    </Column>
                    <Column :header="t('sms.message')">
                        <template #body="{ data }">
                            <div dir="auto" class="text-xs text-slate-700 max-w-[360px] cursor-pointer" :class="expanded[data.id] ? 'whitespace-pre-wrap' : 'truncate'"
                                @click="expanded[data.id] = !expanded[data.id]" :title="data.message">{{ data.message }}</div>
                        </template>
                    </Column>
                    <Column header="">
                        <template #body="{ data }">
                            <Button v-if="canSend && data.status === 'failed'" icon="pi pi-replay" text size="small" severity="warn" v-tooltip.top="t('sms.logs.resend')" :aria-label="t('sms.logs.resend')" @click="resend([data.id])" />
                        </template>
                    </Column>
                    <template #empty><div class="text-center text-slate-400 py-6">{{ t('sms.logs.empty') }}</div></template>
                </DataTable>

                <div class="flex justify-between items-center mt-3 text-sm text-slate-500">
                    <span>{{ t('shared.terms.showing', { from: logs.from || 0, to: logs.to || 0, total: logs.total || 0 }) }}</span>
                    <div class="flex gap-1">
                        <Button icon="pi pi-angle-left" text :aria-label="t('shared.terms.previous')" :disabled="!logs.prev_page_url" @click="goToPage(logs.prev_page_url)" />
                        <Button icon="pi pi-angle-right" text :aria-label="t('shared.terms.next')" :disabled="!logs.next_page_url" @click="goToPage(logs.next_page_url)" />
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
