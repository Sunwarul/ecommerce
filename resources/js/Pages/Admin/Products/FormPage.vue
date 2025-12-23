<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Link, router } from "@inertiajs/vue3";
import { computed } from "vue";
import ProductForm from "./ProductForm.vue";

const props = defineProps({
    product: { type: Object, default: null },
    categories: Array,
    brands: Array,
    taxes: Array,
    tags: Array,
    attributes: Array,
    warehouses: Array,
});
console.log("debug data", props.product);

const isEditing = computed(() => !!props.product?.id);

/* ✅ handle emitted events here */
const handleSaved = (data) => {
    console.log("Product saved:", data);

    // example redirect
    router.visit(route("admin.products.index"));
};

const handleCancel = () => {
    console.log("Form cancelled");

    router.visit(route("admin.products.index"));
};
</script>

<template>
    <AuthenticatedLayout>
        <div class="p-6 card">
            <div class="flex justify-between items-center">
                <h1 class="text-xl font-semibold mb-4">
                    {{ isEditing ? "Update Product" : "Create Product" }}
                </h1>
                <!-- back to list -->
                <Link
                    :href="route('products.index')"
                    class="px-4 py-2 bg-gray-300 text-gray-800 rounded-md hover:bg-gray-400"
                >
                    Back to List
                </Link>
            </div>

            <ProductForm
                :product="product"
                :is-editing="isEditing"
                :categories="categories"
                :brands="brands"
                :taxes="taxes"
                :tags="tags"
                :attributes="attributes"
                :warehouses="warehouses"
                @saved="handleSaved"
                @cancel="handleCancel"
            />
        </div>
    </AuthenticatedLayout>
</template>
