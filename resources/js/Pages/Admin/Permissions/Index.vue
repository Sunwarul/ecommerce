<template>
    <CrudComponent :form="form" icon="pi pi-lock" :can-delete="false" :can-edit="false">
        <template #messages>
            <span class="text-xs text-slate-500 flex items-center gap-1.5">
                <i class="pi pi-info-circle" />{{ t('admin.permissions.locked_note') }}
            </span>
        </template>
        <template #columns>
            <Column field="name" :header="t('admin.permissions.name')" bodyClass="font-medium text-slate-800"></Column>
            <Column :header="t('admin.permissions.module')">
                <template #body="{ data }">
                    <Tag :value="moduleOf(data.name)" severity="secondary" />
                </template>
            </Column>
        </template>
        <template #form="{ submitted }">
            <Form :form="form" v-bind="{ submitted }" />
        </template>
    </CrudComponent>
</template>
<script setup>
import CrudComponent from '@/Components/CrudComponent.vue';
import Form from './Form.vue';
import { useForm } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import Column from 'primevue/column';
import Tag from 'primevue/tag';

const { t } = useI18n();
const moduleOf = (name) => String(name).replace(/ (Bulk Delete|Bulk Restore|Plan Manage|\w+)$/, '');

const form = useForm({
    name: '',
});
</script>
