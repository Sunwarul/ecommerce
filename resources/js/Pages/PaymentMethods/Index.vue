<template>
    <div>
        <CrudComponent :form="form">
            <template #columns>
                <Column field="name" header="Name"></Column>
                <Column field="is_active" header="Is Active" sortable>
                    <template #body="{ data }">
                        <span v-if="data.is_active" class="text-green-400 font-bold">Active</span>
                        <span v-else class="text-red-400 font-bold">In-Active</span>
                    </template>
                </Column>
            </template>

            <template #form="{ submitted, handlePhotoUpload, photoPreview, resolveImagePath }">
                <Form :form="form" :handlePhotoUpload :photoPreview :resolveImagePath />
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
    is_active: false,
    photo: null
});

</script>
