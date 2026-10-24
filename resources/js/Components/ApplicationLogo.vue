<script setup>
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    showName: { type: Boolean, default: true },
    size: { type: String, default: 'md' }, // sm | md | lg
});

const page = usePage();
const configs = computed(() => page.props.configs || {});
const name = computed(() => configs.value.shop_name || configs.value.app_name || 'POS');
const heights = { sm: 'h-8', md: 'h-10', lg: 'h-16' };
</script>

<template>
    <span class="inline-flex items-center gap-2 min-w-0">
        <img v-if="configs.logo" :src="configs.logo" :class="heights[size]" class="w-auto object-contain rounded" :alt="name" />
        <span v-if="showName" class="font-semibold text-slate-800 truncate" :class="size === 'lg' ? 'text-2xl' : 'text-lg'">{{ name }}</span>
    </span>
</template>
