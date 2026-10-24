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
import { useToast } from "primevue/usetoast";
import { useFlashToast } from "@/Composables/useFlashToast";
import { firstError } from "@/Helpers/format";

useFlashToast();
const toast = useToast();

const props = defineProps({
    templates: { type: Array, default: () => [] },
    gateways: { type: Array, default: () => [] },
    audiences: { type: Array, default: () => [] },
    enabled: { type: Boolean, default: false },
});

// ---------------- Recipients ----------------
const modeOptions = [
    { label: "Pick customers", value: "pick" },
    { label: "Whole audience", value: "audience" },
];
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
        toast.add({ severity: "warn", summary: "Search error", detail: "Could not load customers", life: 2500 });
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
const audienceInfo = computed(() => props.audiences.find((a) => a.value === audience.value));
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
        .replaceAll("{customer_name}", first?.name || "Customer")
        .replaceAll("{name}", first?.name || "Customer")
        .replaceAll("{phone}", first?.phone || "01XXXXXXXXX")
        .replaceAll("{date}", new Date().toLocaleDateString("en-GB").replaceAll("/", "-"));
});

// ---------------- Submit ----------------
const showConfirm = ref(false);
const sending = ref(false);

function openConfirm() {
    if (!message.value.trim()) {
        toast.add({ severity: "warn", summary: "Message required", detail: "Write a message first", life: 2500 });
        return;
    }
    if (!recipientEstimate.value) {
        toast.add({ severity: "warn", summary: "No recipients", detail: "Select customers or enter numbers", life: 2500 });
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
                toast.add({ severity: "error", summary: "Not sent", detail: firstError(errors), life: 4000 });
            },
            onFinish: () => (sending.value = false),
        }
    );
}
</script>

<template>
    <Head title="Send SMS" />
    <AuthenticatedLayout>
        <div class="p-4 md:p-6 space-y-4">
            <div class="flex flex-wrap justify-between items-start gap-3">
                <div>
                    <h2 class="text-xl font-semibold text-slate-800 flex items-center gap-2"><i class="pi pi-send text-primary" /> Send SMS</h2>
                    <p class="text-sm text-slate-500">Festival greetings, offers and custom messages to your customers</p>
                </div>
                <div class="flex gap-2">
                    <Link :href="route('sms.dashboard')"><Button label="Dashboard" icon="pi pi-chart-bar" size="small" severity="secondary" outlined /></Link>
                    <Link :href="route('sms.logs')"><Button label="History" icon="pi pi-history" size="small" severity="secondary" outlined /></Link>
                </div>
            </div>

            <div v-if="!enabled" class="p-3 rounded-xl border bg-amber-50 border-amber-200 text-amber-900 text-sm flex flex-wrap items-center gap-2">
                <i class="pi pi-exclamation-triangle" /> SMS sending is turned off.
                <Link :href="route('sms.settings')" class="font-semibold underline">Enable in SMS Settings</Link>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                <!-- Recipients -->
                <div class="card p-4 space-y-3">
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <h3 class="font-semibold text-slate-700">Recipients</h3>
                        <SelectButton v-model="mode" :options="modeOptions" optionLabel="label" optionValue="value" :allowEmpty="false" size="small" />
                    </div>

                    <div>
                        <label class="text-xs text-slate-500 block mb-1">Audience</label>
                        <Select v-model="audience" :options="audiences" optionValue="value" class="w-full">
                            <template #value="{ value }">
                                <span>{{ audiences.find((a) => a.value === value)?.label }} ({{ audiences.find((a) => a.value === value)?.count ?? 0 }})</span>
                            </template>
                            <template #option="{ option }">
                                <div class="flex justify-between w-full gap-3"><span>{{ option.label }}</span><span class="text-slate-500">{{ option.count }}</span></div>
                            </template>
                        </Select>
                    </div>

                    <template v-if="mode === 'pick'">
                        <InputText v-model="search" placeholder="Search name or phone (partial, * wildcard)" class="w-full" />
                        <div class="flex flex-wrap items-center justify-between gap-2 text-xs text-slate-500">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <Checkbox :modelValue="allOnPageSelected" binary @update:modelValue="toggleAllOnPage" />
                                Select all shown ({{ customers.length }} of {{ total }})
                            </label>
                            <span v-if="loading"><i class="pi pi-spin pi-spinner" /> Loading…</span>
                        </div>
                        <div class="border border-slate-200 rounded-xl max-h-80 overflow-y-auto divide-y divide-slate-100">
                            <label v-for="c in customers" :key="c.id" class="flex items-center gap-3 px-3 py-2 cursor-pointer hover:bg-slate-50">
                                <Checkbox :modelValue="isSelected(c)" binary @update:modelValue="toggle(c)" />
                                <div class="min-w-0">
                                    <div class="text-sm font-medium text-slate-800 truncate">{{ c.name }}</div>
                                    <div class="text-xs text-slate-500">{{ c.phone }}</div>
                                </div>
                            </label>
                            <div v-if="!customers.length && !loading" class="p-4 text-center text-sm text-slate-400">No customers found</div>
                        </div>
                        <Button v-if="page < lastPage" label="Load more" icon="pi pi-angle-down" text size="small" :loading="loading" @click="load(false)" />

                        <div class="flex flex-wrap items-center gap-2 p-2 rounded-lg bg-slate-50 border border-slate-200">
                            <span class="text-sm text-slate-700 font-medium">{{ selectedList.length }} selected</span>
                            <span v-for="c in selectedList.slice(0, 6)" :key="c.id"
                                class="text-xs px-2 py-0.5 rounded-full bg-white border border-slate-200 text-slate-600 flex items-center gap-1">
                                {{ c.name }} <i class="pi pi-times cursor-pointer !text-[10px]" @click="toggle(c)" />
                            </span>
                            <span v-if="selectedList.length > 6" class="text-xs text-slate-500">+{{ selectedList.length - 6 }} more</span>
                            <Button v-if="selectedList.length" label="Clear" text size="small" class="ml-auto" @click="clearSelected" />
                        </div>
                    </template>

                    <div v-else class="p-3 rounded-xl bg-sky-50 border border-sky-200 text-sm text-slate-700">
                        Message goes to <b>{{ audienceInfo?.count ?? 0 }}</b> customers in “{{ audienceInfo?.label }}”.
                    </div>

                    <div>
                        <label class="text-xs text-slate-500 block mb-1">Extra numbers (comma or new line separated)</label>
                        <Textarea v-model="numbers" rows="3" class="w-full text-sm" placeholder="01712345678, 01812345678" />
                        <div class="text-xs text-slate-500 mt-1">{{ manualNumbers.length }} number(s)</div>
                    </div>
                </div>

                <!-- Message -->
                <div class="card p-4 space-y-3">
                    <h3 class="font-semibold text-slate-700">Message</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="text-xs text-slate-500 block mb-1">Campaign name (optional)</label>
                            <InputText v-model="name" class="w-full" placeholder="Eid ul-Fitr greetings" />
                        </div>
                        <div>
                            <label class="text-xs text-slate-500 block mb-1">Template</label>
                            <Select v-model="templateId" :options="templates" optionLabel="name" optionValue="id" placeholder="Choose template" showClear class="w-full" />
                        </div>
                    </div>

                    <div>
                        <div class="flex flex-wrap gap-1 mb-1">
                            <Button v-for="ph in placeholders" :key="ph" :label="ph" size="small" severity="secondary" outlined class="!text-xs !py-0.5" @click="insert(ph)" />
                        </div>
                        <Textarea ref="textareaRef" v-model="message" rows="6" class="w-full" placeholder="Type your message…" />
                        <div class="flex flex-wrap justify-between text-xs text-slate-500 mt-1 gap-2">
                            <span>{{ charCount }} chars · {{ parts }} part(s) · {{ isUnicode ? "Unicode (70/67)" : "GSM (160/153)" }}</span>
                            <span>Estimated: {{ recipientEstimate }} recipients × {{ parts }} = <b class="text-slate-700">{{ totalParts }}</b> parts</span>
                        </div>
                    </div>

                    <div>
                        <label class="text-xs text-slate-500 block mb-1">Preview</label>
                        <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 text-sm text-slate-700 whitespace-pre-wrap min-h-[3rem]">{{ previewText || "—" }}</div>
                    </div>

                    <div>
                        <label class="text-xs text-slate-500 block mb-1">Gateway</label>
                        <Select v-model="gatewayId" :options="gateways" optionLabel="name" optionValue="id" placeholder="Default gateway" showClear class="w-full" />
                    </div>

                    <Button label="Send SMS" icon="pi pi-send" class="w-full" :disabled="!enabled" @click="openConfirm" />
                </div>
            </div>
        </div>

        <Dialog v-model:visible="showConfirm" modal header="Confirm sending" :style="{ width: '440px' }" :breakpoints="{ '640px': '95vw' }">
            <div class="space-y-2 text-sm text-slate-700">
                <p>Send this message to about <b>{{ recipientEstimate }}</b> recipient(s)?</p>
                <p class="text-slate-500">Estimated usage: {{ totalParts }} SMS part(s). Duplicate and invalid numbers are skipped.</p>
                <div class="p-2 rounded-lg bg-slate-50 border border-slate-200 whitespace-pre-wrap">{{ previewText }}</div>
            </div>
            <template #footer>
                <Button label="Cancel" text @click="showConfirm = false" />
                <Button label="Send now" icon="pi pi-send" :loading="sending" @click="send" />
            </template>
        </Dialog>
    </AuthenticatedLayout>
</template>
