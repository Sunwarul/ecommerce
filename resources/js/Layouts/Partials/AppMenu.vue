<script setup>
import { ref } from "vue";
import AppMenuItem from "./AppMenuItem.vue";

const model = ref([
    {
        label: "Products",
        icon: "pi pi-box",
        items: [
            {
                label: "Catalog",
                icon: "pi pi-list",
                items: [
                    {
                        label: "Category",
                        icon: "pi pi-id-card",
                        to: route("categories.index"),
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
                ],
            },
            {
                label: "Payment",
                icon: "pi pi-credit-card",
                items: [
                    {
                        label: "Payment Method",
                        icon: "pi pi-credit-card",
                        to: route("payment-methods.index"),
                    },
                ],
            },
        ],
    },

    // 🔥 New Sales / POS section
    {
        label: "Sales",
        icon: "pi pi-shopping-cart",
        items: [
            {
                label: "Point of Sale",
                icon: "pi pi-desktop",
                to: route("pos.index"), // POS screen (your Vue POS page)
            },
            {
                label: "Orders",
                icon: "pi pi-receipt",
                to: route("pos.orders.index"), // Sales orders list page
            },
            // চাইলে পরে আরও যোগ করতে পারো, যেমন:
            // {
            //     label: "Returns",
            //     icon: "pi pi-undo",
            //     to: route("sales.returns.index"),
            // },
            // {
            //     label: "Invoices",
            //     icon: "pi pi-file",
            //     to: route("invoices.index"),
            // },
        ],
    },

    {
        label: "User Management",
        icon: "pi pi-users",
        items: [
            {
                label: "Access Control",
                icon: "pi pi-shield",
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
                ],
            },
            {
                label: "Users",
                icon: "pi pi-user",
                to: route("users.index"), // single item, no submenu
            },
        ],
    },

    {
        label: "Setup",
        icon: "pi pi-cog",
        items: [
            {
                label: "Organization",
                icon: "pi pi-building",
                items: [
                    {
                        label: "Branches",
                        icon: "pi pi-building",
                        to: route("branches.index"),
                    },
                    {
                        label: "Warehouses",
                        icon: "pi pi-warehouse",
                        to: route("warehouses.index"),
                    },
                ],
            },
            {
                label: "Units & Inventory",
                icon: "pi pi-clone",
                items: [
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
                        label: "Suppliers",
                        icon: "pi pi-truck",
                        to: route("suppliers.index"),
                    },
                    {
                        label: "Customers",
                        icon: "pi pi-users",
                        to: route("customers.index"),
                    },
                ],
            },
            {
                label: "Finance",
                icon: "pi pi-wallet",
                items: [
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
                ],
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
