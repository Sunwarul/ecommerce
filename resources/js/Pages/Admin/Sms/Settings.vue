<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, router } from "@inertiajs/vue3";
import { computed, reactive, ref, watch } from "vue";
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
import { useI18n } from "vue-i18n";
import { dateTime, firstError, money } from "@/Helpers/format";

useFlashToast();
const toast = useToast();
const { t, te } = useI18n();

const props = defineProps({
    settings: { type: Object, default: () => ({}) },
    events: { type: Array, default: () => [] },
    templates: { type: Array, default: () => [] },
    gateways: { type: Array, default: () => [] },
    drivers: { type: Array, default: () => [] },
    placeholders: { type: Array, default: () => [] },
});

const onError = (errors) => toast.add({ severity: "error", summary: t("shared.terms.error"), detail: firstError(errors), life: 4000 });
const eventLabel = (key, fallback) => (te(`sms.events.${key}`) ? t(`sms.events.${key}`) : fallback || key);

// ---------------- General ----------------
const general = reactive({ ...props.settings });
const eventToggles = reactive(Object.fromEntries(props.events.map((e) => [e.key, !!e.enabled])));
const savingGeneral = ref(false);

const deliveryModes = computed(() => ["after_response", "queue", "sync"].map((v) => ({ label: t(`sms.settings.delivery.${v}`), value: v })));
const roundingOptions = computed(() => ["integer", "none"].map((v) => ({ label: t(`sms.settings.rounding.${v}`), value: v })));

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
    if (!confirm(t("sms.settings.confirm_delete_gateway", { name: g.name }))) return;
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

const savingTpl = ref(null);
function saveTemplate(tpl) {
    savingTpl.value = tpl.id;
    router.put(route("sms.templates.update", tpl.id), { name: tpl.name, body: tpl.body, is_active: tpl.is_active }, {
        preserveScroll: true,
        onError,
        onFinish: () => (savingTpl.value = null),
    });
}
function deleteTemplate(tpl) {
    if (!confirm(t("sms.settings.confirm_delete_template", { name: tpl.name }))) return;
    router.delete(route("sms.templates.destroy", tpl.id), { preserveScroll: true, onError });
}

const showNewTpl = ref(false);
const newTpl = reactive({ name: "", body: "" });
const creatingTpl = ref(false);
function createTemplate() {
    creatingTpl.value = true;
    router.post(route("sms.templates.store"), { ...newTpl }, {
        preserveScroll: true,
        onFinish: () => (creatingTpl.value = false),
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
    <Head :title="t('sms.settings_title')" />
    <AuthenticatedLayout>
        <div class="p-4 md:p-6 space-y-4 max-w-5xl mx-auto">
            <div class="flex flex-wrap justify-between items-start gap-3">
                <div>
                    <h1 class="text-xl font-semibold text-slate-800 flex items-center gap-2"><i class="pi pi-cog text-primary" /> {{ t('sms.settings_title') }}</h1>
                    <p class="text-sm text-slate-500">{{ t('sms.settings.subtitle') }}</p>
                </div>
                <Link :href="route('sms.dashboard')"><Button :label="t('sms.dashboard.title')" icon="pi pi-chart-pie" size="small" severity="secondary" outlined /></Link>
            </div>

            <div class="card p-0 md:p-2">
                <Tabs value="general" scrollable>
                    <TabList>
                        <Tab value="general"><i class="pi pi-sliders-h me-2" />{{ t('sms.settings.tab_general') }}</Tab>
                        <Tab value="gateways"><i class="pi pi-server me-2" />{{ t('sms.settings.tab_gateways') }}</Tab>
                        <Tab value="templates"><i class="pi pi-file-edit me-2" />{{ t('sms.settings.tab_templates') }}</Tab>
                    </TabList>
                    <TabPanels>
                        <!-- GENERAL -->
                        <TabPanel value="general">
                            <div class="space-y-5">
                                <div class="flex items-center justify-between gap-4 p-3 rounded-xl border"
                                    :class="general.sms_enabled ? 'bg-emerald-50 border-emerald-200' : 'bg-slate-50 border-slate-200'">
                                    <div>
                                        <div class="font-semibold text-slate-800">{{ t('sms.settings.master') }}</div>
                                        <div class="text-xs text-slate-500">{{ t('sms.settings.master_help') }}</div>
                                    </div>
                                    <ToggleSwitch v-model="general.sms_enabled" :aria-label="t('sms.settings.master')" />
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="text-xs text-slate-500 block mb-1">{{ t('sms.settings.country_code') }}</label>
                                        <InputText v-model="general.sms_country_code" class="w-full ltr-nums" inputmode="numeric" placeholder="880" />
                                        <div class="text-[11px] text-slate-500 mt-1">{{ t('sms.settings.country_code_help') }}</div>
                                    </div>
                                    <div>
                                        <label class="text-xs text-slate-500 block mb-1">{{ t('sms.settings.delivery_mode') }}</label>
                                        <Select v-model="general.sms_delivery_mode" :options="deliveryModes" optionLabel="label" optionValue="value" class="w-full" />
                                    </div>
                                    <div>
                                        <label class="text-xs text-slate-500 block mb-1">{{ t('sms.settings.reminder_days') }}</label>
                                        <InputNumber v-model="general.sms_installment_reminder_days" :min="0" :max="30" class="w-full" inputClass="w-full" />
                                    </div>
                                    <div>
                                        <label class="text-xs text-slate-500 block mb-1">{{ t('sms.settings.installment_rounding') }}</label>
                                        <Select v-model="general.installment_rounding" :options="roundingOptions" optionLabel="label" optionValue="value" class="w-full" />
                                    </div>
                                </div>

                                <div>
                                    <div class="font-semibold text-slate-700 mb-2">{{ t('sms.settings.automatic') }}</div>
                                    <div class="divide-y divide-slate-100 border border-slate-200 rounded-xl">
                                        <div v-for="e in events" :key="e.key" class="flex items-center justify-between gap-3 px-3 py-2.5">
                                            <div>
                                                <div class="text-sm text-slate-800">{{ eventLabel(e.key, e.label) }}</div>
                                                <div class="text-[11px] text-slate-500">{{ e.key }}</div>
                                            </div>
                                            <ToggleSwitch v-model="eventToggles[e.key]" :disabled="!general.sms_enabled" :aria-label="eventLabel(e.key, e.label)" />
                                        </div>
                                    </div>
                                    <div class="text-[11px] text-slate-500 mt-1">{{ t('sms.settings.scheduler_note') }}</div>
                                </div>

                                <div class="flex justify-end">
                                    <Button :label="t('sms.settings.save_settings')" icon="pi pi-save" :loading="savingGeneral" @click="saveGeneral" />
                                </div>
                            </div>
                        </TabPanel>

                        <!-- GATEWAYS -->
                        <TabPanel value="gateways">
                            <div class="space-y-4">
                                <div class="flex flex-wrap items-center justify-between gap-2">
                                    <p class="text-xs text-slate-500 max-w-xl">
                                        {{ t('sms.settings.vendor_note') }}
                                    </p>
                                    <Button :label="t('sms.settings.add_gateway')" icon="pi pi-plus" size="small" @click="openGateway()" />
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                    <div v-for="g in gateways" :key="g.id" class="border border-slate-200 rounded-xl p-4 space-y-2">
                                        <div class="flex items-start justify-between gap-2">
                                            <div class="min-w-0">
                                                <div class="font-semibold text-slate-800 truncate">{{ g.name }}</div>
                                                <div class="text-xs text-slate-500">{{ driverLabel(g.driver) }}<span v-if="g.sender_id"> · {{ t('sms.settings.sender') }}: {{ g.sender_id }}</span></div>
                                            </div>
                                            <div class="flex gap-1 shrink-0">
                                                <Tag v-if="g.is_default" :value="t('sms.default')" severity="info" />
                                                <Tag :value="g.is_active ? t('shared.status.active') : t('shared.status.inactive')" :severity="g.is_active ? 'success' : 'secondary'" />
                                            </div>
                                        </div>
                                        <div class="text-sm text-slate-700">
                                            {{ t('shared.terms.balance') }}: <b class="ltr-nums">{{ g.balance !== null && g.balance !== undefined ? money(g.balance) : "—" }}</b>
                                            <span class="text-xs text-slate-500 ms-1">{{ g.balance_checked_at ? "(" + dateTime(g.balance_checked_at) + ")" : "" }}</span>
                                        </div>
                                        <div class="flex flex-wrap gap-1">
                                            <Button :label="t('common.edit')" icon="pi pi-pencil" size="small" outlined @click="openGateway(g)" />
                                            <Button :label="t('sms.settings.test')" icon="pi pi-send" size="small" outlined severity="secondary" @click="openTest(g)" />
                                            <Button v-if="drivers.find((d) => d.value === g.driver)?.supports_balance" :label="t('shared.terms.balance')" icon="pi pi-wallet" size="small" outlined
                                                severity="secondary" :loading="checking === g.id" @click="checkBalance(g)" />
                                            <Button icon="pi pi-trash" size="small" text severity="danger" @click="deleteGateway(g)" v-tooltip.top="t('common.delete')" :aria-label="t('common.delete')" />
                                        </div>
                                    </div>
                                    <div v-if="!gateways.length" class="text-sm text-slate-500 p-4 border border-dashed border-slate-300 rounded-xl">
                                        {{ t('sms.settings.no_gateway') }}
                                    </div>
                                </div>
                            </div>
                        </TabPanel>

                        <!-- TEMPLATES -->
                        <TabPanel value="templates">
                            <div class="space-y-4">
                                <div class="flex flex-wrap items-center justify-between gap-2">
                                    <div class="flex flex-wrap gap-1">
                                        <span class="text-xs text-slate-500 w-full">{{ t('sms.settings.placeholders_help') }}</span>
                                        <span v-for="ph in placeholders" :key="ph" dir="ltr" class="text-[11px] px-2 py-0.5 rounded-full bg-slate-100 border border-slate-200 text-slate-600">{{ ph }}</span>
                                    </div>
                                    <Button :label="t('sms.settings.new_template')" icon="pi pi-plus" size="small" @click="showNewTpl = true" />
                                </div>

                                <div v-for="tpl in tplForms" :key="tpl.id" class="border border-slate-200 rounded-xl p-4 space-y-2">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <InputText v-model="tpl.name" dir="auto" class="flex-1 min-w-0 sm:min-w-[180px]" size="small" :aria-label="t('common.name')" />
                                        <span class="text-[11px] text-slate-500">{{ tpl.key }}</span>
                                        <label class="flex items-center gap-2 text-xs text-slate-600 ms-auto">{{ t('shared.status.active') }} <ToggleSwitch v-model="tpl.is_active" /></label>
                                    </div>
                                    <Textarea :ref="(el) => (tplRefs[tpl.id] = el)" v-model="tpl.body" rows="3" dir="auto" class="w-full text-sm" />
                                    <div class="flex flex-wrap items-center gap-1">
                                        <Button v-for="ph in placeholders" :key="ph" :label="ph" size="small" text class="!text-[11px] !px-1.5 !py-0.5" @click="insertInto(tpl, ph)" />
                                    </div>
                                    <div class="flex flex-wrap items-center justify-between gap-2">
                                        <span class="text-xs text-slate-500">{{ t('sms.counter', { chars: parts(tpl.body).len, parts: parts(tpl.body).parts }) }}{{ parts(tpl.body).unicode ? " · Unicode" : "" }}</span>
                                        <div class="flex gap-1">
                                            <Button v-if="tpl.key.startsWith('custom_')" icon="pi pi-trash" size="small" text severity="danger" :aria-label="t('common.delete')" @click="deleteTemplate(tpl)" />
                                            <Button :label="t('shared.terms.save')" icon="pi pi-save" size="small" :loading="savingTpl === tpl.id" @click="saveTemplate(tpl)" />
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
        <Dialog v-model:visible="showGateway" modal :header="editingId ? t('sms.settings.edit_gateway') : t('sms.settings.add_gateway')" :style="{ width: '560px' }" :breakpoints="{ '640px': '95vw' }">
            <div class="space-y-3">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="text-xs text-slate-500 block mb-1">{{ t('common.name') }} *</label>
                        <InputText v-model="gForm.name" class="w-full" />
                    </div>
                    <div>
                        <label class="text-xs text-slate-500 block mb-1">{{ t('sms.settings.driver') }} *</label>
                        <Select v-model="gForm.driver" :options="drivers" optionLabel="label" optionValue="value" class="w-full" />
                    </div>
                    <div>
                        <label class="text-xs text-slate-500 block mb-1">{{ t('sms.settings.sender_id') }}</label>
                        <InputText v-model="gForm.sender_id" class="w-full" />
                    </div>
                    <div class="flex items-end gap-4 pb-1">
                        <label class="flex items-center gap-2 text-sm text-slate-700"><ToggleSwitch v-model="gForm.is_active" /> {{ t('shared.status.active') }}</label>
                        <label class="flex items-center gap-2 text-sm text-slate-700"><ToggleSwitch v-model="gForm.is_default" /> {{ t('sms.default') }}</label>
                    </div>
                </div>

                <div v-if="currentDriver?.fields?.length" class="space-y-3 pt-2 border-t border-slate-100">
                    <div v-for="field in currentDriver.fields" :key="field.key">
                        <label class="text-xs text-slate-500 block mb-1">{{ field.label }}</label>
                        <Select v-if="field.type === 'select'" v-model="gForm.credentials[field.key]" :options="field.options" class="w-full" />
                        <Textarea v-else-if="field.type === 'textarea'" v-model="gForm.credentials[field.key]" rows="3" dir="ltr" class="w-full text-sm font-mono" />
                        <InputText v-else v-model="gForm.credentials[field.key]" :type="field.type === 'password' ? 'password' : 'text'" dir="ltr" class="w-full" autocomplete="off" />
                        <div v-if="field.help" class="text-[11px] text-slate-500 mt-0.5">{{ field.help }}</div>
                    </div>
                </div>
                <div v-else class="text-xs text-slate-500">{{ t('sms.settings.no_credentials') }}</div>
            </div>
            <template #footer>
                <Button :label="t('shared.terms.cancel')" text @click="showGateway = false" />
                <Button :label="t('shared.terms.save')" icon="pi pi-save" :loading="gSaving" @click="saveGateway" />
            </template>
        </Dialog>

        <!-- Test dialog -->
        <Dialog v-model:visible="showTest" modal :header="t('sms.settings.test_title', { name: testTarget?.name || '' })" :style="{ width: '440px' }" :breakpoints="{ '640px': '95vw' }">
            <div class="space-y-3">
                <div>
                    <label class="text-xs text-slate-500 block mb-1">{{ t('shared.terms.phone') }} *</label>
                    <InputText v-model="testForm.phone" type="tel" class="w-full" placeholder="01712345678" />
                </div>
                <div>
                    <label class="text-xs text-slate-500 block mb-1">{{ t('sms.settings.message_optional') }}</label>
                    <Textarea v-model="testForm.message" rows="3" dir="auto" class="w-full text-sm" />
                </div>
            </div>
            <template #footer>
                <Button :label="t('shared.terms.cancel')" text @click="showTest = false" />
                <Button :label="t('sms.settings.send_test')" icon="pi pi-send" :loading="testing" :disabled="!testForm.phone" @click="sendTest" />
            </template>
        </Dialog>

        <!-- New template dialog -->
        <Dialog v-model:visible="showNewTpl" modal :header="t('sms.settings.new_template')" :style="{ width: '520px' }" :breakpoints="{ '640px': '95vw' }">
            <div class="space-y-3">
                <div>
                    <label class="text-xs text-slate-500 block mb-1">{{ t('common.name') }} *</label>
                    <InputText v-model="newTpl.name" dir="auto" class="w-full" :placeholder="t('sms.settings.template_name_placeholder')" />
                </div>
                <div>
                    <label class="text-xs text-slate-500 block mb-1">{{ t('sms.message') }} *</label>
                    <Textarea v-model="newTpl.body" rows="4" dir="auto" class="w-full text-sm" />
                    <div class="text-xs text-slate-500 mt-1">{{ t('sms.counter', { chars: parts(newTpl.body).len, parts: parts(newTpl.body).parts }) }}</div>
                </div>
            </div>
            <template #footer>
                <Button :label="t('shared.terms.cancel')" text @click="showNewTpl = false" />
                <Button :label="t('common.create')" icon="pi pi-plus" :loading="creatingTpl" :disabled="!newTpl.name || !newTpl.body" @click="createTemplate" />
            </template>
        </Dialog>
    </AuthenticatedLayout>
</template>
