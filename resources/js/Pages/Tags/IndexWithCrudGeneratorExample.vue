<template>
    <AuthenticatedLayout>
        <CrudGenerator resource-name="Tags" :config="config" />
    </AuthenticatedLayout>
</template>

<script setup>
import CrudGenerator from '@/Components/CrudGenerator.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const config = {
    routePrefix: 'tags',
    columns: [
        { field: 'name', header: 'Name' },
        { field: 'slug', header: 'Slug' },
        { field: 'created_at', header: 'Created At' }
    ]
};
</script>