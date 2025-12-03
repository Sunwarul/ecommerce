<script setup>
import { ref } from "vue";
import AppMenuItem from "./AppMenuItem.vue";

const model = ref([
    {
        label: "Products",
        icon: "pi pi-box",              // group icon
        items: [
            {
                label: "Category",
                icon: "pi pi-id-card",
                to: route("categories.index"),
            },
            {
                label: "Sub-category",
                icon: "pi pi-id-card",
                to: route("sub-categories.index"),
            },
            {
                label: "Brand",
                icon: "pi pi-check-square",
                to: route("brands.index"),
            },
            {
                label: "Tag",
                icon: "pi pi-check-square",
                to: route("tags.index"),
            },
            {
                label: "Product",
                icon: "pi pi-shopping-bag",
                to: route("products.index"),
            },
            {
                label: "Payment Method",
                icon: "pi pi-credit-card",
                to: route("payment-methods.index"),
            },
        ],
    },
    {
        label: "User",
        icon: "pi pi-users",
        items: [
            {
                label: "Role",
                icon: "pi pi-id-card",
                to: route("roles.index"),
            },
            {
                label: "Permission",
                icon: "pi pi-lock",
                to: route("permissions.index"),
            },
            {
                label: "User",
                icon: "pi pi-user",
                to: route("users.index"),
            },
        ],
    },
    {
        label: "Setup",
        icon: "pi pi-cog",
        items: [
            {
                label: "Branches",
                icon: "pi pi-building",
                to: route("branches.index"),
            },
            {
                label: "Base Units",
                icon: "pi pi-clone",
                to: route("base-units.index"),
            },
            {
                label: "Units",
                icon: "pi pi-clone",
                to: route("units.index"),
            },
            {
                label: "Warehouses",
                icon: "pi pi-warehouse",
                to: route("warehouses.index"),
            },
            {
                label: "Suppliers",
                icon: "pi pi-truck",
                to: route("suppliers.index"),
            },
            {
                label: "Expense Categories",
                icon: "pi pi-list",
                to: route("expense-categories.index"),
            },
            {
                label: "Expenses",
                icon: "pi pi-wallet",
                to: route("expenses.index"),
            },
            {
                label: "Currencies",
                icon: "pi pi-money-bill",
                to: route("currencies.index"),
            },
            {
                label: "Customers",
                icon: "pi pi-users",
                to: route("customers.index"),
            },
            {
                label: "Settings",
                icon: "pi pi-cog",
                to: route("settings.general"),
            },
        ],
    },
]);
</script>

<template>
    <ul class="layout-menu">
        <template v-for="(item, i) in model" :key="`${item.label}-${i}`">
            <app-menu-item
                v-if="!item.separator"
                :item="item"
                :index="i"
                :root="true"
            />
            <li v-else class="menu-separator"></li>
        </template>
    </ul>
</template>
