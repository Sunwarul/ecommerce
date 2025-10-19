<template>
  <div>
    <CrudComponent :form="form">
      <template #columns>
        <Column field="name" header="Name"></Column>
        <Column field="address" header="Address"></Column>
        <Column field="status" header="Status">
            <template #body="{ data }">
                <Badge :severity="data.status ? 'success' : 'danger'">
                    {{ data.status ? 'Active' : 'Inactive' }}
                </Badge>
            </template>
                </Column>
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
    address: '',
    phone: '',
    status: '',
});

</script>
