<script setup>
import { router } from "@inertiajs/vue3";
import { computed } from "vue";

import { resolveImagePath } from "@/Helpers/imageHelper";

// PrimeVue
import Button from "primevue/button";
import Column from "primevue/column";
import DataTable from "primevue/datatable";

// Local
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";

const props = defineProps({
    products: { type: Object, required: true }, // paginator from controller
});

// ============ TABLE & PAGINATION ============

const products = computed(() => props.products || { data: [], links: [] });

const visitLink = (url) => {
    if (!url) return;
    router.visit(url, {
        preserveScroll: true,
        preserveState: true,
    });
};

// ============ PAGE NAVIGATION ============

const goCreate = () => {
    router.visit(route("products.create"));
};

const goEdit = (rowProduct) => {
    router.visit(route("products.edit", rowProduct.id));
};

// ============ DELETE ============

const deleteProduct = (product) => {
    if (!confirm("Delete this product?")) return;

    router.delete(route("products.destroy", product.id), {
        preserveScroll: true,
        preserveState: true,
    });
};
</script>

<template>
    <AuthenticatedLayout>
        <div class="p-4 space-y-6">
            <!-- PRODUCTS TABLE -->
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
                    <Column field="thumbnail" header="Thumbnail">
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

                    <Column field="name" header="Name" />
                    <Column field="sku" header="SKU" />

                    <Column header="Category">
                        <template #body="{ data }">
                            {{ data.category?.name }}
                        </template>
                    </Column>

                    <Column header="Brand">
                        <template #body="{ data }">
                            {{ data.brand?.name }}
                        </template>
                    </Column>

                    <Column field="base_price" header="Base Price" />

                    <Column header="Actions">
                        <template #body="{ data }">
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
