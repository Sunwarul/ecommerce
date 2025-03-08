<template>
    <AuthenticatedLayout>
        <CrudGenerator :config="config" />
    </AuthenticatedLayout>
</template>

<script setup>
import { usePage } from '@inertiajs/vue3';
import CrudGenerator from '@/Components/CrudGenerator.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const page = usePage();
const config = page.props.config;
</script>
