<script setup>
import { Head, router, useForm } from "@inertiajs/vue3";
import { computed, watch } from "vue";
import { useI18n } from "vue-i18n";

import Button from "primevue/button";
import InputNumber from "primevue/inputnumber";
import InputText from "primevue/inputtext";
import Message from "primevue/message";
import Select from "primevue/select";
import SelectButton from "primevue/selectbutton";
import Textarea from "primevue/textarea";
import { useToast } from "primevue/usetoast";

import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { useFlashToast } from "@/Composables/useFlashToast";
import { firstError } from "@/Helpers/format";

const props = defineProps({
    product: { type: Object, required: true },
    warehouses: { type: Array, required: true },
    branches: { type: Array, required: true },
});

const { t } = useI18n();
const toast = useToast();
useFlashToast();

const typeOptions = computed(() => [
    { label: t("stock.types.in"), value: "in", icon: "pi pi-arrow-down" },
    { label: t("stock.types.out"), value: "out", icon: "pi pi-arrow-up" },
    { label: t("stock.types.transfer"), value: "transfer", icon: "pi pi-arrow-right-arrow-left" },
    { label: t("stock.types.adjust"), value: "adjust", icon: "pi pi-sliders-h" },
]);

const variationOptions = computed(() =>
    (props.product.variations || []).map((v) => ({ label: v.sku || t("stock.variation_n", { id: v.id }), value: v.id }))
);

const isVariable = computed(() => props.product.type === "variable");

const form = useForm({
    type: "in",
    product_id: props.product.id,
    variation_id: null,
    branch_id: null,
    quantity: null,
    from_warehouse_id: null,
    to_warehouse_id: null,
    reference: "",
    note: "",
});

const needsFrom = computed(() => ["out", "transfer"].includes(form.type));
const needsTo = computed(() => ["in", "transfer", "adjust"].includes(form.type));
const sameWarehouse = computed(
    () => form.type === "transfer" && form.from_warehouse_id && form.to_warehouse_id && form.from_warehouse_id === form.to_warehouse_id
);

watch(
    () => form.type,
    () => {
        if (!needsFrom.value) form.from_warehouse_id = null;
        if (!needsTo.value) form.to_warehouse_id = null;
        form.clearErrors();
    }
);

function validateLocally() {
    if (isVariable.value && !form.variation_id) {
        form.setError("variation_id", t("stock.variation_required"));
        return false;
    }
    if (!form.quantity || Number(form.quantity) <= 0) {
        form.setError("quantity", t("stock.quantity_required"));
        return false;
    }
    if (needsFrom.value && !form.from_warehouse_id) {
        form.setError("from_warehouse_id", t("stock.warehouse_required"));
        return false;
    }
    if (needsTo.value && !form.to_warehouse_id) {
        form.setError("to_warehouse_id", t("stock.warehouse_required"));
        return false;
    }
    if (sameWarehouse.value) return false;
    return true;
}

function onError(errors) {
    toast.add({ severity: "error", summary: t("shared.terms.error"), detail: firstError(errors) || t("stock.fix_errors"), life: 3500 });
}

function submit(move = false) {
    form.clearErrors();
    if (!validateLocally()) return;
    form.transform((data) => ({ ...data, move: move ? 1 : 0 })).post(route("admin.stock.move"), {
        preserveScroll: true,
        onSuccess: () => {
            if (!move) form.reset("quantity", "reference", "note");
        },
        onError,
    });
}

const back = () => router.visit(route("products.show", props.product.id));

const fieldError = (k) => form.errors?.[k];
</script>

<template>
    <AuthenticatedLayout>
        <Head :title="t('stock.title')" />

        <div class="p-2 md:p-4 space-y-4 max-w-4xl mx-auto">
            <!-- Header -->
            <div class="flex flex-wrap items-start justify-between gap-3">
                <div class="min-w-0">
                    <h1 class="text-xl md:text-2xl font-semibold text-slate-800 flex items-center gap-2">
                        <i class="pi pi-box text-emerald-600" /> {{ t("stock.title") }}
                    </h1>
                    <p class="text-sm text-slate-500 truncate">
                        {{ product.name }}
                        <span class="text-slate-400">· {{ product.type === "variable" ? t("stock.variable_product") : t("stock.simple_product") }}</span>
                    </p>
                </div>
                <Button :label="t('shared.terms.back')" icon="pi pi-arrow-left" severity="secondary" text @click="back" />
            </div>

            <div class="bg-white rounded-xl border border-slate-200 p-4 md:p-6 space-y-5">
                <!-- Movement type -->
                <div>
                    <span class="block text-sm font-medium text-slate-700 mb-2">{{ t("stock.movement_type") }} <span class="text-rose-600">*</span></span>
                    <SelectButton v-model="form.type" :options="typeOptions" optionLabel="label" optionValue="value" :allowEmpty="false"
                        class="flex-wrap">
                        <template #option="{ option }">
                            <i :class="option.icon" class="text-sm" />
                            <span>{{ option.label }}</span>
                        </template>
                    </SelectButton>
                    <p class="text-xs text-slate-500 mt-2">{{ t(`stock.type_hints.${form.type}`) }}</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Variation -->
                    <div v-if="isVariable" class="md:col-span-2">
                        <label for="variation" class="block text-sm font-medium text-slate-700 mb-1">{{ t("stock.variation") }} <span class="text-rose-600">*</span></label>
                        <Select inputId="variation" v-model="form.variation_id" :options="variationOptions" optionLabel="label" optionValue="value"
                            :placeholder="t('stock.select_variation')" class="w-full" filter showClear :invalid="!!fieldError('variation_id')" />
                        <small v-if="fieldError('variation_id')" class="text-rose-600 text-xs mt-1 block">{{ fieldError("variation_id") }}</small>
                    </div>

                    <!-- Quantity -->
                    <div>
                        <label for="quantity" class="block text-sm font-medium text-slate-700 mb-1">
                            {{ form.type === "adjust" ? t("stock.new_quantity") : t("common.quantity") }} <span class="text-rose-600">*</span>
                        </label>
                        <InputNumber inputId="quantity" v-model="form.quantity" class="w-full" inputClass="w-full" :min="0" :maxFractionDigits="2"
                            :invalid="!!fieldError('quantity')" />
                        <small v-if="form.type === 'adjust'" class="text-xs text-slate-500 mt-1 block">{{ t("stock.adjust_hint") }}</small>
                        <small v-if="fieldError('quantity')" class="text-rose-600 text-xs mt-1 block">{{ fieldError("quantity") }}</small>
                    </div>

                    <!-- Branch -->
                    <div>
                        <label for="branch" class="block text-sm font-medium text-slate-700 mb-1">{{ t("shared.terms.branch") }}</label>
                        <Select inputId="branch" v-model="form.branch_id" :options="branches" optionLabel="name" optionValue="id"
                            :placeholder="t('stock.select_branch')" filter showClear class="w-full" :invalid="!!fieldError('branch_id')" />
                        <small v-if="fieldError('branch_id')" class="text-rose-600 text-xs mt-1 block">{{ fieldError("branch_id") }}</small>
                    </div>

                    <!-- From warehouse -->
                    <div v-if="needsFrom">
                        <label for="from_wh" class="block text-sm font-medium text-slate-700 mb-1">{{ t("stock.from_warehouse") }} <span class="text-rose-600">*</span></label>
                        <Select inputId="from_wh" v-model="form.from_warehouse_id" :options="warehouses" optionLabel="name" optionValue="id"
                            :placeholder="t('stock.select_warehouse')" filter class="w-full" :invalid="!!fieldError('from_warehouse_id')" />
                        <small v-if="fieldError('from_warehouse_id')" class="text-rose-600 text-xs mt-1 block">{{ fieldError("from_warehouse_id") }}</small>
                    </div>

                    <!-- To warehouse -->
                    <div v-if="needsTo">
                        <label for="to_wh" class="block text-sm font-medium text-slate-700 mb-1">
                            {{ form.type === "adjust" ? t("stock.warehouse") : t("stock.to_warehouse") }} <span class="text-rose-600">*</span>
                        </label>
                        <Select inputId="to_wh" v-model="form.to_warehouse_id" :options="warehouses" optionLabel="name" optionValue="id"
                            :placeholder="t('stock.select_warehouse')" filter class="w-full" :invalid="!!fieldError('to_warehouse_id')" />
                        <small v-if="fieldError('to_warehouse_id')" class="text-rose-600 text-xs mt-1 block">{{ fieldError("to_warehouse_id") }}</small>
                    </div>

                    <!-- Reference -->
                    <div class="md:col-span-2">
                        <label for="reference" class="block text-sm font-medium text-slate-700 mb-1">{{ t("stock.reference") }}</label>
                        <InputText id="reference" v-model="form.reference" class="w-full" :placeholder="t('stock.reference_placeholder')"
                            :invalid="!!fieldError('reference')" />
                        <small v-if="fieldError('reference')" class="text-rose-600 text-xs mt-1 block">{{ fieldError("reference") }}</small>
                    </div>

                    <!-- Note -->
                    <div class="md:col-span-2">
                        <label for="note" class="block text-sm font-medium text-slate-700 mb-1">{{ t("stock.note") }}</label>
                        <Textarea id="note" v-model="form.note" rows="3" autoResize class="w-full" :invalid="!!fieldError('note')" />
                        <small v-if="fieldError('note')" class="text-rose-600 text-xs mt-1 block">{{ fieldError("note") }}</small>
                    </div>
                </div>

                <Message v-if="sameWarehouse" severity="warn" :closable="false">{{ t("stock.same_warehouse") }}</Message>
                <Message v-if="fieldError('warehouse')" severity="error" :closable="false">{{ fieldError("warehouse") }}</Message>

                <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-2 pt-2 border-t border-slate-100">
                    <Button :label="t('shared.terms.cancel')" severity="secondary" text @click="back" />
                    <Button :label="t('stock.save_and_add')" icon="pi pi-plus" severity="secondary" outlined :loading="form.processing"
                        :disabled="sameWarehouse" @click="submit(false)" />
                    <Button :label="t('stock.save_and_back')" icon="pi pi-check" :loading="form.processing" :disabled="sameWarehouse"
                        @click="submit(true)" />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
