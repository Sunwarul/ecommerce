<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { ref, onMounted } from 'vue';
import { useCrud } from '@/Composables/useCrud';
import { usePage } from '@inertiajs/vue3';

const page = usePage();
const config = ref(page.props.config);
const fields = ref(config.value.fields);
const { items, form, selectedItems, itemDialog, deleteItemDialog, openNew, saveItem, editItem, deleteItem, confirmDeleteItem, exportExcel, confirmDeleteSelected, fetchItems, filters, handlePagination } = useCrud(config);
const loading = ref(false);

onMounted(() => {
    fetchItems();
}),
console.log(items);
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
                @page="handlePagination($event, config.endpoints.list, config.entity)"
                paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
                :rowsPerPageOptions="[5, 10, 25]"
                currentPageReportTemplate="Showing {first} to {last} of {totalRecords} categories">
                <Column selectionMode="multiple" style="width: 3rem"></Column>
                <Column v-for="field in fields" :key="field.name" :field="field.name" :header="field.label" />
                <Column header="Actions">
                    <template #body="slotProps">
                        <Button icon="pi pi-pencil" @click="editItem(slotProps.data)" />
                        <Button icon="pi pi-trash" severity="danger" outlined
                            @click="confirmDeleteItem(slotProps.data)" />
                    </template>
                </Column>
            </DataTable>

            <!-- Form Modal -->
            <Dialog v-model:visible="itemDialog" header="Edit" maximizable :style="{ width: '600px' }"
                pt:mask:class="backdrop-blur-sm">
                <form @submit.prevent="saveItem">
                    <div v-for="field in fields" :key="field.name" class="field">
                        <div class="flex flex-col gap-6 mb-3">
                            <div>
                                <label for="{{ form[field.name] }}" class="block font-bold mb-2">{{ field.name
                                    }}</label>
                                <InputText v-if="field.type === 'text'" v-model="form[field.name]" fluid />
                                <Textarea v-if="field.type === 'textarea'" v-model="form[field.name]" rows="4" fluid />
                                <InputNumber v-if="field.type === 'number'" v-model="form[field.name]" fluid />
                                <Dropdown v-if="field.type === 'select'" v-model="form[field.name]"
                                    :options="field.options" optionLabel="label" optionValue="value" />
                                <Calendar v-if="field.type === 'date'" v-model="form[field.name]" showIcon />
                                <Checkbox v-if="field.type === 'boolean'" v-model="form[field.name]" :binary="true" />
                                <FileUpload v-if="field.type === 'file'" mode="basic" @select="handleFileUpload" />

                            </div>
                        </div>
                    </div>
                    <Button type="submit" label="Save" />
                </form>
            </Dialog>

            <!-- Delete Confirmation Dialog -->
            <Dialog v-model:visible="deleteItemDialog" header="Confirm">
                <span>Are you sure you want to delete <b>{{ form.name }}</b>?</span>
                <template #footer>
                    <Button label="No" icon="pi pi-times" @click="deleteItemDialog = false" />
                    <Button label="Yes" icon="pi pi-check" @click="deleteItem" />
                </template>
            </Dialog>
        </div>
    </div>
</template>


