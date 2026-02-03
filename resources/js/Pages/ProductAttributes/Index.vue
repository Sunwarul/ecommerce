<template>
    <div>
        <CrudComponent :form="form">
            <template #columns>
                <Column field="name" header="Name" />
                <Column field="display_name" header="Display Name" />
                <Column field="type" header="Type" />

                <Column field="is_active" header="Status">
                    <template #body="{ data }">
                        <Badge :severity="data.is_active ? 'success' : 'danger'">
                            {{ data.is_active ? 'Active' : 'Inactive' }}
                        </Badge>
                    </template>
                </Column>
            </template>

            <template #form>
                <Form :form="form" />
            </template>
        </CrudComponent>
    </div>
</template>

<script setup>
import CrudComponent from "@/Components/CrudComponent.vue";
import { useForm } from "@inertiajs/vue3";
import Form from "./Form.vue";

const form = useForm({
    name: "",
    display_name: "",
    type: "select",
    is_active: true,
});
</script>
