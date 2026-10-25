<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, router } from "@inertiajs/vue3";
import Button from "primevue/button";
import IconField from "primevue/iconfield";
import InputIcon from "primevue/inputicon";
import InputText from "primevue/inputtext";
import { computed, nextTick, ref, watch } from "vue";
import { useI18n } from "vue-i18n";

const props = defineProps({
    chapters: { type: Array, default: () => [] },
    current: { type: String, default: null },
});
const { t } = useI18n();

const active = ref(props.current);
watch(() => props.current, (v) => (active.value = v));
const chapter = computed(() => props.chapters.find((c) => c.slug === active.value) || props.chapters[0]);
const index = computed(() => props.chapters.findIndex((c) => c.slug === chapter.value?.slug));
const prev = computed(() => props.chapters[index.value - 1]);
const next = computed(() => props.chapters[index.value + 1]);

// Search across the whole manual (all chapters are on the page already)
const q = ref("");
const results = computed(() => {
    const term = q.value.trim().toLowerCase();
    if (term.length < 2) return [];
    return props.chapters
        .map((c) => {
            const at = c.text.indexOf(term);
            const inTitle = c.title.toLowerCase().includes(term);
            if (at < 0 && !inTitle) return null;
            const start = Math.max(0, at - 50);
            return { ...c, snippet: at >= 0 ? `${start ? "…" : ""}${c.text.slice(start, at + term.length + 80)}…` : "" };
        })
        .filter(Boolean);
});

const mobileNav = ref(false);
const printPage = () => window.print();
function open(slug, anchor = null) {
    mobileNav.value = false;
    q.value = "";
    if (slug !== active.value) {
        active.value = slug;
        router.get(route("docs.index", slug), {}, { preserveState: true, preserveScroll: true, only: ["current"], replace: false });
    }
    nextTick(() => {
        const el = anchor && document.getElementById(anchor);
        el ? el.scrollIntoView({ behavior: "smooth", block: "start" }) : window.scrollTo({ top: 0, behavior: "smooth" });
    });
}
</script>

<template>
    <Head :title="chapter ? `${chapter.title} · ${t('menu.user_manual')}` : t('menu.user_manual')" />
    <AuthenticatedLayout>
        <div class="max-w-7xl mx-auto">
            <div class="flex flex-wrap items-center justify-between gap-3 mb-4 print:hidden">
                <div class="flex items-center gap-3">
                    <span class="hidden sm:flex w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 items-center justify-center shrink-0"><i class="pi pi-book text-lg" /></span>
                    <div>
                        <h1 class="text-xl font-semibold text-slate-800">{{ t('menu.user_manual') }}</h1>
                        <p class="text-sm text-slate-500">{{ t('docs.subtitle') }}</p>
                    </div>
                </div>
                <div class="flex gap-2">
                    <Button class="lg:!hidden" :label="t('docs.contents')" icon="pi pi-list" severity="secondary" outlined size="small" @click="mobileNav = !mobileNav" />
                    <Button :label="t('docs.print')" icon="pi pi-print" severity="secondary" outlined size="small" @click="printPage" />
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-[16rem_minmax(0,1fr)] xl:grid-cols-[16rem_minmax(0,1fr)_13rem] gap-6 items-start">
                <!-- Chapters -->
                <aside class="lg:sticky lg:top-24 print:hidden" :class="mobileNav ? 'block' : 'hidden lg:block'">
                    <div class="card !p-3 space-y-3">
                        <IconField>
                            <InputIcon class="pi pi-search" />
                            <InputText v-model="q" :placeholder="t('docs.search')" size="small" fluid />
                        </IconField>
                        <div v-if="q.trim().length >= 2" class="space-y-1 max-h-[60vh] overflow-y-auto">
                            <p v-if="!results.length" class="text-sm text-slate-500 px-2 py-3">{{ t('docs.no_results') }}</p>
                            <button v-for="r in results" :key="r.slug" type="button" class="w-full text-start rounded-lg px-2 py-2 hover:bg-slate-100" @click="open(r.slug)">
                                <div class="text-sm font-medium text-slate-800">{{ r.title }}</div>
                                <div v-if="r.snippet" class="text-xs text-slate-500 line-clamp-2">{{ r.snippet }}</div>
                            </button>
                        </div>
                        <nav v-else class="flex flex-col gap-0.5 max-h-[70vh] overflow-y-auto">
                            <button v-for="(c, i) in chapters" :key="c.slug" type="button" class="text-start rounded-lg px-2 py-1.5 text-sm flex gap-2"
                                :class="c.slug === chapter?.slug ? 'bg-emerald-50 text-emerald-800 font-semibold' : 'text-slate-600 hover:bg-slate-100'" @click="open(c.slug)">
                                <span class="w-5 text-end shrink-0 text-slate-400 tabular-nums">{{ i + 1 }}.</span><span>{{ c.title }}</span>
                            </button>
                        </nav>
                    </div>
                </aside>

                <!-- Chapter -->
                <article v-if="chapter" class="card !p-5 sm:!p-8 min-w-0">
                    <div class="text-xs uppercase tracking-wide text-slate-400 mb-1">{{ t('docs.chapter', { n: index + 1 }) }}</div>
                    <h1 class="text-2xl sm:text-3xl font-bold text-slate-900 mb-4">{{ chapter.title }}</h1>
                    <div class="docs-body" v-html="chapter.html" />
                    <div class="mt-10 pt-6 border-t border-slate-200 flex flex-wrap justify-between gap-3 print:hidden">
                        <Button v-if="prev" :label="prev.title" icon="pi pi-arrow-left" severity="secondary" text @click="open(prev.slug)" />
                        <span v-else />
                        <Button v-if="next" :label="next.title" icon="pi pi-arrow-right" iconPos="right" severity="secondary" text @click="open(next.slug)" />
                    </div>
                </article>
                <div v-else class="card text-center text-slate-500">{{ t('docs.empty') }}</div>

                <!-- On this page -->
                <aside v-if="chapter?.toc?.length" class="hidden xl:block sticky top-24 print:hidden">
                    <div class="text-xs font-semibold uppercase tracking-wide text-slate-400 mb-2">{{ t('docs.on_this_page') }}</div>
                    <nav class="flex flex-col gap-1 border-s border-slate-200 max-h-[75vh] overflow-y-auto">
                        <a v-for="h in chapter.toc" :key="h.id" :href="`#${h.id}`" class="text-sm text-slate-600 hover:text-emerald-700 -ms-px border-s border-transparent hover:border-emerald-500"
                            :class="h.level === 3 ? 'ps-6 text-xs' : 'ps-3'" @click.prevent="open(chapter.slug, h.id)">{{ h.text }}</a>
                    </nav>
                </aside>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.docs-body { color: var(--text-color); line-height: 1.75; overflow-wrap: anywhere; }
.docs-body :deep(h2) { font-size: 1.4rem; font-weight: 700; color: var(--body-text-color); margin: 2em 0 0.6em; padding-top: 0.5em; scroll-margin-top: 6rem; border-top: 1px solid var(--border-color); }
.docs-body :deep(h2:first-child) { border-top: 0; margin-top: 0; padding-top: 0; }
.docs-body :deep(h3) { font-size: 1.15rem; font-weight: 600; color: var(--body-text-color); margin: 1.6em 0 0.5em; scroll-margin-top: 6rem; }
.docs-body :deep(h4) { font-weight: 600; margin: 1.2em 0 0.4em; }
.docs-body :deep(p) { margin: 0 0 1em; }
.docs-body :deep(ul) { list-style: disc; padding-inline-start: 1.5em; margin: 0 0 1em; }
.docs-body :deep(ol) { list-style: decimal; padding-inline-start: 1.5em; margin: 0 0 1em; }
.docs-body :deep(li) { margin: 0.25em 0; }
.docs-body :deep(li > ul), .docs-body :deep(li > ol) { margin: 0.25em 0; }
.docs-body :deep(strong) { color: var(--body-text-color); }
.docs-body :deep(a) { color: var(--p-primary-600); text-decoration: underline; }
.docs-body :deep(code) { background: var(--p-surface-100); border-radius: 4px; padding: 0.1em 0.35em; font-size: 0.9em; }
:global(.app-dark) .docs-body :deep(code) { background: var(--p-surface-800); }
.docs-body :deep(pre) { background: var(--p-surface-100); padding: 1em; border-radius: 8px; overflow-x: auto; margin: 0 0 1em; }
.docs-body :deep(pre code) { background: transparent; padding: 0; }
.docs-body :deep(blockquote) { border-inline-start: 4px solid var(--p-primary-400); background: var(--p-primary-50); padding: 0.75em 1em; border-radius: 0 8px 8px 0; margin: 1em 0; }
:global(.app-dark) .docs-body :deep(blockquote) { background: color-mix(in srgb, var(--p-primary-500) 12%, transparent); }
.docs-body :deep(blockquote p:last-child) { margin-bottom: 0; }
.docs-body :deep(table) { width: 100%; border-collapse: collapse; margin: 0 0 1.2em; font-size: 0.95em; display: block; overflow-x: auto; }
.docs-body :deep(th) { background: var(--p-surface-100); text-align: start; font-weight: 600; }
:global(.app-dark) .docs-body :deep(th) { background: var(--p-surface-800); }
.docs-body :deep(th), .docs-body :deep(td) { border: 1px solid var(--border-color); padding: 0.45em 0.7em; vertical-align: top; }
.docs-body :deep(hr) { border: 0; border-top: 1px solid var(--border-color); margin: 2em 0; }
@media print {
    .docs-body :deep(h2) { break-after: avoid; }
}
</style>
