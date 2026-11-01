<template>
    <Head :title="t('menu.warranty_guarantees')" />
    <div>
        <CrudComponent :form :categories>
            <template #columns>
                <Column field="name" :header="t('common.name')" sortable>
                    <template #body="{ data }">
                        <div class="font-medium text-slate-800">{{ data.name }}</div>
                        <div v-if="data.description" class="text-xs text-slate-500 line-clamp-1 max-w-sm">{{ data.description }}</div>
                    </template>
                </Column>
                <Column field="type" :header="t('common.type')" sortable>
                    <template #body="{ data }">
                        <Tag :severity="typeSeverity(data.type)" :value="typeLabel(data.type)" :icon="typeIcon(data.type)" />
                    </template>
                </Column>
                <Column field="duration" :header="t('catalog.warranty.duration')" sortable />
                <Column field="category.name" :header="t('products.category')" sortable>
                    <template #body="{ data }">
                        <span v-if="data.category" class="text-sm">{{ data.category.name }}</span>
                        <span v-else class="text-xs text-slate-400">{{ t("catalog.warranty.all_categories") }}</span>
                    </template>
                </Column>
                <Column field="is_active" :header="t('common.status')">
                    <template #body="{ data }">
                        <Tag :severity="data.is_active ? 'success' : 'secondary'"
                            :value="data.is_active ? t('shared.status.active') : t('shared.status.inactive')" />
                    </template>
                </Column>
            </template>

            <template #form="{ submitted }">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="name" class="block text-sm font-medium text-slate-700 mb-1.5">{{ t("common.name") }} <span class="text-rose-600">*</span></label>
                        <InputText id="name" v-model.trim="form.name" autofocus :invalid="(submitted && !form.name) || !!form.errors.name" fluid
                            :placeholder="t('catalog.warranty.name_placeholder')" />
                        <small v-if="form.errors.name" class="p-error">{{ form.errors.name }}</small>
                        <small v-else-if="submitted && !form.name" class="p-error">{{ t("catalog.validation.name_required") }}</small>
                    </div>

                    <div>
                        <label for="category_id" class="block text-sm font-medium text-slate-700 mb-1.5">{{ t("products.category") }}</label>
                        <Select inputId="category_id" v-model="form.category_id" :options="categories" optionLabel="name" optionValue="id" filter
                            :placeholder="t('catalog.warranty.all_categories')" class="w-full" showClear />
                        <small class="text-slate-500">{{ t("catalog.warranty.category_hint") }}</small>
                    </div>

                    <div>
                        <label for="type" class="block text-sm font-medium text-slate-700 mb-1.5">{{ t("common.type") }} <span class="text-rose-600">*</span></label>
                        <Select inputId="type" v-model="form.type" :options="typeOptions" optionLabel="label" optionValue="value" class="w-full"
                            :invalid="submitted && !form.type" />
                        <small v-if="submitted && !form.type" class="p-error">{{ t("catalog.validation.type_required") }}</small>
                    </div>

                    <div>
                        <label for="duration" class="block text-sm font-medium text-slate-700 mb-1.5">{{ t("catalog.warranty.duration") }} <span class="text-rose-600">*</span></label>
                        <InputText id="duration" v-model.trim="form.duration" :invalid="(submitted && !form.duration) || !!form.errors.duration" fluid
                            :placeholder="t('catalog.warranty.duration_placeholder')" />
                        <small v-if="form.errors.duration" class="p-error">{{ form.errors.duration }}</small>
                        <small v-else-if="submitted && !form.duration" class="p-error">{{ t("catalog.validation.duration_required") }}</small>
                    </div>

                    <div class="md:col-span-2">
                        <label for="description" class="block text-sm font-medium text-slate-700 mb-1.5">{{ t("catalog.warranty.text") }}</label>
                        <Textarea id="description" v-model.trim="form.description" rows="4" fluid :placeholder="t('catalog.warranty.text_placeholder')" />
                        <small class="text-slate-500">{{ t("catalog.warranty.text_hint") }}</small>
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
import Textarea from "primevue/textarea";

const { t } = useI18n();

defineProps({
    categories: { type: Array, default: () => [] },
});

const form = useForm({
    name: "",
    type: "Warranty",
    duration: "",
    description: "",
    category_id: null,
    is_active: 1,
});

// Stored values stay in English (existing data); only the labels are translated
const TYPES = {
    Warranty: { key: "warranty", severity: "success", icon: "pi pi-shield" },
    Guaranty: { key: "guaranty", severity: "info", icon: "pi pi-verified" },
    Service: { key: "service", severity: "warn", icon: "pi pi-wrench" },
    None: { key: "none", severity: "secondary", icon: "pi pi-minus-circle" },
};

const typeOptions = computed(() => Object.entries(TYPES).map(([value, m]) => ({ value, label: t(`catalog.warranty.types.${m.key}`) })));
const typeLabel = (v) => (TYPES[v] ? t(`catalog.warranty.types.${TYPES[v].key}`) : v || "—");
const typeSeverity = (v) => TYPES[v]?.severity || "secondary";
const typeIcon = (v) => TYPES[v]?.icon;

const statusOptions = computed(() => [
    { label: t("shared.status.active"), value: 1 },
    { label: t("shared.status.inactive"), value: 0 },
]);
</script>
