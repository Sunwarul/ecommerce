<script setup>
import { $t, updatePreset, updateSurfacePalette } from "@primevue/themes";
import Button from "primevue/button";
import SelectButton from "primevue/selectbutton";
import { computed } from "vue";
import { useI18n } from "vue-i18n";
import { DEFAULT_THEME, PRESETS, PRIMARY_COLORS, SURFACES, primaryExtension } from "@/Helpers/Theme";
import { useLayout } from "./LayoutComposable";

const { t } = useI18n();
const { layoutConfig, setDarkMode, setMenuMode } = useLayout();

const presetOptions = Object.keys(PRESETS);
const modeOptions = computed(() => [
    { label: t("layout.light"), value: false, icon: "pi pi-sun" },
    { label: t("layout.dark"), value: true, icon: "pi pi-moon" },
]);
const menuModeOptions = computed(() => [
    { label: t("layout.menu_static"), value: "static" },
    { label: t("layout.menu_overlay"), value: "overlay" },
]);

function setPrimary(name) {
    layoutConfig.primary = name;
    updatePreset(primaryExtension(name));
}

function setSurface(name) {
    layoutConfig.surface = name;
    updateSurfacePalette(SURFACES[name]);
}

function setPreset(name) {
    if (!name) return;
    layoutConfig.preset = name;
    $t().preset(PRESETS[name]).preset(primaryExtension(layoutConfig.primary)).surfacePalette(SURFACES[layoutConfig.surface]).use({ useDefaultOptions: true });
}

function resetTheme() {
    setPreset(DEFAULT_THEME.preset);
    setPrimary(DEFAULT_THEME.primary);
    setSurface(DEFAULT_THEME.surface);
    setDarkMode(DEFAULT_THEME.darkTheme);
    setMenuMode(DEFAULT_THEME.menuMode);
}
</script>

<template>
    <div class="flex flex-col gap-4 w-72 max-w-[85vw]">
        <div class="flex flex-col gap-2">
            <span class="text-sm font-semibold text-slate-600">{{ t("layout.mode") }}</span>
            <SelectButton :modelValue="layoutConfig.darkTheme" @update:modelValue="(v) => v !== null && setDarkMode(v)"
                :options="modeOptions" optionLabel="label" optionValue="value" :allowEmpty="false" class="w-full [&>*]:flex-1">
                <template #option="{ option }">
                    <i :class="option.icon" class="me-1" />{{ option.label }}
                </template>
            </SelectButton>
        </div>

        <div>
            <span class="text-sm font-semibold text-slate-600">{{ t("layout.primary_color") }}</span>
            <div class="pt-2 flex gap-2 flex-wrap">
                <button v-for="(palette, name) in PRIMARY_COLORS" :key="name" type="button" :title="name"
                    :aria-label="name" :aria-pressed="layoutConfig.primary === name" @click="setPrimary(name)"
                    class="w-6 h-6 rounded-full p-0 cursor-pointer outline outline-2 outline-offset-2 transition"
                    :class="layoutConfig.primary === name ? 'outline-slate-500' : 'outline-transparent'"
                    :style="{ backgroundColor: palette['500'] }"></button>
            </div>
        </div>

        <div>
            <span class="text-sm font-semibold text-slate-600">{{ t("layout.surface_color") }}</span>
            <div class="pt-2 flex gap-2 flex-wrap">
                <button v-for="(palette, name) in SURFACES" :key="name" type="button" :title="name" :aria-label="name"
                    :aria-pressed="layoutConfig.surface === name" @click="setSurface(name)"
                    class="w-6 h-6 rounded-full p-0 cursor-pointer outline outline-2 outline-offset-2 transition"
                    :class="layoutConfig.surface === name ? 'outline-slate-500' : 'outline-transparent'"
                    :style="{ backgroundColor: palette['500'] }"></button>
            </div>
        </div>

        <div class="flex flex-col gap-2">
            <span class="text-sm font-semibold text-slate-600">{{ t("layout.style") }}</span>
            <SelectButton :modelValue="layoutConfig.preset" @update:modelValue="setPreset" :options="presetOptions"
                :allowEmpty="false" class="w-full [&>*]:flex-1" />
        </div>

        <div class="flex flex-col gap-2">
            <span class="text-sm font-semibold text-slate-600">{{ t("layout.menu_mode") }}</span>
            <SelectButton :modelValue="layoutConfig.menuMode" @update:modelValue="(v) => v && setMenuMode(v)"
                :options="menuModeOptions" :allowEmpty="false" optionLabel="label" optionValue="value" class="w-full [&>*]:flex-1" />
        </div>

        <Button :label="t('layout.reset_theme')" icon="pi pi-refresh" severity="secondary" text size="small" @click="resetTheme" />
    </div>
</template>
