<script setup>
import { router } from "@inertiajs/vue3";
import { computed } from "vue";

import { resolveImagePath } from "@/Helpers/imageHelper";

// PrimeVue
import Badge from "primevue/badge";
import Button from "primevue/button";
import Column from "primevue/column";
import DataTable from "primevue/datatable";

// Local
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";

const props = defineProps({
    products: { type: Object, required: true }, // paginator from controller
});

const products = computed(() => props.products || { data: [], links: [] });

const visitLink = (url) => {
    if (!url) return;
    router.visit(url, { preserveScroll: true, preserveState: true });
};

// Navigation
const goCreate = () => router.visit(route("products.create"));
const goShow = (rowProduct) =>
    router.visit(route("products.show", rowProduct.id));
const goEdit = (rowProduct) =>
    router.visit(route("products.edit", rowProduct.id));

// Delete
const deleteProduct = (product) => {
    if (!confirm("Delete this product?")) return;

    router.delete(route("products.destroy", product.id), {
        preserveScroll: true,
        preserveState: true,
    });
};

// Stock helper (works even if you don't send total_stock yet)
const getTotalStock = (p) => {
    if (p.total_stock !== undefined && p.total_stock !== null)
        return Number(p.total_stock);
    if (Array.isArray(p.stocks)) {
        return p.stocks.reduce((sum, s) => sum + Number(s.quantity || 0), 0);
    }
    return 0;
};
</script>

<template>
    <AuthenticatedLayout>
        <div class="space-y-6">
            <div class="card">
                <div class="flex justify-between items-center mb-3">
                    <h2 class="text-xl font-semibold">Products</h2>
                    <Button
                        label="New Product"
                        icon="pi pi-plus"
                        class="p-button-sm"
                        @click="goCreate"
                    />
                </div>

                <DataTable
                    :value="products.data"
                    dataKey="id"
                    :paginator="false"
                    class="w-full"
                >
                    <!-- Thumbnail -->
                    <Column header="Thumbnail">
                        <template #body="{ data }">
                            <img
                                v-if="data.thumbnail"
                                :src="resolveImagePath(data.thumbnail)"
                                alt="Thumbnail"
                                class="w-10 h-10 object-cover rounded"
                            />
                            <span v-else class="text-gray-500">No image</span>
                        </template>
                    </Column>

                    <!-- Name -->
                    <Column field="name" header="Name" />

                    <!-- Type -->
                    <Column header="Type">
                        <template #body="{ data }">
                            <Badge
                                :severity="
                                    data.type === 'variable'
                                        ? 'info'
                                        : 'secondary'
                                "
                                :value="data.type"
                            />
                        </template>
                    </Column>

                    <!-- SKU -->
                    <Column field="sku" header="SKU" />

                    <!-- Category -->
                    <Column header="Category">
                        <template #body="{ data }">
                            {{ data.category?.name || "-" }}
                        </template>
                    </Column>

                    <!-- Brand -->
                    <Column header="Brand">
                        <template #body="{ data }">
                            {{ data.brand?.name || "-" }}
                        </template>
                    </Column>

                    <!-- Base Price -->
                    <Column field="base_price" header="Base Price" />

                    <!-- Total Stock -->
                    <Column header="Total Stock">
                        <template #body="{ data }">
                            <span class="font-medium">{{
                                getTotalStock(data)
                            }}</span>
                        </template>
                    </Column>

                    <!-- Status -->
                    <Column header="Status">
                        <template #body="{ data }">
                            <Badge
                                :severity="
                                    data.is_active ? 'success' : 'danger'
                                "
                            >
                                {{ data.is_active ? "Active" : "Inactive" }}
                            </Badge>
                        </template>
                    </Column>

                    <!-- Actions -->
                    <Column header="Actions">
                        <template #body="{ data }">
                            <Button
                                label="View"
                                icon="pi pi-eye"
                                class="p-button-text p-button-sm mr-1"
                                @click="goShow(data)"
                            />
                            <Button
                                label="Edit"
                                icon="pi pi-pencil"
                                class="p-button-text p-button-sm mr-1"
                                @click="goEdit(data)"
                            />
                            <Button
                                label="Delete"
                                icon="pi pi-trash"
                                class="p-button-text p-button-danger p-button-sm"
                                @click="deleteProduct(data)"
                            />
                            <Button
                                label="Stock Movement"
                                icon="pi pi-sort-alt"
                                class="p-button-secondary"
                                @click="
                                    router.visit(
                                        route(
                                            'admin.stock.move.form',
                                            data.id
                                        )
                                    )
                                "
                            />
                        </template>
                    </Column>
                </DataTable>

                <!-- Laravel pagination links -->
                <div class="mt-3 flex flex-wrap gap-1">
                    <button
                        v-for="link in products.links || []"
                        :key="(link?.url || '') + link.label"
                        v-if="link?.url"
                        class="px-3 py-1 border rounded text-sm"
                        :class="{ 'bg-gray-200 font-semibold': link.active }"
                        @click.prevent="visitLink(link.url)"
                        v-html="link.label"
                    />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
