<script setup>
import ContactForm from "@/Components/Website/ContactForm.vue";
import WebsiteMap from "@/Components/Website/WebsiteMap.vue";
import { date } from "@/Helpers/format";
import WebsiteLayout from "@/Layouts/WebsiteLayout.vue";
import { Link, usePage } from "@inertiajs/vue3";
import Accordion from "primevue/accordion";
import AccordionContent from "primevue/accordioncontent";
import AccordionHeader from "primevue/accordionheader";
import AccordionPanel from "primevue/accordionpanel";
import Button from "primevue/button";
import { computed } from "vue";
import { useI18n } from "vue-i18n";
import defaultHero from "../../../assets/images/pos-banner.jpg";

const props = defineProps({
    site: { type: Object, required: true },
    posts: { type: Array, default: () => [] },
    faqs: { type: Array, default: () => [] },
    form_token: { type: String, required: true },
});

const { t } = useI18n();
const page = usePage();
const shopName = computed(() => page.props.configs?.shop_name || "");

// Admin text wins; otherwise the translated default for the visitor's language
const s = (key, fallback) => props.site[key] || t(fallback);
const ctaHref = computed(() => props.site.hero_cta_url || (props.site.show_contact ? "#contact" : route("login")));

const features = computed(() => [
    { icon: "pi pi-verified", title: t("website.features.genuine"), text: t("website.features.genuine_text") },
    { icon: "pi pi-calendar-clock", title: t("website.features.installments"), text: t("website.features.installments_text") },
    { icon: "pi pi-credit-card", title: t("website.features.emi"), text: t("website.features.emi_text") },
    { icon: "pi pi-shield", title: t("website.features.warranty"), text: t("website.features.warranty_text") },
    { icon: "pi pi-wrench", title: t("website.features.service"), text: t("website.features.service_text") },
    { icon: "pi pi-truck", title: t("website.features.delivery"), text: t("website.features.delivery_text") },
]);
</script>

<template>
    <WebsiteLayout :site="site" :title="shopName">
        <!-- Hero -->
        <section class="relative overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-emerald-50 via-white to-sky-50 dark:from-slate-900 dark:via-slate-950 dark:to-slate-900" aria-hidden="true" />
            <div class="relative max-w-7xl mx-auto px-4 sm:px-6 py-14 sm:py-24 grid grid-cols-1 md:grid-cols-2 gap-10 md:gap-16 items-center">
                <div>
                    <span class="inline-flex items-center gap-2 rounded-full bg-emerald-100 text-emerald-800 text-xs font-semibold px-3 py-1">
                        <i class="pi pi-star-fill text-[10px]" /> {{ site.hero_badge || shopName }}
                    </span>
                    <h1 class="mt-4 text-3xl sm:text-5xl font-bold text-slate-900 leading-tight">{{ s('hero_title', 'website.home.headline') }}</h1>
                    <p class="mt-5 text-base sm:text-lg text-slate-600 max-w-xl">{{ s('hero_subtitle', 'website.home.lead') }}</p>
                    <div class="mt-8 flex flex-wrap gap-3">
                        <a :href="ctaHref">
                            <Button :label="s('hero_cta_label', 'website.home.cta_contact')" icon="pi pi-arrow-right" iconPos="right" size="large" />
                        </a>
                        <a v-if="site.contact_phone" :href="`tel:${site.contact_phone}`">
                            <Button :label="t('website.home.call_us')" icon="pi pi-phone" severity="secondary" outlined size="large" />
                        </a>
                    </div>
                    <div class="mt-8 flex flex-wrap gap-x-6 gap-y-2 text-sm text-slate-500">
                        <span v-if="site.address"><i class="pi pi-map-marker me-1 text-emerald-600" />{{ site.address }}</span>
                        <span v-if="site.opening_hours"><i class="pi pi-clock me-1 text-emerald-600" />{{ site.opening_hours }}</span>
                    </div>
                </div>
                <div class="relative">
                    <div class="absolute -inset-4 bg-emerald-200/50 rounded-3xl blur-2xl" aria-hidden="true" />
                    <img :src="site.hero_image || defaultHero" :alt="shopName" class="relative rounded-2xl shadow-xl w-full object-cover aspect-[4/3]" fetchpriority="high" />
                </div>
            </div>
        </section>

        <!-- Why us -->
        <section v-if="site.show_features" id="features" class="border-t border-slate-100">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 py-14 sm:py-20">
                <div class="text-center max-w-2xl mx-auto">
                    <h2 class="text-2xl sm:text-3xl font-bold text-slate-900">{{ t('website.features.title') }}</h2>
                    <p class="mt-3 text-slate-600">{{ t('website.features.subtitle') }}</p>
                </div>
                <div class="mt-10 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div v-for="f in features" :key="f.icon" class="rounded-2xl border border-slate-200 p-6 hover:shadow-md hover:-translate-y-0.5 transition bg-white">
                        <span class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center"><i :class="f.icon" class="text-lg" /></span>
                        <h3 class="mt-4 font-semibold text-slate-900">{{ f.title }}</h3>
                        <p class="mt-1 text-sm text-slate-600">{{ f.text }}</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- About -->
        <section v-if="site.show_about" id="about" class="bg-slate-50 border-y border-slate-100">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 py-14 sm:py-20 grid grid-cols-1 md:grid-cols-2 gap-10 items-center">
                <img v-if="site.about_image" :src="site.about_image" alt="" class="rounded-2xl shadow-lg w-full object-cover aspect-[4/3]" loading="lazy" />
                <div :class="site.about_image ? '' : 'md:col-span-2 max-w-3xl mx-auto text-center'">
                    <h2 class="text-2xl sm:text-3xl font-bold text-slate-900">{{ s('about_title', 'website.home.about_title') }}</h2>
                    <p class="mt-4 text-slate-600 whitespace-pre-line leading-relaxed">{{ s('about_body', 'website.home.about_body') }}</p>
                </div>
            </div>
        </section>

        <!-- Latest posts -->
        <section v-if="site.show_blog && posts.length">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 py-14 sm:py-20">
                <div class="flex flex-wrap items-end justify-between gap-3">
                    <div>
                        <h2 class="text-2xl sm:text-3xl font-bold text-slate-900">{{ t('website.blog.latest') }}</h2>
                        <p class="mt-2 text-slate-600">{{ t('website.blog.latest_subtitle') }}</p>
                    </div>
                    <Link :href="route('website.blog')" class="text-emerald-700 font-medium hover:underline">{{ t('website.blog.view_all') }} <i class="pi pi-arrow-right text-xs" /></Link>
                </div>
                <div class="mt-8 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    <Link v-for="post in posts" :key="post.id" :href="route('website.post', post.slug)" class="group rounded-2xl border border-slate-200 overflow-hidden bg-white hover:shadow-md transition">
                        <div class="aspect-[16/9] bg-slate-100 overflow-hidden">
                            <img v-if="post.cover_url" :src="post.cover_url" :alt="post.title" class="w-full h-full object-cover group-hover:scale-105 transition-transform" loading="lazy" />
                            <div v-else class="w-full h-full flex items-center justify-center"><i class="pi pi-book text-4xl text-slate-300" /></div>
                        </div>
                        <div class="p-5">
                            <div class="text-xs text-slate-500">{{ date(post.published_at) }}</div>
                            <h3 class="mt-1 font-semibold text-slate-900 group-hover:text-emerald-700 line-clamp-2">{{ post.title }}</h3>
                            <p v-if="post.excerpt" class="mt-2 text-sm text-slate-600 line-clamp-3">{{ post.excerpt }}</p>
                        </div>
                    </Link>
                </div>
            </div>
        </section>

        <!-- FAQ -->
        <section v-if="site.show_faq && faqs.length" class="bg-slate-50 border-y border-slate-100">
            <div class="max-w-3xl mx-auto px-4 sm:px-6 py-14 sm:py-20">
                <h2 class="text-2xl sm:text-3xl font-bold text-slate-900 text-center">{{ t('website.faq.title') }}</h2>
                <Accordion class="mt-8" :value="faqs[0]?.id">
                    <AccordionPanel v-for="f in faqs" :key="f.id" :value="f.id">
                        <AccordionHeader>{{ f.question }}</AccordionHeader>
                        <AccordionContent><p class="whitespace-pre-line text-slate-600 m-0">{{ f.answer }}</p></AccordionContent>
                    </AccordionPanel>
                </Accordion>
                <div class="mt-6 text-center">
                    <Link :href="route('website.faq')" class="text-emerald-700 font-medium hover:underline">{{ t('website.faq.view_all') }} <i class="pi pi-arrow-right text-xs" /></Link>
                </div>
            </div>
        </section>

        <!-- Contact + map -->
        <section v-if="site.show_contact || (site.show_map && site.map_embed_url)" id="contact">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 py-14 sm:py-20">
                <div class="text-center max-w-2xl mx-auto">
                    <h2 class="text-2xl sm:text-3xl font-bold text-slate-900">{{ t('website.contact.title') }}</h2>
                    <p class="mt-3 text-slate-600">{{ t('website.contact.subtitle') }}</p>
                </div>
                <div class="mt-10 grid grid-cols-1 lg:grid-cols-2 gap-8 items-start">
                    <div v-if="site.show_contact" class="rounded-2xl border border-slate-200 p-6 sm:p-8 bg-white shadow-sm">
                        <ContactForm :token="form_token" />
                    </div>
                    <div class="space-y-4" :class="!site.show_contact ? 'lg:col-span-2' : ''">
                        <WebsiteMap v-if="site.show_map && site.map_embed_url" :src="site.map_embed_url" :address="site.address" />
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <a v-if="site.contact_phone" :href="`tel:${site.contact_phone}`" class="flex items-center gap-3 rounded-xl border border-slate-200 p-4 hover:border-emerald-300 bg-white">
                                <span class="w-10 h-10 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center"><i class="pi pi-phone" /></span>
                                <span><span class="block text-xs text-slate-500">{{ t('website.contact.call') }}</span><span class="font-medium text-slate-900" dir="ltr">{{ site.contact_phone }}</span></span>
                            </a>
                            <a v-if="site.contact_email" :href="`mailto:${site.contact_email}`" class="flex items-center gap-3 rounded-xl border border-slate-200 p-4 hover:border-emerald-300 bg-white min-w-0">
                                <span class="w-10 h-10 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0"><i class="pi pi-envelope" /></span>
                                <span class="min-w-0"><span class="block text-xs text-slate-500">{{ t('website.contact.email') }}</span><span class="font-medium text-slate-900 break-all">{{ site.contact_email }}</span></span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </WebsiteLayout>
</template>
