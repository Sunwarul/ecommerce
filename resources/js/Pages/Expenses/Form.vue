<template>
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div class="flex flex-col gap-1.5 sm:col-span-2">
            <label for="title" class="text-sm font-medium text-slate-700">{{ t('crud.fields.title') }} <span class="text-red-500">*</span></label>
            <InputText id="title" v-model.trim="form.title" fluid autofocus :invalid="!!form.errors.title" />
            <small v-if="form.errors.title" class="text-red-600">{{ form.errors.title }}</small>
        </div>

        <div class="flex flex-col gap-1.5">
            <label for="date" class="text-sm font-medium text-slate-700">{{ t('crud.fields.date') }} <span class="text-red-500">*</span></label>
            <DatePicker inputId="date" v-model="dateModel" dateFormat="dd-mm-yy" showIcon fluid :invalid="!!form.errors.date" />
            <small v-if="form.errors.date" class="text-red-600">{{ form.errors.date }}</small>
        </div>

        <div class="flex flex-col gap-1.5">
            <label for="amount" class="text-sm font-medium text-slate-700">{{ t('crud.fields.amount') }} <span class="text-red-500">*</span></label>
            <InputNumber inputId="amount" v-model="form.amount" :min="0" :minFractionDigits="0" :maxFractionDigits="2"
                fluid :invalid="!!form.errors.amount" />
            <small v-if="form.errors.amount" class="text-red-600">{{ form.errors.amount }}</small>
        </div>

        <div class="flex flex-col gap-1.5">
            <label for="expense_category_id" class="text-sm font-medium text-slate-700">{{ t('crud.fields.expense_category') }} <span class="text-red-500">*</span></label>
            <Select inputId="expense_category_id" v-model="form.expense_category_id" :options="expenseCategories" optionLabel="name"
                optionValue="id" filter :placeholder="t('crud.fields.select_category')" fluid :invalid="!!form.errors.expense_category_id" />
            <small v-if="form.errors.expense_category_id" class="text-red-600">{{ form.errors.expense_category_id }}</small>
        </div>

        <div class="flex flex-col gap-1.5">
            <label for="warehouse_id" class="text-sm font-medium text-slate-700">{{ t('crud.fields.warehouse') }} <span class="text-red-500">*</span></label>
            <Select inputId="warehouse_id" v-model="form.warehouse_id" :options="warehouses" optionLabel="name" optionValue="id"
                filter :placeholder="t('crud.fields.select_warehouse')" fluid :invalid="!!form.errors.warehouse_id" />
            <small v-if="form.errors.warehouse_id" class="text-red-600">{{ form.errors.warehouse_id }}</small>
        </div>

        <div class="flex flex-col gap-1.5 sm:col-span-2">
            <label for="user_id" class="text-sm font-medium text-slate-700">{{ t('crud.fields.spent_by') }} <span class="text-red-500">*</span></label>
            <Select inputId="user_id" v-model="form.user_id" :options="users" optionLabel="name" optionValue="id"
                filter :placeholder="t('crud.fields.select_user')" fluid :invalid="!!form.errors.user_id" />
            <small v-if="form.errors.user_id" class="text-red-600">{{ form.errors.user_id }}</small>
        </div>

        <div class="flex flex-col gap-1.5 sm:col-span-2">
            <label for="details" class="text-sm font-medium text-slate-700">{{ t('crud.fields.details') }}</label>
            <Textarea id="details" v-model="form.details" rows="3" autoResize fluid :invalid="!!form.errors.details" />
            <small v-if="form.errors.details" class="text-red-600">{{ form.errors.details }}</small>
        </div>

        <div class="flex flex-col gap-1.5 sm:col-span-2">
            <label class="text-sm font-medium text-slate-700">{{ t('crud.fields.attachment') }}</label>
            <div class="flex flex-wrap items-center gap-3">
                <FileUpload mode="basic" name="photo" customUpload :auto="true" accept="image/*,application/pdf"
                    :chooseLabel="t('crud.fields.choose_file')" @select="handlePhotoUpload" />
                <span v-if="selectedName" class="text-sm text-slate-600">{{ t('crud.fields.selected_file', { name: selectedName }) }}</span>
                <a v-else-if="typeof form.photo === 'string' && form.photo" :href="resolveImagePath?.(form.photo)" target="_blank"
                    class="text-sm text-emerald-700 hover:underline">
                    <i class="pi pi-paperclip !text-xs" /> {{ t('crud.fields.view_file') }}
                </a>
            </div>
            <small class="text-slate-500">{{ t('admin.expenses.receipt_hint') }}</small>
            <img v-if="photoPreview && photoPreview.startsWith('data:image')" :src="photoPreview" alt=""
                class="rounded-lg border border-slate-200 max-h-40 w-auto object-contain mt-1" />
            <small v-if="form.errors.photo" class="text-red-600">{{ form.errors.photo }}</small>
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
import FileUpload from 'primevue/fileupload';
import InputNumber from 'primevue/inputnumber';
import InputText from 'primevue/inputtext';
import Select from 'primevue/select';
import Textarea from 'primevue/textarea';
import ToggleSwitch from 'primevue/toggleswitch';
import { ymd } from '@/Helpers/format';

const props = defineProps({
    form: { type: Object, required: true },
    users: { type: Array, default: () => [] },
    warehouses: { type: Array, default: () => [] },
    expenseCategories: { type: Array, default: () => [] },
    handlePhotoUpload: { type: Function, default: () => {} },
    photoPreview: { type: String, default: null },
    resolveImagePath: { type: Function, default: null },
});

const { t } = useI18n();

// keep the form value as a local "YYYY-MM-DD" string (a Date would be posted as a UTC timestamp)
const dateModel = computed({
    get: () => (props.form.date ? new Date(`${String(props.form.date).slice(0, 10)}T00:00:00`) : null),
    set: (v) => (props.form.date = v ? ymd(v) : ''),
});

const selectedName = computed(() => (props.form.photo instanceof File ? props.form.photo.name : ''));
</script>
