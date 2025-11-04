<template>
    <div>
        <CrudComponent :form="form">
            <template #columns>
                <Column field="name" header="Name"></Column>
                <Column field="email" header="Email"></Column>
                <!-- roles column -->
                <Column field="roles" header="Roles">
                    <template #body="slotProps">
                        <div>
                            <span
                                v-for="role in slotProps.data.roles"
                                :key="role.id"
                                class="inline-block bg-blue-100 text-blue-800 text-xs px-2 rounded-full mr-1"
                            >
                                {{ role.name }}
                            </span>
                        </div>
                    </template>
                </Column>
                <Column
                    field="updated_at"
                    header="Created At"
                    sortable
                ></Column>
            </template>
            <template #form="{ submitted }">
                <Form
                    :form="form"
                    :roles="roles"
                    v-bind="{ submitted, permissions }"
                />
            </template>
        </CrudComponent>
    </div>
</template>
<script setup>
import CrudComponent from "@/Components/CrudComponent.vue";
import { useForm } from "@inertiajs/vue3";
import Form from "./Form.vue";
const { permissions, roles } = defineProps(["permissions", "roles"]);

const form = useForm({
    name: "",
    email: "",
    password: "",
    password_confirmation: "",
    roles: [],
});
</script>
