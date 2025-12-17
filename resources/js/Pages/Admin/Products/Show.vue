<script setup>
import { resolveImagePath } from "@/Helpers/imageHelper";
import { router } from "@inertiajs/vue3";
import { computed } from "vue";

import Button from "primevue/button";
import Divider from "primevue/divider";
import Tag from "primevue/tag";

import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";

const props = defineProps({
    product: { type: Object, required: true },
});

const product = computed(() => props.product);

const goBack = () => router.visit(route("products.index"));
const goEdit = () => router.visit(route("products.edit", props.product.id));

const isVariable = computed(() => props.product.type === "variable");

// ✅ Simple product stocks = stocks where variation_id is null
const simpleStocks = computed(() =>
    (props.product.stocks || []).filter((s) => !s.variation_id)
);

// ✅ Total product stock = sum of all product stocks
const totalStock = computed(() =>
    (props.product.stocks || []).reduce(
        (sum, s) => sum + Number(s.quantity || 0),
        0
    )
);

const formatMoney = (val) => {
    if (val === null || val === undefined) return "-";
    return Number(val).toFixed(2);
};

const yesNo = (v) => (v ? "Yes" : "No");

const descriptionHtml = computed(() => {
    return props.product?.description
        ? props.product.description
        : '<span class="text-gray-500">No description</span>';
});

const additionalInfoHtml = computed(() => {
    return props.product?.additional_info
        ? props.product.additional_info
        : '<span class="text-gray-500">No additional info</span>';
});
</script>

<template>
    <AuthenticatedLayout>
        <div class="space-y-6">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-semibold">{{ product.name }}</h2>
                    <p class="text-sm text-gray-500">
                        Type:
                        <span class="font-medium">{{ product.type }}</span>
                        • Status:
                        <span class="font-medium">{{
                            product.is_active ? "Active" : "Inactive"
                        }}</span>
                    </p>
                </div>

                <!-- ONLY 2 buttons -->
                <div class="flex gap-2">
                    <Button
                        label="Stock Movement"
                        icon="pi pi-sort-alt"
                        class="p-button-secondary"
                        @click="
                            router.visit(
                                route('admin.stock.move.form', product.id)
                            )
                        "
                    />

                    <Button
                        label="Back to List"
                        icon="pi pi-arrow-left"
                        class="p-button-secondary"
                        @click="goBack"
                    />
                    <Button label="Edit" icon="pi pi-pencil" @click="goEdit" />
                </div>
            </div>

            <!-- Main card -->
            <div class="card p-4 space-y-4">
                <!-- Top summary -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="flex gap-4 items-start">
                        <img
                            v-if="product.thumbnail"
                            :src="resolveImagePath(product.thumbnail)"
                            class="w-24 h-24 object-cover rounded"
                            alt="thumbnail"
                        />
                        <div
                            v-else
                            class="w-24 h-24 flex items-center justify-center border rounded text-gray-500"
                        >
                            No Image
                        </div>

                        <div>
                            <div class="text-sm text-gray-500">SKU</div>
                            <div class="font-medium">
                                {{ product.sku || "-" }}
                            </div>

                            <div class="text-sm text-gray-500 mt-2">
                                Total Stock
                            </div>
                            <div class="font-medium">{{ totalStock }}</div>
                        </div>
                    </div>

                    <div>
                        <div class="text-sm text-gray-500">Category</div>
                        <div class="font-medium">
                            {{ product.category?.name || "-" }}
                        </div>

                        <div class="text-sm text-gray-500 mt-2">Brand</div>
                        <div class="font-medium">
                            {{ product.brand?.name || "-" }}
                        </div>

                        <div class="text-sm text-gray-500 mt-2">Tax</div>
                        <div class="font-medium">
                            {{ product.tax?.name || "-" }}
                        </div>
                    </div>

                    <div>
                        <div class="text-sm text-gray-500">Base Price</div>
                        <div class="font-medium">
                            {{ formatMoney(product.base_price) }}
                        </div>

                        <div class="text-sm text-gray-500 mt-2">
                            Discount Price
                        </div>
                        <div class="font-medium">
                            {{
                                product.base_discount_price
                                    ? formatMoney(product.base_discount_price)
                                    : "-"
                            }}
                        </div>

                        <div class="text-sm text-gray-500 mt-2">
                            Barcode / Code
                        </div>
                        <div class="font-medium">
                            {{ product.barcode || "-" }} /
                            {{ product.code || "-" }}
                        </div>
                    </div>
                </div>

                <Divider />

                <!-- Tags -->
                <div>
                    <div class="text-sm text-gray-500 mb-2">Tags</div>
                    <div class="flex flex-wrap gap-2">
                        <Tag
                            v-for="t in product.tags || []"
                            :key="t.id"
                            :value="t.name"
                        />
                        <span
                            v-if="!(product.tags || []).length"
                            class="text-gray-500"
                            >No tags</span
                        >
                    </div>
                </div>

                <Divider />

                <!-- Physical -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <div class="text-sm text-gray-500">Weight</div>
                        <div class="font-medium">
                            {{ product.weight ?? "-" }}
                        </div>
                    </div>

                    <div>
                        <div class="text-sm text-gray-500">Dimensions</div>
                        <div class="font-medium">
                            <span v-if="product.dimensions">
                                L: {{ product.dimensions.length ?? "-" }}, W:
                                {{ product.dimensions.width ?? "-" }}, H:
                                {{ product.dimensions.height ?? "-" }}
                            </span>
                            <span v-else>-</span>
                        </div>
                    </div>

                    <div>
                        <div class="text-sm text-gray-500">Materials</div>
                        <div class="font-medium">
                            <span
                                v-if="
                                    Array.isArray(product.materials) &&
                                    product.materials.length
                                "
                            >
                                {{ product.materials.join(", ") }}
                            </span>
                            <span v-else>-</span>
                        </div>
                    </div>
                </div>

                <Divider />

                <!-- Simple Stock table -->
                <div v-if="!isVariable">
                    <h3 class="text-lg font-semibold mb-2">Warehouse Stock</h3>

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
                                <tr
                                    v-for="s in simpleStocks"
                                    :key="s.id"
                                    class="border-b"
                                >
                                    <td class="p-2">
                                        {{ s.warehouse?.name || "-" }}
                                    </td>
                                    <td class="p-2">{{ s.quantity }}</td>
                                    <td class="p-2">
                                        {{ s.alert_quantity ?? "-" }}
                                    </td>
                                </tr>
                                <tr v-if="!simpleStocks.length">
                                    <td class="p-2 text-gray-500" colspan="3">
                                        No warehouse stock set.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Variable variations -->
                <div v-else>
                    <h3 class="text-lg font-semibold mb-2">Variations</h3>

                    <div class="overflow-x-auto border rounded">
                        <table class="min-w-full text-sm">
                            <thead>
                                <tr class="border-b">
                                    <th class="p-2 text-left">SKU</th>
                                    <th class="p-2 text-left">Price</th>
                                    <th class="p-2 text-left">Discount</th>
                                    <th class="p-2 text-left">Attributes</th>
                                    <th class="p-2 text-left">
                                        Warehouse Stock
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="v in product.variations || []"
                                    :key="v.id"
                                    class="border-b align-top"
                                >
                                    <td class="p-2 font-medium">{{ v.sku }}</td>
                                    <td class="p-2">
                                        {{ formatMoney(v.price) }}
                                    </td>
                                    <td class="p-2">
                                        {{
                                            v.discount_price
                                                ? formatMoney(v.discount_price)
                                                : "-"
                                        }}
                                    </td>

                                    <td class="p-2">
                                        <div class="space-y-1">
                                            <div
                                                v-for="av in v.attribute_values ||
                                                v.attributeValues ||
                                                []"
                                                :key="av.id"
                                            >
                                                <span class="text-gray-500">
                                                    {{
                                                        av.attribute
                                                            ?.display_name ||
                                                        av.attribute?.name ||
                                                        "Attr"
                                                    }}:
                                                </span>
                                                <span class="font-medium">{{
                                                    av.display_value || av.value
                                                }}</span>
                                            </div>
                                            <span
                                                v-if="
                                                    !(
                                                        v.attribute_values ||
                                                        v.attributeValues ||
                                                        []
                                                    ).length
                                                "
                                                class="text-gray-500"
                                            >
                                                No attributes
                                            </span>
                                        </div>
                                    </td>

                                    <td class="p-2">
                                        <div class="space-y-1">
                                            <div
                                                v-for="s in v.stocks || []"
                                                :key="s.id"
                                                class="flex justify-between gap-4"
                                            >
                                                <span class="text-gray-600">{{
                                                    s.warehouse?.name || "-"
                                                }}</span>
                                                <span class="font-medium">{{
                                                    s.quantity
                                                }}</span>
                                            </div>
                                            <span
                                                v-if="!(v.stocks || []).length"
                                                class="text-gray-500"
                                                >No stock</span
                                            >
                                        </div>
                                    </td>
                                </tr>

                                <tr v-if="!(product.variations || []).length">
                                    <td class="p-2 text-gray-500" colspan="5">
                                        No variations found.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <Divider />

                <!-- Description -->
                <div>
                    <h3 class="text-lg font-semibold mb-2">Description</h3>
                    <div
                        class="prose max-w-none"
                        v-html="descriptionHtml"
                    ></div>
                </div>

                <!-- Additional info -->
                <div>
                    <h3 class="text-lg font-semibold mb-2">
                        Additional Information
                    </h3>
                    <div
                        class="prose max-w-none"
                        v-html="additionalInfoHtml"
                    ></div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
