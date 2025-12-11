<script setup>
import { router } from "@inertiajs/vue3";
import axios from "axios";
import { computed, ref } from "vue";

import { resolveImagePath } from "@/Helpers/imageHelper";

// PrimeVue
import Button from "primevue/button";
import Column from "primevue/column";
import DataTable from "primevue/datatable";
import Dialog from "primevue/dialog";

// Local
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import ProductForm from "./ProductForm.vue";

const props = defineProps({
    products: { type: Object, required: true }, // paginator from controller
    categories: { type: Array, default: () => [] },
    brands: { type: Array, default: () => [] },
    taxes: { type: Array, default: () => [] },
    tags: { type: Array, default: () => [] },
    attributes: { type: Array, default: () => [] },
    warehouses: { type: Array, default: () => [] },
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

// ============ MODAL / FORM STATE ============

const showFormDialog = ref(false);
const isEditing = ref(false);
const editingProduct = ref(null);
const isFormLoading = ref(false); // 👈 loading state for edit

const openCreate = () => {
    isEditing.value = false;
    editingProduct.value = null;
    isFormLoading.value = false; // no need to load anything
    showFormDialog.value = true;
};

const openEdit = async (rowProduct) => {
    isEditing.value = true;
    showFormDialog.value = true;
    isFormLoading.value = true;
    editingProduct.value = null;

    try {
        // call API to get full product with relationships
        const response = await axios.get(
            route("products.edit-data", rowProduct.id)
        );

        editingProduct.value = response.data;
    } catch (error) {
        console.error("Failed to load product edit data", error);
        // optional: show toast / alert
        showFormDialog.value = false;
    } finally {
        isFormLoading.value = false;
    }
};

const closeDialog = () => {
    showFormDialog.value = false;
};

const handleSaved = () => {
    showFormDialog.value = false;
    // reload to see updated table
    router.reload({ preserveScroll: true, preserveState: true });
};

const deleteProduct = (product) => {
    if (!confirm("Delete this product?")) return;

    router.delete(route("products.destroy", product.id), {
        preserveScroll: true,
        onSuccess: () => {
            // optional: toast
        },
    });
};

console.log(props.products);
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
                        @click="openCreate"
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
                                @click="openEdit(data)"
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

            <!-- MODAL WITH FORM -->
            <Dialog
                v-model:visible="showFormDialog"
                :modal="true"
                :style="{ width: '80vw', maxWidth: '1100px' }"
                :breakpoints="{ '960px': '95vw', '640px': '100vw' }"
                :header="isEditing ? 'Update Product' : 'Create Product'"
            >
                <!-- loader state while fetching edit data -->
                <div
                    v-if="isEditing && isFormLoading"
                    class="py-10 flex flex-col items-center justify-center gap-2"
                >
                    <i class="pi pi-spin pi-spinner text-2xl" />
                    <span>Loading product data...</span>
                </div>

                <!-- form only when not loading -->
                <ProductForm
                    v-else
                    :product="editingProduct"
                    :categories="categories"
                    :brands="brands"
                    :taxes="taxes"
                    :tags="tags"
                    :attributes="attributes"
                    :warehouses="warehouses"
                    :is-editing="isEditing"
                    @cancel="closeDialog"
                    @saved="handleSaved"
                />
            </Dialog>
        </div>
    </AuthenticatedLayout>
</template>
