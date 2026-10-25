<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import ImageUpload from "@/Components/Website/ImageUpload.vue";
import { useFlashToast } from "@/Composables/useFlashToast";
import { firstError, ymdhms } from "@/Helpers/format";
import { LOCALE_NAMES } from "@/Stores/localeStore";
import { Head, Link, useForm, usePage } from "@inertiajs/vue3";
import Button from "primevue/button";
import DatePicker from "primevue/datepicker";
import Editor from "primevue/editor";
import InputText from "primevue/inputtext";
import Select from "primevue/select";
import SelectButton from "primevue/selectbutton";
import Textarea from "primevue/textarea";
import { useToast } from "primevue/usetoast";
import { computed, ref } from "vue";
import { useI18n } from "vue-i18n";

useFlashToast({ error: false });
const props = defineProps({ post: { type: Object, default: null } });
const { t } = useI18n();
const toast = useToast();
const page = usePage();

const form = useForm({
    title: props.post?.title ?? "",
    slug: props.post?.slug ?? "",
    excerpt: props.post?.excerpt ?? "",
    body: props.post?.body ?? "",
    locale: props.post?.locale ?? null,
    status: props.post?.status ?? "draft",
    published_at: props.post?.published_at ? new Date(props.post.published_at.replace(" ", "T")) : null,
    meta_title: props.post?.meta_title ?? "",
    meta_description: props.post?.meta_description ?? "",
    cover: null,
    remove_cover: false,
});
const slugTouched = ref(!!props.post);

const statuses = computed(() => [
    { label: t("website.admin.draft"), value: "draft" },
    { label: t("website.admin.published"), value: "published" },
]);
const localeOptions = computed(() => [
    { label: t("website.admin.all_languages"), value: null },
    ...(page.props.locale?.supported || []).map((l) => ({ label: LOCALE_NAMES[l] || l, value: l })),
]);

function save() {
    const url = props.post ? route("website.posts.update", props.post.id) : route("website.posts.store");
    form.transform((d) => ({ ...d, published_at: d.published_at ? ymdhms(d.published_at) : null, remove_cover: d.remove_cover ? 1 : 0 }))
        .post(url, {
            forceFormData: true,
            preserveScroll: true,
            onSuccess: () => {
                form.cover = null;
                form.remove_cover = false;
            },
            onError: (e) => toast.add({ severity: "error", summary: t("shared.terms.error"), detail: firstError(e), life: 5000 }),
        });
}
const err = (k) => form.errors[k];
const labelCls = "text-sm font-medium text-slate-700";
</script>

<template>
    <Head :title="post ? t('website.admin.edit_post') : t('website.admin.new_post')" />
    <AuthenticatedLayout>
        <div class="max-w-6xl mx-auto space-y-4">
            <div class="flex flex-wrap justify-between items-start gap-3">
                <div class="flex items-center gap-2">
                    <Link :href="route('website.posts.index')"><Button icon="pi pi-arrow-left" text rounded :aria-label="t('common.back')" /></Link>
                    <h1 class="text-xl font-semibold text-slate-800">{{ post ? t('website.admin.edit_post') : t('website.admin.new_post') }}</h1>
                </div>
                <div class="flex gap-2">
                    <a v-if="post && post.status === 'published'" :href="route('website.post', post.slug)" target="_blank" rel="noopener">
                        <Button :label="t('website.admin.view_site')" icon="pi pi-external-link" severity="secondary" outlined />
                    </a>
                    <Button :label="t('common.save')" icon="pi pi-check" :loading="form.processing" @click="save" />
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                <div class="lg:col-span-2 space-y-4">
                    <div class="card !p-4 space-y-4">
                        <div class="flex flex-col gap-1.5">
                            <label for="p_title" :class="labelCls">{{ t('website.admin.post_title') }} <span class="text-red-500">*</span></label>
                            <InputText id="p_title" v-model="form.title" maxlength="191" fluid class="!text-lg" :invalid="!!err('title')" />
                            <small v-if="err('title')" class="text-red-600">{{ err('title') }}</small>
                        </div>
                        <div class="flex flex-col gap-1.5">
                            <label for="p_slug" :class="labelCls">{{ t('website.admin.slug') }}</label>
                            <div class="flex items-center gap-1 text-sm text-slate-500">
                                <span class="shrink-0">/blog/</span>
                                <InputText id="p_slug" v-model="form.slug" maxlength="191" size="small" fluid :placeholder="t('website.admin.slug_auto')" @input="slugTouched = true" :invalid="!!err('slug')" />
                            </div>
                            <small v-if="err('slug')" class="text-red-600">{{ err('slug') }}</small>
                        </div>
                        <div class="flex flex-col gap-1.5">
                            <label for="p_excerpt" :class="labelCls">{{ t('website.admin.excerpt') }}</label>
                            <Textarea id="p_excerpt" v-model="form.excerpt" rows="2" autoResize maxlength="500" fluid :placeholder="t('website.admin.excerpt_hint')" />
                        </div>
                        <div class="flex flex-col gap-1.5">
                            <span :class="labelCls">{{ t('website.admin.body') }}</span>
                            <Editor v-model="form.body" editorStyle="min-height: 380px" />
                            <small v-if="err('body')" class="text-red-600">{{ err('body') }}</small>
                        </div>
                    </div>
                </div>

                <div class="space-y-4">
                    <div class="card !p-4 space-y-4">
                        <div class="flex flex-col gap-1.5">
                            <span :class="labelCls">{{ t('common.status') }}</span>
                            <SelectButton v-model="form.status" :options="statuses" optionLabel="label" optionValue="value" :allowEmpty="false" />
                        </div>
                        <div class="flex flex-col gap-1.5">
                            <label for="p_date" :class="labelCls">{{ t('website.admin.published_on') }}</label>
                            <DatePicker inputId="p_date" v-model="form.published_at" showTime hourFormat="12" showIcon fluid showButtonBar />
                            <small class="text-slate-500">{{ t('website.admin.publish_date_hint') }}</small>
                        </div>
                        <div class="flex flex-col gap-1.5">
                            <label for="p_locale" :class="labelCls">{{ t('website.admin.language') }}</label>
                            <Select inputId="p_locale" v-model="form.locale" :options="localeOptions" optionLabel="label" optionValue="value" fluid />
                        </div>
                    </div>
                    <div class="card !p-4 space-y-2">
                        <span :class="labelCls">{{ t('website.admin.cover_image') }}</span>
                        <ImageUpload v-model="form.cover" v-model:removed="form.remove_cover" :current="post?.cover_url" previewClass="h-24 w-40" :hint="t('website.admin.cover_hint')" />
                        <small v-if="err('cover')" class="text-red-600">{{ err('cover') }}</small>
                    </div>
                    <div class="card !p-4 space-y-4">
                        <h2 class="font-semibold text-slate-800">{{ t('website.admin.seo') }}</h2>
                        <div class="flex flex-col gap-1.5">
                            <label for="p_mt" :class="labelCls">{{ t('website.admin.meta_title') }}</label>
                            <InputText id="p_mt" v-model="form.meta_title" maxlength="191" fluid :placeholder="form.title" />
                        </div>
                        <div class="flex flex-col gap-1.5">
                            <label for="p_md" :class="labelCls">{{ t('website.admin.fields.meta_description') }}</label>
                            <Textarea id="p_md" v-model="form.meta_description" rows="3" autoResize maxlength="300" fluid :placeholder="form.excerpt" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
