<template>
    <CrudComponent :form="form" icon="pi pi-shield" formWidth="56rem" @editingItem="handleEditingItem">
        <template #columns>
            <Column field="name" :header="t('admin.roles.name')">
                <template #body="{ data }">
                    <div class="flex items-center gap-2">
                        <span class="font-medium text-slate-800">{{ data.name }}</span>
                        <i v-if="data.name === 'Super Admin'" class="pi pi-star-fill text-amber-500 !text-xs" />
                    </div>
                </template>
            </Column>
            <Column field="permissions" :header="t('admin.roles.permissions')">
                <template #body="{ data }">
                    <Tag :value="t('admin.roles.permissions_count', { count: data.permissions?.length || 0 })" severity="secondary" />
                </template>
            </Column>
            <Column field="created_at" :header="t('crud.fields.created_at')">
                <template #body="{ data }">{{ date(data.created_at) }}</template>
            </Column>
        </template>
        <template #form="{ submitted }">
            <Form :form="form" v-bind="{ submitted, permissions }" />
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

const { permissions } = defineProps({
    permissions: { type: Array, default: () => [] },
});

const { t } = useI18n();

const form = useForm({
    name: '',
    ids: [],
});

// role rows come with their permissions; the form works with plain ids
const handleEditingItem = (role) => {
    form.ids = (role.permissions || []).map((p) => p.id);
};
</script>
