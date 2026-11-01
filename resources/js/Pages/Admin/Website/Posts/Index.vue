<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { useFlashToast } from "@/Composables/useFlashToast";
import { cleanParams, date, num } from "@/Helpers/format";
import { Head, Link, router } from "@inertiajs/vue3";
import Button from "primevue/button";
import Column from "primevue/column";
import DataTable from "primevue/datatable";
import IconField from "primevue/iconfield";
import InputIcon from "primevue/inputicon";
import InputText from "primevue/inputtext";
import Paginator from "primevue/paginator";
import Tag from "primevue/tag";
import { useConfirm } from "primevue/useconfirm";
import { computed, ref } from "vue";
import { useI18n } from "vue-i18n";
import debounce from "lodash/debounce";

useFlashToast({ error: false });
const props = defineProps({ posts: Object, filters: Object, counts: Object });
const { t } = useI18n();
const confirm = useConfirm();

const search = ref(props.filters?.search || "");
const status = computed(() => props.filters?.status || "");
const tabs = computed(() => [
    { value: "", label: t("common.all"), count: props.counts.all },
    { value: "published", label: t("website.admin.published"), count: props.counts.published },
    { value: "draft", label: t("website.admin.draft"), count: props.counts.draft },
    { value: "trashed", label: t("website.admin.trash"), count: props.counts.trashed },
]);

function load(params = {}) {
    router.get(route("website.posts.index"), cleanParams({ search: search.value, status: status.value, ...params }), { preserveState: true, preserveScroll: true, replace: true });
}
const onSearch = debounce(() => load({ page: undefined }), 350);

function ask(message, accept, severity = "danger") {
    confirm.require({
        message, header: t("common.are_you_sure"), icon: "pi pi-exclamation-triangle",
        acceptProps: { label: t("common.confirm"), severity }, rejectProps: { label: t("common.cancel"), severity: "secondary", outlined: true }, accept,
    });
}
const trash = (p) => ask(t("website.admin.confirm_trash_post", { title: p.title }), () => router.delete(route("website.posts.destroy", p.id), { preserveScroll: true }));
const restore = (p) => router.post(route("website.posts.restore", p.id), {}, { preserveScroll: true });
const forceDelete = (p) => ask(t("website.admin.confirm_force_delete_post", { title: p.title }), () => router.delete(route("website.posts.force-delete", p.id), { preserveScroll: true }));
</script>

<template>
    <Head :title="t('menu.blog_posts')" />
    <AuthenticatedLayout>
        <div class="space-y-4">
            <div class="flex flex-wrap justify-between items-start gap-3">
                <div>
                    <h1 class="text-xl font-semibold text-slate-800 flex items-center gap-2"><i class="pi pi-book text-primary" /> {{ t('menu.blog_posts') }}</h1>
                    <p class="text-sm text-slate-500">{{ t('website.admin.posts_subtitle') }}</p>
                </div>
                <Link :href="route('website.posts.create')"><Button :label="t('website.admin.new_post')" icon="pi pi-plus" size="small" /></Link>
            </div>

            <div class="card !p-2 sm:!p-4 space-y-3">
                <div class="flex flex-wrap items-center justify-between gap-2">
                    <div class="flex flex-wrap gap-1">
                        <Button v-for="tab in tabs" :key="tab.value" size="small" :severity="status === tab.value ? 'primary' : 'secondary'" :text="status !== tab.value"
                            :label="`${tab.label} (${num(tab.count)})`" @click="load({ status: tab.value || undefined, page: undefined })" />
                    </div>
                    <IconField>
                        <InputIcon class="pi pi-search" />
                        <InputText v-model="search" :placeholder="t('common.search')" size="small" @input="onSearch" />
                    </IconField>
                </div>

                <DataTable :value="posts.data" dataKey="id" size="small" scrollable>
                    <template #empty><div class="text-center text-slate-400 py-8">{{ t('website.admin.no_posts') }}</div></template>
                    <Column :header="t('website.admin.post_title')">
                        <template #body="{ data }">
                            <div class="flex items-center gap-3">
                                <img v-if="data.cover_url" :src="data.cover_url" alt="" class="w-14 h-10 rounded object-cover shrink-0" />
                                <span v-else class="w-14 h-10 rounded bg-slate-100 flex items-center justify-center shrink-0"><i class="pi pi-image text-slate-300" /></span>
                                <div class="min-w-0">
                                    <component :is="data.deleted_at ? 'span' : Link" :href="data.deleted_at ? undefined : route('website.posts.edit', data.id)" class="font-medium text-slate-800 hover:text-primary line-clamp-1">{{ data.title }}</component>
                                    <div class="text-xs text-slate-500">/blog/{{ data.slug }}</div>
                                </div>
                            </div>
                        </template>
                    </Column>
                    <Column :header="t('common.status')" style="width: 8rem">
                        <template #body="{ data }">
                            <Tag v-if="data.deleted_at" :value="t('website.admin.trash')" severity="danger" />
                            <Tag v-else :value="data.status === 'published' ? t('website.admin.published') : t('website.admin.draft')" :severity="data.status === 'published' ? 'success' : 'secondary'" />
                        </template>
                    </Column>
                    <Column :header="t('website.admin.published_on')" style="width: 9rem"><template #body="{ data }">{{ data.published_at ? date(data.published_at) : '—' }}</template></Column>
                    <Column :header="t('website.admin.views')" style="width: 6rem"><template #body="{ data }">{{ num(data.views) }}</template></Column>
                    <Column style="width: 8rem">
                        <template #body="{ data }">
                            <div class="flex justify-end gap-1">
                                <template v-if="!data.deleted_at">
                                    <a v-if="data.status === 'published'" :href="route('website.post', data.slug)" target="_blank" rel="noopener">
                                        <Button icon="pi pi-external-link" text rounded size="small" :aria-label="t('website.admin.view_site')" />
                                    </a>
                                    <Link :href="route('website.posts.edit', data.id)"><Button icon="pi pi-pencil" text rounded size="small" :aria-label="t('common.edit')" /></Link>
                                    <Button icon="pi pi-trash" text rounded size="small" severity="danger" :aria-label="t('common.delete')" @click="trash(data)" />
                                </template>
                                <template v-else>
                                    <Button icon="pi pi-replay" text rounded size="small" :aria-label="t('website.admin.restore')" @click="restore(data)" />
                                    <Button icon="pi pi-times" text rounded size="small" severity="danger" :aria-label="t('website.admin.delete_forever')" @click="forceDelete(data)" />
                                </template>
                            </div>
                        </template>
                    </Column>
                </DataTable>
                <Paginator v-if="posts.total > posts.per_page" :rows="posts.per_page" :totalRecords="posts.total" :first="(posts.current_page - 1) * posts.per_page"
                    @page="(e) => load({ page: e.page + 1 })" />
            </div>
        </div>
    </AuthenticatedLayout>
</template>
