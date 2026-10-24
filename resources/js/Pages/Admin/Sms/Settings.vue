<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, router } from "@inertiajs/vue3";
import { reactive, ref, watch } from "vue";
import Button from "primevue/button";
import Dialog from "primevue/dialog";
import InputNumber from "primevue/inputnumber";
import InputText from "primevue/inputtext";
import Select from "primevue/select";
import Tab from "primevue/tab";
import TabList from "primevue/tablist";
import TabPanel from "primevue/tabpanel";
import TabPanels from "primevue/tabpanels";
import Tabs from "primevue/tabs";
import Tag from "primevue/tag";
import Tooltip from "primevue/tooltip";

const vTooltip = Tooltip;
import Textarea from "primevue/textarea";
import ToggleSwitch from "primevue/toggleswitch";
import { useToast } from "primevue/usetoast";
import { useFlashToast } from "@/Composables/useFlashToast";
import { dateTime, firstError, money } from "@/Helpers/format";

useFlashToast();
const toast = useToast();

const props = defineProps({
    settings: { type: Object, default: () => ({}) },
    events: { type: Array, default: () => [] },
    templates: { type: Array, default: () => [] },
    gateways: { type: Array, default: () => [] },
    drivers: { type: Array, default: () => [] },
    placeholders: { type: Array, default: () => [] },
});

const onError = (errors) => toast.add({ severity: "error", summary: "Error", detail: firstError(errors), life: 4000 });

// ---------------- General ----------------
const general = reactive({ ...props.settings });
const eventToggles = reactive(Object.fromEntries(props.events.map((e) => [e.key, !!e.enabled])));
const savingGeneral = ref(false);

const deliveryModes = [
    { label: "Send right after the request (no worker needed)", value: "after_response" },
    { label: "Queue worker (php artisan queue:work)", value: "queue" },
    { label: "Immediately (slower)", value: "sync" },
];
const roundingOptions = [
    { label: "Round installments to whole amount", value: "integer" },
    { label: "Keep decimals", value: "none" },
];

function saveGeneral() {
    savingGeneral.value = true;
    router.put(route("sms.settings.update"), { ...general, events: { ...eventToggles } }, {
        preserveScroll: true,
        onError,
        onFinish: () => (savingGeneral.value = false),
    });
}

// ---------------- Gateways ----------------
const driverLabel = (key) => props.drivers.find((d) => d.value === key)?.label || key;
const showGateway = ref(false);
const editingId = ref(null);
const gForm = reactive({ name: "", driver: "bulksmsbd", sender_id: "", is_active: true, is_default: false, credentials: {} });
const gSaving = ref(false);

const currentDriver = ref(null);
watch(() => gForm.driver, (d) => (currentDriver.value = props.drivers.find((x) => x.value === d) || null), { immediate: true });

function openGateway(g = null) {
    editingId.value = g?.id ?? null;
    Object.assign(gForm, {
        name: g?.name ?? "",
        driver: g?.driver ?? (props.drivers[0]?.value || "log"),
        sender_id: g?.sender_id ?? "",
        is_active: g ? !!g.is_active : true,
        is_default: g ? !!g.is_default : props.gateways.length === 0,
        credentials: { ...(g?.credentials || {}) },
    });
    showGateway.value = true;
}

function saveGateway() {
    gSaving.value = true;
    const payload = { ...gForm, credentials: { ...gForm.credentials } };
    const opts = {
        preserveScroll: true,
        onSuccess: () => (showGateway.value = false),
        onError,
        onFinish: () => (gSaving.value = false),
    };
    editingId.value
        ? router.put(route("sms.gateways.update", editingId.value), payload, opts)
        : router.post(route("sms.gateways.store"), payload, opts);
}

function deleteGateway(g) {
    if (!confirm(`Delete gateway "${g.name}"?`)) return;
    router.delete(route("sms.gateways.destroy", g.id), { preserveScroll: true, onError });
}

const checking = ref(null);
function checkBalance(g) {
    checking.value = g.id;
    router.post(route("sms.gateways.balance", g.id), {}, { preserveScroll: true, onFinish: () => (checking.value = null) });
}

const showTest = ref(false);
const testTarget = ref(null);
const testForm = reactive({ phone: "", message: "" });
const testing = ref(false);
function openTest(g) {
    testTarget.value = g;
    testForm.phone = "";
    testForm.message = "";
    showTest.value = true;
}
function sendTest() {
    testing.value = true;
    router.post(route("sms.gateways.test", testTarget.value.id), { ...testForm }, {
        preserveScroll: true,
        onSuccess: () => (showTest.value = false),
        onError,
        onFinish: () => (testing.value = false),
    });
}

// ---------------- Templates ----------------
const tplForms = ref(props.templates.map((t) => ({ id: t.id, key: t.key, name: t.name, body: t.body, is_active: !!t.is_active })));
watch(() => props.templates, (list) => {
    tplForms.value = list.map((t) => ({ id: t.id, key: t.key, name: t.name, body: t.body, is_active: !!t.is_active }));
});
const tplRefs = {};

function parts(text) {
    const s = text || "";
    const len = [...s].length;
    if (!len) return { len: 0, parts: 0 };
    const unicode = /[^\x00-\x7F]/.test(s);
    const [single, multi] = unicode ? [70, 67] : [160, 153];
    return { len, parts: len <= single ? 1 : Math.ceil(len / multi), unicode };
}

function insertInto(tpl, ph) {
    const el = tplRefs[tpl.id]?.$el;
    if (el && typeof el.selectionStart === "number") {
        const s = el.selectionStart;
        tpl.body = tpl.body.slice(0, s) + ph + tpl.body.slice(el.selectionEnd);
        requestAnimationFrame(() => {
            el.focus();
            el.selectionStart = el.selectionEnd = s + ph.length;
        });
    } else {
        tpl.body += ph;
    }
}

function saveTemplate(tpl) {
    router.put(route("sms.templates.update", tpl.id), { name: tpl.name, body: tpl.body, is_active: tpl.is_active }, { preserveScroll: true, onError });
}
function deleteTemplate(tpl) {
    if (!confirm(`Delete template "${tpl.name}"?`)) return;
    router.delete(route("sms.templates.destroy", tpl.id), { preserveScroll: true, onError });
}

const showNewTpl = ref(false);
const newTpl = reactive({ name: "", body: "" });
function createTemplate() {
    router.post(route("sms.templates.store"), { ...newTpl }, {
        preserveScroll: true,
        onSuccess: () => {
            showNewTpl.value = false;
            newTpl.name = "";
            newTpl.body = "";
        },
        onError,
    });
}
</script>

<template>
    <Head title="SMS Settings" />
    <AuthenticatedLayout>
        <div class="p-4 md:p-6 space-y-4 max-w-5xl mx-auto">
            <div class="flex flex-wrap justify-between items-start gap-3">
                <div>
                    <h2 class="text-xl font-semibold text-slate-800 flex items-center gap-2"><i class="pi pi-cog text-primary" /> SMS Settings</h2>
                    <p class="text-sm text-slate-500">Turn SMS on/off, configure vendors and message templates</p>
                </div>
                <Link :href="route('sms.dashboard')"><Button label="SMS Dashboard" icon="pi pi-chart-bar" size="small" severity="secondary" outlined /></Link>
            </div>

            <div class="card p-0 md:p-2">
                <Tabs value="general">
                    <TabList>
                        <Tab value="general">General</Tab>
                        <Tab value="gateways">Gateways</Tab>
                        <Tab value="templates">Templates</Tab>
                    </TabList>
                    <TabPanels>
                        <!-- GENERAL -->
                        <TabPanel value="general">
                            <div class="space-y-5">
                                <div class="flex items-center justify-between gap-4 p-3 rounded-xl border"
                                    :class="general.sms_enabled ? 'bg-emerald-50 border-emerald-200' : 'bg-slate-50 border-slate-200'">
                                    <div>
                                        <div class="font-semibold text-slate-800">SMS sending</div>
                                        <div class="text-xs text-slate-500">Master switch. When off, no SMS (automatic or manual) is sent.</div>
                                    </div>
                                    <ToggleSwitch v-model="general.sms_enabled" />
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="text-xs text-slate-500 block mb-1">Country code</label>
                                        <InputText v-model="general.sms_country_code" class="w-full" placeholder="880" />
                                        <div class="text-[11px] text-slate-500 mt-1">Local numbers like 01712345678 become 8801712345678.</div>
                                    </div>
                                    <div>
                                        <label class="text-xs text-slate-500 block mb-1">Delivery mode</label>
                                        <Select v-model="general.sms_delivery_mode" :options="deliveryModes" optionLabel="label" optionValue="value" class="w-full" />
                                    </div>
                                    <div>
                                        <label class="text-xs text-slate-500 block mb-1">Installment reminder (days before due)</label>
                                        <InputNumber v-model="general.sms_installment_reminder_days" :min="0" :max="30" class="w-full" inputClass="w-full" />
                                    </div>
                                    <div>
                                        <label class="text-xs text-slate-500 block mb-1">Installment rounding</label>
                                        <Select v-model="general.installment_rounding" :options="roundingOptions" optionLabel="label" optionValue="value" class="w-full" />
                                    </div>
                                </div>

                                <div>
                                    <div class="font-semibold text-slate-700 mb-2">Automatic messages</div>
                                    <div class="divide-y divide-slate-100 border border-slate-200 rounded-xl">
                                        <div v-for="e in events" :key="e.key" class="flex items-center justify-between gap-3 px-3 py-2.5">
                                            <div>
                                                <div class="text-sm text-slate-800">{{ e.label }}</div>
                                                <div class="text-[11px] text-slate-500">{{ e.key }}</div>
                                            </div>
                                            <ToggleSwitch v-model="eventToggles[e.key]" :disabled="!general.sms_enabled" />
                                        </div>
                                    </div>
                                    <div class="text-[11px] text-slate-500 mt-1">Reminders and overdue notices are sent by the daily scheduler (php artisan schedule:run).</div>
                                </div>

                                <div class="flex justify-end">
                                    <Button label="Save settings" icon="pi pi-save" :loading="savingGeneral" @click="saveGeneral" />
                                </div>
                            </div>
                        </TabPanel>

                        <!-- GATEWAYS -->
                        <TabPanel value="gateways">
                            <div class="space-y-4">
                                <div class="flex flex-wrap items-center justify-between gap-2">
                                    <p class="text-xs text-slate-500 max-w-xl">
                                        Vendor endpoints for BulkSMSBD / Alpha SMS / Greenweb follow their public docs — verify with your vendor; use Generic HTTP for any other provider.
                                    </p>
                                    <Button label="Add gateway" icon="pi pi-plus" size="small" @click="openGateway()" />
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                    <div v-for="g in gateways" :key="g.id" class="border border-slate-200 rounded-xl p-4 space-y-2">
                                        <div class="flex items-start justify-between gap-2">
                                            <div class="min-w-0">
                                                <div class="font-semibold text-slate-800 truncate">{{ g.name }}</div>
                                                <div class="text-xs text-slate-500">{{ driverLabel(g.driver) }}<span v-if="g.sender_id"> · Sender: {{ g.sender_id }}</span></div>
                                            </div>
                                            <div class="flex gap-1 shrink-0">
                                                <Tag v-if="g.is_default" value="Default" severity="info" />
                                                <Tag :value="g.is_active ? 'Active' : 'Inactive'" :severity="g.is_active ? 'success' : 'secondary'" />
                                            </div>
                                        </div>
                                        <div class="text-sm text-slate-700">
                                            Balance: <b>{{ g.balance !== null && g.balance !== undefined ? money(g.balance) : "—" }}</b>
                                            <span class="text-xs text-slate-500 ml-1">{{ g.balance_checked_at ? "(" + dateTime(g.balance_checked_at) + ")" : "" }}</span>
                                        </div>
                                        <div class="flex flex-wrap gap-1">
                                            <Button label="Edit" icon="pi pi-pencil" size="small" outlined @click="openGateway(g)" />
                                            <Button label="Test" icon="pi pi-send" size="small" outlined severity="secondary" @click="openTest(g)" />
                                            <Button v-if="drivers.find((d) => d.value === g.driver)?.supports_balance" label="Balance" icon="pi pi-wallet" size="small" outlined
                                                severity="secondary" :loading="checking === g.id" @click="checkBalance(g)" />
                                            <Button icon="pi pi-trash" size="small" text severity="danger" @click="deleteGateway(g)" v-tooltip.top="'Delete'" />
                                        </div>
                                    </div>
                                    <div v-if="!gateways.length" class="text-sm text-slate-500 p-4 border border-dashed border-slate-300 rounded-xl">
                                        No gateway yet. Add your SMS vendor to start sending.
                                    </div>
                                </div>
                            </div>
                        </TabPanel>

                        <!-- TEMPLATES -->
                        <TabPanel value="templates">
                            <div class="space-y-4">
                                <div class="flex flex-wrap items-center justify-between gap-2">
                                    <div class="flex flex-wrap gap-1">
                                        <span v-for="ph in placeholders" :key="ph" class="text-[11px] px-2 py-0.5 rounded-full bg-slate-100 border border-slate-200 text-slate-600">{{ ph }}</span>
                                    </div>
                                    <Button label="New template" icon="pi pi-plus" size="small" @click="showNewTpl = true" />
                                </div>

                                <div v-for="tpl in tplForms" :key="tpl.id" class="border border-slate-200 rounded-xl p-4 space-y-2">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <InputText v-model="tpl.name" class="flex-1 min-w-[180px]" size="small" />
                                        <span class="text-[11px] text-slate-500">{{ tpl.key }}</span>
                                        <label class="flex items-center gap-2 text-xs text-slate-600 ml-auto">Active <ToggleSwitch v-model="tpl.is_active" /></label>
                                    </div>
                                    <Textarea :ref="(el) => (tplRefs[tpl.id] = el)" v-model="tpl.body" rows="3" class="w-full text-sm" />
                                    <div class="flex flex-wrap items-center gap-1">
                                        <Button v-for="ph in placeholders" :key="ph" :label="ph" size="small" text class="!text-[11px] !px-1.5 !py-0.5" @click="insertInto(tpl, ph)" />
                                    </div>
                                    <div class="flex flex-wrap items-center justify-between gap-2">
                                        <span class="text-xs text-slate-500">{{ parts(tpl.body).len }} chars · {{ parts(tpl.body).parts }} part(s){{ parts(tpl.body).unicode ? " · Unicode" : "" }}</span>
                                        <div class="flex gap-1">
                                            <Button v-if="tpl.key.startsWith('custom_')" icon="pi pi-trash" size="small" text severity="danger" @click="deleteTemplate(tpl)" />
                                            <Button label="Save" icon="pi pi-save" size="small" @click="saveTemplate(tpl)" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </TabPanel>
                    </TabPanels>
                </Tabs>
            </div>
        </div>

        <!-- Gateway dialog -->
        <Dialog v-model:visible="showGateway" modal :header="editingId ? 'Edit gateway' : 'Add gateway'" :style="{ width: '560px' }" :breakpoints="{ '640px': '95vw' }">
            <div class="space-y-3">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="text-xs text-slate-500 block mb-1">Name *</label>
                        <InputText v-model="gForm.name" class="w-full" />
                    </div>
                    <div>
                        <label class="text-xs text-slate-500 block mb-1">Driver *</label>
                        <Select v-model="gForm.driver" :options="drivers" optionLabel="label" optionValue="value" class="w-full" />
                    </div>
                    <div>
                        <label class="text-xs text-slate-500 block mb-1">Sender ID / From</label>
                        <InputText v-model="gForm.sender_id" class="w-full" />
                    </div>
                    <div class="flex items-end gap-4 pb-1">
                        <label class="flex items-center gap-2 text-sm text-slate-700"><ToggleSwitch v-model="gForm.is_active" /> Active</label>
                        <label class="flex items-center gap-2 text-sm text-slate-700"><ToggleSwitch v-model="gForm.is_default" /> Default</label>
                    </div>
                </div>

                <div v-if="currentDriver?.fields?.length" class="space-y-3 pt-2 border-t border-slate-100">
                    <div v-for="field in currentDriver.fields" :key="field.key">
                        <label class="text-xs text-slate-500 block mb-1">{{ field.label }}</label>
                        <Select v-if="field.type === 'select'" v-model="gForm.credentials[field.key]" :options="field.options" class="w-full" />
                        <Textarea v-else-if="field.type === 'textarea'" v-model="gForm.credentials[field.key]" rows="3" class="w-full text-sm" />
                        <InputText v-else v-model="gForm.credentials[field.key]" :type="field.type === 'password' ? 'password' : 'text'" class="w-full" autocomplete="off" />
                        <div v-if="field.help" class="text-[11px] text-slate-500 mt-0.5">{{ field.help }}</div>
                    </div>
                </div>
                <div v-else class="text-xs text-slate-500">This driver needs no credentials.</div>
            </div>
            <template #footer>
                <Button label="Cancel" text @click="showGateway = false" />
                <Button label="Save" icon="pi pi-save" :loading="gSaving" @click="saveGateway" />
            </template>
        </Dialog>

        <!-- Test dialog -->
        <Dialog v-model:visible="showTest" modal :header="'Test ' + (testTarget?.name || '')" :style="{ width: '440px' }" :breakpoints="{ '640px': '95vw' }">
            <div class="space-y-3">
                <div>
                    <label class="text-xs text-slate-500 block mb-1">Phone *</label>
                    <InputText v-model="testForm.phone" class="w-full" placeholder="01712345678" />
                </div>
                <div>
                    <label class="text-xs text-slate-500 block mb-1">Message (optional)</label>
                    <Textarea v-model="testForm.message" rows="3" class="w-full text-sm" />
                </div>
            </div>
            <template #footer>
                <Button label="Cancel" text @click="showTest = false" />
                <Button label="Send test" icon="pi pi-send" :loading="testing" @click="sendTest" />
            </template>
        </Dialog>

        <!-- New template dialog -->
        <Dialog v-model:visible="showNewTpl" modal header="New template" :style="{ width: '520px' }" :breakpoints="{ '640px': '95vw' }">
            <div class="space-y-3">
                <div>
                    <label class="text-xs text-slate-500 block mb-1">Name *</label>
                    <InputText v-model="newTpl.name" class="w-full" placeholder="Puja greetings" />
                </div>
                <div>
                    <label class="text-xs text-slate-500 block mb-1">Message *</label>
                    <Textarea v-model="newTpl.body" rows="4" class="w-full text-sm" />
                    <div class="text-xs text-slate-500 mt-1">{{ parts(newTpl.body).len }} chars · {{ parts(newTpl.body).parts }} part(s)</div>
                </div>
            </div>
            <template #footer>
                <Button label="Cancel" text @click="showNewTpl = false" />
                <Button label="Create" icon="pi pi-plus" @click="createTemplate" />
            </template>
        </Dialog>
    </AuthenticatedLayout>
</template>
