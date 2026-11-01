<template>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="md:col-span-2">
            <label for="attribute_id" class="block text-sm font-medium text-slate-700 mb-1.5">
                {{ t("catalog.attributes.attribute") }} <span class="text-rose-600">*</span>
            </label>
            <Select inputId="attribute_id" v-model="form.attribute_id" :options="attributes" optionLabel="display_name" optionValue="id" filter
                :placeholder="t('catalog.attributes.select_attribute')" class="w-full"
                :invalid="(submitted && !form.attribute_id) || !!form.errors?.attribute_id" />
            <small v-if="form.errors?.attribute_id" class="p-error">{{ form.errors.attribute_id }}</small>
            <small v-else-if="submitted && !form.attribute_id" class="p-error">{{ t("catalog.validation.attribute_required") }}</small>
        </div>

        <div>
            <label for="value" class="block text-sm font-medium text-slate-700 mb-1.5">{{ t("catalog.attributes.value") }} <span class="text-rose-600">*</span></label>
            <InputText id="value" v-model.trim="form.value" class="w-full" :invalid="(submitted && !form.value) || !!form.errors?.value"
                :placeholder="t('catalog.attributes.value_placeholder')" />
            <small v-if="form.errors?.value" class="p-error">{{ form.errors.value }}</small>
            <small v-else-if="submitted && !form.value" class="p-error">{{ t("catalog.validation.value_required") }}</small>
        </div>

        <div>
            <label for="display_value" class="block text-sm font-medium text-slate-700 mb-1.5">{{ t("catalog.attributes.display_value") }}</label>
            <InputText id="display_value" v-model="form.display_value" class="w-full" :placeholder="t('catalog.attributes.display_value_placeholder')" />
            <small class="text-slate-500">{{ t("catalog.attributes.display_value_hint") }}</small>
        </div>

        <div v-if="isColorAttribute" class="md:col-span-2">
            <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ t("catalog.attributes.color") }}</label>
            <div class="flex gap-3 items-center">
                <ColorPicker v-model="pickerValue" format="hex" />
                <InputText v-model="form.color_code" placeholder="#FF0000" class="w-40 ltr-nums" maxlength="7" />
                <span v-if="form.color_code" class="w-8 h-8 rounded-lg border border-slate-300" :style="{ backgroundColor: form.color_code }" />
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from "vue";
import { useI18n } from "vue-i18n";
import ColorPicker from "primevue/colorpicker";
import InputText from "primevue/inputtext";
import Select from "primevue/select";

const { t } = useI18n();

const props = defineProps({
    form: { type: Object, required: true },
    attributes: { type: Array, default: () => [] },
    submitted: { type: Boolean, default: false },
});

const isColorAttribute = computed(() => props.attributes.find((a) => a.id === props.form.attribute_id)?.type === "color");

// ColorPicker works with "ff0000"; we store "#ff0000" so it renders as a CSS colour everywhere
const pickerValue = computed({
    get: () => (props.form.color_code || "").replace(/^#/, "") || null,
    set: (v) => (props.form.color_code = v ? `#${String(v).replace(/^#/, "")}` : null),
});
</script>
