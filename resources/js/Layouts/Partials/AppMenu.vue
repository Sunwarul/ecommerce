<script setup>
import useAuth from "@/Composables/useAuth";
import { PERMISSIONS } from "@/constants/permissions";
import { computed, ref } from "vue";
import AppMenuItem from "./AppMenuItem.vue";

const model = ref([

    // ================= PRODUCTS =================
    {
        label: "Products",
        icon: "pi pi-box",
        permissionMode: "any",
        permissions: [
            PERMISSIONS.CATEGORY.INDEX,
            PERMISSIONS.BRAND.INDEX,
            PERMISSIONS.TAG.INDEX,
            PERMISSIONS.TAX.INDEX,
            PERMISSIONS.PAYMENT_METHOD.INDEX,
            PERMISSIONS.PRODUCT.INDEX,
        ],
        items: [
            {
                label: "Catalog",
                icon: "pi pi-list",
                permissionMode: "any",
                permissions: [
                    PERMISSIONS.CATEGORY.INDEX,
                    PERMISSIONS.BRAND.INDEX,
                    PERMISSIONS.TAG.INDEX,
                    PERMISSIONS.TAX.INDEX,
                    PERMISSIONS.PAYMENT_METHOD.INDEX,
                ],
                items: [
                    {
                        label: "Product Attributes",
                        icon: "pi pi-id-card",
                        to: route("product-attributes.index"),
                        permissions: [PERMISSIONS.CATEGORY.INDEX],
                    },
                    {
                        label: "Attributes Values",
                        icon: "pi pi-id-card",
                        to: route("product-attribute-values.index"),
                        permissions: [PERMISSIONS.CATEGORY.INDEX],
                    },
                    {
                        label: "Category",
                        icon: "pi pi-id-card",
                        to: route("categories.index"),
                        permissions: [PERMISSIONS.CATEGORY.INDEX],
                    },
                    {
                        label: "Brand",
                        icon: "pi pi-check-square",
                        to: route("brands.index"),
                        permissions: [PERMISSIONS.BRAND.INDEX],
                    },
                    {
                        label: "Tag",
                        icon: "pi pi-check-square",
                        to: route("tags.index"),
                        permissions: [PERMISSIONS.TAG.INDEX],
                    },
                    {
                        label: "Tax",
                        icon: "pi pi-check-square",
                        to: route("taxes.index"),
                        permissions: [PERMISSIONS.TAX.INDEX],
                    },
                    {
                        label: "Payment Method",
                        icon: "pi pi-credit-card",
                        to: route("payment-methods.index"),
                        permissions: [PERMISSIONS.PAYMENT_METHOD.INDEX],
                    },
                ],
            },
            {
                label: "Products",
                icon: "pi pi-shopping-bag",
                to: route("products.index"),
                permissions: [PERMISSIONS.PRODUCT.INDEX],
            },
        ],
    },

    // ================= SALES =================
    {
        label: "Sales",
        icon: "pi pi-shopping-cart",
        permissionMode: "any",
        permissions: [PERMISSIONS.POS.INDEX, PERMISSIONS.ORDER.INDEX],
        items: [
            {
                label: "Point of Sale",
                icon: "pi pi-desktop",
                to: route("pos.index"),
                permissions: [PERMISSIONS.POS.INDEX],
            },
            {
                label: "Orders",
                icon: "pi pi-receipt",
                to: route("pos.orders.index"),
                permissions: [PERMISSIONS.ORDER.INDEX],
            },
            {
                label: "Customers",
                icon: "pi pi-users",
                to: route("customers.index"),
                permissions: [PERMISSIONS.CUSTOMER.INDEX],
            },
        ],
    },

    // ================= USER MANAGEMENT =================
    {
        label: "User Management",
        icon: "pi pi-users",
        permissionMode: "any",
        permissions: [
            PERMISSIONS.ROLE.INDEX,
            PERMISSIONS.PERMISSION.INDEX,
            PERMISSIONS.USER.INDEX,
        ],
        items: [
            {
                label: "Access Control",
                icon: "pi pi-shield",
                permissionMode: "any",
                permissions: [
                    PERMISSIONS.ROLE.INDEX,
                    PERMISSIONS.PERMISSION.INDEX,
                ],
                items: [
                    {
                        label: "Role",
                        icon: "pi pi-id-card",
                        to: route("roles.index"),
                        permissions: [PERMISSIONS.ROLE.INDEX],
                    },
                    {
                        label: "Permission",
                        icon: "pi pi-lock",
                        to: route("permissions.index"),
                        permissions: [PERMISSIONS.PERMISSION.INDEX],
                    },
                ],
            },
            {
                label: "Users",
                icon: "pi pi-user",
                to: route("users.index"),
                permissions: [PERMISSIONS.USER.INDEX],
            },
        ],
    },

    // ================= SETUP =================
    {
        label: "Setup",
        icon: "pi pi-cog",
        permissionMode: "any",
        permissions: [
            PERMISSIONS.BRANCH.INDEX,
            PERMISSIONS.WAREHOUSE.INDEX,
            PERMISSIONS.BASE_UNIT.INDEX,
            PERMISSIONS.UNIT.INDEX,
            PERMISSIONS.SUPPLIER.INDEX,
            PERMISSIONS.CUSTOMER.INDEX,
            PERMISSIONS.EXPENSE_CATEGORY.INDEX,
            PERMISSIONS.EXPENSE.INDEX,
            PERMISSIONS.COUNTRY.INDEX,
            PERMISSIONS.SETTING.INDEX,
        ],
        items: [
            {
                label: "Organization",
                icon: "pi pi-building",
                permissionMode: "any",
                permissions: [
                    PERMISSIONS.BRANCH.INDEX,
                    PERMISSIONS.WAREHOUSE.INDEX,
                ],
                items: [
                    {
                        label: "Branches",
                        icon: "pi pi-building",
                        to: route("branches.index"),
                        permissions: [PERMISSIONS.BRANCH.INDEX],
                    },
                    {
                        label: "Warehouses",
                        icon: "pi pi-warehouse",
                        to: route("warehouses.index"),
                        permissions: [PERMISSIONS.WAREHOUSE.INDEX],
                    },
                ],
            },
            {
                label: "Units & Inventory",
                icon: "pi pi-clone",
                permissionMode: "any",
                permissions: [
                    PERMISSIONS.BASE_UNIT.INDEX,
                    PERMISSIONS.UNIT.INDEX,
                    PERMISSIONS.SUPPLIER.INDEX,
                ],
                items: [
                    {
                        label: "Base Units",
                        icon: "pi pi-clone",
                        to: route("base-units.index"),
                        permissions: [PERMISSIONS.BASE_UNIT.INDEX],
                    },
                    {
                        label: "Units",
                        icon: "pi pi-clone",
                        to: route("units.index"),
                        permissions: [PERMISSIONS.UNIT.INDEX],
                    },
                    {
                        label: "Suppliers",
                        icon: "pi pi-truck",
                        to: route("suppliers.index"),
                        permissions: [PERMISSIONS.SUPPLIER.INDEX],
                    },
                    {
                        label: "Warranty & Guarantees",
                        icon: "pi pi-clone",
                        to: route("warranty-guarantees.index"),
                        permissions: [PERMISSIONS.SUPPLIER.INDEX],
                    },
                ],
            },
            {
                label: "Finance",
                icon: "pi pi-wallet",
                permissionMode: "any",
                permissions: [
                    PERMISSIONS.EXPENSE_CATEGORY.INDEX,
                    PERMISSIONS.EXPENSE.INDEX,
                ],
                items: [
                    {
                        label: "Expense Categories",
                        icon: "pi pi-list",
                        to: route("expense-categories.index"),
                        permissions: [PERMISSIONS.EXPENSE_CATEGORY.INDEX],
                    },
                    {
                        label: "Expenses",
                        icon: "pi pi-wallet",
                        to: route("expenses.index"),
                        permissions: [PERMISSIONS.EXPENSE.INDEX],
                    },
                    {
                        label: "Currencies",
                        icon: "pi pi-money-bill",
                        to: route("currencies.index"),
                        permissions: [PERMISSIONS.COUNTRY.INDEX],
                    },
                ],
            },
            {
                label: "Settings",
                icon: "pi pi-cog",
                to: route("settings.general"),
                permissions: [PERMISSIONS.SETTING.INDEX],
            },
        ],
    },
]);

const { hasAnyPermission, permissions } = useAuth();

// console.log(permissions.value);

const filteredMenu = computed(() => {
    const filterItems = (items = []) => {
        return items
            .map((item) => {
                const children = item.items ? filterItems(item.items) : [];

                const allowedSelf = hasAnyPermission(item.permissions || []);
                const allowedByChildren = children.length > 0;

                // keep item if itself allowed OR has any allowed children
                if (!allowedSelf && !allowedByChildren) return null;

                return {
                    ...item,
                    items: item.items ? children : undefined,
                };
            })
            .filter(Boolean);
    };

    return filterItems(model.value);
});
</script>

<template>
    <ul class="layout-menu">
        <template v-for="(item, i) in filteredMenu" :key="`${item.label}-${i}`">
            <AppMenuItem v-if="!item.separator" :item="item" :index="i" :root="true" />
            <li v-else class="menu-separator"></li>
        </template>
    </ul>
</template>
