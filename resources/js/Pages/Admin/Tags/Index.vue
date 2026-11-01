<template>
    <Head :title="t('menu.tags')" />
    <div>
        <CrudComponent :form>
            <template #columns>
                <Column field="name" :header="t('common.name')">
                    <template #body="{ data }">
                        <span class="inline-flex items-center gap-1.5 font-medium text-slate-800">
                            <i class="pi pi-hashtag text-slate-400 !text-xs" />{{ data.name }}
                        </span>
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

            <template #form="{ submitted }">
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
import InputText from "primevue/inputtext";
import Select from "primevue/select";
import Tag from "primevue/tag";
import { date } from "@/Helpers/format";

const { t } = useI18n();

const form = useForm({
    name: "",
    is_active: 1,
});

const statusOptions = computed(() => [
    { label: t("shared.status.active"), value: 1 },
    { label: t("shared.status.inactive"), value: 0 },
]);
</script>
