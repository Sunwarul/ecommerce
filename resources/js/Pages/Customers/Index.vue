<template>
    <div>
        <CrudComponent :form="form">
            <template #columns>
                <Column field="name" header="Name"></Column>
                <Column field="email" header="Email"></Column>
                <Column field="phone" header="Phone"></Column>
                <Column field="status" header="Status"></Column>
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
    customer_type: "",
    name: "",
    email: "",
    phone: "",
    mobile: "",
    whatsapp_number: "",
    tax_number: "",
    currency_id: "",
    status: "",
    billing_address: "",
    shipping_address: "",
    opening_balance: "",
    opening_balance_date: "",
    opening_balance_type: "",
    credit_limit: "",
    has_credit_limit: "",
    photo: "",
    file: "",
    created_by: "",
});
</script>
