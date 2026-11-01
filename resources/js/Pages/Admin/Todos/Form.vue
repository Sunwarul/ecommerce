<template>
    <div class="grid grid-cols-1 gap-4">
        <div class="flex flex-col gap-1.5">
            <label for="title" class="text-sm font-medium text-slate-700">{{ t('crud.fields.title') }} <span class="text-red-500">*</span></label>
            <InputText id="title" v-model.trim="form.title" fluid autofocus :invalid="!!form.errors.title" />
            <small v-if="form.errors.title" class="text-red-600">{{ form.errors.title }}</small>
        </div>

        <div class="flex items-center gap-3">
            <ToggleSwitch inputId="is_completed" :modelValue="!!Number(form.is_completed)" @update:modelValue="(v) => (form.is_completed = v ? 1 : 0)" />
            <label for="is_completed" class="text-sm text-slate-700">{{ t('crud.fields.is_completed') }}</label>
        </div>
    </div>
</template>

<script setup>
import { useI18n } from 'vue-i18n';
import InputText from 'primevue/inputtext';
import ToggleSwitch from 'primevue/toggleswitch';

defineProps({
    form: { type: Object, required: true },
});

const { t } = useI18n();
</script>
