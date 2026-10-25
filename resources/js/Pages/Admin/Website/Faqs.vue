<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { useFlashToast } from "@/Composables/useFlashToast";
import { firstError } from "@/Helpers/format";
import { LOCALE_NAMES } from "@/Stores/localeStore";
import { Head, router, usePage } from "@inertiajs/vue3";
import Button from "primevue/button";
import Column from "primevue/column";
import DataTable from "primevue/datatable";
import Dialog from "primevue/dialog";
import InputText from "primevue/inputtext";
import Select from "primevue/select";
import Tag from "primevue/tag";
import Textarea from "primevue/textarea";
import ToggleSwitch from "primevue/toggleswitch";
import { useConfirm } from "primevue/useconfirm";
import { useToast } from "primevue/usetoast";
import { computed, ref, watch } from "vue";
import { useI18n } from "vue-i18n";

useFlashToast({ error: false });
const props = defineProps({ faqs: { type: Array, default: () => [] } });
const { t } = useI18n();
const toast = useToast();
const confirm = useConfirm();
const page = usePage();

const rows = ref([...props.faqs]);
watch(() => props.faqs, (v) => (rows.value = [...v]));

const localeOptions = computed(() => [
    { label: t("website.admin.all_languages"), value: null },
    ...(page.props.locale?.supported || []).map((l) => ({ label: LOCALE_NAMES[l] || l, value: l })),
]);
const localeLabel = (l) => (l ? LOCALE_NAMES[l] || l : t("website.admin.all_languages"));

const blank = () => ({ id: null, question: "", answer: "", locale: page.props.locale?.current === "bn" ? "bn" : null, is_active: true });
const show = ref(false);
const form = ref(blank());
const saving = ref(false);

function openCreate() {
    form.value = blank();
    show.value = true;
}
function openEdit(faq) {
    form.value = { ...faq };
    show.value = true;
}
function save() {
    const { id, ...payload } = form.value;
    saving.value = true;
    const opts = {
        preserveScroll: true,
        onSuccess: () => (show.value = false),
        onError: (e) => toast.add({ severity: "error", summary: t("shared.terms.error"), detail: firstError(e), life: 4000 }),
        onFinish: () => (saving.value = false),
    };
    id ? router.put(route("website.faqs.update", id), payload, opts) : router.post(route("website.faqs.store"), payload, opts);
}
function destroy(faq) {
    confirm.require({
        message: t("website.admin.confirm_delete_faq"),
        header: t("common.are_you_sure"),
        icon: "pi pi-exclamation-triangle",
        acceptProps: { label: t("common.delete"), severity: "danger" },
        rejectProps: { label: t("common.cancel"), severity: "secondary", outlined: true },
        accept: () => router.delete(route("website.faqs.destroy", faq.id), { preserveScroll: true }),
    });
}
function onReorder(e) {
    rows.value = e.value;
    router.post(route("website.faqs.reorder"), { ids: rows.value.map((r) => r.id) }, { preserveScroll: true, preserveState: true });
}
</script>

<template>
    <Head :title="t('menu.faqs')" />
    <AuthenticatedLayout>
        <div class="space-y-4">
            <div class="flex flex-wrap justify-between items-start gap-3">
                <div>
                    <h1 class="text-xl font-semibold text-slate-800 flex items-center gap-2"><i class="pi pi-question-circle text-primary" /> {{ t('menu.faqs') }}</h1>
                    <p class="text-sm text-slate-500">{{ t('website.admin.faqs_subtitle') }}</p>
                </div>
                <div class="flex gap-2">
                    <a :href="route('website.faq')" target="_blank" rel="noopener"><Button :label="t('website.admin.view_site')" icon="pi pi-external-link" severity="secondary" outlined size="small" /></a>
                    <Button :label="t('website.admin.new_faq')" icon="pi pi-plus" size="small" @click="openCreate" />
                </div>
            </div>

            <div class="card !p-2 sm:!p-4">
                <DataTable :value="rows" dataKey="id" size="small" @rowReorder="onReorder" scrollable>
                    <template #empty><div class="text-center text-slate-400 py-6">{{ t('website.admin.no_faqs') }}</div></template>
                    <Column rowReorder headerStyle="width: 3rem" />
                    <Column :header="t('website.admin.question')">
                        <template #body="{ data }">
                            <div class="font-medium text-slate-800">{{ data.question }}</div>
                            <div class="text-xs text-slate-500 line-clamp-2 max-w-xl">{{ data.answer }}</div>
                        </template>
                    </Column>
                    <Column :header="t('website.admin.language')" style="width: 10rem"><template #body="{ data }">{{ localeLabel(data.locale) }}</template></Column>
                    <Column :header="t('common.status')" style="width: 8rem">
                        <template #body="{ data }"><Tag :value="data.is_active ? t('shared.status.active') : t('shared.status.inactive')" :severity="data.is_active ? 'success' : 'secondary'" /></template>
                    </Column>
                    <Column style="width: 7rem">
                        <template #body="{ data }">
                            <div class="flex justify-end gap-1">
                                <Button icon="pi pi-pencil" text rounded size="small" :aria-label="t('common.edit')" @click="openEdit(data)" />
                                <Button icon="pi pi-trash" text rounded size="small" severity="danger" :aria-label="t('common.delete')" @click="destroy(data)" />
                            </div>
                        </template>
                    </Column>
                </DataTable>
                <p v-if="rows.length > 1" class="text-xs text-slate-400 mt-2"><i class="pi pi-arrows-v me-1" />{{ t('website.admin.drag_to_reorder') }}</p>
            </div>
        </div>

        <Dialog v-model:visible="show" modal :header="form.id ? t('website.admin.edit_faq') : t('website.admin.new_faq')" :style="{ width: '40rem' }" :breakpoints="{ '640px': '95vw' }">
            <div class="flex flex-col gap-4">
                <div class="flex flex-col gap-1.5">
                    <label for="faq_q" class="text-sm font-medium">{{ t('website.admin.question') }} <span class="text-red-500">*</span></label>
                    <InputText id="faq_q" v-model="form.question" maxlength="255" fluid autofocus />
                </div>
                <div class="flex flex-col gap-1.5">
                    <label for="faq_a" class="text-sm font-medium">{{ t('website.admin.answer') }} <span class="text-red-500">*</span></label>
                    <Textarea id="faq_a" v-model="form.answer" rows="5" autoResize maxlength="5000" fluid />
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="flex flex-col gap-1.5">
                        <label for="faq_l" class="text-sm font-medium">{{ t('website.admin.language') }}</label>
                        <Select inputId="faq_l" v-model="form.locale" :options="localeOptions" optionLabel="label" optionValue="value" fluid />
                    </div>
                    <div class="flex items-center gap-3 pt-6">
                        <ToggleSwitch inputId="faq_active" v-model="form.is_active" />
                        <label for="faq_active" class="text-sm">{{ t('website.admin.show_on_site') }}</label>
                    </div>
                </div>
            </div>
            <template #footer>
                <Button :label="t('common.cancel')" severity="secondary" text @click="show = false" />
                <Button :label="t('common.save')" icon="pi pi-check" :loading="saving" @click="save" />
            </template>
        </Dialog>
    </AuthenticatedLayout>
</template>
