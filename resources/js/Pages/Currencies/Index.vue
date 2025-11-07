<template>
  <div>
    <CrudComponent :form="form">
      <template #columns>
        <Column field="title" header="Title"></Column>
        <Column field="short_code" header="Short Code"></Column>
        <Column field="date" header="Date"></Column>
        <Column field="symbol" header="Symbol"></Column>
        <Column field="exchange_rate" header="Exchange Rate"></Column>
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
    title: '',
    short_code: '',
    date: '',
    symbol: '',
    exchange_rate: '',
    status: '',
});

</script>
