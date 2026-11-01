<template>
    <Head>
        <link rel="icon" :href="favicon" />
    </Head>
</template>
<script setup>
import { Head, usePage } from "@inertiajs/vue3";
import { computed } from "vue";

// Uploaded in Website > Website settings > Branding; the bundled icon otherwise
const page = usePage();
const favicon = computed(() => page.props.configs?.favicon || "/images/favicon.svg");
</script>
