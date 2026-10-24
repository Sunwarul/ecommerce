<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import { computed } from "vue";
import { useI18n } from "vue-i18n";
import { useToast } from "primevue/usetoast";
import Button from "primevue/button";
import InputNumber from "primevue/inputnumber";
import InputText from "primevue/inputtext";
import Message from "primevue/message";
import Select from "primevue/select";
import Tab from "primevue/tab";
import TabList from "primevue/tablist";
import TabPanel from "primevue/tabpanel";
import TabPanels from "primevue/tabpanels";
import Tabs from "primevue/tabs";
import Textarea from "primevue/textarea";
import { firstError } from "@/Helpers/format";

const props = defineProps({ settings: Object });
const toast = useToast();
const { t } = useI18n();

// Older installs stored the language as a display name ("English"); normalise to a locale code
const LEGACY_LANG = { English: "en", Bangla: "bn", Bengali: "bn", Arabic: "ar", Spanish: "es", Hindi: "hi" };
const initial = { ...props.settings };
initial.general_language = LEGACY_LANG[initial.general_language] ?? initial.general_language ?? "en";
if (initial.mail_port !== null && initial.mail_port !== undefined && initial.mail_port !== "") {
    initial.mail_port = Number(initial.mail_port) || null;
}

const form = useForm({ settings: initial });

const languages = [
    { label: "English", value: "en" },
    { label: "বাংলা (Bangla)", value: "bn" },
    { label: "العربية (Arabic)", value: "ar" },
    { label: "Español (Spanish)", value: "es" },
    { label: "हिन्दी (Hindi)", value: "hi" },
];

const timezoneValues = [
    "Asia/Dhaka", "Asia/Kolkata", "Asia/Karachi", "Asia/Kathmandu", "Asia/Colombo", "Asia/Dubai", "Asia/Riyadh",
    "Asia/Qatar", "Asia/Kuwait", "Asia/Singapore", "Asia/Kuala_Lumpur", "Asia/Jakarta", "Asia/Tokyo",
    "Europe/London", "Europe/Berlin", "Europe/Madrid", "Africa/Cairo", "America/New_York", "America/Chicago",
    "America/Los_Angeles", "America/Mexico_City", "Australia/Sydney", "UTC",
];
function offsetLabel(tz) {
    try {
        const part = new Intl.DateTimeFormat("en-US", { timeZone: tz, timeZoneName: "shortOffset" })
            .formatToParts(new Date()).find((p) => p.type === "timeZoneName");
        return `(${part?.value ?? "GMT"}) ${tz.replace(/_/g, " ")}`;
    } catch {
        return tz;
    }
}
const timezones = computed(() => {
    const values = [...timezoneValues];
    const current = form.settings.general_timezone;
    if (current && !values.includes(current)) values.unshift(current); // keep a legacy/custom value selectable
    return values.map((v) => ({ label: offsetLabel(v), value: v }));
});

const dateFormats = [
    { label: "24-10-2026 (d-m-Y)", value: "d-m-Y" },
    { label: "24/10/2026 (d/m/Y)", value: "d/m/Y" },
    { label: "10/24/2026 (m/d/Y)", value: "m/d/Y" },
    { label: "2026-10-24 (Y-m-d)", value: "Y-m-d" },
    { label: "24 Oct 2026 (d M Y)", value: "d M Y" },
];
const timeFormats = computed(() => [
    { label: t("settings_page.fields.h24"), value: "24 Hours" },
    { label: t("settings_page.fields.h12"), value: "12 Hours" },
]);
const encryptions = computed(() => [
    { label: "TLS", value: "tls" },
    { label: "SSL", value: "ssl" },
    { label: t("settings_page.fields.none"), value: "null" },
]);

const err = (key) => form.errors[`settings.${key}`];

function save() {
    form.put(route("settings.update"), {
        preserveScroll: true,
        onSuccess: () => {
            form.defaults();
            toast.add({ severity: "success", summary: t("shared.terms.success"), detail: t("settings_page.saved"), life: 3000 });
        },
        onError: (errors) => {
            toast.add({ severity: "error", summary: t("shared.terms.error"), detail: firstError(errors), life: 4000 });
        },
    });
}

const labelCls = "text-sm font-medium text-slate-700";
</script>

<template>
    <AuthenticatedLayout>
        <Head :title="t('settings_page.title')" />

        <div class="max-w-5xl mx-auto space-y-4">
            <div class="flex flex-wrap items-start justify-between gap-3">
                <div class="flex items-start gap-3">
                    <span class="hidden sm:flex w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 items-center justify-center shrink-0">
                        <i class="pi pi-cog text-lg" />
                    </span>
                    <div>
                        <h1 class="text-xl font-semibold text-slate-800">{{ t('settings_page.title') }}</h1>
                        <p class="text-sm text-slate-500">{{ t('settings_page.subtitle') }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <span v-if="form.isDirty" class="text-xs text-amber-700 flex items-center gap-1">
                        <i class="pi pi-circle-fill !text-[8px]" />{{ t('settings_page.unsaved') }}
                    </span>
                    <Button :loading="form.processing" :label="t('settings_page.save')" icon="pi pi-check" @click="save" />
                </div>
            </div>

            <div class="card !p-0 overflow-hidden">
                <Tabs value="general" scrollable>
                    <TabList>
                        <Tab value="general"><i class="pi pi-building me-2" />{{ t('settings_page.tabs.general') }}</Tab>
                        <Tab value="email"><i class="pi pi-envelope me-2" />{{ t('settings_page.tabs.email') }}</Tab>
                        <Tab value="sms"><i class="pi pi-comment me-2" />{{ t('settings_page.tabs.sms') }}</Tab>
                    </TabList>
                    <TabPanels>
                        <!-- General -->
                        <TabPanel value="general">
                            <div class="space-y-8">
                                <section class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                                    <div>
                                        <h2 class="font-semibold text-slate-800">{{ t('settings_page.sections.business') }}</h2>
                                        <p class="text-sm text-slate-500 mt-1">{{ t('settings_page.sections.business_hint') }}</p>
                                    </div>
                                    <div class="lg:col-span-2 grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        <div class="flex flex-col gap-1.5 sm:col-span-2">
                                            <label for="app_name" :class="labelCls">{{ t('settings_page.fields.app_name') }} <span class="text-red-500">*</span></label>
                                            <InputText id="app_name" v-model="form.settings.general_application_name" fluid :invalid="!!err('general_application_name')" />
                                            <small v-if="err('general_application_name')" class="text-red-600">{{ err('general_application_name') }}</small>
                                        </div>
                                        <div class="flex flex-col gap-1.5">
                                            <label for="phone" :class="labelCls">{{ t('settings_page.fields.phone') }} <span class="text-red-500">*</span></label>
                                            <InputText id="phone" type="tel" v-model="form.settings.general_phone" fluid :invalid="!!err('general_phone')" />
                                            <small v-if="err('general_phone')" class="text-red-600">{{ err('general_phone') }}</small>
                                        </div>
                                        <div class="flex flex-col gap-1.5">
                                            <label for="address" :class="labelCls">{{ t('settings_page.fields.address') }} <span class="text-red-500">*</span></label>
                                            <InputText id="address" v-model="form.settings.general_address" maxlength="100" fluid :invalid="!!err('general_address')" />
                                            <small v-if="err('general_address')" class="text-red-600">{{ err('general_address') }}</small>
                                        </div>
                                        <div class="flex flex-col gap-1.5 sm:col-span-2">
                                            <label for="footer" :class="labelCls">{{ t('settings_page.fields.footer_text') }}</label>
                                            <Textarea id="footer" rows="2" autoResize v-model="form.settings.general_footer_text" maxlength="255" fluid
                                                :invalid="!!err('general_footer_text')" />
                                            <small v-if="err('general_footer_text')" class="text-red-600">{{ err('general_footer_text') }}</small>
                                        </div>
                                    </div>
                                </section>

                                <section class="grid grid-cols-1 lg:grid-cols-3 gap-4 pt-6 border-t border-slate-100">
                                    <div>
                                        <h2 class="font-semibold text-slate-800">{{ t('settings_page.sections.localization') }}</h2>
                                        <p class="text-sm text-slate-500 mt-1">{{ t('settings_page.sections.localization_hint') }}</p>
                                    </div>
                                    <div class="lg:col-span-2 grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        <div class="flex flex-col gap-1.5">
                                            <label for="language" :class="labelCls">{{ t('settings_page.fields.language') }}</label>
                                            <Select inputId="language" :options="languages" optionLabel="label" optionValue="value"
                                                v-model="form.settings.general_language" fluid />
                                        </div>
                                        <div class="flex flex-col gap-1.5">
                                            <label for="timezone" :class="labelCls">{{ t('settings_page.fields.timezone') }}</label>
                                            <Select inputId="timezone" filter :options="timezones" optionLabel="label" optionValue="value"
                                                v-model="form.settings.general_timezone" fluid :invalid="!!err('general_timezone')" />
                                        </div>
                                        <div class="flex flex-col gap-1.5">
                                            <label for="date_format" :class="labelCls">{{ t('settings_page.fields.date_format') }}</label>
                                            <Select inputId="date_format" :options="dateFormats" optionLabel="label" optionValue="value"
                                                v-model="form.settings.general_date_format" fluid />
                                        </div>
                                        <div class="flex flex-col gap-1.5">
                                            <label for="time_format" :class="labelCls">{{ t('settings_page.fields.time_format') }}</label>
                                            <Select inputId="time_format" :options="timeFormats" optionLabel="label" optionValue="value"
                                                v-model="form.settings.general_time_format" fluid />
                                        </div>
                                    </div>
                                </section>
                            </div>
                        </TabPanel>

                        <!-- Email -->
                        <TabPanel value="email">
                            <div class="space-y-8">
                                <section class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                                    <div>
                                        <h2 class="font-semibold text-slate-800">{{ t('settings_page.sections.smtp') }}</h2>
                                        <p class="text-sm text-slate-500 mt-1">{{ t('settings_page.sections.smtp_hint') }}</p>
                                    </div>
                                    <div class="lg:col-span-2 grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        <div class="flex flex-col gap-1.5">
                                            <label for="mail_host" :class="labelCls">{{ t('settings_page.fields.host') }}</label>
                                            <InputText id="mail_host" v-model="form.settings.mail_host" placeholder="smtp.example.com" fluid :invalid="!!err('mail_host')" />
                                            <small v-if="err('mail_host')" class="text-red-600">{{ err('mail_host') }}</small>
                                        </div>
                                        <div class="flex flex-col gap-1.5">
                                            <label for="mail_port" :class="labelCls">{{ t('settings_page.fields.port') }}</label>
                                            <InputNumber inputId="mail_port" v-model="form.settings.mail_port" :useGrouping="false" :min="1" :max="65535"
                                                placeholder="587" fluid :invalid="!!err('mail_port')" />
                                            <small v-if="err('mail_port')" class="text-red-600">{{ err('mail_port') }}</small>
                                        </div>
                                        <div class="flex flex-col gap-1.5">
                                            <label for="mail_username" :class="labelCls">{{ t('settings_page.fields.username') }}</label>
                                            <InputText id="mail_username" v-model="form.settings.mail_username" autocomplete="off" fluid />
                                        </div>
                                        <div class="flex flex-col gap-1.5">
                                            <label for="mail_password" :class="labelCls">{{ t('settings_page.fields.password') }}</label>
                                            <InputText id="mail_password" type="password" v-model="form.settings.mail_password" placeholder="******"
                                                autocomplete="new-password" fluid />
                                            <small class="text-slate-500">{{ t('settings_page.fields.password_hint') }}</small>
                                        </div>
                                        <div class="flex flex-col gap-1.5">
                                            <label for="mail_encryption" :class="labelCls">{{ t('settings_page.fields.encryption') }}</label>
                                            <Select inputId="mail_encryption" :options="encryptions" optionLabel="label" optionValue="value"
                                                v-model="form.settings.mail_encryption" fluid />
                                        </div>
                                    </div>
                                </section>

                                <section class="grid grid-cols-1 lg:grid-cols-3 gap-4 pt-6 border-t border-slate-100">
                                    <div>
                                        <h2 class="font-semibold text-slate-800">{{ t('settings_page.sections.sender') }}</h2>
                                    </div>
                                    <div class="lg:col-span-2 grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        <div class="flex flex-col gap-1.5">
                                            <label for="mail_from_address" :class="labelCls">{{ t('settings_page.fields.from_address') }}</label>
                                            <InputText id="mail_from_address" type="email" v-model="form.settings.mail_from_address"
                                                placeholder="no-reply@example.com" fluid :invalid="!!err('mail_from_address')" />
                                            <small v-if="err('mail_from_address')" class="text-red-600">{{ err('mail_from_address') }}</small>
                                        </div>
                                        <div class="flex flex-col gap-1.5">
                                            <label for="mail_from_name" :class="labelCls">{{ t('settings_page.fields.from_name') }}</label>
                                            <InputText id="mail_from_name" v-model="form.settings.mail_from_name" fluid />
                                        </div>
                                    </div>
                                </section>
                            </div>
                        </TabPanel>

                        <!-- SMS (moved) -->
                        <TabPanel value="sms">
                            <div class="flex flex-col items-center text-center py-10 px-4 gap-3">
                                <span class="w-14 h-14 rounded-2xl bg-sky-50 text-sky-600 flex items-center justify-center">
                                    <i class="pi pi-comments text-2xl" />
                                </span>
                                <h2 class="text-lg font-semibold text-slate-800">{{ t('settings_page.sms_moved_title') }}</h2>
                                <p class="text-sm text-slate-500 max-w-md">{{ t('settings_page.sms_moved_body') }}</p>
                                <Link :href="route('sms.settings')">
                                    <Button :label="t('settings_page.open_sms_settings')" icon="pi pi-arrow-right" iconPos="right" />
                                </Link>
                            </div>
                        </TabPanel>
                    </TabPanels>
                </Tabs>
            </div>

            <Message v-if="form.hasErrors" severity="error" :closable="false" size="small" icon="pi pi-exclamation-circle">
                {{ firstError(form.errors) }}
            </Message>
        </div>
    </AuthenticatedLayout>
</template>
