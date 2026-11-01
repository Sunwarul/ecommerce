<template>
    <Head :title="t('menu.attributes')" />
    <div>
        <CrudComponent :form="form">
            <template #columns>
                <Column field="name" :header="t('catalog.attributes.key')">
                    <template #body="{ data }"><span class="ltr-nums text-sm text-slate-600">{{ data.name }}</span></template>
                </Column>
                <Column field="display_name" :header="t('catalog.attributes.display_name')">
                    <template #body="{ data }"><span class="font-medium text-slate-800">{{ data.display_name }}</span></template>
                </Column>
                <Column field="type" :header="t('catalog.attributes.input_type')">
                    <template #body="{ data }">
                        <Tag severity="secondary" :value="t(`catalog.attributes.types.${data.type}`, data.type)" />
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
                <Form :form="form" :submitted="submitted" />
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

const form = useForm({
    name: "",
    display_name: "",
    type: "select",
    is_active: true,
});
</script>
