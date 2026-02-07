<script setup>
import { resolveImagePath } from "@/Helpers/imageHelper";
import { useForm } from "@inertiajs/vue3";
import { computed, onMounted, ref, watch } from "vue";

// PrimeVue
import Button from "primevue/button";
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
    product: { type: Object, default: null },
    isEditing: { type: Boolean, default: false },

    categories: { type: Array, default: () => [] },
    brands: { type: Array, default: () => [] },
    taxes: { type: Array, default: () => [] },
    tags: { type: Array, default: () => [] },
    attributes: { type: Array, default: () => [] },
    warehouses: { type: Array, default: () => [] },
});

const emit = defineEmits(["cancel", "saved"]);

const productTypes = [
    { label: "Simple Product", value: "simple" },
    { label: "Variable Product", value: "variable" },
];

// -----------------------------
// FORM BASE (warehouse stock)
// -----------------------------
const emptyForm = {
    category_id: null,
    brand_id: null,
    tax_id: null,

    name: "",
    slug: "",
    sku: "",
    barcode: "",
    code: "",

    base_price: null,
    base_discount_price: null,

    type: "simple",

    // ✅ stock comes from product_stocks now
    stocks: [], // for SIMPLE: [{warehouse_id, quantity, alert_quantity}]

    thumbnail: null,
    images: [],

    weight: null,
    dimensions: { length: null, width: null, height: null },
    materials: [],

    description: "",
    additional_info: "",
    is_active: true,

    meta_title: "",
    meta_description: "",
    meta_keywords: "",

    tag_ids: [],

    // for VARIABLE: each variation has stocks[]
    variations: [],
};

const mapProductToForm = (product) => {
    if (!product) return { ...emptyForm };

    const mapped = {
        ...emptyForm,
        category_id: product.category_id ?? null,
        brand_id: product.brand_id ?? null,
        tax_id: product.tax_id ?? null,

        name: product.name ?? "",
        slug: product.slug ?? "",
        sku: product.sku ?? "",
        barcode: product.barcode ?? "",
        code: product.code ?? "",

        base_price: product.base_price ?? null,
        base_discount_price: product.base_discount_price ?? null,

        type: product.type ?? "simple",

        thumbnail: null,
        images: product.images ?? [],

        weight: product.weight ?? null,
        dimensions: product.dimensions ?? {
            length: null,
            width: null,
            height: null,
        },
        materials: product.materials ?? [],

        description: product.description ?? "",
        additional_info: product.additional_info ?? "",
        is_active: product.is_active ?? true,

        meta_title: product.meta_title ?? "",
        meta_description: product.meta_description ?? "",
        meta_keywords: product.meta_keywords ?? "",

        tag_ids: product.tag_ids
            ? product.tag_ids
            : (product.tags || []).map((t) => t.id),

        // ✅ preload stocks from API (if you return them)
        stocks: (product.stocks || [])
            .filter((s) => !s.variation_id)
            .map((s) => ({
                warehouse_id: s.warehouse_id,
                quantity: Number(s.quantity ?? 0),
                alert_quantity: s.alert_quantity ?? null,
            })),

        variations: (product.variations || []).map((v) => ({
            id: v.id, // keep id for edit mode if you need
            sku: v.sku ?? "",
            price: v.price ?? null,
            discount_price: v.discount_price ?? null,
            image: v.image ?? null,
            attribute_value_ids: (
                v.attribute_values ||
                v.attribute_value_ids ||
                []
            ).map((x) => (typeof x === "object" ? x.id : x)),
            stocks: (v.stocks || []).map((s) => ({
                warehouse_id: s.warehouse_id,
                quantity: Number(s.quantity ?? 0),
                alert_quantity: s.alert_quantity ?? null,
            })),
        })),
    };

    // If no stocks returned, initialize empty stocks rows for simple
    if (mapped.type === "simple" && (!mapped.stocks || !mapped.stocks.length)) {
        mapped.stocks = props.warehouses.map((w) => ({
            warehouse_id: w.id,
            quantity: 0,
            alert_quantity: null,
        }));
    }

    return mapped;
};

const submitted = ref(false);
const form = useForm(mapProductToForm(props.product));

// -----------------------------
// CATEGORY TREESELECT
// -----------------------------
const selectedCategoryKey = ref(null);

const categoryTreeNodes = computed(() => {
    const mapCategory = (cat) => ({
        key: String(cat.id),
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

        // PrimeVue TreeSelect v-model logic:
        // Ensure we handle both object keys { "ID": true } and primitives (though TreeSelect usually uses object for selection)
        if (typeof newVal === "object") {
            const keys = Object.keys(newVal);
            if (keys.length > 0) {
                // Return the first key as number
                form.category_id = Number(keys[0]);
            } else {
                form.category_id = null;
            }
        } else {
            // Fallback for primitive values
            form.category_id = Number(newVal);
        }
    },
    { immediate: true }
);

// Fix initial value mapping to compatible TreeSelect format { ID: true }
const initCategory = () => {
    if (props.product?.category_id) {
        selectedCategoryKey.value = { [String(props.product.category_id)]: true };
    } else {
        selectedCategoryKey.value = null;
    }
};



// -----------------------------
// MATERIALS INPUT (STRING <-> ARRAY)
// -----------------------------
const materialsInput = ref("");

watch(materialsInput, (value) => {
    form.materials = value
        ? value
            .split(",")
            .map((v) => v.trim())
            .filter(Boolean)
        : [];
});

// -----------------------------
// AUTO SLUG FROM NAME
// -----------------------------
watch(
    () => form.name,
    (newValue) => {
        if (!form.slug) {
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

// -----------------------------
// THUMBNAIL UPLOAD
// -----------------------------
const photoPreview = ref(null);

const handlePhotoUpload = (event) => {
    const file = event.files?.[0];
    if (!file) return;

    form.thumbnail = file;

    const reader = new FileReader();
    reader.onload = (e) => (photoPreview.value = e.target.result);
    reader.readAsDataURL(file);
};

// -----------------------------
// VARIATIONS / ATTRIBUTES
// -----------------------------
const attributeValueOptions = computed(() => {
    const opts = [];
    props.attributes.forEach((attr) => {
        (attr.values || []).forEach((val) => {
            opts.push({
                id: val.id,
                label: `${attr.display_name || attr.name}: ${val.display_value || val.value
                    }`,
            });
        });
    });
    return opts;
});

const selectedAttrValues = ref({});

const cartesian = (arrays) =>
    arrays.reduce(
        (acc, curr) => acc.flatMap((a) => curr.map((c) => [...a, c])),
        [[]]
    );

const comboKey = (valueIds) =>
    valueIds
        .slice()
        .sort((a, b) => a - b)
        .join("-");

const valueIdToLabel = computed(() => {
    const map = new Map();
    props.attributes.forEach((a) => {
        (a.values || []).forEach((v) =>
            map.set(v.id, String(v.display_value || v.value || ""))
        );
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

// keep existing edits when regen
const mergeExisting = (newVariations) => {
    const existingMap = new Map(
        form.variations.map((v) => [comboKey(v.attribute_value_ids || []), v])
    );

    return newVariations.map((v) => {
        const old = existingMap.get(comboKey(v.attribute_value_ids));
        if (!old) return v;

        return {
            ...v,
            id: old.id, // keep id in edit mode
            sku: old.sku || v.sku,
            price: old.price ?? v.price,
            discount_price: old.discount_price ?? v.discount_price,
            image: old.image || v.image,
            stocks: old.stocks?.length ? old.stocks : v.stocks,
        };
    });
};

const initStocksForAllWarehouses = () =>
    props.warehouses.map((w) => ({
        warehouse_id: w.id,
        quantity: 0,
        alert_quantity: null,
    }));

const addVariation = () => {
    form.variations.push({
        sku: "",
        price: form.base_price || null,
        discount_price: null,
        image: null,
        attribute_value_ids: [],
        stocks: initStocksForAllWarehouses(),
    });
};

const removeVariation = (index) => form.variations.splice(index, 1);

const generateVariations = () => {
    const groups = Object.values(selectedAttrValues.value).filter(
        (arr) => Array.isArray(arr) && arr.length
    );
    if (!groups.length) return (form.variations = []);

    const combos = cartesian(groups);
    const basePrice = form.base_price || null;

    const newVars = combos.map((valueIds) => ({
        sku: makeSku(valueIds),
        price: basePrice,
        discount_price: null,
        image: null,
        attribute_value_ids: valueIds,
        stocks: initStocksForAllWarehouses(),
    }));

    form.variations = mergeExisting(newVars);
};

const clearGeneratedVariations = () => {
    form.variations = [];
    Object.keys(selectedAttrValues.value).forEach(
        (k) => (selectedAttrValues.value[k] = [])
    );
};

// -----------------------------
// Ensure stocks are initialized when type changes
// -----------------------------
watch(
    () => form.type,
    (t) => {
        if (t === "simple") {
            if (!form.stocks?.length)
                form.stocks = initStocksForAllWarehouses();
            form.variations = [];
        } else if (t === "variable") {
            form.stocks = [];
            // init attribute picker keys
            selectedAttrValues.value = {};
            props.attributes.forEach(
                (a) => (selectedAttrValues.value[a.id] = [])
            );
        }
    },
    { immediate: true }
);

// -----------------------------
// RESET
// -----------------------------
const resetFromProps = () => {
    const mapped = mapProductToForm(props.product);
    form.defaults(mapped);
    form.reset(mapped);
    form.clearErrors();

    materialsInput.value = Array.isArray(mapped.materials)
        ? mapped.materials.join(", ")
        : "";

    initCategory();

    photoPreview.value = props.product?.thumbnail
        ? resolveImagePath(props.product.thumbnail)
        : null;

    selectedAttrValues.value = {};
    props.attributes.forEach((a) => (selectedAttrValues.value[a.id] = []));
};

onMounted(resetFromProps);
watch(() => props.product, resetFromProps);

// -----------------------------
// SUBMIT
// -----------------------------
const submitForm = () => {
    submitted.value = true;

    const url = props.isEditing
        ? route("products.update", props.product.id)
        : route("products.store");

    const options = {
        preserveScroll: true,
        forceFormData: true,
        onSuccess: () => emit("saved"),
        onFinish: () => (submitted.value = false),
    };

    if (props.isEditing) {
        form.transform((data) => ({ ...data, _method: "put" })).post(
            url,
            options
        );
    } else {
        form.post(url, options);
    }
};

const cancel = () => emit("cancel");
</script>

<template>
    <form @submit.prevent="submitForm" class="space-y-6">
        <!-- Basic Information Section -->
        <div>
            <h3 class="text-xl font-semibold uppercase mb-3">
                Basic Information
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Name -->
                <div class="field col-12 sm:col-6 mb-4 pr-md-2">
                    <label for="name" class="block font-bold mb-2">Name *</label>
                    <InputText id="name" v-model.trim="form.name" required class="w-full"
                        :class="{ 'p-invalid': submitted && !form.name }" />
                    <small v-if="form.errors.name" class="p-error">{{
                        form.errors.name
                    }}</small>
                </div>

                <!-- Slug -->
                <div class="field col-12 sm:col-6 mb-4 pl-md-2">
                    <label for="slug" class="block font-bold mb-2">Slug *</label>
                    <InputText id="slug" v-model.trim="form.slug" required class="w-full"
                        :class="{ 'p-invalid': submitted && !form.slug }" />
                    <small v-if="form.errors.slug" class="p-error">{{
                        form.errors.slug
                    }}</small>
                </div>

                <!-- SKU -->
                <div class="field col-12 md:col-4 mb-4 pr-md-2">
                    <label for="sku" class="block font-bold mb-2">SKU</label>
                    <InputText id="sku" v-model.trim="form.sku" class="w-full" />
                    <small v-if="form.errors.sku" class="p-error">{{
                        form.errors.sku
                    }}</small>
                </div>

                <!-- Barcode -->
                <div class="field col-12 md:col-4 mb-4 px-md-2">
                    <label for="barcode" class="block font-bold mb-2">Barcode</label>
                    <InputText id="barcode" v-model.trim="form.barcode" class="w-full" />
                    <small v-if="form.errors.barcode" class="p-error">{{
                        form.errors.barcode
                    }}</small>
                </div>

                <!-- Product Code -->
                <div class="field col-12 md:col-4 mb-4 pl-md-2">
                    <label for="code" class="block font-bold mb-2">Product Code</label>
                    <InputText id="code" v-model.trim="form.code" class="w-full" />
                    <small v-if="form.errors.code" class="p-error">{{
                        form.errors.code
                    }}</small>
                </div>

                <!-- Product Type -->
                <div class="field col-12 md:col-4 mb-4 pr-md-2">
                    <label for="type" class="block font-bold mb-2">Product Type *</label>
                    <Dropdown id="type" v-model="form.type" :options="productTypes" optionLabel="label"
                        optionValue="value" placeholder="Select Type" class="w-full"
                        :class="{ 'p-invalid': form.errors.type }" />
                    <small v-if="form.errors.type" class="p-error">{{
                        form.errors.type
                    }}</small>
                </div>

                <!-- Active / Inactive -->
                <div class="field col-12 md:col-4 mb-4 pl-md-2">
                    <label for="status" class="block font-bold mb-2">Status</label>
                    <div class="pt-2 flex items-center">
                        <ToggleSwitch v-model="form.is_active" />
                        <span class="ml-2">{{
                            form.is_active ? "Active" : "Inactive"
                        }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pricing & Inventory Section -->
        <div>
            <h3 class="text-xl font-semibold uppercase mb-3">
                Pricing & Inventory
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Base Price -->
                <div class="field col-12 sm:col-6 mb-4 pr-md-2">
                    <label for="base_price" class="block font-bold mb-2">Base Price *</label>
                    <InputNumber id="base_price" v-model.number="form.base_price" class="w-full" :min="0"
                        :class="{ 'p-invalid': form.errors.base_price }" />
                    <small v-if="form.errors.base_price" class="p-error">{{
                        form.errors.base_price
                    }}</small>
                </div>

                <!-- Discount Price -->
                <div class="field col-12 sm:col-6 mb-4 pl-md-2">
                    <label for="base_discount_price" class="block font-bold mb-2">Discount Price</label>
                    <InputNumber id="base_discount_price" v-model.number="form.base_discount_price" class="w-full"
                        :min="0" :max="form.base_price || null" />
                    <small v-if="form.errors.base_discount_price" class="p-error">
                        {{ form.errors.base_discount_price }}
                    </small>
                </div>

                <!-- SIMPLE: Warehouse Stock Table -->
                <div v-if="form.type === 'simple'" class="col-span-full">
                    <h4 class="font-semibold mb-2">Warehouse Stock (Simple)</h4>

                    <div class="overflow-x-auto border rounded">
                        <table class="min-w-full text-sm">
                            <thead>
                                <tr class="border-b">
                                    <th class="p-2 text-left">Warehouse</th>
                                    <th class="p-2 text-left">Quantity</th>
                                    <th class="p-2 text-left">Alert Qty</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(s, i) in form.stocks" :key="s.warehouse_id" class="border-b">
                                    <td class="p-2">
                                        {{
                                            warehouses.find(
                                                (w) => w.id === s.warehouse_id
                                            )?.name || "Warehouse"
                                        }}
                                    </td>
                                    <td class="p-2">
                                        <InputNumber v-model="s.quantity" class="w-full" :min="0" />
                                    </td>
                                    <td class="p-2">
                                        <InputNumber v-model="s.alert_quantity" class="w-full" :min="0" />
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <small v-if="form.errors.stocks" class="p-error">{{
                        form.errors.stocks
                    }}</small>
                </div>
            </div>
        </div>

        <!-- Categories & Tags Section -->
        <div>
            <h3 class="text-xl font-semibold uppercase mb-3">
                Categories & Tags
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Category (TreeSelect) -->
                <div class="field col-12 sm:col-6 mb-4 pr-md-2">
                    <label for="category_id" class="block font-bold mb-2">Category *</label>

                    <TreeSelect id="category_id" v-model="selectedCategoryKey" :options="categoryTreeNodes"
                        placeholder="Select Category" class="w-full" selectionMode="single"
                        :class="{ 'p-invalid': submitted && !form.category_id }" />

                    <small v-if="submitted && !form.category_id" class="p-error">
                        Category is required.
                    </small>
                </div>

                <!-- Brand -->
                <div class="field col-12 sm:col-6 mb-4 pr-md-2">
                    <label for="brand_id" class="block font-bold mb-2">Brand</label>
                    <Dropdown id="brand_id" v-model="form.brand_id" :options="brands" optionLabel="name"
                        optionValue="id" placeholder="Select Brand" class="w-full" />
                </div>

                <!-- Tax -->
                <div class="field col-12 sm:col-6 mb-4 pl-md-2">
                    <label for="tax_id" class="block font-bold mb-2">Tax</label>
                    <Dropdown id="tax_id" v-model="form.tax_id" :options="taxes" optionLabel="name" optionValue="id"
                        placeholder="Select Tax" class="w-full" />
                </div>

                <!-- Tags -->
                <div class="field col-12 mb-4 col-span-full">
                    <label for="tag_ids" class="block font-bold mb-2">Tags</label>
                    <MultiSelect id="tag_ids" v-model="form.tag_ids" :options="tags" optionLabel="name" optionValue="id"
                        display="chip" placeholder="Select Tags" class="w-full" />
                </div>
            </div>
        </div>

        <!-- Physical Properties Section -->
        <div>
            <h3 class="text-xl font-semibold uppercase mb-3">
                Physical Properties
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Weight -->
                <div class="field col-12 md:col-4 mb-4 pr-md-2">
                    <label for="weight" class="block font-bold mb-2">Weight (kg)</label>
                    <InputNumber id="weight" v-model.number="form.weight" class="w-full" :min="0"
                        placeholder="e.g. 1.5" />
                </div>

                <!-- Dimensions -->
                <div class="field col-span-2 mb-4 px-md-2">
                    <label class="block font-bold mb-2">Dimensions (cm)</label>
                    <div class="grid grid-cols-3 gap-2">
                        <InputNumber v-model.number="form.dimensions.length" :min="0" placeholder="L" />
                        <InputNumber v-model.number="form.dimensions.width" :min="0" placeholder="W" />
                        <InputNumber v-model.number="form.dimensions.height" :min="0" placeholder="H" />
                    </div>
                    <small class="text-gray-500">Stored as JSON: { length, width, height }</small>
                </div>

                <!-- Materials -->
                <div class="field col-12 md:col-4 mb-4 pl-md-2">
                    <label for="materials" class="block font-bold mb-2">Materials</label>
                    <InputText id="materials" v-model.trim="materialsInput" class="w-full"
                        placeholder="e.g. Cotton, Polyester" />
                    <small class="text-gray-500">Comma-separated, stored as array.</small>
                </div>
            </div>
        </div>

        <!-- Product Image Section -->
        <div>
            <h3 class="text-xl font-semibold uppercase mb-3">Product Image</h3>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="field col-12 mb-4">
                    <label for="thumbnail" class="block font-bold mb-2">Thumbnail</label>

                    <div class="flex items-center gap-4">
                        <div v-if="photoPreview || form.thumbnail" class="thumbnail-preview mb-3">
                            <img :src="photoPreview
                                ? photoPreview
                                : resolveImagePath(form.thumbnail)
                                " alt="Product Thumbnail" class="w-20 h-20 object-cover rounded" />
                        </div>

                        <FileUpload mode="basic" name="thumbnail" accept="image/*" :maxFileSize="2000000"
                            @select="handlePhotoUpload" :auto="true" chooseLabel="Browse" />
                    </div>

                    <small class="text-gray-500">Max size: 2MB. Accepted formats: JPEG, PNG, JPG,
                        GIF</small>
                </div>
            </div>
        </div>

        <!-- Variations Section (for variable products) -->
        <div v-if="form.type === 'variable'">
            <h3 class="text-xl font-semibold uppercase mb-3">Variations</h3>

            <!-- AUTO COMBINATION PICKERS -->
            <div class="mb-4 border rounded p-3">
                <h4 class="font-semibold mb-3">Variant Attributes</h4>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div v-for="attr in attributes" :key="attr.id" class="mb-3">
                        <label class="block font-bold mb-2">{{
                            attr.display_name || attr.name
                        }}</label>

                        <MultiSelect v-model="selectedAttrValues[attr.id]" :options="(attr.values || []).map((v) => ({
                            id: v.id,
                            label: v.display_value || v.value,
                        }))
                            " optionLabel="label" optionValue="id" display="chip" placeholder="Select values"
                            class="w-full" />
                    </div>
                </div>

                <div class="flex gap-2 mt-3">
                    <Button label="Generate Variations" icon="pi pi-sitemap" class="p-button-sm"
                        @click="generateVariations" />
                    <Button label="Clear" icon="pi pi-times" class="p-button-sm p-button-secondary"
                        @click="clearGeneratedVariations" />
                </div>
            </div>

            <div class="mb-3">
                <Button label="Add Variation" icon="pi pi-plus" class="p-button-sm" @click="addVariation" />
                <small v-if="
                    submitted &&
                    form.type === 'variable' &&
                    !form.variations.length
                " class="p-error ml-3">
                    At least one variation is required for variable products.
                </small>
            </div>

            <div v-if="form.variations.length" class="w-full overflow-x-auto">
                <table class="min-w-[1100px] w-full text-sm table-fixed">
                    <thead class="sticky top-0 bg-white z-10">
                        <tr class="border-b">
                            <th class="p-2 text-left w-[180px]">SKU *</th>
                            <th class="p-2 text-left w-[120px]">Price *</th>
                            <th class="p-2 text-left w-[120px]">Discount</th>
                            <th class="p-2 text-left w-[280px]">
                                Attribute Values *
                            </th>
                            <th class="p-2 text-left w-[320px]">
                                Warehouse Alert Qty *
                            </th>
                            <th class="p-2 text-left w-[220px]">Image Path</th>
                            <th class="p-2 text-left w-[70px]"></th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr v-for="(variation, index) in form.variations" :key="index" class="border-b align-top">
                            <!-- SKU -->
                            <td class="p-2">
                                <InputText v-model.trim="variation.sku" class="w-full" placeholder="Variation SKU"
                                    :class="{
                                        'p-invalid ':
                                            submitted && !variation.sku,
                                    }" />
                            </td>

                            <!-- Price -->
                            <td class="p-2">
                                <div class="grid grid-cols-4">
                                    <InputNumber v-model.number="variation.price" :min="0" :class="{
                                        'p-invalid':
                                            submitted && !variation.price,
                                    }" />
                                </div>
                            </td>

                            <!-- Discount -->
                            <td class="p-2">
                                <InputNumber v-model.number="variation.discount_price" :min="0" />
                            </td>

                            <!-- Attribute Values -->
                            <td class="p-2">
                                <div class="max-w-[260px]">
                                    <MultiSelect v-model="variation.attribute_value_ids"
                                        :options="attributeValueOptions" optionLabel="label" optionValue="id"
                                        display="chip" placeholder="Select attribute values" class="w-full" :class="{
                                            'p-invalid':
                                                submitted &&
                                                (!variation.attribute_value_ids ||
                                                    !variation
                                                        .attribute_value_ids
                                                        .length),
                                        }" />
                                </div>
                            </td>

                            <!-- Warehouse Stocks -->
                            <td class="p-2">
                                <div class="space-y-2">
                                    <div v-for="(s, si) in variation.stocks" :key="s.warehouse_id ?? si"
                                        class="grid grid-cols-12 gap-2 items-center">
                                        <span class="col-span-5 text-xs truncate">
                                            {{
                                                warehouses.find(
                                                    (w) =>
                                                        w.id === s.warehouse_id
                                                )?.name || "Warehouse"
                                            }}
                                        </span>

                                        <!-- <div class="col-span-3">
                                            <InputNumber
                                                v-model.number="s.quantity"
                                                class="w-full"
                                                :min="0"
                                            />
                                        </div> -->

                                        <div class="col-span-4">
                                            <InputNumber v-model.number="s.alert_quantity
                                                " class="w-full" :min="0" placeholder="Alert" />
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- Image -->
                            <td class="p-2">
                                <InputText v-model.trim="variation.image" class="w-full"
                                    placeholder="e.g. products/hoodie/red-s.jpg" />
                            </td>

                            <!-- Remove -->
                            <td class="p-2 text-right">
                                <Button icon="pi pi-trash"
                                    class="p-button-rounded p-button-text p-button-danger p-button-sm"
                                    @click="removeVariation(index)" />
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <small v-if="form.errors.variations" class="p-error">{{
                form.errors.variations
            }}</small>
        </div>

        <!-- Description Section -->
        <div>
            <h3 class="text-xl font-semibold uppercase mb-3">
                Description & Details
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-1 gap-4">
                <div class="field col-12 mb-4">
                    <label for="description" class="block font-bold mb-2">Description</label>
                    <Editor v-model="form.description" editorStyle="height: 250px" />
                </div>

                <div class="field col-12 mb-4">
                    <label for="additional_info" class="block font-bold mb-2">Additional Information</label>
                    <Editor v-model="form.additional_info" editorStyle="height: 150px" />
                </div>
            </div>
        </div>

        <!-- SEO Section -->
        <div>
            <h3 class="text-xl font-semibold uppercase mb-3">
                SEO Information
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="field col-12 sm:col-6 mb-4 pr-md-2">
                    <label for="meta_title" class="block font-bold mb-2">Meta Title</label>
                    <InputText id="meta_title" v-model.trim="form.meta_title" class="w-full" />
                </div>

                <div class="field col-12 sm:col-6 mb-4 pl-md-2">
                    <label for="meta_keywords" class="block font-bold mb-2">Meta Keywords</label>
                    <InputText id="meta_keywords" v-model.trim="form.meta_keywords" class="w-full" />
                </div>

                <div class="field col-12 mb-4 col-span-full">
                    <label for="meta_description" class="block font-bold mb-2">Meta Description</label>
                    <Textarea id="meta_description" v-model="form.meta_description" rows="4" class="w-full" />
                </div>
            </div>
        </div>

        <!-- Buttons -->
        <div class="flex justify-end gap-2 pt-2">
            <Button type="button" label="Cancel" icon="pi pi-times" class="p-button-text" @click="cancel" />
            <Button type="submit" :label="isEditing ? 'Update Product' : 'Create Product'" icon="pi pi-check" />
        </div>
    </form>
</template>

<style scoped>
.p-editor-container .p-editor-content .ql-editor {
    min-height: 150px;
}
</style>
