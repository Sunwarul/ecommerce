<template>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label for="display_name" class="block text-sm font-medium text-slate-700 mb-1.5">
                {{ t("catalog.attributes.display_name") }} <span class="text-rose-600">*</span>
            </label>
            <InputText id="display_name" v-model.trim="form.display_name" autofocus class="w-full"
                :invalid="(submitted && !form.display_name) || !!form.errors?.display_name" :placeholder="t('catalog.attributes.display_name_placeholder')" />
            <small v-if="form.errors?.display_name" class="p-error">{{ form.errors.display_name }}</small>
            <small v-else-if="submitted && !form.display_name" class="p-error">{{ t("catalog.validation.display_name_required") }}</small>
            <small v-else class="text-slate-500">{{ t("catalog.attributes.display_name_hint") }}</small>
        </div>

        <div>
            <label for="name" class="block text-sm font-medium text-slate-700 mb-1.5">
                {{ t("catalog.attributes.key") }} <span class="text-rose-600">*</span>
            </label>
            <InputText id="name" v-model.trim="form.name" class="w-full ltr-nums" :invalid="(submitted && !form.name) || !!form.errors?.name"
                placeholder="color" @input="keyTouched = true" />
            <small v-if="form.errors?.name" class="p-error">{{ form.errors.name }}</small>
            <small v-else-if="submitted && !form.name" class="p-error">{{ t("catalog.validation.name_required") }}</small>
            <small v-else class="text-slate-500">{{ t("catalog.attributes.key_hint") }}</small>
        </div>

        <div class="md:col-span-2">
            <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ t("catalog.attributes.input_type") }} <span class="text-rose-600">*</span></label>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                <button v-for="opt in types" :key="opt.value" type="button" class="rounded-xl border p-2.5 text-start transition-colors"
                    :class="form.type === opt.value ? 'border-emerald-500 bg-emerald-50' : 'border-slate-200 hover:bg-slate-50'"
                    @click="form.type = opt.value">
                    <i :class="[opt.icon, form.type === opt.value ? 'text-emerald-600' : 'text-slate-400']" />
                    <div class="text-sm font-medium text-slate-800 mt-1">{{ t(`catalog.attributes.types.${opt.value}`) }}</div>
                </button>
            </div>
            <small v-if="submitted && !form.type" class="p-error">{{ t("catalog.validation.type_required") }}</small>
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ t("common.status") }}</label>
            <div class="flex items-center gap-3">
                <ToggleSwitch v-model="form.is_active" inputId="attr_active" />
                <label for="attr_active" class="text-sm text-slate-700">{{ form.is_active ? t("shared.status.active") : t("shared.status.inactive") }}</label>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, watch } from "vue";
import { useI18n } from "vue-i18n";
import InputText from "primevue/inputtext";
import ToggleSwitch from "primevue/toggleswitch";

const { t } = useI18n();

const props = defineProps({
    form: { type: Object, required: true },
    submitted: { type: Boolean, default: false },
});

const types = [
    { value: "select", icon: "pi pi-list" },
    { value: "radio", icon: "pi pi-circle" },
    { value: "color", icon: "pi pi-palette" },
    { value: "button", icon: "pi pi-th-large" },
];

// Suggest a machine key from the display name for new attributes
const keyTouched = ref(!!props.form.name);
watch(
    () => props.form.display_name,
    (v) => {
        if (keyTouched.value) return;
        props.form.name = (v || "").toLowerCase().trim().replace(/\s+/g, "_").replace(/[^\p{L}\p{N}_]+/gu, "");
    }
);
</script>
