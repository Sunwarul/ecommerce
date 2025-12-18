<script setup>
import { router, useForm } from "@inertiajs/vue3";
import { computed, watch } from "vue";

import Button from "primevue/button";
import Card from "primevue/card";
import Dropdown from "primevue/dropdown";
import InputNumber from "primevue/inputnumber";
import InputText from "primevue/inputtext";
import Message from "primevue/message";
import Textarea from "primevue/textarea";

import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { useToast } from "primevue";

const props = defineProps({
    product: { type: Object, required: true },
    warehouses: { type: Array, required: true },
});
const toast = useToast();

const typeOptions = [
    { label: "Stock IN", value: "in" },
    { label: "Stock OUT", value: "out" },
    { label: "Transfer", value: "transfer" },
    { label: "Adjust (Set Qty)", value: "adjust" },
];

const variationOptions = computed(() => {
    const vars = props.product.variations || [];
    return vars.map((v) => ({
        label: v.sku || `Variation #${v.id}`,
        value: v.id,
    }));
});

const isVariable = computed(() => props.product.type === "variable");

const form = useForm({
    type: "in",
    product_id: props.product.id,
    variation_id: null,

    quantity: null,

    from_warehouse_id: null,
    to_warehouse_id: null,

    reference: "",
    note: "",
});

const needsFrom = computed(() => ["out", "transfer"].includes(form.type));
const needsTo = computed(() =>
    ["in", "transfer", "adjust"].includes(form.type)
);

watch(
    () => form.type,
    () => {
        // reset warehouses when switching types
        if (!needsFrom.value) form.from_warehouse_id = null;
        if (!needsTo.value) form.to_warehouse_id = null;

        // adjust can be "set quantity", allow 0? your backend requires gt:0
        // so keep as gt:0 for now
    }
);

const resetFormAfterSave = () => {
    form.quantity = null;
    form.reference = "";
    form.note = "";
    // keep type + warehouses selection (optional)
};

const submitSave = () => {
    form.post(route("admin.stock.move"), {
        preserveScroll: true,
        onSuccess: () => {
            toast.add({
                severity: "success",
                summary: "Saved",
                detail: "Stock updated successfully.",
                life: 2500,
            });
            resetFormAfterSave();
        },
        onError: () => {
            toast.add({
                severity: "error",
                summary: "Error",
                detail: "Please fix the errors.",
                life: 3000,
            });
        },
    });
};

const submitSaveAndMove = () => {
    form.transform((data) => ({ ...data, move: 1 })).post(
        route("admin.stock.move"),
        {
            preserveScroll: true,
            onError: () => {
                toast.add({
                    severity: "error",
                    summary: "Error",
                    detail: "Please fix the errors.",
                    life: 3000,
                });
            },
        }
    );
};

const back = () => router.visit(route("products.show", props.product.id));
</script>

<template>
    <AuthenticatedLayout>
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-semibold">Stock Movement</h2>
                    <p class="text-sm text-gray-500">
                        {{ product.name }} ({{ product.type }})
                    </p>
                </div>

                <div class="flex gap-2">
                    <div class="flex gap-2">
                        <Button
                            label="Back"
                            icon="pi pi-arrow-left"
                            class="p-button-text"
                            @click="back"
                        />
                        <Button
                            label="Save"
                            icon="pi pi-save"
                            @click="submitSave"
                            :loading="form.processing"
                        />
                    </div>
                </div>
            </div>

            <Card>
                <template #content>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Type -->
                        <div>
                            <label class="block font-medium mb-2"
                                >Movement Type *</label
                            >
                            <Dropdown
                                v-model="form.type"
                                :options="typeOptions"
                                optionLabel="label"
                                optionValue="value"
                                class="w-full"
                            />
                            <small v-if="form.errors.type" class="p-error">{{
                                form.errors.type
                            }}</small>
                        </div>

                        <!-- Variation (only if product is variable) -->
                        <div v-if="isVariable">
                            <label class="block font-medium mb-2"
                                >Variation (optional)</label
                            >
                            <Dropdown
                                v-model="form.variation_id"
                                :options="variationOptions"
                                optionLabel="label"
                                optionValue="value"
                                placeholder="Select variation (or leave empty)"
                                class="w-full"
                                showClear
                            />
                            <small
                                v-if="form.errors.variation_id"
                                class="p-error"
                                >{{ form.errors.variation_id }}</small
                            >
                            <small class="text-gray-500 text-xs">
                                If you select a variation, movement applies to
                                that variation stock.
                            </small>
                        </div>

                        <!-- Quantity -->
                        <div>
                            <label class="block font-medium mb-2">
                                Quantity *
                                <span
                                    v-if="form.type === 'adjust'"
                                    class="text-xs text-gray-500"
                                    >(Set target qty)</span
                                >
                            </label>
                            <InputNumber
                                v-model.number="form.quantity"
                                class="w-full"
                                :min="0"
                            />
                            <small
                                v-if="form.errors.quantity"
                                class="p-error"
                                >{{ form.errors.quantity }}</small
                            >
                        </div>

                        <!-- From warehouse -->
                        <div v-if="needsFrom">
                            <label class="block font-medium mb-2"
                                >From Warehouse *</label
                            >
                            <Dropdown
                                v-model="form.from_warehouse_id"
                                :options="warehouses"
                                optionLabel="name"
                                optionValue="id"
                                placeholder="Select warehouse"
                                class="w-full"
                            />
                            <small
                                v-if="form.errors.from_warehouse_id"
                                class="p-error"
                            >
                                {{ form.errors.from_warehouse_id }}
                            </small>
                        </div>

                        <!-- To warehouse -->
                        <div v-if="needsTo">
                            <label class="block font-medium mb-2"
                                >To Warehouse *</label
                            >
                            <Dropdown
                                v-model="form.to_warehouse_id"
                                :options="warehouses"
                                optionLabel="name"
                                optionValue="id"
                                placeholder="Select warehouse"
                                class="w-full"
                            />
                            <small
                                v-if="form.errors.to_warehouse_id"
                                class="p-error"
                            >
                                {{ form.errors.to_warehouse_id }}
                            </small>
                        </div>

                        <!-- Reference -->
                        <div class="md:col-span-2">
                            <label class="block font-medium mb-2"
                                >Reference (optional)</label
                            >
                            <InputText
                                v-model="form.reference"
                                class="w-full"
                                placeholder="e.g. PO-102, INV-55, TRF-09"
                            />
                            <small
                                v-if="form.errors.reference"
                                class="p-error"
                                >{{ form.errors.reference }}</small
                            >
                        </div>

                        <!-- Note -->
                        <div class="md:col-span-2">
                            <label class="block font-medium mb-2"
                                >Note (optional)</label
                            >
                            <Textarea
                                v-model="form.note"
                                rows="3"
                                class="w-full"
                            />
                            <small v-if="form.errors.note" class="p-error">{{
                                form.errors.note
                            }}</small>
                        </div>
                    </div>

                    <!-- extra validation helper -->
                    <div class="mt-4">
                        <Message
                            v-if="
                                form.type === 'transfer' &&
                                form.from_warehouse_id &&
                                form.to_warehouse_id &&
                                form.from_warehouse_id === form.to_warehouse_id
                            "
                            severity="warn"
                        >
                            From and To warehouse should not be the same.
                        </Message>
                    </div>

                    <div class="flex justify-end gap-2 mt-5">
                        <Button
                            label="Cancel"
                            class="p-button-text"
                            @click="back"
                        />
                        <Button
                            label="Save & Move"
                            icon="pi pi-check"
                            class="p-button-success"
                            @click="submitSaveAndMove"
                            :loading="form.processing"
                        />
                    </div>
                </template>
            </Card>
        </div>
    </AuthenticatedLayout>
</template>
