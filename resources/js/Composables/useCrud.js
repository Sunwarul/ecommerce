import { ref, watch } from 'vue';
import { useToast } from 'primevue/usetoast';
import { router } from '@inertiajs/vue3';
import { FilterMatchMode } from '@primevue/core/api';
import debounce from 'lodash/debounce';

export function useCrud(config) {
    const toast = useToast();
    const items = ref([]);
    const selectedItems = ref([]);
    const form = ref({});
    const itemDialog = ref(false);
    const deleteItemDialog = ref(false);
    const deleteItemsDialog = ref(false);
    const submitted = ref(false);
    const isEdit = ref(false);
    const filters = ref({
        'global': { value: null, matchMode: FilterMatchMode.CONTAINS },
    });

    const fetchItems = async () => {
        const configValue = config;
        if (!configValue?.endpoints?.list) {
            console.error('Config endpoints.list is not defined');
            return;
        }
        router.get(configValue.endpoints.list, {
            search: filters.value.global.value || ''
        }, {
            preserveState: true,
            onSuccess: (page) => {
                items.value = page.props.items;
            }
        });
    };

    const debouncedSearch = debounce(() => {
        fetchItems();
    }, 300);

    const openNew = () => {
        form.value = {};
        submitted.value = false;
        isEdit.value = false;
        itemDialog.value = true;
    };

    const hideDialog = () => {
        itemDialog.value = false;
        submitted.value = false;
    };

    const saveItem = async (continueEditing = false) => {
        submitted.value = true;

        if (!validateForm()) {
            return;
        }

        const method = form.value.id ? 'put' : 'post';
        const url = form.value.id
            ? config.endpoints.update.replace('__ID__', form.value.id)
            : config.endpoints.create;

        router[method](url, form.value, {
            onSuccess: () => {
                toast.add({ severity: 'success', summary: 'Saved', detail: 'Item saved successfully!', life: 3000 });
                if (!continueEditing) {
                    hideDialog();
                }
                fetchItems();
                if (!form.value.id) {
                    form.value = {};
                    submitted.value = false;
                }
            }
        });
    };

    const validateForm = () => {
        // Basic validation - check if required fields have values
        return Object.keys(form.value).every(key => {
            if (config.fields[key]?.required) {
                return form.value[key] != null && form.value[key] !== '';
            }
            return true;
        });
    };

    const editItem = (item) => {
        form.value = { ...item };
        isEdit.value = true;
        itemDialog.value = true;
    };

    const confirmDeleteItem = (item) => {
        form.value = item;
        deleteItemDialog.value = true;
    };

    const deleteItem = async () => {
        router.delete(config.endpoints.delete.replace('__ID__', form.value.id), {
            onSuccess: () => {
                toast.add({ severity: 'success', summary: 'Deleted', detail: 'Item deleted successfully!', life: 3000 });
                fetchItems();
                deleteItemDialog.value = false;
                form.value = {};
            }
        });
    };

    const exportExcel = () => {
        const params = new URLSearchParams({
            search: filters.value.global.value || ''
        }).toString();

        const link = document.createElement('a');
        link.href = `${config.endpoints.export}?${params}`;
        link.setAttribute('download', '');
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
        const itemIds = selectedItems.value.map(item => item.id);
        router.post(config.endpoints.bulkDelete, { ids: itemIds }, {
            onSuccess: () => {
                toast.add({ severity: 'success', summary: 'Deleted', detail: 'Selected items deleted successfully!', life: 3000 });
                fetchItems();
                deleteItemsDialog.value = false;
                selectedItems.value = [];
            }
        });
    };

    const handlePagination = (event, route, entity) => {
        router.get(route, {
            page: event.page + 1,
            search: filters.value.global.value || ''
        }, {
            preserveState: true,
            preserveScroll: true,
            onSuccess: (page) => {
                items.value = page.props[entity];
            }
        });
    };

    return {
        items,
        form,
        selectedItems,
        itemDialog,
        deleteItemDialog,
        deleteItemsDialog,
        submitted,
        isEdit,
        filters,
        openNew,
        hideDialog,
        saveItem,
        editItem,
        deleteItem,
        confirmDeleteItem,
        exportExcel,
        confirmDeleteSelected,
        deleteSelectedItems,
        fetchItems,
        handlePagination,
        debouncedSearch
    };
}
