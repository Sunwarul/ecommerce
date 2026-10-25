<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import ImageUpload from "@/Components/Website/ImageUpload.vue";
import { firstError } from "@/Helpers/format";
import { LOCALE_NAMES } from "@/Stores/localeStore";
import { Head, Link, useForm, usePage } from "@inertiajs/vue3";
import Button from "primevue/button";
import InputText from "primevue/inputtext";
import Message from "primevue/message";
import SelectButton from "primevue/selectbutton";
import Tab from "primevue/tab";
import TabList from "primevue/tablist";
import TabPanel from "primevue/tabpanel";
import TabPanels from "primevue/tabpanels";
import Tabs from "primevue/tabs";
import Textarea from "primevue/textarea";
import ToggleSwitch from "primevue/toggleswitch";
import { useToast } from "primevue/usetoast";
import { computed, ref } from "vue";
import { useI18n } from "vue-i18n";

const props = defineProps({
    website: { type: Object, required: true },
    branding: { type: Object, default: () => ({}) },
    map_preview: { type: String, default: null },
});

const { t } = useI18n();
const toast = useToast();
const page = usePage();

const TRANSLATABLE = ["hero_badge", "hero_title", "hero_subtitle", "hero_cta_label", "about_title", "about_body", "address", "opening_hours", "footer_about", "meta_description"];
const SOCIALS = ["facebook", "instagram", "youtube", "linkedin", "tiktok"];
const SECTIONS = ["show_features", "show_about", "show_blog", "show_faq", "show_map", "show_contact"];
const LOCALES = page.props.locale?.supported || ["en", "bn"];

const initial = {};
for (const f of TRANSLATABLE) {
    initial[f] = Object.fromEntries(LOCALES.map((l) => [l, props.website[f]?.[l] ?? ""]));
}
for (const f of ["hero_cta_url", "contact_email", "notify_email", "contact_phone", "whatsapp", "map_location"]) {
    initial[f] = props.website[f] ?? "";
}
for (const s of SOCIALS) initial[`social_${s}`] = props.website[`social_${s}`] ?? "";
for (const s of ["enabled", ...SECTIONS]) initial[s] = !!props.website[s];

const form = useForm({ ...initial, logo: null, favicon: null, hero_image: null, about_image: null, remove: [] });
const removed = ref({ logo: false, favicon: false, hero_image: false, about_image: false });

// Text for the language picked here; other languages keep their own text. Empty = built-in default.
const lang = ref(LOCALES.includes("bn") ? "bn" : LOCALES[0]);
const langOptions = computed(() => LOCALES.map((l) => ({ label: LOCALE_NAMES[l] || l, value: l })));
const filledIn = (l) => TRANSLATABLE.some((f) => (form[f][l] || "").trim() !== "");

function save() {
    form.remove = Object.keys(removed.value).filter((k) => removed.value[k]);
    form.post(route("website.settings.update"), {
        forceFormData: true,
        preserveScroll: true,
        onSuccess: () => {
            form.logo = form.favicon = form.hero_image = form.about_image = null;
            removed.value = { logo: false, favicon: false, hero_image: false, about_image: false };
            toast.add({ severity: "success", summary: t("shared.terms.success"), detail: t("website.admin.saved"), life: 3000 });
        },
        onError: (errors) => toast.add({ severity: "error", summary: t("shared.terms.error"), detail: firstError(errors), life: 5000 }),
    });
}

const err = (key) => form.errors[key] || Object.entries(form.errors).find(([k]) => k.startsWith(`${key}.`))?.[1];
const labelCls = "text-sm font-medium text-slate-700";
</script>

<template>
    <AuthenticatedLayout>
        <Head :title="t('menu.website_settings')" />

        <div class="max-w-5xl mx-auto space-y-4">
            <div class="flex flex-wrap items-start justify-between gap-3">
                <div class="flex items-start gap-3">
                    <span class="hidden sm:flex w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 items-center justify-center shrink-0">
                        <i class="pi pi-globe text-lg" />
                    </span>
                    <div>
                        <h1 class="text-xl font-semibold text-slate-800">{{ t('menu.website_settings') }}</h1>
                        <p class="text-sm text-slate-500">{{ t('website.admin.subtitle') }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <a :href="route('welcome')" target="_blank" rel="noopener">
                        <Button :label="t('website.admin.view_site')" icon="pi pi-external-link" severity="secondary" outlined />
                    </a>
                    <Button :loading="form.processing" :label="t('settings_page.save')" icon="pi pi-check" @click="save" />
                </div>
            </div>

            <Message v-if="!form.enabled" severity="warn" :closable="false" icon="pi pi-eye-slash">{{ t('website.admin.disabled_notice') }}</Message>

            <!-- Language of the texts being edited -->
            <div class="card !p-4 flex flex-wrap items-center gap-3">
                <span class="text-sm font-medium text-slate-700"><i class="pi pi-language me-1" />{{ t('website.admin.editing_language') }}</span>
                <SelectButton v-model="lang" :options="langOptions" optionLabel="label" optionValue="value" :allowEmpty="false">
                    <template #option="{ option }">
                        <span>{{ option.label }}</span>
                        <i v-if="filledIn(option.value)" class="pi pi-check-circle text-emerald-500 text-xs ms-1" />
                    </template>
                </SelectButton>
                <small class="text-slate-500 basis-full">{{ t('website.admin.editing_language_hint') }}</small>
            </div>

            <div class="card !p-0 overflow-hidden">
                <Tabs value="branding" scrollable>
                    <TabList>
                        <Tab value="branding"><i class="pi pi-image me-2" />{{ t('website.admin.tabs.branding') }}</Tab>
                        <Tab value="home"><i class="pi pi-home me-2" />{{ t('website.admin.tabs.home') }}</Tab>
                        <Tab value="contact"><i class="pi pi-map-marker me-2" />{{ t('website.admin.tabs.contact') }}</Tab>
                        <Tab value="social"><i class="pi pi-share-alt me-2" />{{ t('website.admin.tabs.social') }}</Tab>
                        <Tab value="sections"><i class="pi pi-th-large me-2" />{{ t('website.admin.tabs.sections') }}</Tab>
                    </TabList>
                    <TabPanels>
                        <!-- Branding -->
                        <TabPanel value="branding">
                            <div class="space-y-8">
                                <section class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                                    <div>
                                        <h2 class="font-semibold text-slate-800">{{ t('website.admin.logo') }}</h2>
                                        <p class="text-sm text-slate-500 mt-1">{{ t('website.admin.logo_hint') }}</p>
                                    </div>
                                    <div class="lg:col-span-2 space-y-6">
                                        <div class="flex flex-col gap-2">
                                            <span :class="labelCls">{{ t('website.admin.logo') }}</span>
                                            <ImageUpload v-model="form.logo" v-model:removed="removed.logo" :current="branding.logo"
                                                accept="image/png,image/jpeg,image/webp,image/svg+xml" :hint="t('website.admin.logo_file_hint')" />
                                            <small v-if="err('logo')" class="text-red-600">{{ err('logo') }}</small>
                                        </div>
                                        <div class="flex flex-col gap-2">
                                            <span :class="labelCls">{{ t('website.admin.favicon') }}</span>
                                            <ImageUpload v-model="form.favicon" v-model:removed="removed.favicon" :current="branding.favicon"
                                                accept="image/png,image/svg+xml,image/x-icon,.ico" previewClass="h-10 w-10" :hint="t('website.admin.favicon_hint')" />
                                            <small v-if="err('favicon')" class="text-red-600">{{ err('favicon') }}</small>
                                        </div>
                                    </div>
                                </section>
                                <section class="grid grid-cols-1 lg:grid-cols-3 gap-4 pt-6 border-t border-slate-100">
                                    <div>
                                        <h2 class="font-semibold text-slate-800">{{ t('website.admin.site_status') }}</h2>
                                        <p class="text-sm text-slate-500 mt-1">{{ t('website.admin.site_status_hint') }}</p>
                                    </div>
                                    <div class="lg:col-span-2 flex items-start gap-3">
                                        <ToggleSwitch inputId="enabled" v-model="form.enabled" />
                                        <label for="enabled" class="text-sm">
                                            <span class="font-medium text-slate-700">{{ t('website.admin.enabled') }}</span>
                                            <span class="block text-slate-500">{{ t('website.admin.enabled_hint') }}</span>
                                        </label>
                                    </div>
                                </section>
                            </div>
                        </TabPanel>

                        <!-- Home page -->
                        <TabPanel value="home">
                            <div class="space-y-8">
                                <section class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                                    <div>
                                        <h2 class="font-semibold text-slate-800">{{ t('website.admin.hero') }}</h2>
                                        <p class="text-sm text-slate-500 mt-1">{{ t('website.admin.hero_hint') }}</p>
                                    </div>
                                    <div class="lg:col-span-2 grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        <div class="flex flex-col gap-1.5">
                                            <label for="hero_badge" :class="labelCls">{{ t('website.admin.fields.hero_badge') }}</label>
                                            <InputText id="hero_badge" v-model="form.hero_badge[lang]" maxlength="60" :placeholder="t('website.admin.placeholder_default')" fluid />
                                        </div>
                                        <div class="flex flex-col gap-1.5">
                                            <label for="hero_title" :class="labelCls">{{ t('website.admin.fields.hero_title') }}</label>
                                            <InputText id="hero_title" v-model="form.hero_title[lang]" maxlength="160" :placeholder="t('website.home.headline')" fluid />
                                        </div>
                                        <div class="flex flex-col gap-1.5 sm:col-span-2">
                                            <label for="hero_subtitle" :class="labelCls">{{ t('website.admin.fields.hero_subtitle') }}</label>
                                            <Textarea id="hero_subtitle" v-model="form.hero_subtitle[lang]" rows="3" autoResize maxlength="500" :placeholder="t('website.home.lead')" fluid />
                                        </div>
                                        <div class="flex flex-col gap-1.5">
                                            <label for="cta_label" :class="labelCls">{{ t('website.admin.fields.hero_cta_label') }}</label>
                                            <InputText id="cta_label" v-model="form.hero_cta_label[lang]" maxlength="40" :placeholder="t('website.home.cta_contact')" fluid />
                                        </div>
                                        <div class="flex flex-col gap-1.5">
                                            <label for="cta_url" :class="labelCls">{{ t('website.admin.fields.hero_cta_url') }}</label>
                                            <InputText id="cta_url" v-model="form.hero_cta_url" placeholder="/contact" fluid :invalid="!!err('hero_cta_url')" />
                                            <small v-if="err('hero_cta_url')" class="text-red-600">{{ err('hero_cta_url') }}</small>
                                        </div>
                                        <div class="flex flex-col gap-2 sm:col-span-2">
                                            <span :class="labelCls">{{ t('website.admin.fields.hero_image') }}</span>
                                            <ImageUpload v-model="form.hero_image" v-model:removed="removed.hero_image" :current="website.hero_image"
                                                previewClass="h-24 w-40" :hint="t('website.admin.hero_image_hint')" />
                                            <small v-if="err('hero_image')" class="text-red-600">{{ err('hero_image') }}</small>
                                        </div>
                                    </div>
                                </section>

                                <section class="grid grid-cols-1 lg:grid-cols-3 gap-4 pt-6 border-t border-slate-100">
                                    <div>
                                        <h2 class="font-semibold text-slate-800">{{ t('website.admin.about') }}</h2>
                                        <p class="text-sm text-slate-500 mt-1">{{ t('website.admin.about_hint') }}</p>
                                    </div>
                                    <div class="lg:col-span-2 grid grid-cols-1 gap-4">
                                        <div class="flex flex-col gap-1.5">
                                            <label for="about_title" :class="labelCls">{{ t('website.admin.fields.about_title') }}</label>
                                            <InputText id="about_title" v-model="form.about_title[lang]" maxlength="160" :placeholder="t('website.home.about_title')" fluid />
                                        </div>
                                        <div class="flex flex-col gap-1.5">
                                            <label for="about_body" :class="labelCls">{{ t('website.admin.fields.about_body') }}</label>
                                            <Textarea id="about_body" v-model="form.about_body[lang]" rows="5" autoResize maxlength="3000" :placeholder="t('website.home.about_body')" fluid />
                                        </div>
                                        <div class="flex flex-col gap-2">
                                            <span :class="labelCls">{{ t('website.admin.fields.about_image') }}</span>
                                            <ImageUpload v-model="form.about_image" v-model:removed="removed.about_image" :current="website.about_image" previewClass="h-24 w-40" />
                                        </div>
                                        <div class="flex flex-col gap-1.5">
                                            <label for="footer_about" :class="labelCls">{{ t('website.admin.fields.footer_about') }}</label>
                                            <Textarea id="footer_about" v-model="form.footer_about[lang]" rows="2" autoResize maxlength="500" fluid />
                                        </div>
                                    </div>
                                </section>
                            </div>
                        </TabPanel>

                        <!-- Contact & map -->
                        <TabPanel value="contact">
                            <div class="space-y-8">
                                <section class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                                    <div>
                                        <h2 class="font-semibold text-slate-800">{{ t('website.admin.contact_details') }}</h2>
                                        <p class="text-sm text-slate-500 mt-1">{{ t('website.admin.contact_details_hint') }}</p>
                                    </div>
                                    <div class="lg:col-span-2 grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        <div class="flex flex-col gap-1.5">
                                            <label for="c_phone" :class="labelCls">{{ t('common.phone') }}</label>
                                            <InputText id="c_phone" type="tel" v-model="form.contact_phone" fluid />
                                        </div>
                                        <div class="flex flex-col gap-1.5">
                                            <label for="c_whatsapp" :class="labelCls">WhatsApp</label>
                                            <InputText id="c_whatsapp" type="tel" v-model="form.whatsapp" placeholder="+8801712345678" fluid :invalid="!!err('whatsapp')" />
                                            <small v-if="err('whatsapp')" class="text-red-600">{{ err('whatsapp') }}</small>
                                        </div>
                                        <div class="flex flex-col gap-1.5">
                                            <label for="c_email" :class="labelCls">{{ t('common.email') }}</label>
                                            <InputText id="c_email" type="email" v-model="form.contact_email" fluid :invalid="!!err('contact_email')" />
                                            <small v-if="err('contact_email')" class="text-red-600">{{ err('contact_email') }}</small>
                                        </div>
                                        <div class="flex flex-col gap-1.5">
                                            <label for="n_email" :class="labelCls">{{ t('website.admin.fields.notify_email') }}</label>
                                            <InputText id="n_email" type="email" v-model="form.notify_email" fluid :invalid="!!err('notify_email')" />
                                            <small class="text-slate-500">{{ t('website.admin.notify_email_hint') }}</small>
                                        </div>
                                        <div class="flex flex-col gap-1.5 sm:col-span-2">
                                            <label for="c_address" :class="labelCls">{{ t('common.address') }}</label>
                                            <InputText id="c_address" v-model="form.address[lang]" maxlength="255" fluid />
                                        </div>
                                        <div class="flex flex-col gap-1.5 sm:col-span-2">
                                            <label for="c_hours" :class="labelCls">{{ t('website.admin.fields.opening_hours') }}</label>
                                            <InputText id="c_hours" v-model="form.opening_hours[lang]" maxlength="255" :placeholder="t('website.admin.opening_hours_placeholder')" fluid />
                                        </div>
                                    </div>
                                </section>

                                <section class="grid grid-cols-1 lg:grid-cols-3 gap-4 pt-6 border-t border-slate-100">
                                    <div>
                                        <h2 class="font-semibold text-slate-800">{{ t('website.admin.map') }}</h2>
                                        <p class="text-sm text-slate-500 mt-1">{{ t('website.admin.map_hint') }}</p>
                                    </div>
                                    <div class="lg:col-span-2 flex flex-col gap-3">
                                        <Textarea v-model="form.map_location" rows="3" autoResize maxlength="3000" fluid
                                            :placeholder="t('website.admin.map_placeholder')" :invalid="!!err('map_location')" />
                                        <small v-if="err('map_location')" class="text-red-600">{{ err('map_location') }}</small>
                                        <ol class="text-xs text-slate-500 list-decimal ps-5 space-y-0.5">
                                            <li>{{ t('website.admin.map_step1') }}</li>
                                            <li>{{ t('website.admin.map_step2') }}</li>
                                            <li>{{ t('website.admin.map_step3') }}</li>
                                        </ol>
                                        <div v-if="map_preview" class="rounded-xl overflow-hidden border border-slate-200">
                                            <iframe :src="map_preview" class="w-full h-64 border-0" loading="lazy" referrerpolicy="no-referrer-when-downgrade" :title="t('website.admin.map')" />
                                        </div>
                                        <small v-if="map_preview" class="text-slate-500">{{ t('website.admin.map_preview_hint') }}</small>
                                    </div>
                                </section>
                            </div>
                        </TabPanel>

                        <!-- Social & SEO -->
                        <TabPanel value="social">
                            <div class="space-y-8">
                                <section class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                                    <div>
                                        <h2 class="font-semibold text-slate-800">{{ t('website.admin.social') }}</h2>
                                        <p class="text-sm text-slate-500 mt-1">{{ t('website.admin.social_hint') }}</p>
                                    </div>
                                    <div class="lg:col-span-2 grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        <div v-for="s in SOCIALS" :key="s" class="flex flex-col gap-1.5">
                                            <label :for="`social_${s}`" :class="labelCls" class="capitalize"><i :class="`pi pi-${s === 'tiktok' ? 'tiktok' : s}`" class="me-1" />{{ s }}</label>
                                            <InputText :id="`social_${s}`" v-model="form[`social_${s}`]" :placeholder="`https://${s}.com/...`" fluid :invalid="!!err(`social_${s}`)" />
                                            <small v-if="err(`social_${s}`)" class="text-red-600">{{ err(`social_${s}`) }}</small>
                                        </div>
                                    </div>
                                </section>
                                <section class="grid grid-cols-1 lg:grid-cols-3 gap-4 pt-6 border-t border-slate-100">
                                    <div>
                                        <h2 class="font-semibold text-slate-800">{{ t('website.admin.seo') }}</h2>
                                        <p class="text-sm text-slate-500 mt-1">{{ t('website.admin.seo_hint') }}</p>
                                    </div>
                                    <div class="lg:col-span-2 flex flex-col gap-1.5">
                                        <label for="meta_description" :class="labelCls">{{ t('website.admin.fields.meta_description') }}</label>
                                        <Textarea id="meta_description" v-model="form.meta_description[lang]" rows="3" autoResize maxlength="300" fluid />
                                        <small class="text-slate-500">{{ (form.meta_description[lang] || '').length }}/300</small>
                                    </div>
                                </section>
                            </div>
                        </TabPanel>

                        <!-- Sections -->
                        <TabPanel value="sections">
                            <p class="text-sm text-slate-500 mb-4">{{ t('website.admin.sections_hint') }}</p>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div v-for="s in SECTIONS" :key="s" class="flex items-center gap-3 rounded-lg border border-slate-200 p-3">
                                    <ToggleSwitch :inputId="s" v-model="form[s]" />
                                    <label :for="s" class="text-sm font-medium text-slate-700">{{ t(`website.admin.sections.${s}`) }}</label>
                                </div>
                            </div>
                            <div class="mt-6 flex flex-wrap gap-2">
                                <Link :href="route('website.posts.index')"><Button :label="t('menu.blog_posts')" icon="pi pi-book" severity="secondary" outlined size="small" /></Link>
                                <Link :href="route('website.faqs.index')"><Button :label="t('menu.faqs')" icon="pi pi-question-circle" severity="secondary" outlined size="small" /></Link>
                            </div>
                        </TabPanel>
                    </TabPanels>
                </Tabs>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
