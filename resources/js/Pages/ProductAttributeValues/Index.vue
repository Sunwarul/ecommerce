<template>
    <div>
        <CrudComponent :form="form">
            <template #columns>
                <Column field="attribute" header="attribute" >
                    <template #body="{ data }">
                        {{ data.attribute?.name || "-" }}
                    </template>
                </Column>
                <Column field="value" header="Value" />
                <Column field="display_value" header="Display Value" />

                <Column field="color_code" header="Color">
                    <template #body="{ data }">
                        <div class="flex items-center gap-2">
                            <span
                                v-if="data.color_code"
                                :style="{ backgroundColor: data.color_code }"
                                class="w-4 h-4 rounded-full border"
                            />
                            <span>
                                {{ data.color_code || 'N/A' }}
                            </span>
                        </div>
                    </template>
                </Column>
            </template>

            <template #form>
                <Form
                    :form="form"
                    :attributes="attributes"
                />
            </template>
        </CrudComponent>
    </div>
</template>

<script setup>
import CrudComponent from "@/Components/CrudComponent.vue";
import { useForm } from "@inertiajs/vue3";
import Form from "./Form.vue";

const props = defineProps({
    attributes: Object,
});
const form = useForm({
    attribute_id: null,
    value: "",
    display_value: "",
    color_code: null,
});
</script>
