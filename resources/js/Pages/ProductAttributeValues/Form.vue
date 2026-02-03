<template>
    <div class="flex flex-col gap-6">

        <!-- Attribute -->
        <div>
            <label class="block font-bold mb-2">Attribute</label>
            <Dropdown
                v-model="form.attribute_id"
                :options="attributes"
                optionLabel="display_name"
                optionValue="id"
                placeholder="Select attribute"
                class="w-full"
                :invalid="submitted && !form.attribute_id"
            />
            <small v-if="submitted && !form.attribute_id" class="text-red-500">
                Attribute is required.
            </small>
        </div>

        <!-- Value -->
        <div>
            <label class="block font-bold mb-2">Value</label>
            <InputText
                v-model.trim="form.value"
                class="w-full"
                :invalid="submitted && !form.value"
            />
            <small v-if="submitted && !form.value" class="text-red-500">
                Value is required.
            </small>
        </div>

        <!-- Display Value -->
        <div>
            <label class="block font-bold mb-2">Display Value</label>
            <InputText
                v-model="form.display_value"
                class="w-full"
            />
            <small class="text-gray-500 text-sm">
                Optional (e.g. “Bright Red”)
            </small>
        </div>

        <!-- Color Code (only for color attributes) -->
        <div v-if="isColorAttribute">
            <label class="block font-bold mb-2">Color</label>
            <div class="flex gap-3 items-center">
                <ColorPicker
                    v-model="form.color_code"
                    format="hex"
                />
                <InputText
                    v-model="form.color_code"
                    placeholder="#FF0000"
                    class="w-full"
                />
            </div>
        </div>

    </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
    form: {
        type: Object,
        required: true,
    },
    attributes: {
        type: Array,
        required: true,
    },
    submitted: {
        type: Boolean,
        default: false,
    },
})

const isColorAttribute = computed(() => {
    const attribute = props.attributes.find(
        a => a.id === props.form.attribute_id
    )
    return attribute?.type === 'color'
})
</script>
