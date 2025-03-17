<script setup>
import { ref } from 'vue';
import AppMenuItem from './AppMenuItem.vue';

const model = ref([
    // {
    //     items: [{ label: 'Dashboard', icon: 'pi pi-fw pi-home', to: '/' }]
    // },
    {
        label: 'Products',
        items: [
            { label: 'Category', icon: 'pi pi-fw pi-id-card', to: route('categories.index') },
            { label: 'Sub-category', icon: 'pi pi-fw pi-id-card', to: route('sub-categories.index') },
            { label: 'Brand', icon: 'pi pi-fw pi-check-square', to: route('brands.index') },
            { label: 'Tag', icon: 'pi pi-fw pi-check-square', to: route('tags.index') },
            { label: 'Product', icon: 'pi pi-fw pi-check-square', to: route('products.index') },
            { label: 'Payment Method', icon: 'pi pi-fw pi-check-square', to: route('payment-methods.index') },
        ]
    },
]);
</script>

<template>
    <ul class="layout-menu">
        <template v-for="(item, i) in model" :key="item">
            <app-menu-item v-if="!item.separator" :item="item" :index="i"></app-menu-item>
            <li v-if="item.separator" class="menu-separator"></li>
        </template>
    </ul>
</template>

<style lang="scss" scoped></style>
