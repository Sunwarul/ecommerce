<template>
    <CrudComponent :form="form" icon="pi pi-check-square">
        <template #columns>
            <Column field="title" :header="t('crud.fields.title')">
                <template #body="{ data }">
                    <span :class="isOn(data.is_completed) ? 'line-through text-slate-400' : 'font-medium text-slate-800'">{{ data.title }}</span>
                </template>
            </Column>
            <Column field="is_completed" :header="t('crud.fields.status')">
                <template #body="{ data }">
                    <Tag :value="isOn(data.is_completed) ? t('crud.fields.done') : t('crud.fields.open')"
                        :severity="isOn(data.is_completed) ? 'success' : 'warn'" />
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
    title: '',
    is_completed: 0,
});
</script>
