<template>
    <AuthenticatedLayout>
        <div class="card">
            <Toolbar class="">
                <template #start>
                    <div class="flex flex-wrap gap-2">
                        <Button label="Create New" icon="pi pi-plus" @click="openNew" outlined severity="primary" />
                        <ButtonGroup>
                            <Link :href="vueProps.config.indexRoute" v-if="vueProps.config.indexRoute">
                                <Button label="All Items" icon="pi pi-list"
                                    :class="{ 'border-bottom-2': !isTrashedPage }" text />
                            </Link>
                            <Link :href="vueProps.config.indexRouteTrashed" v-if="vueProps.config.bulkRestoreRoute">
                                <Button label="Trashed" icon="pi pi-ban" :class="{ 'border-bottom-2': isTrashedPage }"
                                    text />
                            </Link>
                        </ButtonGroup>
                        <Button label="Bulk Delete" icon="pi pi-trash" severity="danger" outlined
                            @click="confirmDeleteSelected"
                            v-show="!(!selectedItems || !selectedItems?.length) && !isTrashedPage"
                            v-if="vueProps.config.bulkRestoreRoute" />
                        <Button label="Bulk Restore" icon="pi pi-undo" severity="warn" outlined @click="restoreSelected"
                            v-show="!(!selectedItems || !selectedItems?.length) && isTrashedPage"
                            v-if="vueProps.config.bulkRestoreRoute" />

                        <Button label="Force Delete" icon="pi pi-trash" severity="danger" outlined
                            @click="forceDeleteSelected"
                            v-show="!(!selectedItems || !selectedItems?.length) && isTrashedPage"
                            v-if="vueProps.config.bulkRestoreRoute" />
                    </div>
                </template>
                <template #center>
                    <slot name="messages"></slot>
                </template>
                <template #end>
                    <Button label="Export" v-if="vueProps?.config?.exportRoute" class="mx-2" icon="pi pi-upload"
                        severity="secondary" @click="exportExcel" />
                </template>
            </Toolbar>

            <DataTable ref="dt" v-model:selection="selectedItems" :value="vueProps.items.data" dataKey="id"
                :paginator="true" :rows="15" :filters="filters" :totalRecords="vueProps.items.total" :lazy="true"
                @page="handlePagination($event, vueProps.config.indexRoute, vueProps.config.resource)"
                paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
                :rowsPerPageOptions="[5, 10, 25]"
                :currentPageReportTemplate="`Showing {first} to {last} of {totalRecords} ${vueProps.config.resource}`"
                resizableColumns columnResizeMode="fit">
                <template #empty>
                    <div class="p-4 text-center">
                        <p class="text-lg">No {{ vueProps.config.resource }} found.</p>
                    </div>
                </template>
                <template #header>
                    <div class="flex flex-wrap gap-2 items-center justify-between">
                        <h1 class="text-3xl">{{ vueProps.config.title }}</h1>
                        <IconField>
                            <InputIcon>
                                <i class="pi pi-search" />
                            </InputIcon>
                            <InputText v-model="filters.global.value" type="search" @input="debouncedSearch"
                                placeholder="Search..." clearable />
                        </IconField>
                    </div>
                </template>
                <Column selectionMode="multiple" style="width: 3rem" :exportable="false" header=""
                    v-if="vueProps.config.bulkRestoreRoute"></Column>

                <slot name="columns"></slot>

                <Column :exportable="false" style="min-width: 12rem">
                    <template #body="slotProps">
                        <Button v-if="!hideEditAction" icon="pi pi-pencil" outlined rounded class="mr-2"
                            @click="editItem(slotProps.data)" :disabled="isTrashedPage" />
                        <Button v-if="!hideDeleteAction" icon="pi pi-trash" outlined rounded severity="danger"
                            @click="confirmDeleteItem(slotProps.data)" :disabled="isTrashedPage" />
                    </template>
                </Column>
            </DataTable>
        </div>

        <!-- Create & Edit Form Dialog -->
        <Dialog v-model:visible="itemDialog" maximizable :style="{ width: formWidth ?? '60vw' }"
            :breakpoints="{ '960px': '75vw', '640px': '95vw' }" :header="`${vueProps.config.modelRaw} Details`"
            pt:mask:class="backdrop-blur-sm">

            <slot name="form" v-bind="{ submitted, statuses, handlePhotoUpload, photoPreview, resolveImagePath }">
            </slot>

            <template #footer>
                <div class="mt-3">
                    <Button label="Cancel" icon="pi pi-times" text @click="hideDialog" />
                    <Button label="Save & Continue" text icon="pi pi-check" @click="saveItem(true)" v-if="!isEdit" />
                    <Button label="Save" icon="pi pi-check" @click="saveItem(false)" v-if="!isEdit" />
                    <Button label="Update" icon="pi pi-check" @click="updateItem" v-if="isEdit" />
                </div>
            </template>
        </Dialog>

        <!-- Single Delete -->
        <Dialog v-model:visible="deleteItemDialog" :style="{ width: '450px' }" header="Confirm Delete">
            <div class="flex items-center gap-4">
                <i class="pi pi-exclamation-triangle !text-3xl" />
                <span>Are you sure you want to delete?</span>
            </div>
            <template #footer>
                <Button label="No" icon="pi pi-times" text @click="deleteItemDialog = false" />
                <Button label="Yes" icon="pi pi-check" @click="deleteItem" />
            </template>
        </Dialog>

        <!-- Bulk Delete -->
        <Dialog v-model:visible="deleteItemsDialog" :style="{ width: '450px' }" header="Confirm Delete">
            <div class="flex items-center gap-4">
                <i class="pi pi-exclamation-triangle !text-3xl" />
                <span>Are you sure you want to delete the selected items?</span>
            </div>
            <template #footer>
                <Button label="No" icon="pi pi-times" text @click="deleteItemsDialog = false" />
                <Button label="Yes" icon="pi pi-check" text @click="deleteSelectedItems" />
            </template>
        </Dialog>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { ref, computed, onMounted, watch } from 'vue';
import { FilterMatchMode } from '@primevue/core/api';
import { useToast } from 'primevue/usetoast';
import { router, useForm, Link, usePage } from '@inertiajs/vue3';
import { Select } from 'primevue';
import { resolveImagePath } from '@/Helpers/imageHelper';
import { handlePagination } from '@/Helpers/pagination';
import debounce from 'lodash/debounce';
import { statuses } from '@/Helpers/enums.js';

const { form, formWidth, canEdit, canDelete } = defineProps(['form', 'formWidth', 'canEdit', 'canDelete']);

const hideDeleteAction = computed(() => canDelete === false);
const hideEditAction = computed(() => canEdit === false);

const page = usePage();
const vueProps = computed(() => page.props);

const toast = useToast();
const dt = ref();
const itemDialog = ref(false);
const submitted = ref(false);
const deleteItemDialog = ref(false);
const deleteItemsDialog = ref(false);

const selectedItems = ref();

const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS },
});

const photoPreview = ref(null);
const handlePhotoUpload = (event) => {
    const file = event.files[0];
    form.photo = file;
    const reader = new FileReader();
    reader.onload = async (e) => {
        photoPreview.value = e.target.result;
    };
    reader.readAsDataURL(file);
};

const openNew = () => {
    isEdit.value = false;
    form.reset();
    submitted.value = false;
    itemDialog.value = true;
    photoPreview.value = null;
};

const hideDialog = () => {
    itemDialog.value = false;
    submitted.value = false;
};

const saveItem = (saveAndContinue = false) => {
    submitted.value = true;
    if (form.ids) { // Only `id` columns (as array) is enough
        form.ids = form.ids.map(i => i.id);
    }
    form.post(vueProps.value.config.storeRoute, {
        onSuccess: () => {
            form.reset();
            photoPreview.value = null;
            toast.add({ severity: 'success', summary: 'Successful', detail: 'Successfully Created!', life: 3000 });
            if (saveAndContinue) {
                submitted.value = false;
                return;
            } else {
                hideDialog();
            }
        },
        onError: (errors) => {
            Object.entries(errors).forEach((val, key) => {
                toast.add({ severity: 'error', summary: 'Validation Error', detail: val[1], life: 3000 });
            })
        },
    });
};

const isEdit = ref(false);
const editingId = ref(null);

const updateItem = () => {
    submitted.value = true;
    const url = vueProps.value.config.updateRoute.replace('__ID__', editingId.value);

    if (form.ids) {
        form.ids = form.ids.map(i => i.id);
    }

    const data = {
        _method: 'put',
        ...form
    };

    router.post(url, data, {
        onSuccess: () => {
            hideDialog();
            photoPreview.value = null;
            form.reset();
            toast.add({ severity: 'success', summary: 'Successful', detail: 'Successfully Updated!', life: 3000 });
        },
        onError: (errors) => {
            Object.entries(errors).forEach((val, key) => {
                toast.add({ severity: 'error', summary: 'Validation Error', detail: val[1], life: 3000 });
            })
        },
    });
};

const emit = defineEmits(['editingItem']);

const editItem = (prod) => {

    emit('editingItem', prod)

    itemDialog.value = true;
    isEdit.value = true;

    photoPreview.value = null;

    Object.assign(form, prod);

    editingId.value = prod.id;
};

const confirmDeleteItem = (prod) => {
    deleteItemDialog.value = true;

    photoPreview.value = null;

    Object.assign(form, prod);

    editingId.value = prod.id;
};

const restoreSelected = () => {
    const itemIds = selectedItems.value.map(c => c.id);
    selectedItems.value = null;

    router.post(vueProps.value.config.bulkRestoreRoute, {
        ids: itemIds
    }, {
        onSuccess: () => {
            isTrashedPage.value = false;
            router.get(vueProps.value.config.indexRoute);
            toast.add({ severity: 'success', summary: 'Restored', detail: 'Selected Items Restored!', life: 3000 });
        },
        onError: (errors) => {
            Object.entries(errors).forEach((val, key) => {
                toast.add({ severity: 'error', summary: 'Restore Error', detail: val[1], life: 3000 });
            })
        },
    })
};

const forceDeleteSelected = () => {
    const itemIds = selectedItems.value.map(c => c.id);
    selectedItems.value = null;

    router.post(vueProps.value.config.bulkForceDeleteRoute, {
        ids: itemIds
    }, {
        onSuccess: () => {
            isTrashedPage.value = false;
            router.get(vueProps.value.config.indexRoute);
            toast.add({ severity: 'warn', summary: 'Permanently Delete', detail: 'Items Permanently Deleted!', life: 3000 });
        },
        onError: (errors) => {
            Object.entries(errors).forEach((val, key) => {
                toast.add({ severity: 'error', summary: 'Permanent Delete Error', detail: val[1], life: 3000 });
            })
        },
    })
};

const deleteItem = () => {
    deleteItemDialog.value = false;
    const url = vueProps.value.config.deleteRoute.replace('__ID__', editingId.value);
    router.delete(url, {
        onSuccess: () => {
            toast.add({ severity: 'error', summary: 'Deleted', detail: 'Successfully Deleted', life: 3000 });
        },
    });
};

const exportExcel = () => {
    const params = new URLSearchParams({
        search: filters.value.global.value || ''
    }).toString();

    // Create a temporary link to trigger the download
    const link = document.createElement('a');
    link.href = `${vueProps.value.config.exportRoute}?${params}`;
    link.setAttribute('download', ''); // This is optional as the server will send the filename
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);

    toast.add({
        severity: 'success',
        summary: 'Export Started',
        detail: 'Your export will download shortly.',
        life: 3000
    });
};

const confirmDeleteSelected = () => {
    deleteItemsDialog.value = true;
};

const deleteSelectedItems = () => {
    const itemIds = selectedItems.value.map(c => c.id);
    deleteItemsDialog.value = false;
    selectedItems.value = null;

    router.post(vueProps.value.config.bulkDeleteRoute, {
        ids: itemIds
    }, {
        onSuccess: () => {
            toast.add({ severity: 'error', summary: 'Deleted', detail: 'Selected Items Deleted', life: 3000 });
        },
        onError: (errors) => {
            Object.entries(errors).forEach((val, key) => {
                toast.add({ severity: 'error', summary: 'Delete Error', detail: val[1], life: 3000 });
            })
        },
    })
};

const debouncedSearch = debounce((e) => {
    filters.value.global.value = e.target.value;
    router.get(
        vueProps.value.config.indexRoute,
        {
            search: e.target.value,
            per_page: dt.value?.rows
        },
        {
            preserveState: true,
            preserveScroll: true,
            only: ['items']
        }
    );
}, 300);

onMounted(() => {
    if (vueProps.value.filters.search) {
        filters.value.global.value = vueProps.value.filters.search;
    }
});

const isTrashedPage = ref(route().params.trashed === '1');

</script>
