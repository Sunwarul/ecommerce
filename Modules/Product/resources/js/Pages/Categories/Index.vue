<template>
    <AuthenticatedLayout>
        <div class="card">
            <Toolbar>
                <template #start>
                    <Button label="New" icon="pi pi-plus" class="mr-2" @click="openNew" />
                    <Button label="Delete" icon="pi pi-trash" severity="danger" outlined @click="confirmDeleteSelected"
                        :disabled="!selectedCategories || !selectedCategories.length" />
                </template>

                <template #end>
                    <Button label="Export" icon="pi pi-upload" severity="secondary" @click="exportExcel" />
                </template>
            </Toolbar>

            <DataTable ref="dt" v-model:selection="selectedCategories" :value="categories.data" dataKey="id"
                :paginator="true" :rows="15" :filters="filters" :totalRecords="categories.total" :lazy="true"
                @page="handlePagination($event, route('categories.index'), 'categories')"
                paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
                :rowsPerPageOptions="[5, 10, 25]"
                currentPageReportTemplate="Showing {first} to {last} of {totalRecords} categories">
                <template #header>
                    <div class="flex flex-wrap gap-2 items-center justify-between">
                        <h1 class="text-3xl">Categories</h1>
                        <IconField>
                            <InputIcon>
                                <i class="pi pi-search" />
                            </InputIcon>
                            <InputText v-model="filters.global.value" type="search" @input="debouncedSearch"
                                placeholder="Search..." clearable />
                        </IconField>
                    </div>
                </template>

                <Column selectionMode="multiple" style="width: 3rem" :exportable="false"></Column>
                <Column field="name" header="Name"></Column>
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
                        <Button icon="pi pi-pencil" outlined rounded class="mr-2"
                            @click="editCategory(slotProps.data)" />
                        <Button icon="pi pi-trash" outlined rounded severity="danger"
                            @click="confirmDeleteCategory(slotProps.data)" />
                    </template>
                </Column>
            </DataTable>
        </div>

        <!-- Create & Edit Form Dialog -->
        <Dialog v-model:visible="categoryDialog" maximizable :style="{ width: '600px' }" header="Category Details"
            pt:mask:class="backdrop-blur-sm">
            <div class="flex flex-col gap-6">
                <div>
                    <label for="name" class="block font-bold mb-2">Name</label>
                    <InputText id="name" v-model.trim="form.name" required="true" autofocus
                        :invalid="submitted && !form.name" fluid />
                    <small v-if="submitted && !form.name" class="text-red-500">Name is required.</small>
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
            <div class="flex flex-col gap-6 mt-3">

                <div>
                    <label for="photo" class="block font-bold mb-2">Photo</label>
                    <FileUpload mode="basic" name="photo" customUpload @select="handlePhotoUpload" :auto="true"
                        accept="image/*" chooseLabel="Choose Image" class="w-full" />
                </div>
                <div>
                    <img v-if="form.photo || photoPreview" :src="photoPreview ?? resolveImagePath(form.photo)"
                        alt="Image" class="shadow-md rounded-xl w-full" style="filter: grayscale(100%)" />
                </div>
            </div>

            <template #footer>
                <div class="mt-3">
                    <Button label="Cancel" icon="pi pi-times" text @click="hideDialog" />
                    <Button label="Save & Continue" text icon="pi pi-check" @click="saveCategory(true)"
                        v-if="!isEdit" />
                    <Button label="Save" icon="pi pi-check" @click="saveCategory(false)" v-if="!isEdit" />
                    <Button label="Update" icon="pi pi-check" @click="updateCategory" v-if="isEdit" />
                </div>
            </template>
        </Dialog>

        <!-- Single Delete -->
        <Dialog v-model:visible="deleteCategoryDialog" :style="{ width: '450px' }" header="Confirm">
            <div class="flex items-center gap-4">
                <i class="pi pi-exclamation-triangle !text-3xl" />
                <span>Are you sure you want to delete <b>{{ form.name }}</b>?</span>
            </div>
            <template #footer>
                <Button label="No" icon="pi pi-times" text @click="deleteCategoryDialog = false" />
                <Button label="Yes" icon="pi pi-check" @click="deleteCategory" />
            </template>
        </Dialog>

        <!-- Bulk Delete -->
        <Dialog v-model:visible="deleteCategoriesDialog" :style="{ width: '450px' }" header="Confirm">
            <div class="flex items-center gap-4">
                <i class="pi pi-exclamation-triangle !text-3xl" />
                <span>Are you sure you want to delete the selected categories?</span>
            </div>
            <template #footer>
                <Button label="No" icon="pi pi-times" text @click="deleteCategoriesDialog = false" />
                <Button label="Yes" icon="pi pi-check" text @click="deleteSelectedCategories" />
            </template>
        </Dialog>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { ref, defineProps, onMounted } from 'vue';
import { FilterMatchMode } from '@primevue/core/api';
import { useToast } from 'primevue/usetoast';
import { router, useForm } from '@inertiajs/vue3';
import { Select } from 'primevue';
import { usePage } from '@inertiajs/vue3';
import { resolveImagePath } from '@/Helpers/imageHelper';
import { handlePagination } from '@/Helpers/pagination';
import debounce from 'lodash/debounce';

const { props } = usePage();

const vueProps = defineProps({
    categories: Object,
    filters: {
        type: Object,
        default: () => ({
            search: '',
        }),
    },
});

const toast = useToast();
const dt = ref();
const categoryDialog = ref(false);
const deleteCategoryDialog = ref(false);
const deleteCategoriesDialog = ref(false);

const form = useForm({
    name: null,
    is_active: 1,
    photo: null,
});

const selectedCategories = ref();
const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS },
});

const submitted = ref(false);

const statuses = [
    { label: 'Active', value: 1 },
    { label: 'Inactive', value: 0 },
];

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
    categoryDialog.value = true;
    photoPreview.value = null;
};

const hideDialog = () => {
    categoryDialog.value = false;
    submitted.value = false;
};

const saveCategory = (saveAndContinue = false) => {
    submitted.value = true;

    form.post(route('categories.store'), {
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

const updateCategory = () => {
    submitted.value = true;
    const url = route('categories.update', { category: editingId.value });

    const data = {
        _method: 'put',
        name: form.name,
        is_active: form.is_active,
        photo: form.photo,
    };

    router.post(url, data, {
        onSuccess: () => {
            hideDialog();
            photoPreview.value = null;
            form.reset();
            toast.add({ severity: 'success', summary: 'Successful', detail: 'Successfully Updated!', life: 3000 });
        },
        onError: () => {
            toast.add({ severity: 'error', summary: 'Error', detail: 'Something went wrong, please try again!', life: 3000 });
        },
    });
};

const editCategory = (prod) => {
    categoryDialog.value = true;
    isEdit.value = true;

    photoPreview.value = null;
    form.name = prod.name;
    form.is_active = prod.is_active;
    form.photo = prod.photo;
    editingId.value = prod.id;
};

const confirmDeleteCategory = (prod) => {
    deleteCategoryDialog.value = true;

    photoPreview.value = null;
    form.name = prod.name;
    form.is_active = prod.is_active;
    form.photo = prod.photo;
    editingId.value = prod.id;
};

const deleteCategory = () => {
    deleteCategoryDialog.value = false;
    router.delete(route('categories.destroy', { category: editingId.value }), {
        onSuccess: () => {
            toast.add({ severity: 'error', summary: 'Deleted', detail: 'Successfully Deleted', life: 3000 });
        }
    });
};

const exportExcel = () => {
    const params = new URLSearchParams({
        search: filters.value.global.value || ''
    }).toString();

    // Create a temporary link to trigger the download
    const link = document.createElement('a');
    link.href = `${route('categories.export')}?${params}`;
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
    deleteCategoriesDialog.value = true;
};

const deleteSelectedCategories = () => {
    const categoryIds = selectedCategories.value.map(c => c.id);
    deleteCategoriesDialog.value = false;
    selectedCategories.value = null;

    router.post(route('categories.bulk-destroy'), {
        categoryIds: categoryIds
    }, {
        onSuccess: () => {
            console.log('all deleted');
            toast.add({ severity: 'error', summary: 'Deleted', detail: 'Selected Categories Deleted', life: 3000 });
        }
    })
};

const onPage = (event) => {
    console.log(event);
    const page = (event.first / event.rows) + 1;
    router.get(route('categories.index'), {
        page: page,
        per_page: event.rows
    }, {
        preserveState: true,
        preserveScroll: true,
        only: ['categories']
    });
};

const debouncedSearch = debounce((e) => {
    filters.value.global.value = e.target.value;
    router.get(
        route('categories.index'),
        {
            search: e.target.value,
            per_page: dt.value?.rows
        },
        {
            preserveState: true,
            preserveScroll: true,
            only: ['categories']
        }
    );
}, 300);

onMounted(() => {
    if (vueProps.filters.search) {
        filters.value.global.value = vueProps.filters.search;
    }
});

</script>
