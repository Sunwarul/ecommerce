<template>
    <img :src="logo" class="h-[50px]" alt="E-Commerce" />
    <span>{{ app_name }}</span>
</template>

<script setup>
import { usePage } from '@inertiajs/vue3';

const page = usePage();

// console.log('DEBUG');
// console.log(page.props);
const { app_name, logo } = page.props.configs;

</script>