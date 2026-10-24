<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, router } from "@inertiajs/vue3";
import axios from "axios";
import { computed, onMounted, ref, watch } from "vue";
import Button from "primevue/button";
import Checkbox from "primevue/checkbox";
import Dialog from "primevue/dialog";
import InputText from "primevue/inputtext";
import Select from "primevue/select";
import SelectButton from "primevue/selectbutton";
import Textarea from "primevue/textarea";
import Tooltip from "primevue/tooltip";
import { useToast } from "primevue/usetoast";
import { useFlashToast } from "@/Composables/useFlashToast";
import { useI18n } from "vue-i18n";
import { firstError } from "@/Helpers/format";

useFlashToast();
const toast = useToast();
const { t } = useI18n();
const vTooltip = Tooltip;

const props = defineProps({
    templates: { type: Array, default: () => [] },
    gateways: { type: Array, default: () => [] },
    audiences: { type: Array, default: () => [] },
    enabled: { type: Boolean, default: false },
});

// ---------------- Recipients ----------------
const modeOptions = computed(() => [
    { label: t("sms.send.pick_customers"), value: "pick" },
    { label: t("sms.send.whole_audience"), value: "audience" },
]);
const audienceOptions = computed(() => props.audiences.map((a) => ({ ...a, label: t(`sms.audience.${a.value}`) })));
const mode = ref("pick");
const audience = ref("all");
const search = ref("");
const customers = ref([]);
const page = ref(1);
const lastPage = ref(1);
const total = ref(0);
const loading = ref(false);
const selected = ref({}); // id => {id,name,phone}
const numbers = ref("");

let timer = null;
let seq = 0;
async function load(reset = true) {
    const mine = ++seq;
    loading.value = true;
    try {
        const { data } = await axios.get(route("sms.customers"), {
            params: { q: search.value || undefined, audience: audience.value, page: reset ? 1 : page.value + 1 },
        });
        if (mine !== seq) return;
        customers.value = reset ? data.data : [...customers.value, ...data.data];
        page.value = data.current_page;
        lastPage.value = data.last_page;
        total.value = data.total;
    } catch (e) {
        toast.add({ severity: "warn", summary: t("shared.terms.error"), detail: t("sms.send.load_failed"), life: 2500 });
    } finally {
        if (mine === seq) loading.value = false;
    }
}
watch([search, audience], () => {
    clearTimeout(timer);
    timer = setTimeout(() => load(true), 300);
});
onMounted(() => load(true));

const selectedList = computed(() => Object.values(selected.value));
function isSelected(c) {
    return !!selected.value[c.id];
}
function toggle(c) {
    const copy = { ...selected.value };
    if (copy[c.id]) delete copy[c.id];
    else copy[c.id] = c;
    selected.value = copy;
}
const allOnPageSelected = computed(() => customers.value.length > 0 && customers.value.every((c) => selected.value[c.id]));
function toggleAllOnPage() {
    const copy = { ...selected.value };
    const all = allOnPageSelected.value;
    customers.value.forEach((c) => (all ? delete copy[c.id] : (copy[c.id] = c)));
    selected.value = copy;
}
function clearSelected() {
    selected.value = {};
}

const manualNumbers = computed(() => numbers.value.split(/[\s,;]+/).map((x) => x.trim()).filter((x) => x.replace(/\D/g, "").length >= 8));
const audienceInfo = computed(() => audienceOptions.value.find((a) => a.value === audience.value));
const recipientEstimate = computed(() =>
    (mode.value === "audience" ? audienceInfo.value?.count || 0 : selectedList.value.length) + manualNumbers.value.length
);

// ---------------- Message ----------------
const name = ref("");
const message = ref("");
const templateId = ref(null);
const defaultGateway = props.gateways.find((g) => g.is_default) || props.gateways[0];
const gatewayId = ref(defaultGateway?.id ?? null);
const textareaRef = ref(null);

watch(templateId, (id) => {
    const tpl = props.templates.find((t) => t.id === id);
    if (tpl) message.value = tpl.body;
});

const placeholders = ["{customer_name}", "{shop_name}", "{phone}", "{date}"];
function insert(ph) {
    const el = textareaRef.value?.$el;
    if (el && typeof el.selectionStart === "number") {
        const start = el.selectionStart;
        const end = el.selectionEnd;
        message.value = message.value.slice(0, start) + ph + message.value.slice(end);
        requestAnimationFrame(() => {
            el.focus();
            el.selectionStart = el.selectionEnd = start + ph.length;
        });
    } else {
        message.value += ph;
    }
}

// same rule as SmsManager::segments
const isUnicode = computed(() => /[^\x00-\x7F]/.test(message.value));
const charCount = computed(() => [...message.value].length);
const parts = computed(() => {
    const len = charCount.value;
    if (!len) return 0;
    const [single, multi] = isUnicode.value ? [70, 67] : [160, 153];
    return len <= single ? 1 : Math.ceil(len / multi);
});
const totalParts = computed(() => parts.value * recipientEstimate.value);

const previewText = computed(() => {
    const first = mode.value === "pick" ? selectedList.value[0] : null;
    return message.value
        .replaceAll("{customer_name}", first?.name || t("shared.terms.customer"))
        .replaceAll("{name}", first?.name || t("shared.terms.customer"))
        .replaceAll("{phone}", first?.phone || "01XXXXXXXXX")
        .replaceAll("{date}", new Date().toLocaleDateString("en-GB").replaceAll("/", "-"));
});

// ---------------- Submit ----------------
const showConfirm = ref(false);
const sending = ref(false);

function openConfirm() {
    if (!message.value.trim()) {
        toast.add({ severity: "warn", summary: t("sms.send.message_required"), detail: t("sms.send.write_first"), life: 2500 });
        return;
    }
    if (!recipientEstimate.value) {
        toast.add({ severity: "warn", summary: t("sms.send.no_recipients"), detail: t("sms.send.select_recipients"), life: 2500 });
        return;
    }
    showConfirm.value = true;
}

function send() {
    sending.value = true;
    router.post(
        route("sms.send"),
        {
            name: name.value || null,
            message: message.value,
            customer_ids: mode.value === "pick" ? selectedList.value.map((c) => c.id) : [],
            audience: mode.value === "audience" ? audience.value : null,
            numbers: numbers.value || null,
            gateway_id: gatewayId.value,
        },
        {
            preserveScroll: true,
            onSuccess: () => (showConfirm.value = false),
            onError: (errors) => {
                showConfirm.value = false;
                toast.add({ severity: "error", summary: t("sms.send.not_sent"), detail: firstError(errors), life: 4000 });
            },
            onFinish: () => (sending.value = false),
        }
    );
}
</script>

<template>
    <Head :title="t('sms.send_sms')" />
    <AuthenticatedLayout>
        <div class="p-4 md:p-6 space-y-4">
            <div class="flex flex-wrap justify-between items-start gap-3">
                <div>
                    <h1 class="text-xl font-semibold text-slate-800 flex items-center gap-2"><i class="pi pi-send text-primary" /> {{ t('sms.send_sms') }}</h1>
                    <p class="text-sm text-slate-500">{{ t('sms.send.subtitle') }}</p>
                </div>
                <div class="flex gap-2">
                    <Link :href="route('sms.dashboard')"><Button :label="t('sms.dashboard.title')" icon="pi pi-chart-pie" size="small" severity="secondary" outlined /></Link>
                    <Link :href="route('sms.logs')"><Button :label="t('sms.history')" icon="pi pi-history" size="small" severity="secondary" outlined /></Link>
                </div>
            </div>

            <div v-if="!enabled" class="p-3 rounded-xl border bg-amber-50 border-amber-200 text-amber-900 text-sm flex flex-wrap items-center gap-2">
                <i class="pi pi-exclamation-triangle" /> {{ t('sms.disabled_banner') }}
                <Link :href="route('sms.settings')" class="font-semibold underline">{{ t('sms.enable_in_settings') }}</Link>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                <!-- Recipients -->
                <div class="card p-4 space-y-3">
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <h2 class="font-semibold text-slate-700">{{ t('sms.recipients') }}</h2>
                        <SelectButton v-model="mode" :options="modeOptions" optionLabel="label" optionValue="value" :allowEmpty="false" size="small" />
                    </div>

                    <div>
                        <label class="text-xs text-slate-500 block mb-1">{{ t('sms.send.audience') }}</label>
                        <Select v-model="audience" :options="audienceOptions" optionValue="value" class="w-full">
                            <template #value="{ value }">
                                <span>{{ audienceOptions.find((a) => a.value === value)?.label }} ({{ audienceOptions.find((a) => a.value === value)?.count ?? 0 }})</span>
                            </template>
                            <template #option="{ option }">
                                <div class="flex justify-between w-full gap-3"><span>{{ option.label }}</span><span class="text-slate-500">{{ option.count }}</span></div>
                            </template>
                        </Select>
                    </div>

                    <template v-if="mode === 'pick'">
                        <InputText v-model="search" type="search" :placeholder="t('sms.send.search_placeholder')" v-tooltip.bottom="t('shared.terms.search_hint')" class="w-full" />
                        <div class="flex flex-wrap items-center justify-between gap-2 text-xs text-slate-500">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <Checkbox :modelValue="allOnPageSelected" binary @update:modelValue="toggleAllOnPage" />
                                {{ t('sms.send.select_all_shown', { shown: customers.length, total }) }}
                            </label>
                            <span v-if="loading"><i class="pi pi-spin pi-spinner" /> {{ t('common.loading') }}</span>
                        </div>
                        <div class="border border-slate-200 rounded-xl max-h-80 overflow-y-auto divide-y divide-slate-100">
                            <label v-for="c in customers" :key="c.id" class="flex items-center gap-3 px-3 py-2 cursor-pointer hover:bg-slate-50">
                                <Checkbox :modelValue="isSelected(c)" binary @update:modelValue="toggle(c)" />
                                <div class="min-w-0">
                                    <div class="text-sm font-medium text-slate-800 truncate">{{ c.name }}</div>
                                    <div class="text-xs text-slate-500 ltr-nums text-start">{{ c.phone }}</div>
                                </div>
                            </label>
                            <div v-if="!customers.length && !loading" class="p-4 text-center text-sm text-slate-400">{{ t('sms.send.no_customers') }}</div>
                        </div>
                        <Button v-if="page < lastPage" :label="t('sms.send.load_more')" icon="pi pi-angle-down" text size="small" :loading="loading" @click="load(false)" />

                        <div class="flex flex-wrap items-center gap-2 p-2 rounded-lg bg-slate-50 border border-slate-200">
                            <span class="text-sm text-slate-700 font-medium">{{ t('sms.send.selected_count', { n: selectedList.length }) }}</span>
                            <span v-for="c in selectedList.slice(0, 6)" :key="c.id"
                                class="text-xs px-2 py-0.5 rounded-full bg-white border border-slate-200 text-slate-600 flex items-center gap-1">
                                {{ c.name }} <button type="button" class="leading-none" :aria-label="t('common.delete')" @click="toggle(c)"><i class="pi pi-times !text-[10px]" /></button>
                            </span>
                            <span v-if="selectedList.length > 6" class="text-xs text-slate-500">{{ t('sms.send.more', { n: selectedList.length - 6 }) }}</span>
                            <Button v-if="selectedList.length" :label="t('sms.send.clear')" text size="small" class="ms-auto" @click="clearSelected" />
                        </div>
                    </template>

                    <div v-else class="p-3 rounded-xl bg-sky-50 border border-sky-200 text-sm text-slate-700">
                        {{ t('sms.send.audience_summary', { n: audienceInfo?.count ?? 0, audience: audienceInfo?.label }) }}
                    </div>

                    <div>
                        <label class="text-xs text-slate-500 block mb-1">{{ t('sms.send.extra_numbers') }}</label>
                        <Textarea v-model="numbers" rows="3" class="w-full text-sm ltr-nums" dir="ltr" placeholder="01712345678, 01812345678" />
                        <div class="text-xs text-slate-500 mt-1">{{ t('sms.send.numbers_count', { n: manualNumbers.length }) }}</div>
                    </div>
                </div>

                <!-- Message -->
                <div class="card p-4 space-y-3">
                    <h2 class="font-semibold text-slate-700">{{ t('sms.message') }}</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="text-xs text-slate-500 block mb-1">{{ t('sms.send.campaign_name') }}</label>
                            <InputText v-model="name" class="w-full" dir="auto" :placeholder="t('sms.send.campaign_placeholder')" />
                        </div>
                        <div>
                            <label class="text-xs text-slate-500 block mb-1">{{ t('sms.template') }}</label>
                            <Select v-model="templateId" :options="templates" optionLabel="name" optionValue="id" :placeholder="t('sms.send.choose_template')" showClear class="w-full" />
                        </div>
                    </div>

                    <div>
                        <div class="flex flex-wrap gap-1 mb-1">
                            <Button v-for="ph in placeholders" :key="ph" :label="ph" size="small" severity="secondary" outlined class="!text-xs !py-0.5" @click="insert(ph)" />
                        </div>
                        <Textarea ref="textareaRef" v-model="message" rows="6" dir="auto" class="w-full" :placeholder="t('sms.send.type_message')" />
                        <div class="flex flex-wrap justify-between text-xs text-slate-500 mt-1 gap-2">
                            <span>{{ t('sms.counter', { chars: charCount, parts }) }} · {{ isUnicode ? "Unicode (70/67)" : "GSM (160/153)" }}</span>
                            <span>{{ t('sms.send.estimate', { n: recipientEstimate, parts, total: totalParts }) }}</span>
                        </div>
                    </div>

                    <div>
                        <label class="text-xs text-slate-500 block mb-1">{{ t('sms.preview') }}</label>
                        <div dir="auto" class="p-3 rounded-xl bg-slate-50 border border-slate-200 text-sm text-slate-700 whitespace-pre-wrap min-h-[3rem]">{{ previewText || "—" }}</div>
                    </div>

                    <div>
                        <label class="text-xs text-slate-500 block mb-1">{{ t('sms.gateway') }}</label>
                        <Select v-model="gatewayId" :options="gateways" optionLabel="name" optionValue="id" :placeholder="t('sms.send.default_gateway')" showClear class="w-full" />
                    </div>

                    <Button :label="t('sms.send_sms')" icon="pi pi-send" class="w-full" :disabled="!enabled || sending" @click="openConfirm" />
                </div>
            </div>
        </div>

        <Dialog v-model:visible="showConfirm" modal :header="t('sms.send.confirm_title')" :style="{ width: '440px' }" :breakpoints="{ '640px': '95vw' }">
            <div class="space-y-2 text-sm text-slate-700">
                <p>{{ t('sms.send.confirm_text', { n: recipientEstimate }) }}</p>
                <p class="text-slate-500">{{ t('sms.send.confirm_usage', { parts: totalParts }) }}</p>
                <div dir="auto" class="p-2 rounded-lg bg-slate-50 border border-slate-200 whitespace-pre-wrap">{{ previewText }}</div>
            </div>
            <template #footer>
                <Button :label="t('shared.terms.cancel')" text @click="showConfirm = false" />
                <Button :label="t('sms.send.send_now')" icon="pi pi-send" :loading="sending" @click="send" />
            </template>
        </Dialog>
    </AuthenticatedLayout>
</template>
