<script setup>
import WebsiteLayout from "@/Layouts/WebsiteLayout.vue";
import { Link } from "@inertiajs/vue3";
import Accordion from "primevue/accordion";
import AccordionContent from "primevue/accordioncontent";
import AccordionHeader from "primevue/accordionheader";
import AccordionPanel from "primevue/accordionpanel";
import Button from "primevue/button";
import IconField from "primevue/iconfield";
import InputIcon from "primevue/inputicon";
import InputText from "primevue/inputtext";
import { computed, ref } from "vue";
import { useI18n } from "vue-i18n";

const props = defineProps({ site: Object, faqs: { type: Array, default: () => [] } });
const { t } = useI18n();
const q = ref("");
const shown = computed(() => {
    const term = q.value.trim().toLowerCase();
    return term ? props.faqs.filter((f) => `${f.question} ${f.answer}`.toLowerCase().includes(term)) : props.faqs;
});
</script>

<template>
    <WebsiteLayout :site="site" :title="t('website.faq.title')">
        <section class="bg-gradient-to-br from-emerald-50 to-white dark:from-slate-900 dark:to-slate-950 border-b border-slate-100">
            <div class="max-w-3xl mx-auto px-4 sm:px-6 py-12 sm:py-16 text-center">
                <h1 class="text-3xl sm:text-4xl font-bold text-slate-900">{{ t('website.faq.title') }}</h1>
                <p class="mt-3 text-slate-600">{{ t('website.faq.subtitle') }}</p>
                <IconField class="mt-6 max-w-md mx-auto">
                    <InputIcon class="pi pi-search" />
                    <InputText v-model="q" :placeholder="t('website.faq.search')" fluid />
                </IconField>
            </div>
        </section>
        <section class="max-w-3xl mx-auto px-4 sm:px-6 py-12">
            <div v-if="!shown.length" class="text-center text-slate-500 py-10">{{ t('website.faq.empty') }}</div>
            <Accordion v-else multiple>
                <AccordionPanel v-for="f in shown" :key="f.id" :value="f.id">
                    <AccordionHeader>{{ f.question }}</AccordionHeader>
                    <AccordionContent><p class="whitespace-pre-line text-slate-600 m-0">{{ f.answer }}</p></AccordionContent>
                </AccordionPanel>
            </Accordion>
            <div v-if="site.show_contact" class="mt-12 rounded-2xl bg-emerald-600 text-white p-6 sm:p-8 flex flex-wrap items-center justify-between gap-4">
                <div>
                    <h2 class="text-xl font-semibold">{{ t('website.faq.still_questions') }}</h2>
                    <p class="text-emerald-50 mt-1">{{ t('website.faq.still_questions_text') }}</p>
                </div>
                <Link :href="route('website.contact')"><Button :label="t('website.nav.contact')" icon="pi pi-envelope" severity="contrast" /></Link>
            </div>
        </section>
    </WebsiteLayout>
</template>
