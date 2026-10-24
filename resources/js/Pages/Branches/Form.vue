<template>
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div class="flex flex-col gap-1.5">
            <label for="name" class="text-sm font-medium text-slate-700">{{ t('crud.fields.name') }} <span class="text-red-500">*</span></label>
            <InputText id="name" v-model.trim="form.name" fluid :invalid="!!form.errors.name" />
            <small v-if="form.errors.name" class="text-red-600">{{ form.errors.name }}</small>
        </div>

        <div class="flex flex-col gap-1.5">
            <label for="code" class="text-sm font-medium text-slate-700">{{ t('crud.fields.code') }} <span class="text-red-500">*</span></label>
            <InputText id="code" v-model.trim="form.code" fluid :invalid="!!form.errors.code" />
            <small v-if="form.errors.code" class="text-red-600">{{ form.errors.code }}</small>
        </div>

        <div class="flex flex-col gap-1.5">
            <label for="phone" class="text-sm font-medium text-slate-700">{{ t('crud.fields.phone') }} <span class="text-red-500">*</span></label>
            <InputText id="phone" type="tel" v-model.trim="form.phone" fluid :invalid="!!form.errors.phone" />
            <small v-if="form.errors.phone" class="text-red-600">{{ form.errors.phone }}</small>
        </div>

        <div class="flex flex-col gap-1.5 sm:col-span-2">
            <label for="address" class="text-sm font-medium text-slate-700">{{ t('crud.fields.address') }} <span class="text-red-500">*</span></label>
            <Textarea id="address" v-model="form.address" rows="2" autoResize fluid :invalid="!!form.errors.address" />
            <small v-if="form.errors.address" class="text-red-600">{{ form.errors.address }}</small>
        </div>

        <div class="flex items-center gap-3 sm:col-span-2">
            <ToggleSwitch inputId="is_active" :modelValue="!!Number(form.is_active)" @update:modelValue="(v) => (form.is_active = v ? 1 : 0)" />
            <label for="is_active" class="text-sm text-slate-700">{{ t('crud.fields.active') }}</label>
            <small v-if="form.errors.is_active" class="text-red-600">{{ form.errors.is_active }}</small>
        </div>
    </div>
</template>

<script setup>
import { useI18n } from 'vue-i18n';
import InputText from 'primevue/inputtext';
import Textarea from 'primevue/textarea';
import ToggleSwitch from 'primevue/toggleswitch';

defineProps({
    form: { type: Object, required: true },
});

const { t } = useI18n();
</script>
