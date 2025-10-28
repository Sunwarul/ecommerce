<template>
    <div>
        <CrudComponent :form="form">
            <template #columns>
                <Column field="name" header="Name"></Column>
                <Column field="status" header="Status">
                    <template #body="{ data }">
                        <Badge :severity="data.status ? 'success' : 'danger'">
                            {{ data.status ? 'Active' : 'Inactive' }}
                        </Badge>
                    </template>
                </Column>
                <Column field="created_at" header="Created At" sortable></Column>
            </template>

            <template #form="{ submitted, handlePhotoUpload, photoPreview, resolveImagePath }">
                <Form :form="form" />
            </template>
        </CrudComponent>
    </div>
</template>
<script setup>
import CrudComponent from '@/Components/CrudComponent.vue';
import Form from './Form.vue';
import { useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    status: '',
});

</script>
