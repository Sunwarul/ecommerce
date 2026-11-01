<template>
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div class="flex flex-col gap-1.5">
            <label for="name" class="text-sm font-medium text-slate-700">{{ t('crud.fields.name') }} <span class="text-red-500">*</span></label>
            <InputText id="name" v-model.trim="form.name" fluid autofocus :invalid="!!form.errors.name" />
            <small v-if="form.errors.name" class="text-red-600">{{ form.errors.name }}</small>
        </div>

        <div class="flex flex-col gap-1.5">
            <label for="short_code" class="text-sm font-medium text-slate-700">{{ t('crud.fields.short_code') }} <span class="text-red-500">*</span></label>
            <InputText id="short_code" v-model.trim="form.short_code" fluid :invalid="!!form.errors.short_code" />
            <small v-if="form.errors.short_code" class="text-red-600">{{ form.errors.short_code }}</small>
        </div>

        <div class="flex flex-col gap-1.5 sm:col-span-2">
            <label for="base_unit_id" class="text-sm font-medium text-slate-700">{{ t('crud.fields.base_unit') }} <span class="text-red-500">*</span></label>
            <Select inputId="base_unit_id" v-model="form.base_unit_id" :options="baseUnits" filter optionLabel="name"
                optionValue="id" :placeholder="t('crud.fields.select_base_unit')" fluid :invalid="!!form.errors.base_unit_id" />
            <small v-if="form.errors.base_unit_id" class="text-red-600">{{ form.errors.base_unit_id }}</small>
        </div>

        <div class="flex items-center gap-3 sm:col-span-2">
            <ToggleSwitch inputId="status" :modelValue="!!Number(form.status)" @update:modelValue="(v) => (form.status = v ? 1 : 0)" />
            <label for="status" class="text-sm text-slate-700">{{ t('crud.fields.active') }}</label>
            <small v-if="form.errors.status" class="text-red-600">{{ form.errors.status }}</small>
        </div>
    </div>
</template>

<script setup>
import { useI18n } from 'vue-i18n';
import InputText from 'primevue/inputtext';
import Select from 'primevue/select';
import ToggleSwitch from 'primevue/toggleswitch';

defineProps({
    form: { type: Object, required: true },
    baseUnits: { type: Array, default: () => [] },
});

const { t } = useI18n();
</script>
