<script setup>
import { useI18n } from "vue-i18n";

const { t } = useI18n();

defineProps({
    label: { type: String, required: true },
    value: { type: [String, Number], required: true },
    hint: { type: String, default: "" },
    icon: { type: String, default: "" },
    tone: { type: String, default: "slate" }, // slate | emerald | rose | amber | sky | violet
    growth: { type: Number, default: undefined }, // % vs previous period (null = new)
});

const tones = {
    slate: "bg-slate-100 text-slate-600",
    emerald: "bg-emerald-50 text-emerald-600",
    rose: "bg-rose-50 text-rose-600",
    amber: "bg-amber-50 text-amber-600",
    sky: "bg-sky-50 text-sky-600",
    violet: "bg-violet-50 text-violet-600",
};
</script>

<template>
    <div class="bg-white rounded-xl border border-slate-200 p-4 flex flex-col gap-1 min-w-0">
        <div class="flex items-start justify-between gap-2">
            <span class="text-xs font-medium text-slate-500 uppercase tracking-wide">{{ label }}</span>
            <span v-if="icon" class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0" :class="tones[tone]">
                <i :class="icon" class="text-sm" />
            </span>
        </div>
        <div class="text-xl font-bold text-slate-800 truncate ltr-nums text-start" :title="String(value)">{{ value }}</div>
        <div class="flex items-center gap-2 text-xs text-slate-500 min-h-[1rem]">
            <span v-if="growth !== undefined && growth !== null"
                :class="growth >= 0 ? 'text-emerald-700' : 'text-rose-700'" class="font-semibold flex items-center gap-0.5">
                <i :class="growth >= 0 ? 'pi pi-arrow-up' : 'pi pi-arrow-down'" class="!text-[10px]" />
                {{ Math.abs(growth) }}%
            </span>
            <span v-else-if="growth === null" class="text-sky-700 font-semibold">{{ t('reports.new') }}</span>
            <span v-if="hint" class="truncate">{{ hint }}</span>
        </div>
    </div>
</template>
