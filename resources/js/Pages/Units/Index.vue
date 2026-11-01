<template>
    <CrudComponent :form="form" icon="pi pi-clone">
        <template #columns>
            <Column field="name" :header="t('crud.fields.name')"></Column>
            <Column field="short_code" :header="t('crud.fields.short_code')"></Column>
            <Column field="base_unit_id" :header="t('crud.fields.base_unit')">
                <template #body="{ data }">{{ baseUnitName(data.base_unit_id) }}</template>
            </Column>
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
            <Form :form="form" :baseUnits="baseUnits" />
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

const { baseUnits } = defineProps({
    baseUnits: { type: Array, default: () => [] },
});

const { t } = useI18n();
const isOn = (v) => !!Number(v);
const baseUnitName = (id) => baseUnits.find((b) => b.id === id)?.name ?? '-';

const form = useForm({
    name: '',
    short_code: '',
    status: 1,
    base_unit_id: null,
});
</script>
