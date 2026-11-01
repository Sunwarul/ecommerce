<template>
    <div class="grid grid-cols-1 gap-4">
        <div class="flex flex-col gap-1.5">
            <label for="name" class="text-sm font-medium text-slate-700">{{ t('crud.fields.name') }} <span class="text-red-500">*</span></label>
            <InputText id="name" v-model.trim="form.name" fluid autofocus :invalid="!!form.errors.name" />
            <small v-if="form.errors.name" class="text-red-600">{{ form.errors.name }}</small>
        </div>

        <div class="flex items-center gap-3">
            <ToggleSwitch inputId="is_active" :modelValue="!!Number(form.is_active)" @update:modelValue="(v) => (form.is_active = v ? 1 : 0)" />
            <label for="is_active" class="text-sm text-slate-700">{{ t('crud.fields.active') }}</label>
            <small v-if="form.errors.is_active" class="text-red-600">{{ form.errors.is_active }}</small>
        </div>

        <div class="flex flex-col gap-1.5">
            <label class="text-sm font-medium text-slate-700">{{ t('crud.fields.photo') }}</label>
            <div class="flex flex-wrap items-center gap-4">
                <img v-if="photoPreview || (typeof form.photo === 'string' && form.photo)"
                    :src="photoPreview ?? resolveImagePath?.(form.photo)" alt=""
                    class="w-20 h-20 rounded-xl object-contain border border-slate-200 bg-white" />
                <FileUpload mode="basic" name="photo" customUpload :auto="true" accept="image/*"
                    :chooseLabel="t('crud.fields.choose_image')" @select="handlePhotoUpload" />
            </div>
            <small v-if="form.errors.photo" class="text-red-600">{{ form.errors.photo }}</small>
        </div>
    </div>
</template>

<script setup>
import { useI18n } from 'vue-i18n';
import FileUpload from 'primevue/fileupload';
import InputText from 'primevue/inputtext';
import ToggleSwitch from 'primevue/toggleswitch';

defineProps({
    form: { type: Object, required: true },
    handlePhotoUpload: { type: Function, default: () => {} },
    photoPreview: { type: String, default: null },
    resolveImagePath: { type: Function, default: null },
});

const { t } = useI18n();
</script>
