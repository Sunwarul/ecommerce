<script setup>
import ContactForm from "@/Components/Website/ContactForm.vue";
import WebsiteMap from "@/Components/Website/WebsiteMap.vue";
import WebsiteLayout from "@/Layouts/WebsiteLayout.vue";
import { useI18n } from "vue-i18n";

defineProps({ site: Object, form_token: { type: String, required: true } });
const { t } = useI18n();
</script>

<template>
    <WebsiteLayout :site="site" :title="t('website.contact.title')" :description="t('website.contact.subtitle')">
        <section class="bg-gradient-to-br from-emerald-50 to-white dark:from-slate-900 dark:to-slate-950 border-b border-slate-100">
            <div class="max-w-3xl mx-auto px-4 sm:px-6 py-12 sm:py-16 text-center">
                <h1 class="text-3xl sm:text-4xl font-bold text-slate-900">{{ t('website.contact.title') }}</h1>
                <p class="mt-3 text-slate-600">{{ t('website.contact.subtitle') }}</p>
            </div>
        </section>
        <section class="max-w-7xl mx-auto px-4 sm:px-6 py-12 grid grid-cols-1 lg:grid-cols-5 gap-8 items-start">
            <div class="lg:col-span-3 rounded-2xl border border-slate-200 p-6 sm:p-8 bg-white shadow-sm">
                <h2 class="text-lg font-semibold text-slate-900 mb-5">{{ t('website.contact.form_title') }}</h2>
                <ContactForm :token="form_token" />
            </div>
            <div class="lg:col-span-2 space-y-4">
                <div class="rounded-2xl border border-slate-200 p-6 bg-white space-y-4">
                    <div v-if="site.address" class="flex gap-3">
                        <span class="w-10 h-10 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0"><i class="pi pi-map-marker" /></span>
                        <div><div class="text-xs text-slate-500">{{ t('website.contact.visit') }}</div><div class="font-medium text-slate-900">{{ site.address }}</div></div>
                    </div>
                    <div v-if="site.contact_phone" class="flex gap-3">
                        <span class="w-10 h-10 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0"><i class="pi pi-phone" /></span>
                        <div><div class="text-xs text-slate-500">{{ t('website.contact.call') }}</div><a :href="`tel:${site.contact_phone}`" class="font-medium text-slate-900 hover:text-emerald-700" dir="ltr">{{ site.contact_phone }}</a></div>
                    </div>
                    <div v-if="site.contact_email" class="flex gap-3">
                        <span class="w-10 h-10 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0"><i class="pi pi-envelope" /></span>
                        <div class="min-w-0"><div class="text-xs text-slate-500">{{ t('website.contact.email') }}</div><a :href="`mailto:${site.contact_email}`" class="font-medium text-slate-900 hover:text-emerald-700 break-all">{{ site.contact_email }}</a></div>
                    </div>
                    <div v-if="site.opening_hours" class="flex gap-3">
                        <span class="w-10 h-10 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0"><i class="pi pi-clock" /></span>
                        <div><div class="text-xs text-slate-500">{{ t('website.contact.hours') }}</div><div class="font-medium text-slate-900">{{ site.opening_hours }}</div></div>
                    </div>
                </div>
                <WebsiteMap v-if="site.show_map && site.map_embed_url" :src="site.map_embed_url" :address="site.address" height="h-72" />
            </div>
        </section>
    </WebsiteLayout>
</template>
