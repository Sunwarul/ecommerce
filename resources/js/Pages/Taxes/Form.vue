<template>
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div class="flex flex-col gap-1.5 sm:col-span-2">
            <label for="name" class="text-sm font-medium text-slate-700">{{ t('crud.fields.name') }} <span class="text-red-500">*</span></label>
            <InputText id="name" v-model.trim="form.name" fluid autofocus :invalid="!!form.errors.name" />
            <small v-if="form.errors.name" class="text-red-600">{{ form.errors.name }}</small>
        </div>

        <div class="flex flex-col gap-1.5">
            <label for="rate_type" class="text-sm font-medium text-slate-700">{{ t('crud.fields.rate_type') }} <span class="text-red-500">*</span></label>
            <Select inputId="rate_type" v-model="form.rate_type" :options="rateTypes" optionLabel="label" optionValue="value"
                fluid :invalid="!!form.errors.rate_type" />
            <small v-if="form.errors.rate_type" class="text-red-600">{{ form.errors.rate_type }}</small>
        </div>

        <div class="flex flex-col gap-1.5">
            <label for="rate_value" class="text-sm font-medium text-slate-700">{{ t('crud.fields.rate_value') }} <span class="text-red-500">*</span></label>
            <InputNumber inputId="rate_value" v-model="form.rate_value" :minFractionDigits="0" :maxFractionDigits="2"
                :suffix="form.rate_type === 'percent' ? ' %' : ''" fluid :invalid="!!form.errors.rate_value" />
            <small v-if="form.errors.rate_value" class="text-red-600">{{ form.errors.rate_value }}</small>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';
import { useI18n } from 'vue-i18n';
import InputNumber from 'primevue/inputnumber';
import InputText from 'primevue/inputtext';
import Select from 'primevue/select';

defineProps({
    form: { type: Object, required: true },
});

const { t } = useI18n();
const rateTypes = computed(() => [
    { label: t('crud.fields.percent'), value: 'percent' },
    { label: t('crud.fields.flat'), value: 'flat' },
]);
</script>
