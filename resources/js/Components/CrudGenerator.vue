<script setup>
import { ref, onMounted, defineProps } from 'vue';
import { useCrud } from '@/Composables/useCrud';
import { usePage } from '@inertiajs/vue3';
import { resolveImagePath } from '@/Helpers/imageHelper';

const props = defineProps(['config'])

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
        <div v-if="loading">Loading...</div>
        <div v-else class="card">
            <Toolbar>
                <template #start>
                    <Button label="New" icon="pi pi-plus" class="mr-2" @click="openNew" />
                    <Button label="Delete" icon="pi pi-trash" severity="danger" outlined @click="confirmDeleteSelected"
                        :disabled="!selectedItems.length" />
                </template>
                <template #end>
                    <Button label="Export" icon="pi pi-upload" severity="secondary" @click="exportExcel" />
                </template>
            </Toolbar>

            <DataTable :value="items" v-model:selection="selectedItems" ref="dt" dataKey="id" :paginator="true"
                :rows="15" :filters="filters" :totalRecords="100" :lazy="true"
                @page="handlePagination($event, props.config.endpoints.list, props.config.entity)"
                paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
                :rowsPerPageOptions="[5, 10, 25]"
                :currentPageReportTemplate="`Showing {first} to {last} of {totalRecords} ${props.config.entity}`">
                <template #header>
                    <div class="flex flex-wrap gap-2 items-center justify-between">
                        <h1 class="text-3xl">{{ props.config.title || 'Manage Items' }}</h1>
                        <IconField>
                            <InputIcon>
                                <i class="pi pi-search" />
                            </InputIcon>
                            <InputText v-model="filters.global.value" type="search" @input="debouncedSearch"
                                placeholder="Search..." clearable />
                        </IconField>
                    </div>
                </template>

                <Column selectionMode="multiple" style="width: 3rem"></Column>
                <Column v-for="field in fields" :key="field.name" :field="field.name" :header="field.label" />
                <Column :exportable="false" style="min-width: 12rem">
                    <template #body="slotProps">
                        <Button icon="pi pi-pencil" outlined rounded class="mr-2" @click="editItem(slotProps.data)" />
                        <Button icon="pi pi-trash" outlined rounded severity="danger"
                            @click="confirmDeleteItem(slotProps.data)" />
                    </template>
                </Column>
            </DataTable>

            <!-- Form Modal -->
            <Dialog v-model:visible="itemDialog" :header="isEdit ? 'Edit Item' : 'New Item'" maximizable
                :style="{ width: '600px' }" pt:mask:class="backdrop-blur-sm">
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
                                    <Dropdown :id="field.name" v-model="form[field.name]" :options="field.options"
                                        optionLabel="label" optionValue="value" :required="field.required"
                                        class="w-full" />
                                </template>
                                <template v-else-if="field.type === 'file'">
                                    <FileUpload mode="basic" :name="field.name" customUpload
                                        @select="(e) => handleFileUpload(e, field.name)" :auto="true"
                                        :accept="field.accept || '*'" :chooseLabel="field.chooseLabel || 'Choose File'"
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
                                    {{ field.label }} is required.
                                </small>
                            </div>
                        </div>
                    </div>
                    <div class="flex justify-end gap-2 mt-4">
                        <Button label="Cancel" icon="pi pi-times" text @click="hideDialog" />
                        <Button v-if="!isEdit" label="Save & Continue" text icon="pi pi-check"
                            @click="saveItem(true)" />
                        <Button :label="isEdit ? 'Update' : 'Save'" icon="pi pi-check" @click="saveItem(false)" />
                    </div>
                </form>
            </Dialog>

            <!-- Delete Confirmation Dialog -->
            <Dialog v-model:visible="deleteItemDialog" header="Confirm Delete" :style="{ width: '450px' }">
                <div class="flex align-items-center justify-content-center">
                    <i class="pi pi-exclamation-triangle mr-3" style="font-size: 2rem" />
                    <span>Are you sure you want to delete this item?</span>
                </div>
                <template #footer>
                    <Button label="No" icon="pi pi-times" text @click="deleteItemDialog = false" />
                    <Button label="Yes" icon="pi pi-check" text severity="danger" @click="deleteItem" />
                </template>
            </Dialog>

            <!-- Bulk Delete Confirmation Dialog -->
            <Dialog v-model:visible="deleteItemsDialog" header="Confirm Delete" :style="{ width: '450px' }">
                <div class="flex align-items-center justify-content-center">
                    <i class="pi pi-exclamation-triangle mr-3" style="font-size: 2rem" />
                    <span>Are you sure you want to delete the selected items?</span>
                </div>
                <template #footer>
                    <Button label="No" icon="pi pi-times" text @click="deleteItemsDialog = false" />
                    <Button label="Yes" icon="pi pi-check" text severity="danger" @click="deleteSelectedItems" />
                </template>
            </Dialog>
        </div>
    </div>
</template>
