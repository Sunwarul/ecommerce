<script setup>
import { resolveImagePath } from "@/Helpers/imageHelper";
import { money } from "@/Helpers/format";
import { Head, router } from "@inertiajs/vue3";
import { computed } from "vue";
import { useI18n } from "vue-i18n";

import Button from "primevue/button";
import Tag from "primevue/tag";

import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";

const { t } = useI18n();

const props = defineProps({
    product: { type: Object, required: true },
});

const product = computed(() => props.product);

const goBack = () => router.visit(route("products.index"));
const goEdit = () => router.visit(route("products.edit", props.product.id));
const goStockMove = () => router.visit(route("admin.stock.move.form", props.product.id));

const isVariable = computed(() => props.product.type === "variable");

// Simple product stocks = stocks where variation_id is null
const simpleStocks = computed(() => (props.product.stocks || []).filter((s) => !s.variation_id));

const totalStock = computed(() => (props.product.stocks || []).reduce((sum, s) => sum + Number(s.quantity || 0), 0));

const hasDiscount = computed(
    () => Number(product.value.base_discount_price) > 0 && Number(product.value.base_discount_price) < Number(product.value.base_price)
);

const fmt = (v) => (v === null || v === undefined || v === "" ? "—" : money(v));

const qtyState = (qty, alert) => {
    const q = Number(qty || 0);
    if (q <= 0) return { severity: "danger", label: t("catalog.stock.out_of_stock") };
    if (alert && q <= Number(alert)) return { severity: "warn", label: t("catalog.stock.low_stock") };
    return { severity: "success", label: t("catalog.stock.in_stock") };
};

const attributeValues = (v) => v.attribute_values || v.attributeValues || [];

const dimensionsText = computed(() => {
    const d = product.value.dimensions;
    if (!d || (d.length == null && d.width == null && d.height == null)) return "—";
    return `${d.length ?? "—"} × ${d.width ?? "—"} × ${d.height ?? "—"} cm`;
});

const facts = computed(() => [
    { label: t("products.sku"), value: product.value.sku || "—", mono: true },
    { label: t("products.barcode"), value: product.value.barcode || "—", mono: true },
    { label: t("products.product_code"), value: product.value.code || "—", mono: true },
    { label: t("products.category"), value: product.value.category?.name || "—" },
    { label: t("products.brand"), value: product.value.brand?.name || "—" },
    { label: t("products.tax"), value: product.value.tax?.name || "—" },
    { label: t("catalog.form.weight"), value: product.value.weight ?? "—" },
    { label: t("catalog.form.dimensions"), value: dimensionsText.value },
    {
        label: t("catalog.form.materials"),
        value: Array.isArray(product.value.materials) && product.value.materials.length ? product.value.materials.join(", ") : "—",
    },
]);
</script>

<template>
    <Head :title="product.name" />
    <AuthenticatedLayout>
        <div class="max-w-6xl mx-auto space-y-4">
            <!-- Header -->
            <div class="flex flex-wrap items-start justify-between gap-3">
                <div class="min-w-0">
                    <button type="button" class="text-sm text-slate-500 hover:text-emerald-700 flex items-center gap-1 mb-1" @click="goBack">
                        <i class="pi pi-arrow-left !text-xs" /> {{ t("menu.products") }}
                    </button>
                    <h1 class="text-xl md:text-2xl font-semibold text-slate-800 break-words">{{ product.name }}</h1>
                    <div class="mt-1 flex flex-wrap items-center gap-1.5">
                        <Tag :severity="isVariable ? 'info' : 'secondary'"
                            :value="isVariable ? t('catalog.products.type_variable') : t('catalog.products.type_simple')" />
                        <Tag :severity="product.is_active ? 'success' : 'secondary'"
                            :value="product.is_active ? t('shared.status.active') : t('shared.status.inactive')" />
                    </div>
                </div>
                <div class="flex flex-wrap gap-2">
                    <Button :label="t('catalog.stock.move')" icon="pi pi-arrow-right-arrow-left" severity="secondary" outlined @click="goStockMove" />
                    <Button :label="t('common.edit')" icon="pi pi-pencil" @click="goEdit" />
                </div>
            </div>

            <!-- Summary -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                <div class="card !mb-0 !p-4 flex flex-col sm:flex-row lg:flex-col gap-4">
                    <div class="w-full sm:w-40 lg:w-full aspect-square rounded-xl border border-slate-200 bg-slate-50 overflow-hidden flex items-center justify-center shrink-0">
                        <img v-if="product.thumbnail" :src="resolveImagePath(product.thumbnail)" :alt="product.name" class="w-full h-full object-contain" />
                        <div v-else class="text-slate-400 flex flex-col items-center gap-1 text-sm">
                            <i class="pi pi-image text-3xl" />{{ t("catalog.common.no_image") }}
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-3 flex-1">
                        <div class="rounded-xl bg-emerald-50 border border-emerald-200 p-3">
                            <div class="text-xs text-slate-500">{{ t("common.price") }}</div>
                            <div class="text-lg font-bold text-slate-800 ltr-nums">{{ fmt(hasDiscount ? product.base_discount_price : product.base_price) }}</div>
                            <div v-if="hasDiscount" class="text-xs text-slate-500 line-through ltr-nums">{{ fmt(product.base_price) }}</div>
                        </div>
                        <div class="rounded-xl bg-slate-50 border border-slate-200 p-3">
                            <div class="text-xs text-slate-500">{{ t("catalog.stock.total") }}</div>
                            <div class="text-lg font-bold text-slate-800 ltr-nums">{{ totalStock }}</div>
                            <Tag :severity="qtyState(totalStock).severity" :value="qtyState(totalStock).label" class="!text-[11px]" />
                        </div>
                    </div>
                </div>

                <div class="card !mb-0 !p-4 lg:col-span-2">
                    <h3 class="text-base font-semibold text-slate-800 mb-3 flex items-center gap-2">
                        <i class="pi pi-info-circle text-emerald-600" />{{ t("products.product_details") }}
                    </h3>
                    <dl class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-x-6 gap-y-3">
                        <div v-for="f in facts" :key="f.label" class="min-w-0">
                            <dt class="text-xs text-slate-500">{{ f.label }}</dt>
                            <dd class="font-medium text-slate-800 break-words" :class="f.mono ? 'ltr-nums' : ''">{{ f.value }}</dd>
                        </div>
                    </dl>
                    <div class="mt-4">
                        <div class="text-xs text-slate-500 mb-1.5">{{ t("menu.tags") }}</div>
                        <div class="flex flex-wrap gap-1.5">
                            <Tag v-for="tg in product.tags || []" :key="tg.id" :value="tg.name" severity="secondary" />
                            <span v-if="!(product.tags || []).length" class="text-sm text-slate-400">{{ t("catalog.common.none") }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stock (simple) -->
            <div v-if="!isVariable" class="card !mb-0 !p-4">
                <h3 class="text-base font-semibold text-slate-800 mb-3 flex items-center gap-2">
                    <i class="pi pi-warehouse text-emerald-600" />{{ t("catalog.form.inventory") }}
                </h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="border-b border-slate-200 text-slate-500">
                                <th class="p-2 text-start font-medium">{{ t("catalog.stock.warehouse") }}</th>
                                <th class="p-2 text-end font-medium">{{ t("common.quantity") }}</th>
                                <th class="p-2 text-end font-medium">{{ t("products.alert_quantity") }}</th>
                                <th class="p-2 text-start font-medium">{{ t("common.status") }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="s in simpleStocks" :key="s.id" class="border-b border-slate-100">
                                <td class="p-2">{{ s.warehouse?.name || "—" }}</td>
                                <td class="p-2 text-end font-semibold ltr-nums">{{ Number(s.quantity) }}</td>
                                <td class="p-2 text-end ltr-nums">{{ s.alert_quantity ?? "—" }}</td>
                                <td class="p-2"><Tag :severity="qtyState(s.quantity, s.alert_quantity).severity" :value="qtyState(s.quantity, s.alert_quantity).label" class="!text-[11px]" /></td>
                            </tr>
                            <tr v-if="!simpleStocks.length">
                                <td class="p-4 text-center text-slate-500" colspan="4">{{ t("catalog.stock.none_set") }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Variations -->
            <div v-else class="card !mb-0 !p-4">
                <h3 class="text-base font-semibold text-slate-800 mb-3 flex items-center gap-2">
                    <i class="pi pi-sitemap text-emerald-600" />{{ t("products.variations") }}
                    <span class="text-sm font-normal text-slate-500 ltr-nums">({{ (product.variations || []).length }})</span>
                </h3>
                <div v-if="!(product.variations || []).length" class="text-center text-slate-500 py-6">{{ t("catalog.products.no_variations") }}</div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div v-for="v in product.variations || []" :key="v.id" class="rounded-xl border border-slate-200 p-3">
                        <div class="flex items-start justify-between gap-2">
                            <div class="min-w-0">
                                <div class="font-semibold text-slate-800 ltr-nums break-all">{{ v.sku }}</div>
                                <div class="flex flex-wrap gap-1 mt-1">
                                    <Tag v-for="av in attributeValues(v)" :key="av.id" severity="secondary" class="!text-[11px]"
                                        :value="`${av.attribute?.display_name || av.attribute?.name || ''}: ${av.display_value || av.value}`" />
                                    <span v-if="!attributeValues(v).length" class="text-xs text-slate-400">{{ t("catalog.common.none") }}</span>
                                </div>
                            </div>
                            <div class="text-end shrink-0">
                                <div class="font-bold text-slate-800 ltr-nums">{{ fmt(v.discount_price > 0 ? v.discount_price : v.price) }}</div>
                                <div v-if="v.discount_price > 0" class="text-xs text-slate-400 line-through ltr-nums">{{ fmt(v.price) }}</div>
                            </div>
                        </div>
                        <div class="mt-2 pt-2 border-t border-slate-100 space-y-1">
                            <div v-for="s in v.stocks || []" :key="s.id" class="flex items-center justify-between gap-3 text-sm">
                                <span class="text-slate-600 truncate">{{ s.warehouse?.name || "—" }}</span>
                                <span class="font-semibold text-slate-800 ltr-nums">{{ Number(s.quantity) }}</span>
                            </div>
                            <div v-if="!(v.stocks || []).length" class="text-xs text-slate-400">{{ t("catalog.stock.none_set") }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Description -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                <div class="card !mb-0 !p-4">
                    <h3 class="text-base font-semibold text-slate-800 mb-2">{{ t("products.description") }}</h3>
                    <div v-if="product.description" class="prose prose-sm max-w-none" v-html="product.description" />
                    <p v-else class="text-sm text-slate-400">{{ t("catalog.common.none") }}</p>
                </div>
                <div class="card !mb-0 !p-4">
                    <h3 class="text-base font-semibold text-slate-800 mb-2">{{ t("catalog.form.additional_info") }}</h3>
                    <div v-if="product.additional_info" class="prose prose-sm max-w-none" v-html="product.additional_info" />
                    <p v-else class="text-sm text-slate-400">{{ t("catalog.common.none") }}</p>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
