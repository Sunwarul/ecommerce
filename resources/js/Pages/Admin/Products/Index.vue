<script setup>
import { Head, router } from "@inertiajs/vue3";
import { computed, ref, watch } from "vue";
import { useI18n } from "vue-i18n";
import axios from "axios";
import debounce from "lodash/debounce";

import { resolveImagePath } from "@/Helpers/imageHelper";
import { money, firstError } from "@/Helpers/format";
import { useFlashToast } from "@/Composables/useFlashToast";

// PrimeVue
import Button from "primevue/button";
import Column from "primevue/column";
import ConfirmDialog from "primevue/confirmdialog";
import DataTable from "primevue/datatable";
import IconField from "primevue/iconfield";
import InputIcon from "primevue/inputicon";
import InputNumber from "primevue/inputnumber";
import InputText from "primevue/inputtext";
import Select from "primevue/select";
import SelectButton from "primevue/selectbutton";
import SplitButton from "primevue/splitbutton";
import Tag from "primevue/tag";
import { useConfirm } from "primevue/useconfirm";
import { useToast } from "primevue/usetoast";

import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";

const { t } = useI18n();
const confirm = useConfirm();
const toast = useToast();
useFlashToast();

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
    sort: props.filters.sort || "id_desc",
});

const advancedKeys = ["warehouse_id", "stock_status", "stock_min", "stock_max", "status", "price_min", "price_max"];
const showMoreFilters = ref(advancedKeys.some((k) => filterForm.value[k] !== null && filterForm.value[k] !== ""));
const activeFilterCount = computed(() =>
    ["category_id", "brand_id", "type", ...advancedKeys].filter((k) => filterForm.value[k] !== null && filterForm.value[k] !== "").length
);

const statusOptions = computed(() => [
    { label: t("shared.status.active"), value: "active" },
    { label: t("shared.status.inactive"), value: "inactive" },
]);

const typeOptions = computed(() => [
    { label: t("catalog.products.type_simple"), value: "simple" },
    { label: t("catalog.products.type_variable"), value: "variable" },
]);

const stockStatusOptions = computed(() => [
    { label: t("catalog.stock.in_stock"), value: "in_stock" },
    { label: t("catalog.stock.low_stock"), value: "low_stock" },
    { label: t("catalog.stock.out_of_stock"), value: "out_of_stock" },
]);

const perPageOptions = [10, 25, 50, 100].map((v) => ({ label: String(v), value: v }));

const sortOptions = computed(() => [
    { label: t("catalog.sort.newest"), value: "id_desc" },
    { label: t("catalog.sort.oldest"), value: "id_asc" },
    { label: t("catalog.sort.name_asc"), value: "name_asc" },
    { label: t("catalog.sort.name_desc"), value: "name_desc" },
    { label: t("catalog.sort.price_asc"), value: "base_price_asc" },
    { label: t("catalog.sort.price_desc"), value: "base_price_desc" },
    { label: t("catalog.sort.stock_asc"), value: "total_stock_asc" },
    { label: t("catalog.sort.stock_desc"), value: "total_stock_desc" },
]);

// All products / Trash switch
const viewOptions = computed(() => [
    { label: t("catalog.products.all_products"), value: "all", icon: "pi pi-list" },
    { label: t("catalog.common.trash"), value: "trash", icon: "pi pi-trash" },
]);
const view = ref(props.filters.trashed === "only" ? "trash" : "all");
watch(view, (v) => {
    filterForm.value.trashed = v === "trash" ? "only" : null;
    selectedProducts.value = [];
});
const isTrashView = computed(() => filterForm.value.trashed === "only");

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

watch(() => filterForm.value, () => debouncedReload(), { deep: true });

const resetFilters = () => {
    filterForm.value = {
        search: "",
        category_id: null,
        brand_id: null,
        status: null,
        trashed: filterForm.value.trashed,
        per_page: 10,
        type: null,
        warehouse_id: null,
        stock_min: null,
        stock_max: null,
        price_min: null,
        price_max: null,
        stock_status: null,
        sort: "id_desc",
    };
};

// -----------------------------
// CONFIRM HELPER
// -----------------------------
function ask({ message, header, danger = false, accept }) {
    confirm.require({
        message,
        header: header || t("shared.terms.confirm_title"),
        icon: danger ? "pi pi-exclamation-triangle" : "pi pi-question-circle",
        rejectProps: { label: t("shared.terms.cancel"), severity: "secondary", outlined: true },
        acceptProps: { label: t("shared.terms.yes"), severity: danger ? "danger" : "primary" },
        accept,
    });
}

const onError = (errors) =>
    toast.add({ severity: "error", summary: t("shared.terms.error"), detail: firstError(errors), life: 4000 });

// -----------------------------
// SELECTION & BULK ACTIONS
// -----------------------------
const selectedProducts = ref([]);
const selectedIds = () => selectedProducts.value.map((p) => p.id);

const bulkActions = computed(() =>
    isTrashView.value
        ? [
              { label: t("catalog.bulk.restore"), icon: "pi pi-refresh", command: () => bulkRestore() },
              { label: t("catalog.bulk.force_delete"), icon: "pi pi-times", command: () => bulkForceDelete() },
          ]
        : [{ label: t("catalog.bulk.trash"), icon: "pi pi-trash", command: () => bulkDelete() }]
);

const clearSelection = () => (selectedProducts.value = []);

const bulkDelete = () => {
    if (!selectedProducts.value.length) return;
    ask({
        message: t("catalog.confirm.bulk_trash", { count: selectedProducts.value.length }),
        accept: () =>
            router.delete(route("products.bulk-delete"), {
                data: { ids: selectedIds() },
                preserveScroll: true,
                onSuccess: clearSelection,
                onError,
            }),
    });
};

const bulkRestore = () => {
    if (!selectedProducts.value.length) return;
    ask({
        message: t("catalog.confirm.bulk_restore", { count: selectedProducts.value.length }),
        accept: () =>
            router.post(route("products.bulk-restore"), { ids: selectedIds() }, { preserveScroll: true, onSuccess: clearSelection, onError }),
    });
};

const bulkForceDelete = () => {
    if (!selectedProducts.value.length) return;
    ask({
        danger: true,
        message: t("catalog.confirm.bulk_force_delete", { count: selectedProducts.value.length }),
        accept: () =>
            router.post(route("products.bulk-force-delete"), { ids: selectedIds() }, { preserveScroll: true, onSuccess: clearSelection, onError }),
    });
};

// -----------------------------
// ROW ACTIONS
// -----------------------------
const goCreate = () => router.visit(route("products.create"));
const goShow = (p) => router.visit(route("products.show", p.id));
const goEdit = (p) => router.visit(route("products.edit", p.id));
const goStockMove = (p) => router.visit(route("admin.stock.move.form", p.id));
const exportExcel = () => window.open(route("products.export", { search: filterForm.value.search || undefined }), "_blank");

const deleteProduct = (product) =>
    ask({
        message: t("catalog.confirm.trash_one", { name: product.name }),
        accept: () => router.delete(route("products.destroy", product.id), { preserveScroll: true, preserveState: true, onError }),
    });

const restoreProduct = (product) =>
    ask({
        message: t("catalog.confirm.restore_one", { name: product.name }),
        accept: () => router.post(route("products.restore", product.id), {}, { preserveScroll: true, preserveState: true, onError }),
    });

const forceDeleteProduct = (product) =>
    ask({
        danger: true,
        message: t("catalog.confirm.force_delete_one", { name: product.name }),
        accept: () => router.post(route("products.bulk-force-delete"), { ids: [product.id] }, { preserveScroll: true, onError }),
    });

// -----------------------------
// STOCK HELPERS
// -----------------------------
const getTotalStock = (p) => {
    if (p.total_stock !== undefined && p.total_stock !== null) return Number(p.total_stock);
    if (Array.isArray(p.stocks)) return p.stocks.reduce((sum, s) => sum + Number(s.quantity || 0), 0);
    return 0;
};

// Low-stock threshold = sum of the warehouses' alert quantities; 10 when none set (same default as the server filter)
const lowStockThreshold = (p) => {
    const alerts = (p.stocks || []).map((s) => Number(s.alert_quantity || 0)).filter((v) => v > 0);
    return alerts.length ? alerts.reduce((a, b) => a + b, 0) : 10;
};

const stockState = (p) => {
    const total = getTotalStock(p);
    if (total <= 0) return "out";
    if (total <= lowStockThreshold(p)) return "low";
    return "in";
};

const stockTag = (p) =>
    ({
        out: { severity: "danger", label: t("catalog.stock.out_of_stock"), icon: "pi pi-times-circle" },
        low: { severity: "warn", label: t("catalog.stock.low_stock"), icon: "pi pi-exclamation-circle" },
        in: { severity: "success", label: t("catalog.stock.in_stock"), icon: "pi pi-check-circle" },
    })[stockState(p)];

const getStockTooltip = (stocks) => {
    if (!stocks || stocks.length === 0) return t("catalog.stock.no_info");
    return stocks
        .map((s) => `${s.warehouse?.name || props.warehouses.find((w) => w.id === s.warehouse_id)?.name || "#" + s.warehouse_id}: ${Number(s.quantity).toFixed(0)}`)
        .join("\n");
};

const hasDiscount = (p) => Number(p.base_discount_price) > 0 && Number(p.base_discount_price) < Number(p.base_price);

// -----------------------------
// INLINE STOCK EDITING
// -----------------------------
const editingStock = ref(null);
const editingStockValue = ref(0);
const editingWarehouseId = ref(null);
const savingStock = ref(false);

const startStockEdit = (product) => {
    if (product.type === "variable" || product.deleted_at) return;
    editingStock.value = product.id;
    const first = (product.stocks || [])[0];
    editingWarehouseId.value = first?.warehouse_id ?? props.warehouses[0]?.id ?? null;
    editingStockValue.value = Number(first?.quantity ?? 0);
};

watch(editingWarehouseId, (wid) => {
    const product = products.value.data.find((p) => p.id === editingStock.value);
    if (!product) return;
    const row = (product.stocks || []).find((s) => s.warehouse_id === wid);
    editingStockValue.value = Number(row?.quantity ?? 0);
});

const saveStockEdit = async (product) => {
    if (editingStock.value === null || !editingWarehouseId.value) return;
    savingStock.value = true;
    try {
        await axios.put(route("products.update-stock", product.id), {
            warehouse_id: editingWarehouseId.value,
            quantity: editingStockValue.value,
        });
        editingStock.value = null;
        toast.add({ severity: "success", summary: t("shared.terms.success"), detail: t("catalog.stock.updated"), life: 2500 });
        router.reload({ preserveState: true, preserveScroll: true });
    } catch (error) {
        const errs = error?.response?.data?.errors;
        toast.add({
            severity: "error",
            summary: t("shared.terms.error"),
            detail: errs ? Object.values(errs)[0][0] : error?.response?.data?.message || t("catalog.stock.update_failed"),
            life: 4000,
        });
    } finally {
        savingStock.value = false;
    }
};

const cancelStockEdit = () => {
    editingStock.value = null;
    editingWarehouseId.value = null;
};

// -----------------------------
// PAGINATION
// -----------------------------
const visitLink = (url) => url && router.visit(url, { preserveScroll: true, preserveState: true });
const pageLinks = computed(() => (products.value.links || []).slice(1, -1));
const prevLink = computed(() => (products.value.links || [])[0]?.url || null);
const nextLink = computed(() => {
    const links = products.value.links || [];
    return links[links.length - 1]?.url || null;
});
</script>

<template>
    <Head :title="t('menu.products')" />
    <AuthenticatedLayout>
        <ConfirmDialog :breakpoints="{ '640px': '95vw' }" :style="{ width: '28rem' }" />

        <div class="space-y-4">
            <!-- Page header -->
            <div class="flex flex-wrap items-start justify-between gap-3">
                <div class="min-w-0">
                    <h1 class="text-xl md:text-2xl font-semibold text-slate-800 flex items-center gap-2">
                        <i class="pi pi-box text-emerald-600" />
                        {{ t("menu.products") }}
                    </h1>
                    <p class="text-sm text-slate-500">{{ t("catalog.products.subtitle") }}</p>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <Button :label="t('shared.terms.export')" icon="pi pi-file-excel" severity="secondary" outlined @click="exportExcel" />
                    <Button :label="t('catalog.products.new')" icon="pi pi-plus" @click="goCreate" />
                </div>
            </div>

            <div class="card !mb-0 space-y-4">
                <!-- View switch + bulk -->
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <SelectButton v-model="view" :options="viewOptions" optionLabel="label" optionValue="value" :allowEmpty="false">
                        <template #option="{ option }">
                            <i :class="option.icon" class="text-sm" />
                            <span class="text-sm">{{ option.label }}</span>
                        </template>
                    </SelectButton>

                    <div v-if="selectedProducts.length" class="flex items-center gap-2">
                        <SplitButton :label="t('catalog.bulk.selected', { count: selectedProducts.length })" :model="bulkActions"
                            :severity="isTrashView ? 'danger' : 'secondary'" size="small"
                            @click="isTrashView ? bulkRestore() : bulkDelete()" />
                        <Button icon="pi pi-times" text rounded size="small" severity="secondary" @click="clearSelection"
                            v-tooltip.top="t('catalog.bulk.clear')" :aria-label="t('catalog.bulk.clear')" />
                    </div>
                </div>

                <!-- Filters -->
                <div class="space-y-3">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
                        <IconField class="sm:col-span-2 lg:col-span-2">
                            <InputIcon class="pi pi-search" />
                            <InputText type="search" v-model="filterForm.search" :placeholder="t('catalog.products.search_placeholder')" class="w-full" />
                        </IconField>
                        <Select v-model="filterForm.category_id" :options="categories" optionLabel="name" optionValue="id"
                            :placeholder="t('products.category')" filter showClear class="w-full" />
                        <Select v-model="filterForm.brand_id" :options="brands" optionLabel="name" optionValue="id"
                            :placeholder="t('products.brand')" filter showClear class="w-full" />
                        <Select v-model="filterForm.type" :options="typeOptions" optionLabel="label" optionValue="value"
                            :placeholder="t('catalog.products.type')" showClear class="w-full" />
                    </div>

                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <Button :label="showMoreFilters ? t('catalog.filters.fewer') : t('catalog.filters.more')"
                            :icon="showMoreFilters ? 'pi pi-chevron-up' : 'pi pi-sliders-h'" text size="small"
                            :badge="activeFilterCount ? String(activeFilterCount) : undefined" badgeSeverity="contrast"
                            @click="showMoreFilters = !showMoreFilters" />
                        <div class="flex flex-wrap items-center gap-2">
                            <Select v-model="filterForm.sort" :options="sortOptions" optionLabel="label" optionValue="value"
                                class="w-48" size="small" :aria-label="t('catalog.sort.label')" />
                            <Button :label="t('catalog.filters.clear')" icon="pi pi-filter-slash" severity="secondary" text size="small"
                                @click="resetFilters" />
                        </div>
                    </div>

                    <div v-if="showMoreFilters" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 p-3 rounded-xl bg-slate-50 border border-slate-200">
                        <Select v-model="filterForm.warehouse_id" :options="warehouses" optionLabel="name" optionValue="id"
                            :placeholder="t('catalog.stock.warehouse')" filter showClear class="w-full" />
                        <Select v-model="filterForm.stock_status" :options="stockStatusOptions" optionLabel="label" optionValue="value"
                            :placeholder="t('catalog.stock.status')" showClear class="w-full" />
                        <Select v-model="filterForm.status" :options="statusOptions" optionLabel="label" optionValue="value"
                            :placeholder="t('common.status')" showClear class="w-full" />
                        <div class="grid grid-cols-2 gap-2">
                            <InputNumber v-model="filterForm.stock_min" :placeholder="t('catalog.filters.min_stock')" :min="0" class="w-full" inputClass="w-full" />
                            <InputNumber v-model="filterForm.stock_max" :placeholder="t('catalog.filters.max_stock')" :min="0" class="w-full" inputClass="w-full" />
                        </div>
                        <div class="grid grid-cols-2 gap-2 sm:col-span-2 lg:col-span-1">
                            <InputNumber v-model="filterForm.price_min" :placeholder="t('catalog.filters.min_price')" :min="0" :maxFractionDigits="2"
                                class="w-full" inputClass="w-full" />
                            <InputNumber v-model="filterForm.price_max" :placeholder="t('catalog.filters.max_price')" :min="0" :maxFractionDigits="2"
                                class="w-full" inputClass="w-full" />
                        </div>
                    </div>
                </div>

                <!-- Desktop table -->
                <div class="hidden md:block">
                    <DataTable :value="products.data" v-model:selection="selectedProducts" dataKey="id" class="w-full" stripedRows size="small" :rowClass="(r) => (r.deleted_at ? 'opacity-70' : '')">
                        <template #empty>
                            <div class="py-10 text-center text-slate-500">
                                <i class="pi pi-inbox text-3xl mb-2 block" />
                                {{ isTrashView ? t("catalog.products.empty_trash") : t("catalog.products.empty") }}
                            </div>
                        </template>

                        <Column selectionMode="multiple" headerStyle="width: 3rem" />

                        <Column :header="t('catalog.products.product')" style="min-width: 16rem">
                            <template #body="{ data }">
                                <div class="flex items-center gap-3 min-w-0">
                                    <img v-if="data.thumbnail" :src="resolveImagePath(data.thumbnail)" :alt="data.name"
                                        class="w-11 h-11 object-cover rounded-lg border border-slate-200 shrink-0" loading="lazy" />
                                    <div v-else class="w-11 h-11 rounded-lg bg-slate-100 text-slate-400 flex items-center justify-center shrink-0">
                                        <i class="pi pi-image" />
                                    </div>
                                    <div class="min-w-0">
                                        <button type="button" class="font-semibold text-slate-800 hover:text-emerald-700 text-start truncate block max-w-[18rem]"
                                            :disabled="!!data.deleted_at" @click="goShow(data)">
                                            {{ data.name }}
                                        </button>
                                        <div class="text-xs text-slate-500 ltr-nums truncate">{{ data.sku || "—" }}</div>
                                    </div>
                                </div>
                            </template>
                        </Column>

                        <Column :header="t('catalog.products.type')">
                            <template #body="{ data }">
                                <Tag :severity="data.type === 'variable' ? 'info' : 'secondary'"
                                    :value="data.type === 'variable' ? t('catalog.products.type_variable') : t('catalog.products.type_simple')" />
                            </template>
                        </Column>

                        <Column :header="t('products.category')">
                            <template #body="{ data }">
                                <div class="text-sm">{{ data.category?.name || "—" }}</div>
                                <div v-if="data.brand?.name" class="text-xs text-slate-500">{{ data.brand.name }}</div>
                            </template>
                        </Column>

                        <Column :header="t('common.price')" bodyClass="text-end" headerClass="[&>div]:justify-end">
                            <template #body="{ data }">
                                <div class="font-semibold text-slate-800 ltr-nums">{{ money(hasDiscount(data) ? data.base_discount_price : data.base_price) }}</div>
                                <div v-if="hasDiscount(data)" class="text-xs text-slate-400 line-through ltr-nums">{{ money(data.base_price) }}</div>
                            </template>
                        </Column>

                        <Column :header="t('products.stock')" style="min-width: 11rem">
                            <template #body="{ data }">
                                <div v-if="editingStock === data.id" class="flex flex-col gap-1.5">
                                    <Select v-model="editingWarehouseId" :options="warehouses" optionLabel="name" optionValue="id"
                                        filter size="small" class="w-44" />
                                    <div class="flex items-center gap-1">
                                        <InputNumber v-model="editingStockValue" :min="0" size="small" inputClass="!w-20" @keyup.enter="saveStockEdit(data)" />
                                        <Button icon="pi pi-check" text rounded size="small" severity="success" :loading="savingStock"
                                            @click="saveStockEdit(data)" :aria-label="t('shared.terms.save')" />
                                        <Button icon="pi pi-times" text rounded size="small" severity="secondary"
                                            @click="cancelStockEdit" :aria-label="t('shared.terms.cancel')" />
                                    </div>
                                </div>
                                <div v-else class="flex items-center gap-2">
                                    <span class="font-semibold text-slate-800 ltr-nums" v-tooltip.top="getStockTooltip(data.stocks)">
                                        {{ getTotalStock(data) }}
                                    </span>
                                    <Tag :severity="stockTag(data).severity" :value="stockTag(data).label" class="!text-[11px]" />
                                    <Button v-if="data.type !== 'variable' && !data.deleted_at && warehouses.length" icon="pi pi-pencil"
                                        text rounded size="small" severity="secondary" class="!w-7 !h-7"
                                        v-tooltip.top="t('catalog.stock.quick_edit')" :aria-label="t('catalog.stock.quick_edit')"
                                        @click="startStockEdit(data)" />
                                </div>
                            </template>
                        </Column>

                        <Column :header="t('common.status')">
                            <template #body="{ data }">
                                <Tag v-if="data.deleted_at" severity="danger" :value="t('catalog.common.trashed')" />
                                <Tag v-else :severity="data.is_active ? 'success' : 'secondary'"
                                    :value="data.is_active ? t('shared.status.active') : t('shared.status.inactive')" />
                            </template>
                        </Column>

                        <Column :header="t('common.actions')" style="min-width: 10rem" headerClass="[&>div]:justify-end">
                            <template #body="{ data }">
                                <div class="flex justify-end gap-1" v-if="!data.deleted_at">
                                    <Button icon="pi pi-eye" text rounded size="small" severity="secondary" @click="goShow(data)"
                                        v-tooltip.top="t('common.view')" :aria-label="t('common.view')" />
                                    <Button icon="pi pi-pencil" text rounded size="small" severity="info" @click="goEdit(data)"
                                        v-tooltip.top="t('common.edit')" :aria-label="t('common.edit')" />
                                    <Button icon="pi pi-arrow-right-arrow-left" text rounded size="small" severity="help" @click="goStockMove(data)"
                                        v-tooltip.top="t('catalog.stock.move')" :aria-label="t('catalog.stock.move')" />
                                    <Button icon="pi pi-trash" text rounded size="small" severity="danger" @click="deleteProduct(data)"
                                        v-tooltip.top="t('catalog.common.move_to_trash')" :aria-label="t('catalog.common.move_to_trash')" />
                                </div>
                                <div class="flex justify-end gap-1" v-else>
                                    <Button icon="pi pi-refresh" text rounded size="small" severity="success" @click="restoreProduct(data)"
                                        v-tooltip.top="t('catalog.common.restore')" :aria-label="t('catalog.common.restore')" />
                                    <Button icon="pi pi-times" text rounded size="small" severity="danger" @click="forceDeleteProduct(data)"
                                        v-tooltip.top="t('catalog.common.delete_forever')" :aria-label="t('catalog.common.delete_forever')" />
                                </div>
                            </template>
                        </Column>
                    </DataTable>
                </div>

                <!-- Mobile card list -->
                <div class="md:hidden space-y-2">
                    <div v-if="!products.data.length" class="py-10 text-center text-slate-500">
                        <i class="pi pi-inbox text-3xl mb-2 block" />
                        {{ isTrashView ? t("catalog.products.empty_trash") : t("catalog.products.empty") }}
                    </div>
                    <div v-for="p in products.data" :key="p.id" class="rounded-xl border border-slate-200 bg-white p-3">
                        <div class="flex gap-3">
                            <img v-if="p.thumbnail" :src="resolveImagePath(p.thumbnail)" :alt="p.name"
                                class="w-14 h-14 object-cover rounded-lg border border-slate-200 shrink-0" loading="lazy" />
                            <div v-else class="w-14 h-14 rounded-lg bg-slate-100 text-slate-400 flex items-center justify-center shrink-0">
                                <i class="pi pi-image" />
                            </div>
                            <div class="min-w-0 flex-1">
                                <button type="button" class="font-semibold text-slate-800 text-start leading-snug" :disabled="!!p.deleted_at" @click="goShow(p)">
                                    {{ p.name }}
                                </button>
                                <div class="text-xs text-slate-500 ltr-nums">{{ p.sku || "—" }}</div>
                                <div class="mt-1 flex flex-wrap items-center gap-1.5">
                                    <Tag :severity="stockTag(p).severity" :value="`${getTotalStock(p)} · ${stockTag(p).label}`" class="!text-[11px]" />
                                    <Tag v-if="p.type === 'variable'" severity="info" :value="t('catalog.products.type_variable')" class="!text-[11px]" />
                                    <Tag v-if="p.deleted_at" severity="danger" :value="t('catalog.common.trashed')" class="!text-[11px]" />
                                    <Tag v-else-if="!p.is_active" severity="secondary" :value="t('shared.status.inactive')" class="!text-[11px]" />
                                </div>
                            </div>
                            <div class="text-end shrink-0">
                                <div class="font-bold text-slate-800 ltr-nums">{{ money(hasDiscount(p) ? p.base_discount_price : p.base_price) }}</div>
                                <div v-if="hasDiscount(p)" class="text-xs text-slate-400 line-through ltr-nums">{{ money(p.base_price) }}</div>
                            </div>
                        </div>
                        <div class="mt-2 pt-2 border-t border-slate-100 flex items-center justify-between gap-2">
                            <div class="text-xs text-slate-500 truncate">{{ p.category?.name || "—" }}</div>
                            <div class="flex gap-1">
                                <template v-if="!p.deleted_at">
                                    <Button icon="pi pi-pencil" text rounded size="small" severity="info" @click="goEdit(p)" :aria-label="t('common.edit')" />
                                    <Button icon="pi pi-arrow-right-arrow-left" text rounded size="small" severity="help" @click="goStockMove(p)"
                                        :aria-label="t('catalog.stock.move')" />
                                    <Button icon="pi pi-trash" text rounded size="small" severity="danger" @click="deleteProduct(p)"
                                        :aria-label="t('catalog.common.move_to_trash')" />
                                </template>
                                <template v-else>
                                    <Button icon="pi pi-refresh" text rounded size="small" severity="success" @click="restoreProduct(p)"
                                        :aria-label="t('catalog.common.restore')" />
                                    <Button icon="pi pi-times" text rounded size="small" severity="danger" @click="forceDeleteProduct(p)"
                                        :aria-label="t('catalog.common.delete_forever')" />
                                </template>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pagination -->
                <div class="flex flex-wrap justify-between items-center gap-3 pt-1" v-if="products.total">
                    <div class="flex items-center gap-2 text-sm text-slate-500">
                        <span>{{ t("shared.terms.showing", { from: products.from ?? 0, to: products.to ?? 0, total: products.total }) }}</span>
                        <Select v-model="filterForm.per_page" :options="perPageOptions" optionLabel="label" optionValue="value"
                            size="small" class="w-20" :aria-label="t('catalog.filters.per_page')" />
                    </div>
                    <div class="flex items-center gap-1" v-if="products.last_page > 1">
                        <Button icon="pi pi-angle-left" text rounded size="small" :disabled="!prevLink" @click="visitLink(prevLink)"
                            :aria-label="t('shared.terms.previous')" />
                        <template v-for="(link, key) in pageLinks" :key="key">
                            <span v-if="!link.url" class="hidden sm:inline px-2 text-slate-400">…</span>
                            <button v-else type="button" class="hidden sm:inline-flex min-w-8 h-8 px-2 items-center justify-center rounded-lg text-sm ltr-nums"
                                :class="link.active ? 'bg-emerald-600 text-white font-semibold' : 'text-slate-600 hover:bg-slate-100'"
                                @click="visitLink(link.url)">
                                {{ link.label }}
                            </button>
                        </template>
                        <span class="sm:hidden text-sm text-slate-600 px-2 ltr-nums">{{ products.current_page }} / {{ products.last_page }}</span>
                        <Button icon="pi pi-angle-right" text rounded size="small" :disabled="!nextLink" @click="visitLink(nextLink)"
                            :aria-label="t('shared.terms.next')" />
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
