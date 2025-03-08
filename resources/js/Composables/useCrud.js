import { ref, watch } from 'vue';
import { useToast } from 'primevue/usetoast';
import { router } from '@inertiajs/vue3';

export function useCrud(config) {
    const toast = useToast();
    const items = ref([]);
    const selectedItems = ref([]);
    const form = ref({});
    const itemDialog = ref(false);
    const deleteItemDialog = ref(false);

    watch(config, async (newConfig) => {
        if (newConfig) {
            await fetchItems();
        }
    });

    const fetchItems = async () => {
        router.get(config.value.endpoints.list, {}, {
            preserveState: true,
            onSuccess: (page) => {
                items.value = page.props.items;
            }
        });
    };

    const openNew = () => {
        form.value = {};
        itemDialog.value = true;
    };

    const saveItem = async () => {
        const method = form.value.id ? 'put' : 'post';
        const url = form.value.id
            ? config.value.endpoints.update.replace('__ID__', form.value.id)
            : config.value.endpoints.create;

        router[method](url, form.value, {
            onSuccess: () => {
                toast.add({ severity: 'success', summary: 'Saved', detail: 'Item saved successfully!', life: 3000 });
                fetchItems();
                itemDialog.value = false;
            }
        });
    };

    const editItem = (item) => {
        form.value = { ...item };
        itemDialog.value = true;
    };

    const confirmDeleteItem = (item) => {
        form.value = item;
        deleteItemDialog.value = true;
    };

    const deleteItem = async () => {
        router.delete(config.value.endpoints.delete.replace('__ID__', form.value.id), {
            onSuccess: () => {
                toast.add({ severity: 'success', summary: 'Deleted', detail: 'Item deleted successfully!', life: 3000 });
                fetchItems();
                deleteItemDialog.value = false;
            }
        });
    };

    const exportExcel = () => {
        console.log('Logic to export to Excel');
    }

    const confirmDeleteSelected = () => {
        console.log('Logic to delete selected items');
    }

    const filters = {

    }

    const handlePagination = () => {

    }

    return {
        items,
        form,
        selectedItems,
        itemDialog,
        deleteItemDialog,
        openNew,
        saveItem,
        editItem,
        deleteItem,
        confirmDeleteItem,
        exportExcel,
        confirmDeleteSelected,
        fetchItems,
        filters,
        handlePagination,
    };
}
