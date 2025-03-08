<template>
    <AuthenticatedLayout>
        <CrudGenerator :config="props.config" />
    </AuthenticatedLayout>
</template>

<script setup>
import { ref } from 'vue';
import { usePage } from '@inertiajs/vue3';
import CrudGenerator from '@/Components/CrudGenerator.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps(['config'])
</script>
