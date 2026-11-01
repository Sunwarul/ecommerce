<template>
    <CrudComponent :form="form" icon="pi pi-credit-card">
        <template #columns>
            <Column field="photo" :header="t('crud.fields.photo')" style="width: 4.5rem">
                <template #body="{ data }">
                    <img v-if="data.photo" :src="$resolveImagePath(data.photo)" alt=""
                        class="w-10 h-10 rounded-lg object-contain border border-slate-200 bg-white" />
                    <span v-else class="w-10 h-10 rounded-lg bg-slate-100 text-slate-400 flex items-center justify-center">
                        <i class="pi pi-wallet" />
                    </span>
                </template>
            </Column>
            <Column field="name" :header="t('crud.fields.name')" bodyClass="font-medium text-slate-800"></Column>
            <Column field="is_active" :header="t('crud.fields.status')">
                <template #body="{ data }">
                    <Tag :value="data.is_active ? t('crud.fields.active') : t('crud.fields.inactive')"
                        :severity="data.is_active ? 'success' : 'secondary'" />
                </template>
            </Column>
        </template>

        <template #form="{ handlePhotoUpload, photoPreview, resolveImagePath }">
            <Form :form="form" :handlePhotoUpload="handlePhotoUpload" :photoPreview="photoPreview" :resolveImagePath="resolveImagePath" />
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

const form = useForm({
    name: '',
    is_active: true,
    photo: null,
});
</script>
