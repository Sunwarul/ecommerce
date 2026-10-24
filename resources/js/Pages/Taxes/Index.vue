<template>
    <CrudComponent :form="form" icon="pi pi-percentage">
        <template #columns>
            <Column field="name" :header="t('crud.fields.name')"></Column>
            <Column field="rate_type" :header="t('crud.fields.rate_type')">
                <template #body="{ data }">
                    <Tag :value="data.rate_type === 'percent' ? t('crud.fields.percent') : t('crud.fields.flat')"
                        :severity="data.rate_type === 'percent' ? 'info' : 'secondary'" />
                </template>
            </Column>
            <Column field="rate_value" :header="t('crud.fields.rate_value')" bodyClass="tabular-nums">
                <template #body="{ data }">
                    {{ data.rate_type === 'percent' ? `${Number(data.rate_value)}%` : money(data.rate_value) }}
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
import { date, money } from '@/Helpers/format';

const { t } = useI18n();

const form = useForm({
    name: '',
    rate_type: 'percent',
    rate_value: null,
});
</script>
