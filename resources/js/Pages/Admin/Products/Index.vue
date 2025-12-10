<template>
    <div>
        <CrudComponent :form="form" >
            <!-- TABLE COLUMNS -->
            <template #columns>
                <Column field="thumbnail" header="Thumbnail">
                    <template #body="{ data }">
                        <img
                            :src="resolveImagePath(data.thumbnail)"
                            alt="Thumbnail"
                            class="w-10 h-10 object-cover rounded"
                        />
                    </template>
                </Column>
                <Column field="name" header="Name"></Column>
                <Column field="sku" header="SKU"></Column>
                <Column field="category.name" header="Category"></Column>
                <Column field="brand.name" header="Brand"></Column>
                <Column field="base_price" header="Base Price"></Column>
            </template>

            <!-- FORM -->
            <template #form="{ submitted, handlePhotoUpload, photoPreview }">
                <!-- Basic Information Section -->
                <div class="col-12 mb-3">
                    <h3 class="text-xl font-semibold uppercase">
                        Basic Information
                    </h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Name -->
                    <div class="field col-12 sm:col-6 mb-4 pr-md-2">
                        <label for="name" class="block font-bold mb-2"
                            >Name *</label
                        >
                        <InputText
                            id="name"
                            v-model.trim="form.name"
                            required
                            class="w-full"
                            :class="{ 'p-invalid': submitted && !form.name }"
                        />
                        <small v-if="submitted && !form.name" class="p-error"
                            >Name is required.</small
                        >
                    </div>

                    <!-- Slug -->
                    <div class="field col-12 sm:col-6 mb-4 pl-md-2">
                        <label for="slug" class="block font-bold mb-2"
                            >Slug *</label
                        >
                        <InputText
                            id="slug"
                            v-model.trim="form.slug"
                            required
                            class="w-full"
                            :class="{ 'p-invalid': submitted && !form.slug }"
                        />
                        <small v-if="submitted && !form.slug" class="p-error"
                            >Slug is required.</small
                        >
                    </div>

                    <!-- SKU -->
                    <div class="field col-12 md:col-4 mb-4 pr-md-2">
                        <label for="sku" class="block font-bold mb-2"
                            >SKU</label
                        >
                        <InputText
                            id="sku"
                            v-model.trim="form.sku"
                            class="w-full"
                        />
                    </div>

                    <!-- Barcode -->
                    <div class="field col-12 md:col-4 mb-4 px-md-2">
                        <label for="barcode" class="block font-bold mb-2"
                            >Barcode</label
                        >
                        <InputText
                            id="barcode"
                            v-model.trim="form.barcode"
                            class="w-full"
                        />
                    </div>

                    <!-- Product Code -->
                    <div class="field col-12 md:col-4 mb-4 pl-md-2">
                        <label for="code" class="block font-bold mb-2"
                            >Product Code</label
                        >
                        <InputText
                            id="code"
                            v-model.trim="form.code"
                            class="w-full"
                        />
                    </div>

                    <!-- Product Type -->
                    <div class="field col-12 md:col-4 mb-4 pr-md-2">
                        <label for="type" class="block font-bold mb-2"
                            >Product Type *</label
                        >
                        <Dropdown
                            id="type"
                            v-model="form.type"
                            :options="productTypes"
                            optionLabel="label"
                            optionValue="value"
                            placeholder="Select Type"
                            class="w-full"
                            :class="{ 'p-invalid': submitted && !form.type }"
                        />
                        <small v-if="submitted && !form.type" class="p-error">
                            Product type is required.
                        </small>
                    </div>

                    <!-- Stock Status (top-level, required by request) -->
                    <div class="field col-12 md:col-4 mb-4 px-md-2">
                        <label for="stock_status" class="block font-bold mb-2"
                            >Stock Status *</label
                        >
                        <Dropdown
                            id="stock_status"
                            v-model="form.stock_status"
                            :options="stockStatuses"
                            optionLabel="label"
                            optionValue="value"
                            placeholder="Select Status"
                            class="w-full"
                            :class="{
                                'p-invalid': submitted && !form.stock_status,
                            }"
                        />
                        <small
                            v-if="submitted && !form.stock_status"
                            class="p-error"
                        >
                            Stock status is required.
                        </small>
                    </div>

                    <!-- Active / Inactive -->
                    <div class="field col-12 md:col-4 mb-4 pl-md-2">
                        <label for="status" class="block font-bold mb-2"
                            >Status</label
                        >
                        <div class="pt-2 flex items-center">
                            <ToggleSwitch v-model="form.is_active" />
                            <span class="ml-2">{{
                                form.is_active ? "Active" : "Inactive"
                            }}</span>
                        </div>
                    </div>
                </div>

                <!-- Pricing & Inventory Section -->
                <div class="col-12 mb-3 mt-3">
                    <h3 class="text-xl font-semibold uppercase">
                        Pricing & Inventory
                    </h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Base Price -->
                    <div class="field col-12 sm:col-6 mb-4 pr-md-2">
                        <label for="base_price" class="block font-bold mb-2"
                            >Base Price *</label
                        >
                        <InputNumber
                            id="base_price"
                            v-model.number="form.base_price"
                            class="w-full"
                            :min="0"
                            :class="{
                                'p-invalid': submitted && !form.base_price,
                            }"
                        />
                        <small
                            v-if="submitted && !form.base_price"
                            class="p-error"
                        >
                            Base price is required.
                        </small>
                    </div>

                    <!-- Discount Price -->
                    <div class="field col-12 sm:col-6 mb-4 pl-md-2">
                        <label
                            for="base_discount_price"
                            class="block font-bold mb-2"
                            >Discount Price</label
                        >
                        <InputNumber
                            id="base_discount_price"
                            v-model.number="form.base_discount_price"
                            class="w-full"
                            :min="0"
                            :max="form.base_price || null"
                        />
                        <small
                            v-if="
                                form.base_discount_price &&
                                form.base_discount_price >= form.base_price
                            "
                            class="p-error"
                        >
                            Discount price must be less than base price.
                        </small>
                    </div>

                    <!-- Stock Quantity (used for simple) -->
                    <div class="field col-12 sm:col-6 mb-4">
                        <label for="stock_quantity" class="block font-bold mb-2"
                            >Stock Quantity (simple)</label
                        >
                        <InputNumber
                            id="stock_quantity"
                            v-model.number="form.stock_quantity"
                            class="w-full"
                            :min="0"
                        />
                    </div>

                    <!-- Warehouse (required_if:type,simple) -->
                    <div
                        class="field col-12 sm:col-6 mb-4"
                        v-if="form.type === 'simple'"
                    >
                        <label for="warehouse_id" class="block font-bold mb-2"
                            >Warehouse *</label
                        >
                        <Dropdown
                            id="warehouse_id"
                            v-model="form.warehouse_id"
                            :options="warehouses"
                            optionLabel="name"
                            optionValue="id"
                            placeholder="Select Warehouse"
                            class="w-full"
                            :class="{
                                'p-invalid':
                                    submitted &&
                                    form.type === 'simple' &&
                                    !form.warehouse_id,
                            }"
                        />
                        <small
                            v-if="
                                submitted &&
                                form.type === 'simple' &&
                                !form.warehouse_id
                            "
                            class="p-error"
                        >
                            Warehouse is required for simple products.
                        </small>
                    </div>
                </div>

                <!-- Categories & Tags Section -->
                <div class="col-12 mb-3 mt-3">
                    <h3 class="text-xl font-semibold uppercase">
                        Categories & Tags
                    </h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Category (TreeSelect) -->
                    <div class="field col-12 sm:col-6 mb-4 pr-md-2">
                        <label for="category_id" class="block font-bold mb-2"
                            >Category *</label
                        >
                        <TreeSelect
                            id="category_id"
                            v-model="selectedCategoryKey"
                            :options="categoryTreeNodes"
                            placeholder="Select Category"
                            class="w-full"
                            selectionMode="single"
                            :class="{
                                'p-invalid': submitted && !form.category_id,
                            }"
                        />

                        <small
                            v-if="submitted && !form.category_id"
                            class="p-error"
                        >
                            Category is required.
                        </small>
                    </div>

                    <!-- Brand -->
                    <div class="field col-12 sm:col-6 mb-4 pr-md-2">
                        <label for="brand_id" class="block font-bold mb-2"
                            >Brand</label
                        >
                        <Dropdown
                            id="brand_id"
                            v-model="form.brand_id"
                            :options="brands"
                            optionLabel="name"
                            optionValue="id"
                            placeholder="Select Brand"
                            class="w-full"
                        />
                    </div>

                    <!-- Tax -->
                    <div class="field col-12 sm:col-6 mb-4 pl-md-2">
                        <label for="tax_id" class="block font-bold mb-2"
                            >Tax</label
                        >
                        <Dropdown
                            id="tax_id"
                            v-model="form.tax_id"
                            :options="taxes"
                            optionLabel="name"
                            optionValue="id"
                            placeholder="Select Tax"
                            class="w-full"
                        />
                    </div>

                    <!-- Tags -->
                    <div class="field col-12 mb-4 col-span-full">
                        <label for="tag_ids" class="block font-bold mb-2"
                            >Tags</label
                        >
                        <MultiSelect
                            id="tag_ids"
                            v-model="form.tag_ids"
                            :options="tags"
                            optionLabel="name"
                            optionValue="id"
                            display="chip"
                            placeholder="Select Tags"
                            class="w-full"
                        />
                    </div>
                </div>

                <!-- Physical Properties Section -->
                <div class="col-12 mb-3 mt-3">
                    <h3 class="text-xl font-semibold uppercase">
                        Physical Properties
                    </h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Weight -->
                    <div class="field col-12 md:col-4 mb-4 pr-md-2">
                        <label for="weight" class="block font-bold mb-2"
                            >Weight (kg)</label
                        >
                        <InputNumber
                            id="weight"
                            v-model.number="form.weight"
                            class="w-full"
                            :min="0"
                            placeholder="e.g. 1.5"
                        />
                    </div>

                    <!-- Dimensions -->
                    <div class="field col-12 md:col-4 mb-4 px-md-2">
                        <label class="block font-bold mb-2"
                            >Dimensions (cm)</label
                        >
                        <div class="flex gap-2">
                            <InputNumber
                                v-model.number="form.dimensions.length"
                                class="w-full"
                                :min="0"
                                placeholder="L"
                            />
                            <InputNumber
                                v-model.number="form.dimensions.width"
                                class="w-full"
                                :min="0"
                                placeholder="W"
                            />
                            <InputNumber
                                v-model.number="form.dimensions.height"
                                class="w-full"
                                :min="0"
                                placeholder="H"
                            />
                        </div>
                        <small class="text-gray-500"
                            >Stored as JSON: { length, width, height }</small
                        >
                    </div>

                    <!-- Materials -->
                    <div class="field col-12 md:col-4 mb-4 pl-md-2">
                        <label for="materials" class="block font-bold mb-2"
                            >Materials</label
                        >
                        <InputText
                            id="materials"
                            v-model.trim="materialsInput"
                            class="w-full"
                            placeholder="e.g. Cotton, Polyester"
                        />
                        <small class="text-gray-500"
                            >Comma-separated, stored as array.</small
                        >
                    </div>
                </div>

                <!-- Product Image Section -->
                <div class="col-12 mb-3 mt-3">
                    <h3 class="text-xl font-semibold uppercase">
                        Product Image
                    </h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="field col-12 mb-4">
                        <label for="thumbnail" class="block font-bold mb-2"
                            >Thumbnail</label
                        >
                        <div class="flex items-center gap-4">
                            <div
                                v-if="photoPreview || form.thumbnail"
                                class="thumbnail-preview mb-3"
                            >
                                <img
                                    :src="
                                        photoPreview
                                            ? photoPreview
                                            : resolveImagePath(form.thumbnail)
                                    "
                                    alt="Product Thumbnail"
                                    class="w-20 h-20 object-cover rounded"
                                />
                            </div>
                            <FileUpload
                                mode="basic"
                                name="thumbnail"
                                accept="image/*"
                                :maxFileSize="2000000"
                                @select="handlePhotoUpload"
                                :auto="true"
                                chooseLabel="Browse"
                            />
                        </div>
                        <small class="text-gray-500">
                            Max size: 2MB. Accepted formats: JPEG, PNG, JPG, GIF
                        </small>
                    </div>
                </div>

                <!-- Variations Section (for variable products) -->
                <div v-if="form.type === 'variable'" class="mt-6">
                    <div class="col-12 mb-3">
                        <h3 class="text-xl font-semibold uppercase">
                            Variations
                        </h3>
                    </div>

                    <!-- AUTO COMBINATION PICKERS -->
                    <div class="mb-4 border rounded p-3">
                        <h4 class="font-semibold mb-3">Variant Attributes</h4>

                        <div
                            v-for="attr in props.attributes"
                            :key="attr.id"
                            class="mb-3"
                        >
                            <label class="block font-bold mb-2">
                                {{ attr.display_name || attr.name }}
                            </label>

                            <MultiSelect
                                v-model="selectedAttrValues[attr.id]"
                                :options="
                                    (attr.values || []).map((v) => ({
                                        id: v.id,
                                        label: v.display_value || v.value,
                                    }))
                                "
                                optionLabel="label"
                                optionValue="id"
                                display="chip"
                                placeholder="Select values"
                                class="w-full"
                            />
                        </div>

                        <div class="flex gap-2 mt-3">
                            <Button
                                label="Generate Variations"
                                icon="pi pi-sitemap"
                                class="p-button-sm"
                                @click="generateVariations"
                            />
                            <Button
                                label="Clear"
                                icon="pi pi-times"
                                class="p-button-sm p-button-secondary"
                                @click="clearGeneratedVariations"
                            />
                        </div>
                    </div>

                    <div class="mb-3">
                        <Button
                            label="Add Variation"
                            icon="pi pi-plus"
                            class="p-button-sm"
                            @click="addVariation"
                        />
                        <small
                            v-if="
                                submitted &&
                                form.type === 'variable' &&
                                !form.variations.length
                            "
                            class="p-error ml-3"
                        >
                            At least one variation is required for variable
                            products.
                        </small>
                    </div>

                    <div v-if="form.variations.length" class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead>
                                <tr class="border-b">
                                    <th class="p-2 text-left">SKU *</th>
                                    <th class="p-2 text-left">Price *</th>
                                    <th class="p-2 text-left">Discount</th>
                                    <th class="p-2 text-left">Stock *</th>
                                    <th class="p-2 text-left">Status *</th>
                                    <th class="p-2 text-left">
                                        Attribute Values *
                                    </th>
                                    <th class="p-2 text-left">Image Path</th>
                                    <th class="p-2 text-left"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="(
                                        variation, index
                                    ) in form.variations"
                                    :key="index"
                                    class="border-b"
                                >
                                    <!-- SKU -->
                                    <td class="p-2">
                                        <InputText
                                            v-model.trim="variation.sku"
                                            class="w-full"
                                            placeholder="Variation SKU"
                                            :class="{
                                                'p-invalid':
                                                    submitted &&
                                                    form.type === 'variable' &&
                                                    !variation.sku,
                                            }"
                                        />
                                    </td>

                                    <!-- Price -->
                                    <td class="p-2">
                                        <InputNumber
                                            v-model.number="variation.price"
                                            class="w-full"
                                            :min="0"
                                            :class="{
                                                'p-invalid':
                                                    submitted &&
                                                    form.type === 'variable' &&
                                                    !variation.price,
                                            }"
                                        />
                                    </td>

                                    <!-- Discount Price -->
                                    <td class="p-2">
                                        <InputNumber
                                            v-model.number="
                                                variation.discount_price
                                            "
                                            class="w-full"
                                            :min="0"
                                        />
                                    </td>

                                    <!-- Stock Quantity -->
                                    <td class="p-2">
                                        <InputNumber
                                            v-model.number="
                                                variation.stock_quantity
                                            "
                                            class="w-full"
                                            :min="0"
                                            :class="{
                                                'p-invalid':
                                                    submitted &&
                                                    form.type === 'variable' &&
                                                    (variation.stock_quantity ===
                                                        null ||
                                                        variation.stock_quantity ===
                                                            '' ||
                                                        variation.stock_quantity <
                                                            0),
                                            }"
                                        />
                                    </td>

                                    <!-- Stock Status -->
                                    <td class="p-2">
                                        <Dropdown
                                            v-model="variation.stock_status"
                                            :options="stockStatuses"
                                            optionLabel="label"
                                            optionValue="value"
                                            class="w-full"
                                            :class="{
                                                'p-invalid':
                                                    submitted &&
                                                    form.type === 'variable' &&
                                                    !variation.stock_status,
                                            }"
                                        />
                                    </td>

                                    <!-- Attribute Values -->
                                    <td class="p-2">
                                        <MultiSelect
                                            v-model="
                                                variation.attribute_value_ids
                                            "
                                            :options="attributeValueOptions"
                                            optionLabel="label"
                                            optionValue="id"
                                            display="chip"
                                            placeholder="Select attribute values"
                                            class="w-full"
                                            :class="{
                                                'p-invalid':
                                                    submitted &&
                                                    form.type === 'variable' &&
                                                    (!variation.attribute_value_ids ||
                                                        !variation
                                                            .attribute_value_ids
                                                            .length),
                                            }"
                                        />
                                    </td>

                                    <!-- Image path (string) -->
                                    <td class="p-2">
                                        <InputText
                                            v-model.trim="variation.image"
                                            class="w-full"
                                            placeholder="e.g. products/hoodie/red-s.jpg"
                                        />
                                    </td>

                                    <!-- Remove -->
                                    <td class="p-2 text-right">
                                        <Button
                                            icon="pi pi-trash"
                                            class="p-button-rounded p-button-text p-button-danger p-button-sm"
                                            @click="removeVariation(index)"
                                        />
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Description Section -->
                <div class="col-12 mb-3 mt-6">
                    <h3 class="text-xl font-semibold uppercase">
                        Description & Details
                    </h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-1 gap-4">
                    <div class="field col-12 mb-4">
                        <label for="description" class="block font-bold mb-2"
                            >Description</label
                        >
                        <Editor
                            v-model="form.description"
                            editorStyle="height: 250px"
                        />
                    </div>

                    <div class="field col-12 mb-4">
                        <label
                            for="additional_info"
                            class="block font-bold mb-2"
                            >Additional Information</label
                        >
                        <Editor
                            v-model="form.additional_info"
                            editorStyle="height: 150px"
                        />
                    </div>
                </div>

                <!-- SEO Section -->
                <div class="col-12 mb-3 mt-3">
                    <h3 class="text-xl font-semibold uppercase">
                        SEO Information
                    </h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="field col-12 sm:col-6 mb-4 pr-md-2">
                        <label for="meta_title" class="block font-bold mb-2"
                            >Meta Title</label
                        >
                        <InputText
                            id="meta_title"
                            v-model.trim="form.meta_title"
                            class="w-full"
                        />
                    </div>

                    <div class="field col-12 sm:col-6 mb-4 pl-md-2">
                        <label for="meta_keywords" class="block font-bold mb-2"
                            >Meta Keywords</label
                        >
                        <InputText
                            id="meta_keywords"
                            v-model.trim="form.meta_keywords"
                            class="w-full"
                        />
                    </div>

                    <div class="field col-12 mb-4 col-span-full">
                        <label
                            for="meta_description"
                            class="block font-bold mb-2"
                            >Meta Description</label
                        >
                        <Textarea
                            id="meta_description"
                            v-model="form.meta_description"
                            rows="4"
                            class="w-full"
                        />
                    </div>
                </div>
            </template>
        </CrudComponent>
    </div>
</template>

<script setup>
import { useForm } from "@inertiajs/vue3";
import { computed, onMounted, ref, watch } from "vue";

import CrudComponent from "@/Components/CrudComponent.vue";
import { resolveImagePath } from "@/Helpers/imageHelper";

// PrimeVue
import Button from "primevue/button";
import Column from "primevue/column";
import Dropdown from "primevue/dropdown";
import Editor from "primevue/editor";
import FileUpload from "primevue/fileupload";
import InputNumber from "primevue/inputnumber";
import InputText from "primevue/inputtext";
import MultiSelect from "primevue/multiselect";
import Textarea from "primevue/textarea";
import ToggleSwitch from "primevue/toggleswitch";
import TreeSelect from "primevue/treeselect";

const props = defineProps({
    categories: { type: Array, default: () => [] },
    brands: { type: Array, default: () => [] },
    taxes: { type: Array, default: () => [] },
    tags: { type: Array, default: () => [] },
    attributes: { type: Array, default: () => [] },
    warehouses: { type: Array, default: () => [] },
});

// main form (Inertia)
const form = useForm({
    category_id: null,
    brand_id: null,
    tax_id: null,
    warehouse_id: null,

    name: "",
    slug: "",
    sku: "",
    barcode: "",
    code: "",

    base_price: null,
    base_discount_price: null,

    stock_status: "in_stock",
    stock_quantity: 0,

    type: "simple",

    thumbnail: null,
    images: [],

    weight: null,
    dimensions: {
        length: null,
        width: null,
        height: null,
    },
    materials: [],

    description: "",
    additional_info: "",
    is_active: true,

    meta_title: "",
    meta_description: "",
    meta_keywords: "",

    tag_ids: [],

    // variations payload to match backend request
    variations: [],
});

// dropdown options
const productTypes = [
    { label: "Simple Product", value: "simple" },
    { label: "Variable Product", value: "variable" },
];

const stockStatuses = [
    { label: "In Stock", value: "in_stock" },
    { label: "Out of Stock", value: "out_of_stock" },
    { label: "Pre-Order", value: "pre_order" },
];

// category tree for TreeSelect
const selectedCategoryKey = ref(null);

const categoryTreeNodes = computed(() => {
    const mapCategory = (cat) => ({
        key: String(cat.id), // keep string key
        label: cat.name,
        data: cat,
        children: (cat.children || []).map(mapCategory),
    });

    return props.categories.map(mapCategory);
});

watch(
    selectedCategoryKey,
    (newVal) => {
        if (!newVal) {
            form.category_id = null;
            return;
        }

        // Case 1: TreeSelect returns primitive key like "3"
        if (typeof newVal === "string" || typeof newVal === "number") {
            form.category_id = Number(newVal);
            return;
        }

        // Case 2: TreeSelect returns node object like { key: "3" }
        if (typeof newVal === "object" && newVal.key) {
            form.category_id = Number(newVal.key);
            return;
        }

        // Case 3: TreeSelect returns selectionKeys object like { "3": true }
        if (typeof newVal === "object") {
            const keys = Object.keys(newVal);
            form.category_id = keys.length ? Number(keys[0]) : null;
            return;
        }

        form.category_id = null;
    },
    { immediate: true }
);

// On edit, pre-select category in TreeSelect

onMounted(() => {
    if (form.category_id) {
        selectedCategoryKey.value = String(form.category_id);
    }
});

// onMounted(() => {
//     if (form.category_id) {
//         selectedCategoryKey.value = String(form.category_id);
//     }

//     if (Array.isArray(form.materials) && form.materials.length) {
//         materialsInput.value = form.materials.join(", ");
//     }
// });

// attribute value options for variations
const attributeValueOptions = computed(() => {
    if (!props.attributes) return [];
    const opts = [];
    props.attributes.forEach((attr) => {
        (attr.values || []).forEach((val) => {
            opts.push({
                id: val.id,
                label: `${attr.display_name || attr.name}: ${
                    val.display_value || val.value
                }`,
            });
        });
    });
    return opts;
});

// materials input (string) -> form.materials (array)
const materialsInput = ref("");

watch(materialsInput, (value) => {
    form.materials = value
        ? value
              .split(",")
              .map((v) => v.trim())
              .filter(Boolean)
        : [];
});

// Auto-generate slug from name if empty
watch(
    () => form.name,
    (newValue) => {
        if (!form.slug || form.slug === "") {
            form.slug = (newValue || "")
                .toLowerCase()
                .replace(/\s+/g, "-")
                .replace(/[^\w\-]+/g, "")
                .replace(/\-\-+/g, "-")
                .replace(/^-+/, "")
                .replace(/-+$/, "");
        }
    }
);

// variations methods (manual add/remove)
const addVariation = () => {
    form.variations.push({
        sku: "",
        price: form.base_price || null,
        discount_price: null,
        stock_quantity: 0,
        stock_status: "in_stock",
        image: null,
        attribute_value_ids: [],
    });
};

const removeVariation = (index) => {
    form.variations.splice(index, 1);
};

// initial sync
onMounted(() => {
    if (Array.isArray(form.materials) && form.materials.length) {
        materialsInput.value = form.materials.join(", ");
    }

    if (form.category_id) {
        selectedCategoryKey.value = String(form.category_id);
    }
});

/* ===========================
   AUTO COMBINATION LOGIC
=========================== */

// selected values per attribute
const selectedAttrValues = ref({});

// init attribute keys
onMounted(() => {
    props.attributes.forEach((a) => {
        if (!selectedAttrValues.value[a.id]) {
            selectedAttrValues.value[a.id] = [];
        }
    });
});

// cartesian product
const cartesian = (arrays) => {
    if (!arrays.length) return [];
    return arrays.reduce(
        (acc, curr) => {
            const res = [];
            acc.forEach((a) => {
                curr.forEach((c) => res.push([...a, c]));
            });
            return res;
        },
        [[]]
    );
};

// stable combo key
const comboKey = (valueIds) =>
    valueIds
        .slice()
        .sort((a, b) => a - b)
        .join("-");

// labels map for SKU generator
const valueIdToLabel = computed(() => {
    const map = new Map();
    props.attributes.forEach((a) => {
        (a.values || []).forEach((v) => {
            map.set(v.id, (v.display_value || v.value || "").toString());
        });
    });
    return map;
});

const makeSku = (valueIds) => {
    const parts = valueIds
        .map((id) => valueIdToLabel.value.get(id))
        .filter(Boolean);
    return `${form.slug || form.name || "product"}-${parts.join("-")}`
        .toUpperCase()
        .replace(/\s+/g, "");
};

// preserve edited fields when regenerating
const mergeExisting = (newVariations) => {
    const existingMap = new Map(
        form.variations.map((v) => [comboKey(v.attribute_value_ids || []), v])
    );

    return newVariations.map((v) => {
        const key = comboKey(v.attribute_value_ids);
        const old = existingMap.get(key);
        if (!old) return v;

        return {
            ...v,
            sku: old.sku || v.sku,
            price: old.price ?? v.price,
            discount_price: old.discount_price ?? v.discount_price,
            stock_quantity: old.stock_quantity ?? v.stock_quantity,
            stock_status: old.stock_status || v.stock_status,
            image: old.image || v.image,
        };
    });
};

// generate variations from selected attributes
const generateVariations = () => {
    const groups = Object.entries(selectedAttrValues.value)
        .map(([, valueIds]) => valueIds)
        .filter((arr) => Array.isArray(arr) && arr.length);

    if (!groups.length) {
        form.variations = [];
        return;
    }

    const combos = cartesian(groups);

    const basePrice = form.base_price || null;

    const newVars = combos.map((valueIds) => ({
        sku: makeSku(valueIds),
        price: basePrice,
        discount_price: null,
        stock_quantity: 0,
        stock_status: "in_stock",
        image: null,
        attribute_value_ids: valueIds,
    }));

    form.variations = mergeExisting(newVars);
};

const clearGeneratedVariations = () => {
    form.variations = [];
    Object.keys(selectedAttrValues.value).forEach((k) => {
        selectedAttrValues.value[k] = [];
    });
};
</script>

<style scoped>
.p-editor-container .p-editor-content .ql-editor {
    min-height: 150px;
}
</style>
