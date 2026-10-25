<script setup>
import { date } from "@/Helpers/format";
import WebsiteLayout from "@/Layouts/WebsiteLayout.vue";
import { Link, router } from "@inertiajs/vue3";
import IconField from "primevue/iconfield";
import InputIcon from "primevue/inputicon";
import InputText from "primevue/inputtext";
import Paginator from "primevue/paginator";
import { ref } from "vue";
import { useI18n } from "vue-i18n";

const props = defineProps({ site: Object, posts: Object, filters: Object });
const { t } = useI18n();
const search = ref(props.filters?.search || "");
const go = (params) => router.get(route("website.blog"), { ...(search.value ? { search: search.value } : {}), ...params }, { preserveScroll: false });
</script>

<template>
    <WebsiteLayout :site="site" :title="t('website.blog.title')" :description="t('website.blog.latest_subtitle')">
        <section class="bg-gradient-to-br from-emerald-50 to-white dark:from-slate-900 dark:to-slate-950 border-b border-slate-100">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 py-12 sm:py-16 flex flex-wrap items-end justify-between gap-4">
                <div>
                    <h1 class="text-3xl sm:text-4xl font-bold text-slate-900">{{ t('website.blog.title') }}</h1>
                    <p class="mt-2 text-slate-600">{{ t('website.blog.latest_subtitle') }}</p>
                </div>
                <form @submit.prevent="go({})">
                    <IconField>
                        <InputIcon class="pi pi-search" />
                        <InputText v-model="search" :placeholder="t('website.blog.search')" />
                    </IconField>
                </form>
            </div>
        </section>
        <section class="max-w-7xl mx-auto px-4 sm:px-6 py-12">
            <div v-if="!posts.data.length" class="text-center text-slate-500 py-16"><i class="pi pi-book text-4xl block mb-3 text-slate-300" />{{ t('website.blog.empty') }}</div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <Link v-for="post in posts.data" :key="post.id" :href="route('website.post', post.slug)" class="group rounded-2xl border border-slate-200 overflow-hidden bg-white hover:shadow-md transition flex flex-col">
                    <div class="aspect-[16/9] bg-slate-100 overflow-hidden">
                        <img v-if="post.cover_url" :src="post.cover_url" :alt="post.title" class="w-full h-full object-cover group-hover:scale-105 transition-transform" loading="lazy" />
                        <div v-else class="w-full h-full flex items-center justify-center"><i class="pi pi-book text-4xl text-slate-300" /></div>
                    </div>
                    <div class="p-5 flex-1 flex flex-col">
                        <div class="text-xs text-slate-500">{{ date(post.published_at) }}<span v-if="post.author"> · {{ post.author.name }}</span></div>
                        <h2 class="mt-1 text-lg font-semibold text-slate-900 group-hover:text-emerald-700 line-clamp-2">{{ post.title }}</h2>
                        <p v-if="post.excerpt" class="mt-2 text-sm text-slate-600 line-clamp-3 flex-1">{{ post.excerpt }}</p>
                        <span class="mt-4 text-sm font-medium text-emerald-700">{{ t('website.blog.read_more') }} <i class="pi pi-arrow-right text-xs" /></span>
                    </div>
                </Link>
            </div>
            <Paginator v-if="posts.total > posts.per_page" class="mt-8" :rows="posts.per_page" :totalRecords="posts.total" :first="(posts.current_page - 1) * posts.per_page"
                @page="(e) => go({ page: e.page + 1 })" />
        </section>
    </WebsiteLayout>
</template>
