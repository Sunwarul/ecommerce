<template>
    <CrudComponent :form="form" icon="pi pi-money-bill">
        <template #columns>
            <Column field="title" :header="t('crud.fields.title')" bodyClass="font-medium text-slate-800"></Column>
            <Column field="short_code" :header="t('crud.fields.short_code')"></Column>
            <Column field="symbol" :header="t('crud.fields.symbol')"></Column>
            <Column field="exchange_rate" :header="t('crud.fields.exchange_rate')" bodyClass="tabular-nums"></Column>
            <Column field="date" :header="t('crud.fields.date')">
                <template #body="{ data }">{{ date(data.date) }}</template>
            </Column>
            <Column field="status" :header="t('crud.fields.status')">
                <template #body="{ data }">
                    <Tag :value="isOn(data.status) ? t('crud.fields.active') : t('crud.fields.inactive')"
                        :severity="isOn(data.status) ? 'success' : 'secondary'" />
                </template>
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
    title: '',
    short_code: '',
    date: '',
    symbol: '',
    exchange_rate: 1,
    status: true,
});
</script>
