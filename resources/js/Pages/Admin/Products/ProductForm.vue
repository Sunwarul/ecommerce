<script setup>
import { resolveImagePath } from "@/Helpers/imageHelper";
import { useForm } from "@inertiajs/vue3";
import { computed, onMounted, ref, watch } from "vue";
import { useI18n } from "vue-i18n";
import { useToast } from "primevue/usetoast";

// PrimeVue
import Button from "primevue/button";
import Message from "primevue/message";
import Select from "primevue/select";
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

const { t } = useI18n();
const toast = useToast();

const productTypes = computed(() => [
    { label: t("catalog.products.type_simple_long"), value: "simple", hint: t("catalog.form.type_simple_hint") },
    { label: t("catalog.products.type_variable_long"), value: "variable", hint: t("catalog.form.type_variable_hint") },
]);

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
const slugify = (value) =>
    (value || "")
        .toLowerCase()
        .trim()
        .replace(/\s+/g, "-")
        .replace(/[^\p{L}\p{N}\-]+/gu, "")
        .replace(/-{2,}/g, "-")
        .replace(/^-+|-+$/g, "");

// Keep the slug in sync with the name until the user edits the slug by hand
const slugTouched = ref(false);
watch(
    () => form.name,
    (newValue, oldValue) => {
        if (!slugTouched.value && (!form.slug || form.slug === slugify(oldValue))) {
            form.slug = slugify(newValue);
        }
    }
);

// -----------------------------
// THUMBNAIL UPLOAD
// -----------------------------
const photoPreview = ref(null);

const MAX_THUMBNAIL_BYTES = 7 * 1024 * 1024; // matches the server rule (max:7048 KB)

const handlePhotoUpload = (event) => {
    const file = event.files?.[0];
    if (!file) return;
    if (file.size > MAX_THUMBNAIL_BYTES) {
        toast.add({ severity: "warn", summary: t("catalog.form.image_too_large"), detail: t("catalog.form.image_hint"), life: 4000 });
        return;
    }

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

const warehouseName = (id) => props.warehouses.find((w) => w.id === id)?.name || t("catalog.stock.warehouse");

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
    form.reset();
    form.clearErrors();
    slugTouched.value = !!props.product?.slug;

    materialsInput.value = Array.isArray(mapped.materials) ? mapped.materials.join(", ") : "";

    initCategory();

    photoPreview.value = props.product?.thumbnail ? resolveImagePath(props.product.thumbnail) : null;

    selectedAttrValues.value = {};
    props.attributes.forEach((a) => (selectedAttrValues.value[a.id] = []));
};

onMounted(resetFromProps);
watch(() => props.product, resetFromProps);

// -----------------------------
// VALIDATION (client side, mirrors the server rules)
// -----------------------------
const err = (key) => form.errors[key];

const discountInvalid = (price, discount) =>
    discount !== null && discount !== undefined && discount !== "" && Number(discount) > 0 && Number(discount) >= Number(price || 0);

const clientErrors = computed(() => {
    const list = [];
    if (!form.name) list.push(t("catalog.form.errors.name"));
    if (!form.category_id) list.push(t("catalog.form.errors.category"));
    if (form.base_price === null || form.base_price === undefined || form.base_price === "") list.push(t("catalog.form.errors.price"));
    if (discountInvalid(form.base_price, form.base_discount_price)) list.push(t("catalog.form.errors.discount"));
    if (form.type === "variable") {
        if (!form.variations.length) list.push(t("catalog.form.errors.variations"));
        form.variations.forEach((v, i) => {
            if (!v.sku || v.price === null || v.price === undefined || !v.attribute_value_ids?.length)
                list.push(t("catalog.form.errors.variation_incomplete", { n: i + 1 }));
            if (discountInvalid(v.price, v.discount_price)) list.push(t("catalog.form.errors.variation_discount", { n: i + 1 }));
        });
    }
    return list;
});

const serverErrorList = computed(() => [...new Set(Object.values(form.errors || {}))]);

// -----------------------------
// SUBMIT
// -----------------------------
const submitForm = () => {
    submitted.value = true;

    if (clientErrors.value.length) {
        toast.add({ severity: "warn", summary: t("catalog.form.check_fields"), detail: clientErrors.value[0], life: 4000 });
        window.scrollTo({ top: 0, behavior: "smooth" });
        return;
    }

    const url = props.isEditing ? route("products.update", props.product.id) : route("products.store");

    const options = {
        preserveScroll: true,
        forceFormData: true,
        onSuccess: () => emit("saved"),
        onError: (errors) => {
            toast.add({ severity: "error", summary: t("catalog.form.check_fields"), detail: Object.values(errors)[0], life: 5000 });
            window.scrollTo({ top: 0, behavior: "smooth" });
        },
    };

    if (props.isEditing) {
        form.transform((data) => ({ ...data, _method: "put" })).post(url, options);
    } else {
        form.post(url, options);
    }
};

const cancel = () => emit("cancel");

const showSeo = ref(false);
const labelCls = "block text-sm font-medium text-slate-700 mb-1.5";
const sectionCls = "rounded-xl border border-slate-200 bg-white p-4 md:p-5";
</script>

<template>
    <form @submit.prevent="submitForm" class="space-y-4" novalidate>
        <!-- Error summary -->
        <Message v-if="submitted && (clientErrors.length || serverErrorList.length)" severity="error" :closable="false">
            <div class="font-semibold mb-1">{{ t("catalog.form.check_fields") }}</div>
            <ul class="list-disc ps-5 text-sm space-y-0.5">
                <li v-for="(e, i) in (clientErrors.length ? clientErrors : serverErrorList).slice(0, 8)" :key="i">{{ e }}</li>
            </ul>
        </Message>

        <!-- Basic information -->
        <section :class="sectionCls">
            <h3 class="section-title"><i class="pi pi-info-circle" />{{ t("catalog.form.basic_info") }}</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label for="name" :class="labelCls">{{ t("products.product_name") }} <span class="text-rose-600">*</span></label>
                    <InputText id="name" v-model.trim="form.name" class="w-full" :invalid="(submitted && !form.name) || !!err('name')" />
                    <small v-if="err('name')" class="p-error">{{ err("name") }}</small>
                </div>

                <div>
                    <label for="slug" :class="labelCls">{{ t("categories.slug") }}</label>
                    <InputText id="slug" v-model.trim="form.slug" class="w-full ltr-nums" :invalid="!!err('slug')" @input="slugTouched = true" />
                    <small v-if="err('slug')" class="p-error">{{ err("slug") }}</small>
                    <small v-else class="text-slate-500">{{ t("catalog.form.slug_hint") }}</small>
                </div>

                <div>
                    <label :class="labelCls">{{ t("common.status") }}</label>
                    <div class="flex items-center gap-2 h-[2.6rem]">
                        <ToggleSwitch v-model="form.is_active" inputId="is_active" />
                        <label for="is_active" class="text-sm text-slate-700">
                            {{ form.is_active ? t("catalog.form.active_hint") : t("catalog.form.inactive_hint") }}
                        </label>
                    </div>
                </div>

                <div>
                    <label for="sku" :class="labelCls">{{ t("products.sku") }}</label>
                    <InputText id="sku" v-model.trim="form.sku" class="w-full ltr-nums" :invalid="!!err('sku')" />
                    <small v-if="err('sku')" class="p-error">{{ err("sku") }}</small>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label for="barcode" :class="labelCls">{{ t("products.barcode") }}</label>
                        <InputText id="barcode" v-model.trim="form.barcode" class="w-full ltr-nums" :invalid="!!err('barcode')" />
                        <small v-if="err('barcode')" class="p-error">{{ err("barcode") }}</small>
                    </div>
                    <div>
                        <label for="code" :class="labelCls">{{ t("products.product_code") }}</label>
                        <InputText id="code" v-model.trim="form.code" class="w-full ltr-nums" :invalid="!!err('code')" />
                        <small v-if="err('code')" class="p-error">{{ err("code") }}</small>
                    </div>
                </div>

                <div class="md:col-span-2">
                    <label :class="labelCls">{{ t("catalog.products.type") }} <span class="text-rose-600">*</span></label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                        <button v-for="opt in productTypes" :key="opt.value" type="button"
                            class="text-start rounded-xl border p-3 transition-colors"
                            :class="form.type === opt.value ? 'border-emerald-500 bg-emerald-50' : 'border-slate-200 hover:bg-slate-50'"
                            @click="form.type = opt.value">
                            <div class="flex items-center gap-2 font-medium text-slate-800">
                                <i :class="[opt.value === 'variable' ? 'pi pi-sitemap' : 'pi pi-box', form.type === opt.value ? 'text-emerald-600' : 'text-slate-400']" />
                                {{ opt.label }}
                            </div>
                            <div class="text-xs text-slate-500 mt-0.5">{{ opt.hint }}</div>
                        </button>
                    </div>
                    <small v-if="err('type')" class="p-error">{{ err("type") }}</small>
                </div>
            </div>
        </section>

        <!-- Pricing -->
        <section :class="sectionCls">
            <h3 class="section-title"><i class="pi pi-tag" />{{ t("catalog.form.pricing") }}</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="base_price" :class="labelCls">{{ t("catalog.form.base_price") }} <span class="text-rose-600">*</span></label>
                    <InputNumber inputId="base_price" v-model="form.base_price" class="w-full" inputClass="w-full" :min="0" :maxFractionDigits="2"
                        :invalid="(submitted && (form.base_price === null || form.base_price === '')) || !!err('base_price')" />
                    <small v-if="err('base_price')" class="p-error">{{ err("base_price") }}</small>
                </div>
                <div>
                    <label for="base_discount_price" :class="labelCls">{{ t("catalog.form.discount_price") }}</label>
                    <InputNumber inputId="base_discount_price" v-model="form.base_discount_price" class="w-full" inputClass="w-full" :min="0"
                        :maxFractionDigits="2" :invalid="discountInvalid(form.base_price, form.base_discount_price) || !!err('base_discount_price')" />
                    <small v-if="err('base_discount_price')" class="p-error">{{ err("base_discount_price") }}</small>
                    <small v-else-if="discountInvalid(form.base_price, form.base_discount_price)" class="p-error">{{ t("catalog.form.errors.discount") }}</small>
                    <small v-else-if="form.base_discount_price > 0 && form.base_price > 0" class="text-emerald-700">
                        {{ t("catalog.form.discount_off", { pct: Math.round((1 - form.base_discount_price / form.base_price) * 100) }) }}
                    </small>
                    <small v-else class="text-slate-500">{{ t("catalog.form.discount_hint") }}</small>
                </div>
            </div>
        </section>

        <!-- Inventory (simple) -->
        <section v-if="form.type === 'simple'" :class="sectionCls">
            <h3 class="section-title"><i class="pi pi-warehouse" />{{ t("catalog.form.inventory") }}</h3>
            <p class="text-xs text-slate-500 -mt-2 mb-3">{{ t("catalog.form.inventory_hint") }}</p>

            <div v-if="!form.stocks.length" class="text-sm text-slate-500">{{ t("catalog.form.no_warehouses") }}</div>
            <div v-else class="space-y-2">
                <div class="hidden sm:grid grid-cols-12 gap-3 text-xs font-medium text-slate-500 px-1">
                    <div class="col-span-6">{{ t("catalog.stock.warehouse") }}</div>
                    <div class="col-span-3">{{ t("common.quantity") }}</div>
                    <div class="col-span-3">{{ t("products.alert_quantity") }}</div>
                </div>
                <div v-for="(s, i) in form.stocks" :key="s.warehouse_id"
                    class="grid grid-cols-2 sm:grid-cols-12 gap-2 sm:gap-3 items-center rounded-lg border border-slate-200 p-2 sm:p-1.5">
                    <div class="col-span-2 sm:col-span-6 text-sm font-medium text-slate-700 flex items-center gap-2 px-1">
                        <i class="pi pi-building text-slate-400" />{{ warehouseName(s.warehouse_id) }}
                    </div>
                    <div class="sm:col-span-3">
                        <label class="sm:hidden text-[11px] text-slate-500">{{ t("common.quantity") }}</label>
                        <InputNumber v-model="s.quantity" class="w-full" inputClass="w-full" :min="0" :invalid="!!err(`stocks.${i}.quantity`)" />
                    </div>
                    <div class="sm:col-span-3">
                        <label class="sm:hidden text-[11px] text-slate-500">{{ t("products.alert_quantity") }}</label>
                        <InputNumber v-model="s.alert_quantity" class="w-full" inputClass="w-full" :min="0" />
                    </div>
                </div>
            </div>
            <small v-if="err('stocks')" class="p-error">{{ err("stocks") }}</small>
        </section>

        <!-- Variations (variable) -->
        <section v-if="form.type === 'variable'" :class="sectionCls">
            <h3 class="section-title"><i class="pi pi-sitemap" />{{ t("products.variations") }}</h3>

            <div class="rounded-xl bg-slate-50 border border-slate-200 p-3 mb-4">
                <div class="text-sm font-medium text-slate-700 mb-1">{{ t("catalog.form.variant_attributes") }}</div>
                <p class="text-xs text-slate-500 mb-3">{{ t("catalog.form.variant_attributes_hint") }}</p>

                <div v-if="!attributes.length" class="text-sm text-slate-500">{{ t("catalog.form.no_attributes") }}</div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    <div v-for="attr in attributes" :key="attr.id">
                        <label :class="labelCls">{{ attr.display_name || attr.name }}</label>
                        <MultiSelect v-model="selectedAttrValues[attr.id]"
                            :options="(attr.values || []).map((v) => ({ id: v.id, label: v.display_value || v.value }))"
                            optionLabel="label" optionValue="id" display="chip" :placeholder="t('catalog.form.select_values')" class="w-full" />
                    </div>
                </div>

                <div class="flex flex-wrap gap-2 mt-3">
                    <Button :label="t('catalog.form.generate_variations')" icon="pi pi-bolt" size="small" @click="generateVariations" />
                    <Button :label="t('catalog.form.add_manually')" icon="pi pi-plus" size="small" severity="secondary" outlined @click="addVariation" />
                    <Button v-if="form.variations.length" :label="t('catalog.form.clear_variations')" icon="pi pi-times" size="small"
                        severity="secondary" text @click="clearGeneratedVariations" />
                </div>
            </div>

            <small v-if="submitted && !form.variations.length" class="p-error block mb-2">{{ t("catalog.form.errors.variations") }}</small>
            <small v-if="err('variations')" class="p-error block mb-2">{{ err("variations") }}</small>

            <div class="space-y-3">
                <div v-for="(variation, index) in form.variations" :key="index" class="rounded-xl border border-slate-200 p-3">
                    <div class="flex items-center justify-between gap-2 mb-2">
                        <div class="text-sm font-semibold text-slate-700">{{ t("catalog.form.variation_n", { n: index + 1 }) }}</div>
                        <Button icon="pi pi-trash" text rounded size="small" severity="danger" @click="removeVariation(index)"
                            :aria-label="t('common.delete')" v-tooltip.top="t('common.delete')" />
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                        <div>
                            <label :class="labelCls">{{ t("products.sku") }} <span class="text-rose-600">*</span></label>
                            <InputText v-model.trim="variation.sku" class="w-full ltr-nums"
                                :invalid="(submitted && !variation.sku) || !!err(`variations.${index}.sku`)" />
                            <small v-if="err(`variations.${index}.sku`)" class="p-error">{{ err(`variations.${index}.sku`) }}</small>
                        </div>
                        <div>
                            <label :class="labelCls">{{ t("common.price") }} <span class="text-rose-600">*</span></label>
                            <InputNumber v-model="variation.price" :min="0" :maxFractionDigits="2" class="w-full" inputClass="w-full"
                                :invalid="(submitted && (variation.price === null || variation.price === undefined)) || !!err(`variations.${index}.price`)" />
                        </div>
                        <div>
                            <label :class="labelCls">{{ t("catalog.form.discount_price") }}</label>
                            <InputNumber v-model="variation.discount_price" :min="0" :maxFractionDigits="2" class="w-full" inputClass="w-full"
                                :invalid="discountInvalid(variation.price, variation.discount_price) || !!err(`variations.${index}.discount_price`)" />
                        </div>
                        <div>
                            <label :class="labelCls">{{ t("catalog.form.variation_image") }}</label>
                            <InputText v-model.trim="variation.image" class="w-full ltr-nums" :placeholder="t('catalog.form.variation_image_placeholder')" />
                        </div>
                        <div class="sm:col-span-2 lg:col-span-4">
                            <label :class="labelCls">{{ t("catalog.form.attribute_values") }} <span class="text-rose-600">*</span></label>
                            <MultiSelect v-model="variation.attribute_value_ids" :options="attributeValueOptions" optionLabel="label" optionValue="id"
                                display="chip" filter :placeholder="t('catalog.form.select_values')" class="w-full"
                                :invalid="submitted && !(variation.attribute_value_ids || []).length" />
                        </div>
                    </div>

                    <div v-if="variation.stocks?.length" class="mt-3">
                        <div class="text-xs font-medium text-slate-500 mb-1">{{ t("catalog.form.alert_per_warehouse") }}</div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2">
                            <div v-for="(s, si) in variation.stocks" :key="s.warehouse_id ?? si"
                                class="flex items-center gap-2 rounded-lg bg-slate-50 border border-slate-200 px-2 py-1">
                                <span class="text-xs text-slate-600 truncate flex-1">{{ warehouseName(s.warehouse_id) }}</span>
                                <InputNumber v-model="s.alert_quantity" :min="0" size="small" inputClass="!w-20" :placeholder="t('catalog.form.alert')" />
                            </div>
                        </div>
                        <p class="text-[11px] text-slate-500 mt-1">{{ t("catalog.form.variation_stock_hint") }}</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Organization -->
        <section :class="sectionCls">
            <h3 class="section-title"><i class="pi pi-folder" />{{ t("catalog.form.organization") }}</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="category_id" :class="labelCls">{{ t("products.category") }} <span class="text-rose-600">*</span></label>
                    <TreeSelect inputId="category_id" v-model="selectedCategoryKey" :options="categoryTreeNodes" selectionMode="single"
                        :placeholder="t('catalog.form.select_category')" class="w-full" filter
                        :invalid="(submitted && !form.category_id) || !!err('category_id')" />
                    <small v-if="err('category_id')" class="p-error">{{ err("category_id") }}</small>
                    <small v-else-if="submitted && !form.category_id" class="p-error">{{ t("catalog.form.errors.category") }}</small>
                </div>
                <div>
                    <label for="brand_id" :class="labelCls">{{ t("products.brand") }}</label>
                    <Select inputId="brand_id" v-model="form.brand_id" :options="brands" optionLabel="name" optionValue="id"
                        :placeholder="t('catalog.form.select_brand')" filter showClear class="w-full" />
                </div>
                <div>
                    <label for="tax_id" :class="labelCls">{{ t("products.tax") }}</label>
                    <Select inputId="tax_id" v-model="form.tax_id" :options="taxes" optionLabel="name" optionValue="id"
                        :placeholder="t('catalog.form.select_tax')" filter showClear class="w-full" />
                </div>
                <div class="md:col-span-3">
                    <label for="tag_ids" :class="labelCls">{{ t("menu.tags") }}</label>
                    <MultiSelect inputId="tag_ids" v-model="form.tag_ids" :options="tags" optionLabel="name" optionValue="id" display="chip" filter
                        :placeholder="t('catalog.form.select_tags')" class="w-full" />
                </div>
            </div>
        </section>

        <!-- Media -->
        <section :class="sectionCls">
            <h3 class="section-title"><i class="pi pi-image" />{{ t("catalog.form.media") }}</h3>
            <div class="flex flex-wrap items-center gap-4">
                <div class="w-24 h-24 rounded-xl border border-slate-200 bg-slate-50 overflow-hidden flex items-center justify-center shrink-0">
                    <img v-if="photoPreview" :src="photoPreview" :alt="form.name" class="w-full h-full object-cover" />
                    <i v-else class="pi pi-image text-2xl text-slate-300" />
                </div>
                <div class="min-w-0">
                    <FileUpload mode="basic" name="thumbnail" accept="image/jpeg,image/png,image/webp" customUpload :auto="true"
                        @select="handlePhotoUpload" :chooseLabel="photoPreview ? t('catalog.form.change_image') : t('catalog.form.choose_image')"
                        chooseIcon="pi pi-upload" />
                    <small class="block text-slate-500 mt-1">{{ t("catalog.form.image_hint") }}</small>
                    <small v-if="err('thumbnail')" class="p-error">{{ err("thumbnail") }}</small>
                </div>
            </div>
        </section>

        <!-- Physical properties -->
        <section :class="sectionCls">
            <h3 class="section-title"><i class="pi pi-objects-column" />{{ t("catalog.form.physical") }}</h3>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label for="weight" :class="labelCls">{{ t("catalog.form.weight") }}</label>
                    <InputNumber inputId="weight" v-model="form.weight" class="w-full" inputClass="w-full" :min="0" :maxFractionDigits="3" placeholder="1.5" />
                </div>
                <div class="md:col-span-2">
                    <label :class="labelCls">{{ t("catalog.form.dimensions") }}</label>
                    <div class="grid grid-cols-3 gap-2">
                        <InputNumber v-model="form.dimensions.length" :min="0" :maxFractionDigits="2" :placeholder="t('catalog.form.length')" inputClass="w-full" />
                        <InputNumber v-model="form.dimensions.width" :min="0" :maxFractionDigits="2" :placeholder="t('catalog.form.width')" inputClass="w-full" />
                        <InputNumber v-model="form.dimensions.height" :min="0" :maxFractionDigits="2" :placeholder="t('catalog.form.height')" inputClass="w-full" />
                    </div>
                </div>
                <div>
                    <label for="materials" :class="labelCls">{{ t("catalog.form.materials") }}</label>
                    <InputText id="materials" v-model.trim="materialsInput" class="w-full" :placeholder="t('catalog.form.materials_placeholder')" />
                    <small class="text-slate-500">{{ t("catalog.form.materials_hint") }}</small>
                </div>
            </div>
        </section>

        <!-- Description -->
        <section :class="sectionCls">
            <h3 class="section-title"><i class="pi pi-align-left" />{{ t("catalog.form.description_details") }}</h3>
            <div class="space-y-4">
                <div>
                    <label :class="labelCls">{{ t("products.description") }}</label>
                    <Editor v-model="form.description" editorStyle="height: 220px" />
                </div>
                <div>
                    <label :class="labelCls">{{ t("catalog.form.additional_info") }}</label>
                    <Editor v-model="form.additional_info" editorStyle="height: 140px" />
                </div>
            </div>
        </section>

        <!-- SEO (collapsible) -->
        <section :class="sectionCls">
            <button type="button" class="section-title w-full !mb-0 justify-between" @click="showSeo = !showSeo" :aria-expanded="showSeo">
                <span class="flex items-center gap-2"><i class="pi pi-search" />{{ t("catalog.form.seo") }}</span>
                <i :class="showSeo ? 'pi pi-chevron-up' : 'pi pi-chevron-down'" class="text-slate-400 !text-sm" />
            </button>
            <div v-if="showSeo" class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                <div>
                    <label for="meta_title" :class="labelCls">{{ t("catalog.form.meta_title") }}</label>
                    <InputText id="meta_title" v-model.trim="form.meta_title" class="w-full" maxlength="255" />
                </div>
                <div>
                    <label for="meta_keywords" :class="labelCls">{{ t("catalog.form.meta_keywords") }}</label>
                    <InputText id="meta_keywords" v-model.trim="form.meta_keywords" class="w-full" maxlength="255" />
                </div>
                <div class="md:col-span-2">
                    <label for="meta_description" :class="labelCls">{{ t("catalog.form.meta_description") }}</label>
                    <Textarea id="meta_description" v-model="form.meta_description" rows="3" class="w-full" maxlength="255" />
                    <small class="text-slate-500 ltr-nums">{{ (form.meta_description || "").length }}/255</small>
                </div>
            </div>
        </section>

        <!-- Actions -->
        <div class="sticky bottom-0 z-10 -mx-1 px-1 py-3 bg-[var(--surface-ground)] flex flex-wrap justify-end gap-2 border-t border-slate-200">
            <Button type="button" :label="t('shared.terms.cancel')" icon="pi pi-times" severity="secondary" text @click="cancel" />
            <Button type="submit" :label="isEditing ? t('catalog.products.update') : t('catalog.products.create')" icon="pi pi-check"
                :loading="form.processing" />
        </div>
    </form>
</template>

<style scoped>
.section-title {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 1rem;
    font-weight: 600;
    color: var(--p-text-color);
    margin-bottom: 1rem;
}

.section-title .pi {
    color: var(--p-primary-color);
}

:deep(.p-editor-content .ql-editor) {
    min-height: 140px;
}
</style>

<style>
[dir="rtl"] .ql-editor {
    direction: rtl;
    text-align: right;
}
</style>
