<template>
    <Head :title="t('menu.attribute_values')" />
    <div>
        <CrudComponent :form="form">
            <template #columns>
                <Column field="attribute" :header="t('catalog.attributes.attribute')">
                    <template #body="{ data }">
                        <Tag severity="secondary" :value="data.attribute?.display_name || data.attribute?.name || '—'" />
                    </template>
                </Column>
                <Column field="value" :header="t('catalog.attributes.value')">
                    <template #body="{ data }"><span class="ltr-nums text-sm text-slate-600">{{ data.value }}</span></template>
                </Column>
                <Column field="display_value" :header="t('catalog.attributes.display_value')">
                    <template #body="{ data }"><span class="font-medium text-slate-800">{{ data.display_value || data.value }}</span></template>
                </Column>
                <Column field="color_code" :header="t('catalog.attributes.color')">
                    <template #body="{ data }">
                        <div v-if="data.color_code" class="flex items-center gap-2">
                            <span :style="{ backgroundColor: asHex(data.color_code) }" class="w-5 h-5 rounded-full border border-slate-300 shadow-sm" />
                            <span class="ltr-nums text-sm">{{ asHex(data.color_code) }}</span>
                        </div>
                        <span v-else class="text-slate-400">—</span>
                    </template>
                </Column>
            </template>

            <template #form="{ submitted }">
                <Form :form="form" :attributes="attributes || []" :submitted="submitted" />
            </template>
        </CrudComponent>
    </div>
</template>

<script setup>
import CrudComponent from "@/Components/CrudComponent.vue";
import { Head, useForm } from "@inertiajs/vue3";
import { useI18n } from "vue-i18n";
import Column from "primevue/column";
import Tag from "primevue/tag";
import Form from "./Form.vue";

const { t } = useI18n();

defineProps({
    attributes: { type: Array, default: () => [] },
});

const form = useForm({
    attribute_id: null,
    value: "",
    display_value: "",
    color_code: null,
});

const asHex = (c) => (c && !String(c).startsWith("#") ? `#${c}` : c);
</script>
