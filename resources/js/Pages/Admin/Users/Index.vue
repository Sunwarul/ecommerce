<template>
    <div>
        <CrudComponent :form="form">
            <template #columns>
                <Column field="name" header="Name"></Column>
                <Column field="email" header="Email"></Column>
                <Column field="updated_at" header="Created At" sortable></Column>
            </template>
            <template #form="{ submitted }">
                <Form :form="form" v-bind="{ submitted, permissions }" />
            </template>
        </CrudComponent>
    </div>
</template>
<script setup>
import CrudComponent from '@/Components/CrudComponent.vue';
import Form from './Form.vue';
import { useForm } from '@inertiajs/vue3';
const { permissions, items } = defineProps(['permissions', 'items'])

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});
</script>
