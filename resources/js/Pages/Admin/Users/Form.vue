<template>
    <div class="space-y-6">
        <section>
            <h3 class="text-sm font-semibold text-slate-800 mb-3 flex items-center gap-2"><i class="pi pi-id-card text-slate-400" />{{ t('admin.users.account') }}</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="flex flex-col gap-1.5 sm:col-span-2">
                    <label for="name" class="text-sm font-medium text-slate-700">{{ t('admin.users.name') }} <span class="text-red-500">*</span></label>
                    <InputText id="name" v-model.trim="form.name" autocomplete="off" fluid autofocus :invalid="!!form.errors.name" />
                    <small v-if="form.errors.name" class="text-red-600">{{ form.errors.name }}</small>
                </div>
                <div class="flex flex-col gap-1.5">
                    <label for="email" class="text-sm font-medium text-slate-700">{{ t('admin.users.email') }} <span class="text-red-500">*</span></label>
                    <InputText id="email" type="email" v-model.trim="form.email" autocomplete="off" fluid :invalid="!!form.errors.email" />
                    <small v-if="form.errors.email" class="text-red-600">{{ form.errors.email }}</small>
                </div>
                <div class="flex flex-col gap-1.5">
                    <label for="phone" class="text-sm font-medium text-slate-700">{{ t('admin.users.phone') }}</label>
                    <InputText id="phone" type="tel" v-model.trim="form.phone" fluid :invalid="!!form.errors.phone" />
                    <small v-if="form.errors.phone" class="text-red-600">{{ form.errors.phone }}</small>
                </div>

                <Message v-if="isEdit" severity="secondary" :closable="false" size="small" icon="pi pi-lock" class="sm:col-span-2">
                    {{ t('admin.users.password_edit_note') }}
                </Message>
                <template>
                    <div class="flex flex-col gap-1.5">
                        <label for="password" class="text-sm font-medium text-slate-700">{{ t('admin.users.password') }} <span v-if="!isEdit" class="text-red-500">*</span></label>
                        <Password inputId="password" v-model="form.password" toggleMask :feedback="false" autocomplete="new-password"
                            fluid :invalid="!!form.errors.password" />
                        <small v-if="form.errors.password" class="text-red-600">{{ form.errors.password }}</small>
                    </div>
                    <div class="flex flex-col gap-1.5">
                        <label for="password_confirmation" class="text-sm font-medium text-slate-700">{{ t('admin.users.password_confirmation') }} <span v-if="!isEdit" class="text-red-500">*</span></label>
                        <Password inputId="password_confirmation" v-model="form.password_confirmation" toggleMask :feedback="false"
                            autocomplete="new-password" fluid :invalid="!!form.errors.password_confirmation" />
                        <small v-if="form.errors.password_confirmation" class="text-red-600">{{ form.errors.password_confirmation }}</small>
                    </div>
                </template>
            </div>
        </section>

        <section>
            <h3 class="text-sm font-semibold text-slate-800 mb-3 flex items-center gap-2"><i class="pi pi-shield text-slate-400" />{{ t('admin.users.access') }}</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="flex flex-col gap-1.5">
                    <label for="roles" class="text-sm font-medium text-slate-700">{{ t('admin.users.roles') }}</label>
                    <MultiSelect inputId="roles" v-model="form.roles" :options="roles" optionLabel="name" optionValue="id"
                        :placeholder="t('admin.users.select_roles')" display="chip" filter fluid :invalid="!!form.errors.roles" />
                    <small v-if="form.errors.roles" class="text-red-600">{{ form.errors.roles }}</small>
                </div>
                <div class="flex flex-col gap-1.5">
                    <label for="branch" class="text-sm font-medium text-slate-700">{{ t('admin.users.branch') }}</label>
                    <Select inputId="branch" v-model="form.branch_id" :options="branches" optionLabel="name" optionValue="id"
                        :placeholder="t('admin.users.select_branch')" showClear fluid :invalid="!!form.errors.branch_id" />
                    <small v-if="form.errors.branch_id" class="text-red-600">{{ form.errors.branch_id }}</small>
                </div>
            </div>
        </section>

        <section>
            <h3 class="text-sm font-semibold text-slate-800 mb-3 flex items-center gap-2"><i class="pi pi-image text-slate-400" />{{ t('admin.users.avatar') }}</h3>
            <div class="flex flex-wrap items-center gap-4">
                <img v-if="photoPreview || (typeof form.photo === 'string' && form.photo)"
                    :src="photoPreview ?? resolveImagePath?.(form.photo)" alt=""
                    class="w-20 h-20 rounded-full object-cover border border-slate-200" />
                <span v-else class="w-20 h-20 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center">
                    <i class="pi pi-user text-2xl" />
                </span>
                <FileUpload mode="basic" name="photo" customUpload :auto="true" accept="image/*"
                    :chooseLabel="t('crud.fields.choose_image')" @select="handlePhotoUpload" />
            </div>
            <small v-if="form.errors.photo" class="text-red-600">{{ form.errors.photo }}</small>
        </section>
    </div>
</template>

<script setup>
import { useI18n } from 'vue-i18n';
import FileUpload from 'primevue/fileupload';
import InputText from 'primevue/inputtext';
import Message from 'primevue/message';
import MultiSelect from 'primevue/multiselect';
import Password from 'primevue/password';
import Select from 'primevue/select';

defineProps({
    form: { type: Object, required: true },
    submitted: Boolean,
    isEdit: Boolean,
    photoPreview: String,
    resolveImagePath: Function,
    handlePhotoUpload: { type: Function, default: () => {} },
    roles: { type: Array, required: true },
    branches: { type: Array, required: true },
});

const { t } = useI18n();
</script>
