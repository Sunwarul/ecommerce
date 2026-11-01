<template>
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div class="flex flex-col gap-1.5 sm:col-span-2">
            <label for="title" class="text-sm font-medium text-slate-700">{{ t('crud.fields.title') }} <span class="text-red-500">*</span></label>
            <InputText id="title" v-model.trim="form.title" fluid autofocus :invalid="!!form.errors.title" />
            <small v-if="form.errors.title" class="text-red-600">{{ form.errors.title }}</small>
        </div>

        <div class="flex flex-col gap-1.5">
            <label for="short_code" class="text-sm font-medium text-slate-700">{{ t('crud.fields.short_code') }}</label>
            <InputText id="short_code" v-model.trim="form.short_code" placeholder="BDT" fluid :invalid="!!form.errors.short_code" />
            <small v-if="form.errors.short_code" class="text-red-600">{{ form.errors.short_code }}</small>
        </div>

        <div class="flex flex-col gap-1.5">
            <label for="symbol" class="text-sm font-medium text-slate-700">{{ t('crud.fields.symbol') }} <span class="text-red-500">*</span></label>
            <InputText id="symbol" v-model.trim="form.symbol" placeholder="৳" fluid :invalid="!!form.errors.symbol" />
            <small v-if="form.errors.symbol" class="text-red-600">{{ form.errors.symbol }}</small>
        </div>

        <div class="flex flex-col gap-1.5">
            <label for="exchange_rate" class="text-sm font-medium text-slate-700">{{ t('crud.fields.exchange_rate') }} <span class="text-red-500">*</span></label>
            <InputNumber inputId="exchange_rate" v-model="form.exchange_rate" :minFractionDigits="0" :maxFractionDigits="6"
                fluid :invalid="!!form.errors.exchange_rate" />
            <small v-if="form.errors.exchange_rate" class="text-red-600">{{ form.errors.exchange_rate }}</small>
        </div>

        <div class="flex flex-col gap-1.5">
            <label for="date" class="text-sm font-medium text-slate-700">{{ t('crud.fields.date') }}</label>
            <DatePicker inputId="date" v-model="dateModel" dateFormat="dd-mm-yy" showIcon showButtonBar fluid :invalid="!!form.errors.date" />
            <small v-if="form.errors.date" class="text-red-600">{{ form.errors.date }}</small>
        </div>

        <div class="flex items-center gap-3 sm:col-span-2">
            <ToggleSwitch inputId="status" :modelValue="!!Number(form.status)" @update:modelValue="(v) => (form.status = v ? 1 : 0)" />
            <label for="status" class="text-sm text-slate-700">{{ t('crud.fields.active') }}</label>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';
import { useI18n } from 'vue-i18n';
import DatePicker from 'primevue/datepicker';
import InputNumber from 'primevue/inputnumber';
import InputText from 'primevue/inputtext';
import ToggleSwitch from 'primevue/toggleswitch';
import { ymd } from '@/Helpers/format';

const props = defineProps({
    form: { type: Object, required: true },
});

const { t } = useI18n();

// keep the form value as a local "YYYY-MM-DD" string (a Date would be posted as a UTC timestamp)
const dateModel = computed({
    get: () => (props.form.date ? new Date(`${String(props.form.date).slice(0, 10)}T00:00:00`) : null),
    set: (v) => (props.form.date = v ? ymd(v) : ''),
});
</script>
