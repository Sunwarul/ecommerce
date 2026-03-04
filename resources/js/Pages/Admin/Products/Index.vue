<script setup>
import { router } from "@inertiajs/vue3";
import { computed, ref, watch } from "vue";
import axios from "axios";
import debounce from "lodash/debounce";

import { resolveImagePath } from "@/Helpers/imageHelper";

// PrimeVue
import Badge from "primevue/badge";
import Button from "primevue/button";
import Column from "primevue/column";
import DataTable from "primevue/datatable";
import InputText from "primevue/inputtext";
import Select from "primevue/select";
import SplitButton from "primevue/splitbutton";
import Toolbar from "primevue/toolbar";
import TabMenu from "primevue/tabmenu";
import InputNumber from "primevue/inputnumber";
import IconField from "primevue/iconfield";
import InputIcon from "primevue/inputicon";

// Local
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";

const props = defineProps({
    products: { type: Object, required: true },
    filters: { type: Object, default: () => ({}) },
    categories: { type: Array, default: () => [] },
    brands: { type: Array, default: () => [] },
    warehouses: { type: Array, default: () => [] },
});

const products = computed(() => props.products || { data: [], links: [] });

// -----------------------------
// FILTERS STATE
// -----------------------------
const filterForm = ref({
    search: props.filters.search || "",
    category_id: props.filters.category_id ? Number(props.filters.category_id) : null,
    brand_id: props.filters.brand_id ? Number(props.filters.brand_id) : null,
    status: props.filters.status || null,
    trashed: props.filters.trashed || null,
    per_page: props.filters.per_page ? Number(props.filters.per_page) : 10,
    type: props.filters.type || null,
    warehouse_id: props.filters.warehouse_id ? Number(props.filters.warehouse_id) : null,
    stock_min: props.filters.stock_min ?? null,
    stock_max: props.filters.stock_max ?? null,
    price_min: props.filters.price_min ?? null,
    price_max: props.filters.price_max ?? null,
    stock_status: props.filters.stock_status || null,
    sort: props.filters.sort || 'id_desc',
});

const statusOptions = [
    { label: "Active", value: "active" },
    { label: "Inactive", value: "inactive" },
];

const typeOptions = [
    { label: "Simple", value: "simple" },
    { label: "Variable", value: "variable" },
];

const stockStatusOptions = [
    { label: "In Stock", value: "in_stock" },
    { label: "Low Stock", value: "low_stock" },
    { label: "Out of Stock", value: "out_of_stock" },
];

const trashOptions = [
    { label: "With Trashed", value: "with" },
    { label: "Only Trashed", value: "only" },
];

const perPageOptions = [
    { label: "10", value: 10 },
    { label: "25", value: 25 },
    { label: "50", value: 50 },
    { label: "100", value: 100 },
];

const sortOptions = [
    { label: "Newest First", value: "id_desc" },
    { label: "Oldest First", value: "id_asc" },
    { label: "Name A-Z", value: "name_asc" },
    { label: "Name Z-A", value: "name_desc" },
    { label: "Price Low-High", value: "base_price_asc" },
    { label: "Price High-Low", value: "base_price_desc" },
    { label: "Stock Low-High", value: "total_stock_asc" },
    { label: "Stock High-Low", value: "total_stock_desc" },
];

// Tabs for Trash view switching
const activeTab = ref(props.filters.trashed === 'only' ? 1 : 0);
const tabItems = [
    { label: 'All Products', icon: 'pi pi-list' },
    { label: 'Trash', icon: 'pi pi-trash' }
];

watch(activeTab, (val) => {
    if (val === 0) {
        filterForm.value.trashed = null;
    } else {
        filterForm.value.trashed = 'only';
    }
});

// -----------------------------
// WATCHERS & RELOAD
// -----------------------------
const debouncedReload = debounce(() => {
    router.get(route("products.index"), filterForm.value, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
}, 300);

watch(
    () => filterForm.value,
    () => debouncedReload(),
    { deep: true }
);

// -----------------------------
// SELECTION & BULK ACTIONS
// -----------------------------
const selectedProducts = ref([]);

const bulkActions = computed(() => {
    const actions = [];
    if (filterForm.value.trashed === 'only') {
        actions.push({
            label: "Restore Selected",
            icon: "pi pi-refresh",
            command: () => bulkRestore(),
        });
        actions.push({
            label: "Force Delete Selected",
            icon: "pi pi-times",
            class: "p-button-danger",
            command: () => bulkForceDelete(),
        });
    } else {
        actions.push({
            label: "Move to Trash",
            icon: "pi pi-trash",
            command: () => bulkDelete(),
        });
    }
    return actions;
});

const bulkDelete = () => {
    if (!selectedProducts.value.length) return;
    if (!confirm(`Move ${selectedProducts.value.length} products to trash?`)) return;

    router.delete(route("products.bulk-delete"), { // Ensure you have this route or use loop
        data: { ids: selectedProducts.value.map((p) => p.id) },
        onSuccess: () => (selectedProducts.value = []),
    });
};

// We don't have a bulk soft delete route in controller yet?
// Actually Controller has `bulkForceDelete`. For soft delete bulk, we'd need a route or loop.
// Let's implement bulk soft delete in controller or loop here.
// For now, I'll assume we might need to add `bulkDestroy` to controller or just disable if not present.
// WAIT, we passed `bulkForceDelete` in plan.
// Let's use loop for soft delete if route missing, or better add route.
// I'll add `bulkDestroy` in next step if missed. For now assuming it exists or I'll add it.
// Re-checking plan: "Bulk Operations". I added `bulkForceDelete` and `bulkRestore`.
// I missed `bulkSoftDelete`. I will add it to controller.

const bulkRestore = () => {
    if (!selectedProducts.value.length) return;
    if (!confirm(`Restore ${selectedProducts.value.length} products?`)) return;

    router.post(route("products.bulk-restore"), {
        ids: selectedProducts.value.map((p) => p.id),
    }, {
        onSuccess: () => (selectedProducts.value = []),
    });
};

const bulkForceDelete = () => {
    if (!selectedProducts.value.length) return;
    if (!confirm(`PERMANENTLY delete ${selectedProducts.value.length} products? THIS CANNOT BE UNDONE.`)) return;

    router.post(route("products.bulk-force-delete"), {
        ids: selectedProducts.value.map((p) => p.id),
    }, {
        onSuccess: () => (selectedProducts.value = []),
    });
};


// -----------------------------
// NAV HELPER
// -----------------------------
const visitLink = (url) => {
    if (!url) return;
    router.visit(url, { preserveScroll: true, preserveState: true });
};

const goCreate = () => router.visit(route("products.create"));
const goShow = (rowProduct) =>
    router.visit(route("products.show", rowProduct.id));
const goEdit = (rowProduct) =>
    router.visit(route("products.edit", rowProduct.id));

const deleteProduct = (product) => {
    if (!confirm("Delete this product?")) return;
    router.delete(route("products.destroy", product.id), {
        preserveScroll: true,
        preserveState: true,
    });
};

const restoreProduct = (product) => {
    if (!confirm("Restore this product?")) return;
    router.post(route("products.restore", product.id), {}, {
        preserveScroll: true,
        preserveState: true,
    });
};

const forceDeleteProduct = (product) => {
    if (!confirm("PERMANENTLY delete this product?")) return;
    // We can reuse bulk force delete for single id, or add single force delete route
    // Using bulk route for simplicity as it takes IDs
    router.post(route("products.bulk-force-delete"), {
        ids: [product.id],
    });
};

// Stock helper
const getTotalStock = (p) => {
    if (p.total_stock !== undefined && p.total_stock !== null)
        return Number(p.total_stock);
    if (Array.isArray(p.stocks)) {
        return p.stocks.reduce((sum, s) => sum + Number(s.quantity || 0), 0);
    }
    return 0;
};

const getStockTooltip = (stocks) => {
    if (!stocks || stocks.length === 0) return "No stock info";
    return stocks.map(s => {
        const whName = s.warehouse?.name || `Warehouse #${s.warehouse_id}`;
        return `${whName}: ${Number(s.quantity).toFixed(0)}`;
    }).join('\n');
};

// -----------------------------
// INLINE STOCK EDITING
// -----------------------------
const editingStock = ref(null);
const editingStockValue = ref(0);
const editingWarehouseId = ref(null);

const startStockEdit = (product) => {
    editingStock.value = product.id;
    // Default to first warehouse if no existing stock
    editingWarehouseId.value = props.warehouses[0]?.id || null;
    editingStockValue.value = getTotalStock(product);
};

const saveStockEdit = async (product) => {
    if (editingStock.value === null) return;

    try {
        const response = await axios.put(route('products.update-stock', product.id), {
            warehouse_id: editingWarehouseId.value,
            quantity: editingStockValue.value,
        });

        editingStock.value = null;
        editingWarehouseId.value = null;

        // Reload the page to refresh data
        router.reload({ preserveState: true, preserveScroll: true });
    } catch (error) {
        console.error('Failed to update stock:', error);
    }
};

const cancelStockEdit = () => {
    editingStock.value = null;
    editingWarehouseId.value = null;
};

const isLowStock = (product) => {
    const total = getTotalStock(product);
    const alertQty = product.stocks?.reduce((min, s) => Math.min(min, s.alert_quantity || 999999), 999999) || 10;
    return total > 0 && total <= alertQty;
};

const isOutOfStock = (product) => {
    return getTotalStock(product) <= 0;
};

const resetFilters = () => {
    filterForm.value = {
        search: "",
        category_id: null,
        brand_id: null,
        status: null,
        trashed: null,
        per_page: 10,
        type: null,
        warehouse_id: null,
        stock_min: null,
        stock_max: null,
        price_min: null,
        price_max: null,
        stock_status: null,
        sort: 'id_desc',
    };
};
</script>

<template>
    <AuthenticatedLayout>
        <div class="space-y-6">
            <div class="card">
                <!-- Toolbar -->
                <Toolbar class="mb-4 flex flex-col md:flex-row gap-4">
                    <template #start>
                        <div class="flex flex-wrap gap-2">
                            <Button label="New Product" icon="pi pi-plus" class="mr-2" @click="goCreate" />
                            <SplitButton v-if="selectedProducts.length" :label="selectedProducts.length + ' Selected'"
                                :model="bulkActions" severity="danger"></SplitButton>
                        </div>
                    </template>

                    <template #end>
                        <!-- Tabs for Trash -->
                        <TabMenu :model="tabItems" v-model:activeIndex="activeTab" class="w-full md:w-auto" />
                    </template>
                </Toolbar>

                <!-- Filters -->
                <div class="space-y-3">
                    <!-- Row 1: Main filters -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                        <div>
                            <span class="p-input-icon-left w-full">
                                <IconField>
                                    <InputIcon class="pi pi-search" />
                                    <InputText type="search" v-model="filterForm.search" placeholder="Search name, sku, barcode..."
                                        class="w-full" />
                                </IconField>
                            </span>
                        </div>

                        <div>
                            <Select v-model="filterForm.category_id" :options="categories" optionLabel="name"
                                optionValue="id" placeholder="Category" filter showClear class="w-full" />
                        </div>

                        <div>
                            <Select v-model="filterForm.brand_id" :options="brands" optionLabel="name" optionValue="id"
                                placeholder="Brand" filter showClear class="w-full" />
                        </div>

                        <div>
                            <Select v-model="filterForm.type" :options="typeOptions" optionLabel="label"
                                optionValue="value" placeholder="Product Type" showClear class="w-full" />
                        </div>
                    </div>

                    <!-- Row 2: Stock & Warehouse filters -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                        <div>
                            <Select v-model="filterForm.warehouse_id" :options="warehouses" optionLabel="name"
                                optionValue="id" placeholder="Warehouse" filter showClear class="w-full" />
                        </div>

                        <div>
                            <Select v-model="filterForm.stock_status" :options="stockStatusOptions" optionLabel="label"
                                optionValue="value" placeholder="Stock Status" showClear class="w-full" />
                        </div>

                        <div class="sm:col-span-2 lg:col-span-2">
                            <div class="grid grid-cols-2 gap-2">
                                <InputNumber v-model="filterForm.stock_min" placeholder="Min Stock" showButtons :min="0" class="w-full" />
                                <InputNumber v-model="filterForm.stock_max" placeholder="Max Stock" showButtons :min="0" class="w-full" />
                            </div>
                        </div>
                    </div>

                    <!-- Row 3: Status, Sort, Price, Actions -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 items-end">
                        <div>
                            <Select v-model="filterForm.status" :options="statusOptions" optionLabel="label"
                                optionValue="value" placeholder="Status" showClear class="w-full" />
                        </div>

                        <div>
                            <Select v-model="filterForm.sort" :options="sortOptions" optionLabel="label"
                                optionValue="value" placeholder="Sort By" class="w-full" />
                        </div>

                        <div class="grid grid-cols-2 gap-2">
                            <InputNumber v-model="filterForm.price_min" placeholder="Min $" mode="currency" currency="USD" locale="en-US" class="w-full" />
                            <InputNumber v-model="filterForm.price_max" placeholder="Max $" mode="currency" currency="USD" locale="en-US" class="w-full" />
                        </div>

                        <div>
                            <Select v-model="filterForm.per_page" :options="perPageOptions" optionLabel="label"
                                optionValue="value" placeholder="Per Page" class="w-full" />
                        </div>

                        <div class="flex items-center">
                            <Button label="Clear" icon="pi pi-filter-slash" severity="secondary"
                                class="w-full" @click="resetFilters" />
                        </div>
                    </div>
                </div>

                <DataTable 
                    :value="products.data" 
                    v-model:selection="selectedProducts" 
                    dataKey="id" 
                    :paginator="false"
                    scrollable 
                    scrollHeight="600px"
                    class="w-full mt-3 pt-3" 
                    stripedRows 
                    responsiveLayout="scroll"
                >
                    <Column selectionMode="multiple" headerStyle="width: 3rem"></Column>

                    <!-- Thumbnail -->
                    <Column header="Image">
                        <template #body="{ data }">
                            <img v-if="data.thumbnail" :src="resolveImagePath(data.thumbnail)" alt="Thumbnail"
                                class="w-12 h-12 object-cover rounded shadow-sm" />
                            <span v-else class="text-gray-400 text-xs">No Image</span>
                        </template>
                    </Column>

                    <!-- Name & SKU -->
                    <Column field="name" header="Product" sortable>
                        <template #body="{ data }">
                            <div class="font-bold">{{ data.name }}</div>
                            <div class="text-xs text-gray-500">{{ data.sku }}</div>
                        </template>
                    </Column>

                    <!-- Type -->
                    <Column header="Type">
                        <template #body="{ data }">
                            <Badge :severity="data.type === 'variable' ? 'info' : 'secondary'" :value="data.type" />
                        </template>
                    </Column>

                    <!-- Category -->
                    <Column header="Category">
                        <template #body="{ data }">
                            {{ data.category?.name || "-" }}
                        </template>
                    </Column>

                    <!-- Price -->
                    <Column field="base_price" header="Price" sortable>
                        <template #body="{ data }">
                            {{ data.base_price }}
                        </template>
                    </Column>

                    <!-- Stock -->
                    <Column header="Stock" sortable field="total_stock">
                        <template #body="{ data }">
                            <div v-if="editingStock === data.id" class="flex items-center gap-2">
                                <Select v-model="editingWarehouseId" :options="warehouses" optionLabel="name"
                                    optionValue="id" filter class="w-32" />
                                <InputNumber v-model="editingStockValue" showButtons :min="0" class="w-20" />
                                <Button icon="pi pi-check" class="p-button-text p-button-success p-button-sm" @click="saveStockEdit(data)" />
                                <Button icon="pi pi-times" class="p-button-text p-button-danger p-button-sm" @click="cancelStockEdit" />
                            </div>
                            <div v-else>
                                <span class="font-bold cursor-pointer cursor-help"
                                    :title="getStockTooltip(data.stocks) + '\n\nDouble-click to edit'"
                                    :class="{
                                        'text-red-500': isOutOfStock(data),
                                        'text-orange-500': isLowStock(data),
                                        'text-green-600': !isOutOfStock(data) && !isLowStock(data)
                                    }"
                                    @dblclick="startStockEdit(data)">
                                    {{ getTotalStock(data) }}
                                </span>
                            </div>
                        </template>
                    </Column>

                    <!-- Stock Status -->
                    <Column header="Stock Status">
                        <template #body="{ data }">
                            <div class="flex flex-col gap-1 justify-between items-center">
                                <Badge v-if="isOutOfStock(data)" severity="danger" value="Out of Stock" />
                                <Badge v-else-if="isLowStock(data)" severity="warning" value="Low Stock" />
                                <Badge v-else severity="success" value="In Stock" />
                                <span class="text-xs text-gray-400">Active: {{ data.is_active ? 'Yes' : 'No' }}</span>
                            </div>
                        </template>
                    </Column>

                    <!-- Actions -->
                    <Column header="Actions" style="min-width: 140px">
                        <template #body="{ data }">
                            <div class="flex gap-1" v-if="!data.deleted_at">
                                <Button icon="pi pi-pencil" class="p-button-text p-button-sm p-button-info"
                                    @click="goEdit(data)" v-tooltip="'Edit'" />
                                <Button icon="pi pi-sort-alt" class="p-button-text p-button-sm p-button-help"
                                    @click="router.visit(route('admin.stock.move.form', data.id))"
                                    v-tooltip="'Stock Move'" />
                                <Button icon="pi pi-trash" class="p-button-text p-button-danger p-button-sm"
                                    @click="deleteProduct(data)" v-tooltip="'Trash'" />
                            </div>
                            <div class="flex gap-1" v-else>
                                <Button icon="pi pi-refresh" class="p-button-text p-button-sm p-button-success"
                                    @click="restoreProduct(data)" v-tooltip="'Restore'" />
                                <Button icon="pi pi-times" class="p-button-text p-button-sm p-button-danger"
                                    @click="forceDeleteProduct(data)" v-tooltip="'Permanently Delete'" />
                            </div>
                        </template>
                    </Column>
                </DataTable>

                <!-- Pagination -->
                <div class="mt-4 flex flex-wrap justify-between items-center"
                    v-if="products.links && products.links.length > 3">
                    <span class="text-sm text-gray-500">
                        Showing {{ products.from }} to {{ products.to }} of {{ products.total }} entries
                    </span>
                    <div class="flex gap-1">
                        <template v-for="(link, key) in products.links" :key="key">
                            <div v-if="link.url === null"
                                class="px-3 py-1 border rounded text-sm text-gray-400 cursor-not-allowed"
                                v-html="link.label" />
                            <button v-else class="px-3 py-1 border rounded text-sm hover:bg-gray-100"
                                :class="{ 'bg-blue-500 text-white font-bold hover:bg-blue-600': link.active }"
                                @click.prevent="visitLink(link.url)" v-html="link.label" />
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
