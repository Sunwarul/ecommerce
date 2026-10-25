<script setup>
import { date } from "@/Helpers/format";
import WebsiteLayout from "@/Layouts/WebsiteLayout.vue";
import { Link } from "@inertiajs/vue3";
import Button from "primevue/button";
import { ref } from "vue";
import { useI18n } from "vue-i18n";

const props = defineProps({ site: Object, post: Object, related: { type: Array, default: () => [] } });
const { t } = useI18n();
const copied = ref(false);
const url = typeof window !== "undefined" ? window.location.href : "";

function share() {
    if (navigator.share) {
        navigator.share({ title: props.post.title, url }).catch(() => {});
    } else {
        navigator.clipboard?.writeText(url);
        copied.value = true;
        setTimeout(() => (copied.value = false), 2000);
    }
}
</script>

<template>
    <WebsiteLayout :site="site" :title="post.meta_title || post.title" :description="post.meta_description || post.excerpt" :image="post.cover_url">
        <article class="max-w-3xl mx-auto px-4 sm:px-6 py-10 sm:py-14">
            <Link :href="route('website.blog')" class="text-sm text-emerald-700 hover:underline"><i class="pi pi-arrow-left text-xs me-1" />{{ t('website.blog.title') }}</Link>
            <h1 class="mt-4 text-3xl sm:text-4xl font-bold text-slate-900 leading-tight">{{ post.title }}</h1>
            <div class="mt-3 flex flex-wrap items-center gap-x-4 gap-y-1 text-sm text-slate-500">
                <span><i class="pi pi-calendar me-1" />{{ date(post.published_at) }}</span>
                <span v-if="post.author"><i class="pi pi-user me-1" />{{ post.author.name }}</span>
                <Button :label="copied ? t('website.blog.copied') : t('website.blog.share')" icon="pi pi-share-alt" text size="small" class="ms-auto" @click="share" />
            </div>
            <img v-if="post.cover_url" :src="post.cover_url" :alt="post.title" class="mt-6 w-full rounded-2xl object-cover aspect-[16/9]" />
            <p v-if="post.excerpt" class="mt-6 text-lg text-slate-600 leading-relaxed">{{ post.excerpt }}</p>
            <!-- body is sanitized on save (App\Support\HtmlSanitizer) -->
            <div class="post-body mt-6" v-html="post.body" />
        </article>

        <section v-if="related.length" class="bg-slate-50 border-t border-slate-100">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 py-12">
                <h2 class="text-xl font-bold text-slate-900">{{ t('website.blog.more') }}</h2>
                <div class="mt-6 grid grid-cols-1 sm:grid-cols-3 gap-6">
                    <Link v-for="p in related" :key="p.id" :href="route('website.post', p.slug)" class="group rounded-xl border border-slate-200 bg-white overflow-hidden hover:shadow-md transition">
                        <div class="aspect-[16/9] bg-slate-100 overflow-hidden">
                            <img v-if="p.cover_url" :src="p.cover_url" :alt="p.title" class="w-full h-full object-cover" loading="lazy" />
                        </div>
                        <div class="p-4">
                            <div class="text-xs text-slate-500">{{ date(p.published_at) }}</div>
                            <h3 class="mt-1 font-semibold text-slate-900 group-hover:text-emerald-700 line-clamp-2">{{ p.title }}</h3>
                        </div>
                    </Link>
                </div>
            </div>
        </section>
    </WebsiteLayout>
</template>

<style scoped>
.post-body { color: var(--p-surface-700); line-height: 1.8; font-size: 1.05rem; overflow-wrap: anywhere; }
:global(.app-dark) .post-body { color: var(--p-surface-200); }
.post-body :deep(h1), .post-body :deep(h2), .post-body :deep(h3) { color: var(--body-text-color); font-weight: 700; line-height: 1.3; margin: 1.8em 0 0.6em; }
.post-body :deep(h1) { font-size: 1.75rem; }
.post-body :deep(h2) { font-size: 1.5rem; }
.post-body :deep(h3) { font-size: 1.25rem; }
.post-body :deep(p) { margin: 0 0 1.1em; }
.post-body :deep(a) { color: var(--p-primary-600); text-decoration: underline; }
.post-body :deep(ul), .post-body :deep(ol) { margin: 0 0 1.1em; padding-inline-start: 1.5em; }
.post-body :deep(ul) { list-style: disc; }
.post-body :deep(ol) { list-style: decimal; }
.post-body :deep(li[data-list="bullet"]) { list-style-type: disc; }
.post-body :deep(li[data-list="ordered"]) { list-style-type: decimal; }
.post-body :deep(blockquote) { border-inline-start: 4px solid var(--p-primary-300); padding: 0.25em 1em; margin: 1.2em 0; color: var(--p-surface-500); font-style: italic; }
.post-body :deep(img) { max-width: 100%; height: auto; border-radius: 0.75rem; margin: 1em 0; }
.post-body :deep(iframe) { width: 100%; aspect-ratio: 16 / 9; border-radius: 0.75rem; margin: 1em 0; }
.post-body :deep(pre) { background: var(--p-surface-100); padding: 1em; border-radius: 0.5rem; overflow-x: auto; font-size: 0.9em; }
.post-body :deep(table) { width: 100%; border-collapse: collapse; margin: 1em 0; }
.post-body :deep(th), .post-body :deep(td) { border: 1px solid var(--border-color); padding: 0.5em 0.75em; }
.post-body :deep(.ql-align-center) { text-align: center; }
.post-body :deep(.ql-align-right) { text-align: end; }
.post-body :deep(.ql-align-justify) { text-align: justify; }
</style>
