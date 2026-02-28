<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';
import { Head } from '@inertiajs/vue3';
import FavIcons from '@/Layouts/Partials/FavIcons.vue';
</script>

<template>
    <FavIcons />
    <div class="flex min-h-screen flex-col items-center bg-gray-100 pt-6 sm:justify-center sm:pt-0 ">
        <div class="uppercase text-blue-400">
            <Link href="/">
                <ApplicationLogo />
            </Link>
        </div>
        <div class="mt-6 w-full overflow-hidden bg-white px-6 py-4 shadow-md sm:max-w-md sm:rounded-lg ">
            <slot />
        </div>
    </div>
</template>
