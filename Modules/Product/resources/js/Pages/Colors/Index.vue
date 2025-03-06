<template>
    <AuthenticatedLayout>
        <div class="card">
            <Toolbar class="mb-6">
                <template #start>
                    <Button label="New" icon="pi pi-plus" class="mr-2" @click="openNew" />
                    <Button label="Delete" icon="pi pi-trash" severity="danger" outlined @click="confirmDeleteSelected"
                        :disabled="!selectedColors || !selectedColors.length" />
                </template>

                <template #end>
                    <!-- <FileUpload mode="basic" :maxFileSize="1000000" label="Import" customUpload chooseLabel="Import"
                        class="mr-2" auto :chooseButtonProps="{ severity: 'secondary' }" /> -->
                    <Button label="Export" icon="pi pi-upload" severity="secondary" @click="exportCSV($event)" />
                </template>
            </Toolbar>

            <DataTable ref="dt" v-model:selection="selectedColors" :value="colors.data" dataKey="id" :paginator="true"
                :rows="10" :filters="filters"
                paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
                :rowsPerPageOptions="[5, 10, 25]"
                currentPageReportTemplate="Showing {first} to {last} of {totalRecords} colors">
                <template #header>
                    <div class="flex flex-wrap gap-2 items-center justify-between">
                        <h4 class="m-0">Manage Colors</h4>
                        <IconField>
                            <InputIcon>
                                <i class="pi pi-search" />
                            </InputIcon>
                            <InputText v-model="filters['global'].value" placeholder="Search..." />
                        </IconField>
                    </div>
                </template>

                <Column selectionMode="multiple" style="width: 3rem" :exportable="false"></Column>
                <Column field="name" header="Name"></Column>
                <Column field="color_code" header="Color Code"></Column>
                <Column field="is_active" header="Status">
                    <template #body="{ data }">
                        <Badge :severity="data.is_active ? 'success' : 'danger'">
                            {{ data.is_active ? 'Active' : 'Inactive' }}
                        </Badge>
                    </template>
                </Column>
                <Column field="created_at" header="Created At" sortable></Column>
                <Column field="updated_at" header="Updated At" sortable></Column>
                <Column :exportable="false" style="min-width: 12rem">
                    <template #body="slotProps">
                        <Button icon="pi pi-pencil" outlined rounded class="mr-2" @click="editColor(slotProps.data)" />
                        <Button icon="pi pi-trash" outlined rounded severity="danger"
                            @click="confirmDeleteColor(slotProps.data)" />
                    </template>
                </Column>
            </DataTable>
        </div>

        <!-- Create & Edit Form Dialog -->
        <Dialog v-model:visible="colorDialog" maximizable :style="{ width: '600px' }" header="Color Details"
            pt:mask:class="backdrop-blur-sm">
            <div class="flex flex-col gap-6">
                <div>
                    <label for="name" class="block font-bold mb-2">Name</label>
                    <InputText id="name" v-model.trim="form.name" required="true" autofocus
                        :invalid="submitted && !form.name" fluid />
                    <small v-if="submitted && !form.name" class="text-red-500">Name is required.</small>
                </div>
            </div>
            <div class="flex flex-col gap-6">
                <div>
                    <label for="color_code" class="block font-bold mb-2">Color Code</label>
                    <InputText type="color" id="color_code" v-model.trim="form.color_code" required="true" autofocus
                        :invalid="submitted && !form.color_code" fluid />
                    <small v-if="submitted && !form.color_code" class="text-red-500">Color Code is required.</small>
                </div>
            </div>
            <div class="flex flex-col gap-6 mt-3">
                <div>
                    <label for="is_active" class="block font-bold mb-2">Status</label>
                    <Select v-model="form.is_active" :options="statuses" optionLabel="label" optionValue="value"
                        placeholder="Select a status" class="w-full" :required="true" />
                    <small v-if="submitted && (form.is_active == null)" class="text-red-500">
                        Status is required.
                    </small>
                </div>
            </div>

            <template #footer>
                <div class="mt-3">
                    <Button label="Cancel" icon="pi pi-times" text @click="hideDialog" />
                    <Button label="Save & Continue" text icon="pi pi-check" @click="saveColor(true)" v-if="!isEdit" />
                    <Button label="Save" icon="pi pi-check" @click="saveColor(false)" v-if="!isEdit" />
                    <Button label="Update" icon="pi pi-check" @click="updateColor" v-if="isEdit" />
                </div>
            </template>
        </Dialog>

        <!-- Single Delete -->
        <Dialog v-model:visible="deleteColorDialog" :style="{ width: '450px' }" header="Confirm">
            <div class="flex items-center gap-4">
                <i class="pi pi-exclamation-triangle !text-3xl" />
                <span v-if="form.name">Are you sure you want to delete <b>{{ form.name }}</b>?</span>
            </div>
            <template #footer>
                <Button label="No" icon="pi pi-times" text @click="deleteColorDialog = false" />
                <Button label="Yes" icon="pi pi-check" @click="deleteColor" />
            </template>
        </Dialog>

        <!-- Bulk Delete -->
        <Dialog v-model:visible="deleteColorsDialog" :style="{ width: '450px' }" header="Confirm">
            <div class="flex items-center gap-4">
                <i class="pi pi-exclamation-triangle !text-3xl" />
                <span v-if="form.name">Are you sure you want to delete the selected colors?</span>
            </div>
            <template #footer>
                <Button label="No" icon="pi pi-times" text @click="deleteColorsDialog = false" />
                <Button label="Yes" icon="pi pi-check" text @click="deleteSelectedColors" />
            </template>
        </Dialog>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { ref, defineProps } from 'vue';
import { FilterMatchMode } from '@primevue/core/api';
import { useToast } from 'primevue/usetoast';
import { router, useForm } from '@inertiajs/vue3';
import { Select } from 'primevue';
import { usePage } from '@inertiajs/vue3';
import { resolveImagePath } from '@/Helpers/imageHelper';

const { props } = usePage();

const vueProps = defineProps({
    colors: Object,
});

const toast = useToast();
const dt = ref();
const colorDialog = ref(false);
const deleteColorDialog = ref(false);
const deleteColorsDialog = ref(false);

const form = useForm({
    name: null,
    is_active: 1,
    color_code: null,
});

const selectedColors = ref();
const filters = ref({
    'global': { value: null, matchMode: FilterMatchMode.CONTAINS },
});
const submitted = ref(false);

const statuses = [
    { label: 'Active', value: 1 },
    { label: 'Inactive', value: 0 },
];

const openNew = () => {
    isEdit.value = false;
    form.reset();
    submitted.value = false;
    colorDialog.value = true;
};

const hideDialog = () => {
    colorDialog.value = false;
    submitted.value = false;
};

const saveColor = (saveAndContinue = false) => {
    submitted.value = true;

    form.post(route('colors.store'), {
        onSuccess: () => {
            form.reset();
            toast.add({ severity: 'success', summary: 'Successful', detail: 'Successfully Created!', life: 3000 });
            if (saveAndContinue) {
                submitted.value = false;
                return;
            } else {
                hideDialog();
            }
        },
        onError: () => {
            toast.add({ severity: 'error', summary: 'Error', detail: 'Something went wrong', life: 3000 });
        },
    });
};

const isEdit = ref(false);
const editingId = ref(null);

const updateColor = () => {
    submitted.value = true;
    const url = route('colors.update', { color: editingId.value });

    const data = {
        _method: 'put',
        name: form.name,
        is_active: form.is_active,
        color_code: form.color_code,
    };

    router.post(url, data, {
        onSuccess: () => {
            hideDialog();
            form.reset();
            toast.add({ severity: 'success', summary: 'Successful', detail: 'Successfully Updated!', life: 3000 });
        },
        onError: () => {
            toast.add({ severity: 'error', summary: 'Error', detail: 'Something went wrong, please try again!', life: 3000 });
        },
    });
};

const editColor = (prod) => {
    colorDialog.value = true;
    isEdit.value = true;

    form.name = prod.name;
    form.is_active = prod.is_active;
    form.color_code = prod.color_code;
    editingId.value = prod.id;
};

const confirmDeleteColor = (prod) => {
    deleteColorDialog.value = true;

    form.name = prod.name;
    form.is_active = prod.is_active;
    form.color_code = prod.color_code;
    editingId.value = prod.id;
};

const deleteColor = () => {
    deleteColorDialog.value = false;
    router.delete(route('colors.destroy', { color: editingId.value }), {
        onSuccess: () => {
            toast.add({ severity: 'error', summary: 'Deleted', detail: 'Successfully Deleted', life: 3000 });
        }
    });
};

const exportCSV = () => {
    dt.value.exportCSV();
};

const confirmDeleteSelected = () => {
    deleteColorsDialog.value = true;
};

const deleteSelectedColors = () => {
    const colorIds = selectedColors.value.map(c => c.id);
    deleteColorsDialog.value = false;
    selectedColors.value = null;
    router.post(route('colors.bulk-destroy'), {
        colorIds: colorIds,
        onSuccess: () => {
            toast.add({ severity: 'error', summary: 'Deleted', detail: 'Selected Colors Deleted', life: 3000 });
        }
    })
};

</script>
