<template>
    <div class="flex flex-col gap-1.5">
        <label for="name" class="text-sm font-medium text-slate-700">{{ t('admin.permissions.name') }} <span class="text-red-500">*</span></label>
        <InputText id="name" v-model.trim="form.name" placeholder="Product Export" fluid autofocus :invalid="!!form.errors.name" />
        <small v-if="form.errors.name" class="text-red-600">{{ form.errors.name }}</small>
        <small class="text-slate-500">{{ t('admin.permissions.name_hint') }}</small>
    </div>
</template>

<script setup>
import { useI18n } from 'vue-i18n';
import InputText from 'primevue/inputtext';

defineProps({
    form: { type: Object, required: true },
    submitted: Boolean,
});

const { t } = useI18n();
</script>
