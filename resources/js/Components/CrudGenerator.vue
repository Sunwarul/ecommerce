<script setup>
import { ref, onMounted } from 'vue';
import { useI18n } from 'vue-i18n';
import { useCrud } from '@/Composables/useCrud';
import { usePage } from '@inertiajs/vue3';
import { resolveImagePath } from '@/Helpers/imageHelper';

const props = defineProps(['config'])
const { t } = useI18n();

const page = usePage();
// const config = ref(page.props.config);
const fields = ref(props.config.fields);
const { items, form, selectedItems, itemDialog, deleteItemDialog, deleteItemsDialog, submitted, isEdit, filters, openNew, hideDialog, saveItem, editItem, deleteItem, confirmDeleteItem, exportExcel, confirmDeleteSelected, deleteSelectedItems, fetchItems, handlePagination, debouncedSearch } = useCrud(props.config);
const loading = ref(false);
const photoPreview = ref(null);

onMounted(() => {
    fetchItems();
});

const handleFileUpload = (event, fieldName) => {
    const file = event.files[0];
    form.value[fieldName] = file;

    if (file.type.startsWith('image/')) {
        const reader = new FileReader();
        reader.onload = (e) => {
            photoPreview.value = e.target.result;
        };
        reader.readAsDataURL(file);
    }
};
</script>

<template>
    <div>
        <div v-if="loading">{{ t('common.loading') }}</div>
        <div v-else class="card">
            <Toolbar>
                <template #start>
                    <Button :label="t('crud.new')" icon="pi pi-plus" class="me-2" @click="openNew" />
                    <Button :label="t('crud.delete')" icon="pi pi-trash" severity="danger" outlined @click="confirmDeleteSelected"
                        :disabled="!selectedItems.length" />
                </template>
                <template #end>
                    <Button :label="t('crud.export')" icon="pi pi-download" severity="secondary" @click="exportExcel" />
                </template>
            </Toolbar>

            <DataTable :value="items.data" v-model:selection="selectedItems" ref="dt" dataKey="id" :paginator="true"
                :rows="15" :filters="filters" :totalRecords="items.total" :lazy="true"
                @page="handlePagination($event, props.config.endpoints.list, props.config.entity)"
                paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
                :rowsPerPageOptions="[5, 10, 25]"
                :currentPageReportTemplate="t('crud.page_report', { first: '{first}', last: '{last}', total: '{totalRecords}' })">
                <template #header>
                    <div class="flex flex-wrap gap-2 items-center justify-between">
                        <h1 class="text-xl font-semibold text-slate-800">{{ props.config.title }}</h1>
                        <IconField>
                            <InputIcon>
                                <i class="pi pi-search" />
                            </InputIcon>
                            <InputText v-model="filters.global.value" type="search" @input="debouncedSearch"
                                :placeholder="t('crud.search_placeholder')" />
                        </IconField>
                    </div>
                </template>

                <Column selectionMode="multiple" style="width: 3rem"></Column>
                <Column v-for="field in fields" :key="field.name" :field="field.name" :header="field.label" />
                <Column :header="t('crud.actions')" :exportable="false" style="min-width: 8rem">
                    <template #body="slotProps">
                        <Button icon="pi pi-pencil" outlined rounded class="me-2" @click="editItem(slotProps.data)" />
                        <Button icon="pi pi-trash" outlined rounded severity="danger"
                            @click="confirmDeleteItem(slotProps.data)" />
                    </template>
                </Column>
            </DataTable>

            <!-- Form Modal -->
            <Dialog v-model:visible="itemDialog" :header="isEdit ? t('crud.edit') : t('crud.new')" maximizable
                :style="{ width: '600px' }" :breakpoints="{ '640px': '96vw' }" pt:mask:class="backdrop-blur-sm">
                <form @submit.prevent="saveItem">
                    <div v-for="field in fields" :key="field.name" class="field">
                        <div class="flex flex-col gap-6 mb-3">
                            <div>
                                <label :for="field.name" class="block font-bold mb-2">{{ field.label }}</label>
                                <template v-if="field.type === 'text'">
                                    <InputText :id="field.name" v-model="form[field.name]" :required="field.required"
                                        class="w-full" />
                                </template>
                                <template v-else-if="field.type === 'textarea'">
                                    <Textarea :id="field.name" v-model="form[field.name]" :required="field.required"
                                        rows="4" class="w-full" />
                                </template>
                                <template v-else-if="field.type === 'number'">
                                    <InputNumber :id="field.name" v-model="form[field.name]" :required="field.required"
                                        class="w-full" />
                                </template>
                                <template v-else-if="field.type === 'select'">
                                    <Select :id="field.name" v-model="form[field.name]" :options="field.options"
                                        optionLabel="label" optionValue="value" :required="field.required"
                                        class="w-full" />
                                </template>
                                <template v-else-if="field.type === 'file'">
                                    <FileUpload mode="basic" :name="field.name" customUpload
                                        @select="(e) => handleFileUpload(e, field.name)" :auto="true"
                                        :accept="field.accept || '*'" :chooseLabel="field.chooseLabel || t('crud.fields.choose_file')"
                                        class="w-full" />
                                    <div v-if="field.isImage && (form[field.name] || photoPreview)" class="mt-2">
                                        <img :src="photoPreview || resolveImagePath(form[field.name])" alt="Preview"
                                            class="shadow-md rounded-xl w-full" />
                                    </div>
                                </template>
                                <template v-else-if="field.type === 'boolean'">
                                    <Checkbox :id="field.name" v-model="form[field.name]" :binary="true" />
                                </template>
                                <small v-if="submitted && field.required && !form[field.name]" class="text-red-500">
                                    {{ t('validation.required', { attribute: field.label }).replace(':attribute', field.label) }}
                                </small>
                            </div>
                        </div>
                    </div>
                    <div class="flex justify-end gap-2 mt-4">
                        <Button :label="t('crud.cancel')" icon="pi pi-times" text @click="hideDialog" />
                        <Button v-if="!isEdit" :label="t('crud.save_continue')" text icon="pi pi-check"
                            @click="saveItem(true)" />
                        <Button :label="isEdit ? t('crud.update') : t('crud.save')" icon="pi pi-check" @click="saveItem(false)" />
                    </div>
                </form>
            </Dialog>

            <!-- Delete Confirmation Dialog -->
            <Dialog v-model:visible="deleteItemDialog" :header="t('crud.confirm_title')" :style="{ width: '450px' }" :breakpoints="{ '640px': '92vw' }">
                <div class="flex items-center justify-center gap-3">
                    <i class="pi pi-exclamation-triangle" style="font-size: 2rem" />
                    <span>{{ t('crud.confirm_delete_hard') }}</span>
                </div>
                <template #footer>
                    <Button :label="t('crud.no')" icon="pi pi-times" text @click="deleteItemDialog = false" />
                    <Button :label="t('crud.yes')" icon="pi pi-check" text severity="danger" @click="deleteItem" />
                </template>
            </Dialog>

            <!-- Bulk Delete Confirmation Dialog -->
            <Dialog v-model:visible="deleteItemsDialog" :header="t('crud.confirm_title')" :style="{ width: '450px' }" :breakpoints="{ '640px': '92vw' }">
                <div class="flex items-center justify-center gap-3">
                    <i class="pi pi-exclamation-triangle" style="font-size: 2rem" />
                    <span>{{ t('crud.confirm_delete_many', { count: selectedItems.length }) }}</span>
                </div>
                <template #footer>
                    <Button :label="t('crud.no')" icon="pi pi-times" text @click="deleteItemsDialog = false" />
                    <Button :label="t('crud.yes')" icon="pi pi-check" text severity="danger" @click="deleteSelectedItems" />
                </template>
            </Dialog>
        </div>
    </div>
</template>
