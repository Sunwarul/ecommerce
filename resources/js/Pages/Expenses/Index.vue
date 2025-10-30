<template>
    <div>
        <CrudComponent :form="form">
            <template #columns>
                <Column field="title" header="Title"></Column>
                <Column field="date" header="Date"></Column>
                <Column field="amount" header="Amount"></Column>
                <Column field="is_active" header="Status">
                    <template #body="{ data }">
                        <Badge
                            :severity="data.is_active ? 'success' : 'danger'"
                        >
                            {{ data.is_active ? "Active" : "Inactive" }}
                        </Badge>
                    </template>
                </Column>
                <Column
                    field="created_at"
                    header="Created At"
                    sortable
                ></Column>
            </template>

            <template
                #form="{
                    submitted,
                    handlePhotoUpload,
                    photoPreview,
                    resolveImagePath,
                }"
            >
                <Form
                    :form="form"
                    :users="users"
                    :warehouses="warehouses"
                    :expenseCategories="expenseCategories"
                />
            </template>
        </CrudComponent>
    </div>
</template>
<script setup>
import CrudComponent from "@/Components/CrudComponent.vue";
import { useForm } from "@inertiajs/vue3";
import Form from "./Form.vue";

const { users, warehouses, expenseCategories } = defineProps({
    users: Object,
    warehouses: Object,
    expenseCategories: Object,
});

const form = useForm({
    title: "",
    date: "",
    amount: "",
    details: "",
    status: "",
    expense_category_id: "",
    user_id: "",
    warehouse_id: "",
});
</script>
