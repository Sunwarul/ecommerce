<template>
    <div>
        <CrudComponent :form>
            <template #columns>
                <Column field="photo" :header="t('common.image')">
                    <template #body="{ data }">
                        <img
                            :src="$resolveImagePath(data.photo)"
                            :alt="t('categories.category')"
                            class="shadow-md rounded-xl w-16 h-16"
                            style="filter: grayscale(100%)"
                            @click="() => console.log('data', data)"
                        />
                    </template>
                </Column>
                <Column field="name" :header="t('common.name')"></Column>
                <Column field="parent" :header="t('categories.parent_category')">
                    <template #body="{ data }">
                        {{ data.parent ? data.parent.name : "N/A" }}
                    </template>
                </Column>
                <Column field="slug" :header="t('categories.slug')"></Column>
                <Column field="is_active" :header="t('common.status')">
                    <template #body="{ data }">
                        <Badge
                            :severity="data.is_active ? 'success' : 'danger'"
                        >
                            {{ data.is_active ? t('common.active') : t('common.inactive') }}
                        </Badge>
                    </template>
                </Column>

                <Column
                    field="created_at"
                    :header="t('common.date')"
                    sortable
                ></Column>
            </template>
            <template
                #form="{
                    submitted,
                    statuses,
                    handlePhotoUpload,
                    photoPreview,
                    resolveImagePath,
                }"
            >
                <div class="flex flex-col gap-6">
                    <div>
                        <label for="name" class="block font-bold mb-2"
                            >{{ t('common.name') }}</label
                        >
                        <InputText
                            id="name"
                            v-model.trim="form.name"
                            required="true"
                            autofocus
                            :invalid="submitted && !form.name"
                            fluid
                        />
                        <small
                            v-if="submitted && !form.name"
                            class="text-red-500"
                            >{{ t('validation.required') }}</small
                        >
                    </div>
                </div>
                <div class="flex flex-col gap-6 mt-3 mb-4">
                    <div>
                        <label for="parent_id" class="block font-bold mb-2"
                            >{{ t('categories.parent_category') }}</label
                        >
                        <Select
                            v-model="form.parent_id"
                            :options="parentCategories"
                            optionLabel="name"
                            optionValue="id"
                            :placeholder="t('common.select_option')"
                            class="w-full"
                        />
                    </div>
                </div>
                <div class="flex flex-col gap-6 mt-3">
                    <div>
                        <label for="photo" class="block font-bold mb-2"
                            >{{ t('common.image') }}</label
                        >
                        <FileUpload
                            mode="basic"
                            name="photo"
                            customUpload
                            @select="handlePhotoUpload"
                            :auto="true"
                            accept="image/*"
                            :chooseLabel="t('common.select')"
                            class="w-full"
                        />
                    </div>
                    <div>
                        <img
                            v-if="form.photo || photoPreview"
                            :src="photoPreview ?? resolveImagePath(form.photo)"
                            :alt="t('common.image')"
                            class="shadow-md rounded-xl w-[200px]"
                            style="filter: grayscale(100%)"
                        />
                    </div>
                </div>
                <div class="flex flex-col gap-6 mt-3">
                    <div>
                        <label for="is_active" class="block font-bold mb-2"
                            >{{ t('common.status') }}</label
                        >
                        <Select
                            v-model="form.is_active"
                            :options="statuses"
                            optionLabel="label"
                            optionValue="value"
                            :placeholder="t('common.select_option')"
                            class="w-full"
                            :required="true"
                        />
                        <small
                            v-if="submitted && form.is_active == null"
                            class="text-red-500"
                        >
                            {{ t('validation.required') }}
                        </small>
                    </div>
                </div>
            </template>
        </CrudComponent>
    </div>
</template>
<script setup>
import CrudComponent from "@/Components/CrudComponent.vue";
import { useForm } from "@inertiajs/vue3";
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

const form = useForm({
    parent_id: null,
    name: "",
    photo: null,
    is_active: 1,
});
const { categories, parentCategories } = defineProps({
    categories: Object,
    parentCategories: Object,
});

</script>
