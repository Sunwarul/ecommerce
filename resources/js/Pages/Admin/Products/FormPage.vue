<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, router } from "@inertiajs/vue3";
import { computed } from "vue";
import { useI18n } from "vue-i18n";
import Button from "primevue/button";
import ProductForm from "./ProductForm.vue";

const { t } = useI18n();

const props = defineProps({
    product: { type: Object, default: null },
    categories: Array,
    brands: Array,
    taxes: Array,
    tags: Array,
    attributes: Array,
    warehouses: Array,
});

const isEditing = computed(() => !!props.product?.id);
const title = computed(() => (isEditing.value ? t("products.edit_product") : t("products.add_product")));

// The controller redirects to the list (with a flash message) after saving.
const handleCancel = () => router.visit(route("products.index"));
</script>

<template>
    <Head :title="title" />
    <AuthenticatedLayout>
        <div class="max-w-5xl mx-auto space-y-4">
            <div class="flex flex-wrap items-start justify-between gap-3">
                <div class="min-w-0">
                    <h1 class="text-xl md:text-2xl font-semibold text-slate-800 flex items-center gap-2">
                        <i :class="isEditing ? 'pi pi-pencil' : 'pi pi-plus-circle'" class="text-emerald-600" />
                        {{ title }}
                    </h1>
                    <p class="text-sm text-slate-500 truncate">
                        {{ isEditing ? product.name : t("catalog.form.create_subtitle") }}
                    </p>
                </div>
                <Button :label="t('catalog.common.back_to_list')" icon="pi pi-arrow-left" severity="secondary" outlined @click="handleCancel" />
            </div>

            <ProductForm :product="product" :is-editing="isEditing" :categories="categories" :brands="brands" :taxes="taxes"
                :tags="tags" :attributes="attributes" :warehouses="warehouses" @cancel="handleCancel" />
        </div>
    </AuthenticatedLayout>
</template>
