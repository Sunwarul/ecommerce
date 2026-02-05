<template>
    <div>
        <CrudComponent :form="form">
            <template #columns>
                <Column field="photo" header="Photo">
                    <template #body="{ data }">
                        <img :src="$resolveImagePath(data.photo)" alt="Category Photo"
                            class="shadow-md rounded-xl w-16 h-16 object-cover" style="filter: grayscale(100%)"
                            @click="() => console.log('data', data)" />
                    </template>
                </Column>
                <Column field="name" header="Name"></Column>
                <Column field="email" header="Email"></Column>
                <Column field="phone" header="Phone"></Column>
                <Column field="status" header="Status">
                    <template #body="{ data }">
                        <Badge :severity="data.status ? 'success' : 'danger'">
                            {{ data.status ? "Active" : "Inactive" }}
                        </Badge>
                    </template>
                </Column>
                <Column field="created_at" header="Created At" sortable></Column>
            </template>

            <template #form="{
                submitted,
                handlePhotoUpload,
                photoPreview,
                resolveImagePath,
            }">
                <Form :form="form" :currencies="currencies" v-bind="{
                    submitted,
                    handlePhotoUpload,
                    photoPreview,
                    resolveImagePath,
                }" />
            </template>
        </CrudComponent>
    </div>
</template>
<script setup>
import CrudComponent from "@/Components/CrudComponent.vue";
import { useForm } from "@inertiajs/vue3";
import Form from "./Form.vue";

const form = useForm({
    customer_type: "retailer",
    name: "",
    email: "",
    phone: "",
    whatsapp_number: "",
    tax_number: "",
    currency_id: "",
    status: 1,
    address: "",
    opening_balance: '0',
    opening_balance_date: "",
    opening_balance_type: "",
    credit_limit: "",
    has_credit_limit: "",
    photo: "",
    file: "",
});
</script>
