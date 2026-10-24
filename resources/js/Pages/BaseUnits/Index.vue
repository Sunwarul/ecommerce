<template>
    <CrudComponent :form="form" icon="pi pi-box">
        <template #columns>
            <Column field="name" :header="t('crud.fields.name')"></Column>
            <Column field="status" :header="t('crud.fields.status')">
                <template #body="{ data }">
                    <Tag :value="isOn(data.status) ? t('crud.fields.active') : t('crud.fields.inactive')"
                        :severity="isOn(data.status) ? 'success' : 'secondary'" />
                </template>
            </Column>
            <Column field="created_at" :header="t('crud.fields.created_at')">
                <template #body="{ data }">{{ date(data.created_at) }}</template>
            </Column>
        </template>

        <template #form>
            <Form :form="form" />
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
import { date } from '@/Helpers/format';

const { t } = useI18n();
const isOn = (v) => !!Number(v);

const form = useForm({
    name: '',
    status: 1,
});
</script>
