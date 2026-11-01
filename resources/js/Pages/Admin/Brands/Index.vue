<template>
    <Head :title="t('menu.brands')" />
    <div>
        <CrudComponent :form>
            <template #columns>
                <Column field="photo" :header="t('catalog.brands.logo')" style="width: 5rem">
                    <template #body="{ data }">
                        <img v-if="data.photo" :src="$resolveImagePath(data.photo)" :alt="data.name"
                            class="rounded-lg w-12 h-12 object-contain bg-white border border-slate-200" loading="lazy" />
                        <div v-else class="w-12 h-12 rounded-lg bg-slate-100 text-slate-400 flex items-center justify-center">
                            <i class="pi pi-verified" />
                        </div>
                    </template>
                </Column>
                <Column field="name" :header="t('common.name')">
                    <template #body="{ data }">
                        <div class="font-medium text-slate-800">{{ data.name }}</div>
                        <div v-if="data.description" class="text-xs text-slate-500 line-clamp-1 max-w-xs">{{ data.description }}</div>
                    </template>
                </Column>
                <Column field="is_active" :header="t('common.status')">
                    <template #body="{ data }">
                        <Tag :severity="data.is_active ? 'success' : 'secondary'"
                            :value="data.is_active ? t('shared.status.active') : t('shared.status.inactive')" />
                    </template>
                </Column>
                <Column field="created_at" :header="t('catalog.common.created')" sortable>
                    <template #body="{ data }"><span class="text-sm text-slate-500">{{ date(data.created_at) }}</span></template>
                </Column>
                <Column field="updated_at" :header="t('catalog.common.updated')" sortable>
                    <template #body="{ data }"><span class="text-sm text-slate-500">{{ date(data.updated_at) }}</span></template>
                </Column>
            </template>

            <template #form="{ submitted, handlePhotoUpload, photoPreview, resolveImagePath }">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="name" class="block text-sm font-medium text-slate-700 mb-1.5">{{ t("common.name") }} <span class="text-rose-600">*</span></label>
                        <InputText id="name" v-model.trim="form.name" autofocus :invalid="(submitted && !form.name) || !!form.errors.name" fluid />
                        <small v-if="form.errors.name" class="p-error">{{ form.errors.name }}</small>
                        <small v-else-if="submitted && !form.name" class="p-error">{{ t("catalog.validation.name_required") }}</small>
                    </div>
                    <div>
                        <label for="is_active" class="block text-sm font-medium text-slate-700 mb-1.5">{{ t("common.status") }} <span class="text-rose-600">*</span></label>
                        <Select inputId="is_active" v-model="form.is_active" :options="statusOptions" optionLabel="label" optionValue="value"
                            class="w-full" :invalid="submitted && form.is_active == null" />
                    </div>
                    <div class="md:col-span-2">
                        <label for="description" class="block text-sm font-medium text-slate-700 mb-1.5">{{ t("common.description") }}</label>
                        <Textarea id="description" v-model.trim="form.description" rows="3" fluid />
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ t("catalog.brands.logo") }}</label>
                        <div class="flex flex-wrap items-center gap-3">
                            <div class="w-20 h-20 rounded-xl border border-slate-200 bg-white overflow-hidden flex items-center justify-center shrink-0">
                                <img v-if="form.photo || photoPreview" :src="photoPreview ?? resolveImagePath(form.photo)" :alt="form.name" class="w-full h-full object-contain" />
                                <i v-else class="pi pi-image text-xl text-slate-300" />
                            </div>
                            <FileUpload mode="basic" name="photo" customUpload @select="handlePhotoUpload" :auto="true" accept="image/*"
                                :chooseLabel="t('catalog.form.choose_image')" chooseIcon="pi pi-upload" />
                        </div>
                        <small v-if="form.errors.photo" class="p-error">{{ form.errors.photo }}</small>
                    </div>
                </div>
            </template>
        </CrudComponent>
    </div>
</template>

<script setup>
import CrudComponent from "@/Components/CrudComponent.vue";
import { Head, useForm } from "@inertiajs/vue3";
import { computed } from "vue";
import { useI18n } from "vue-i18n";
import Column from "primevue/column";
import FileUpload from "primevue/fileupload";
import InputText from "primevue/inputtext";
import Select from "primevue/select";
import Tag from "primevue/tag";
import Textarea from "primevue/textarea";
import { date } from "@/Helpers/format";

const { t } = useI18n();

const form = useForm({
    name: "",
    description: "",
    photo: null,
    is_active: 1,
});

const statusOptions = computed(() => [
    { label: t("shared.status.active"), value: 1 },
    { label: t("shared.status.inactive"), value: 0 },
]);
</script>
