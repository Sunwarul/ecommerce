<template>
    <CrudComponent :form="form" icon="pi pi-building">
        <template #columns>
            <Column field="name" :header="t('crud.fields.name')" bodyClass="font-medium text-slate-800"></Column>
            <Column field="code" :header="t('crud.fields.code')"></Column>
            <Column field="phone" :header="t('crud.fields.phone')" bodyClass="ltr-nums whitespace-nowrap"></Column>
            <Column field="address" :header="t('crud.fields.address')"></Column>
            <Column field="is_active" :header="t('crud.fields.status')">
                <template #body="{ data }">
                    <Tag :value="isOn(data.is_active) ? t('crud.fields.active') : t('crud.fields.inactive')"
                        :severity="isOn(data.is_active) ? 'success' : 'secondary'" />
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
    address: '',
    code: '',
    phone: '',
    is_active: true,
});
</script>
