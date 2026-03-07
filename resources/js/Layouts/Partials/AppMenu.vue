<script setup>
import useAuth from "@/Composables/useAuth";
import { PERMISSIONS } from "@/constants/permissions";
import { computed, watch } from "vue";
import { useI18n } from 'vue-i18n';
import AppMenuItem from "./AppMenuItem.vue";
import { useLocale } from '@/Composables/useLocale';

const { t, locale } = useI18n();
const { initLocale } = useLocale();

initLocale();

// Use a reactive trigger that changes when locale changes
const localeTrigger = computed(() => locale.value);

const model = computed(() => {
    // Access localeTrigger to make this computed depend on locale
    const _ = localeTrigger.value;

    return [

        // ================= PRODUCTS =================
        {
            label: t('menu.products'),
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
                    label: t('menu.categories'),
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
                            label: t('menu.attributes'),
                            icon: "pi pi-id-card",
                            to: route("product-attributes.index"),
                            permissions: [PERMISSIONS.CATEGORY.INDEX],
                        },
                        {
                            label: t('menu.attribute_values'),
                            icon: "pi pi-id-card",
                            to: route("product-attribute-values.index"),
                            permissions: [PERMISSIONS.CATEGORY.INDEX],
                        },
                        {
                            label: t('menu.categories'),
                            icon: "pi pi-id-card",
                            to: route("categories.index"),
                            permissions: [PERMISSIONS.CATEGORY.INDEX],
                        },
                        {
                            label: t('menu.brands'),
                            icon: "pi pi-check-square",
                            to: route("brands.index"),
                            permissions: [PERMISSIONS.BRAND.INDEX],
                        },
                        {
                            label: t('menu.tags'),
                            icon: "pi pi-check-square",
                            to: route("tags.index"),
                            permissions: [PERMISSIONS.TAG.INDEX],
                        },
                        {
                            label: t('menu.taxes'),
                            icon: "pi pi-check-square",
                            to: route("taxes.index"),
                            permissions: [PERMISSIONS.TAX.INDEX],
                        },
                        {
                            label: t('menu.payment_methods'),
                            icon: "pi pi-credit-card",
                            to: route("payment-methods.index"),
                            permissions: [PERMISSIONS.PAYMENT_METHOD.INDEX],
                        },
                    ],
                },
                {
                    label: t('menu.products'),
                    icon: "pi pi-shopping-bag",
                    to: route("products.index"),
                    permissions: [PERMISSIONS.PRODUCT.INDEX],
                },
            ],
        },

        // ================= SALES =================
        {
            label: t('pos.sales'),
            icon: "pi pi-shopping-cart",
            permissionMode: "any",
            permissions: [PERMISSIONS.POS.INDEX, PERMISSIONS.ORDER.INDEX],
            items: [
                {
                    label: t('menu.pos'),
                    icon: "pi pi-desktop",
                    to: route("pos.index"),
                    permissions: [PERMISSIONS.POS.INDEX],
                },
                {
                    label: t('menu.orders'),
                    icon: "pi pi-receipt",
                    to: route("pos.orders.index"),
                    permissions: [PERMISSIONS.ORDER.INDEX],
                },
                {
                    label: t('menu.customers'),
                    icon: "pi pi-users",
                    to: route("customers.index"),
                    permissions: [PERMISSIONS.CUSTOMER.INDEX],
                },
            ],
        },

        // ================= USER MANAGEMENT =================
        {
            label: t('menu.users'),
            icon: "pi pi-users",
            permissionMode: "any",
            permissions: [
                PERMISSIONS.ROLE.INDEX,
                PERMISSIONS.PERMISSION.INDEX,
                PERMISSIONS.USER.INDEX,
            ],
            items: [
                {
                    label: t('menu.permissions'),
                    icon: "pi pi-shield",
                    permissionMode: "any",
                    permissions: [
                        PERMISSIONS.ROLE.INDEX,
                        PERMISSIONS.PERMISSION.INDEX,
                    ],
                    items: [
                        {
                            label: t('menu.roles'),
                            icon: "pi pi-id-card",
                            to: route("roles.index"),
                            permissions: [PERMISSIONS.ROLE.INDEX],
                        },
                        {
                            label: t('menu.permissions'),
                            icon: "pi pi-lock",
                            to: route("permissions.index"),
                            permissions: [PERMISSIONS.PERMISSION.INDEX],
                        },
                    ],
                },
                {
                    label: t('menu.users'),
                    icon: "pi pi-user",
                    to: route("users.index"),
                    permissions: [PERMISSIONS.USER.INDEX],
                },
            ],
        },

        // ================= SETUP =================
        {
            label: t('menu.settings'),
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
                    label: t('branches.branches'),
                    icon: "pi pi-building",
                    permissionMode: "any",
                    permissions: [
                        PERMISSIONS.BRANCH.INDEX,
                        PERMISSIONS.WAREHOUSE.INDEX,
                    ],
                    items: [
                        {
                            label: t('menu.branches'),
                            icon: "pi pi-building",
                            to: route("branches.index"),
                            permissions: [PERMISSIONS.BRANCH.INDEX],
                        },
                        {
                            label: t('menu.warehouses'),
                            icon: "pi pi-warehouse",
                            to: route("warehouses.index"),
                            permissions: [PERMISSIONS.WAREHOUSE.INDEX],
                        },
                    ],
                },
                {
                    label: t('menu.units'),
                    icon: "pi pi-clone",
                    permissionMode: "any",
                    permissions: [
                        PERMISSIONS.BASE_UNIT.INDEX,
                        PERMISSIONS.UNIT.INDEX,
                        PERMISSIONS.SUPPLIER.INDEX,
                    ],
                    items: [
                        {
                            label: t('menu.base_units'),
                            icon: "pi pi-clone",
                            to: route("base-units.index"),
                            permissions: [PERMISSIONS.BASE_UNIT.INDEX],
                        },
                        {
                            label: t('menu.units'),
                            icon: "pi pi-clone",
                            to: route("units.index"),
                            permissions: [PERMISSIONS.UNIT.INDEX],
                        },
                        {
                            label: t('menu.suppliers'),
                            icon: "pi pi-truck",
                            to: route("suppliers.index"),
                            permissions: [PERMISSIONS.SUPPLIER.INDEX],
                        },
                        {
                            label: t('menu.warranty_guarantees'),
                            icon: "pi pi-clone",
                            to: route("warranty-guarantees.index"),
                            permissions: [PERMISSIONS.SUPPLIER.INDEX],
                        },
                    ],
                },
                {
                    label: t('settings.settings'),
                    icon: "pi pi-wallet",
                    permissionMode: "any",
                    permissions: [
                        PERMISSIONS.EXPENSE_CATEGORY.INDEX,
                        PERMISSIONS.EXPENSE.INDEX,
                    ],
                    items: [
                        {
                            label: t('menu.expense_categories'),
                            icon: "pi pi-list",
                            to: route("expense-categories.index"),
                            permissions: [PERMISSIONS.EXPENSE_CATEGORY.INDEX],
                        },
                        {
                            label: t('menu.expenses'),
                            icon: "pi pi-wallet",
                            to: route("expenses.index"),
                            permissions: [PERMISSIONS.EXPENSE.INDEX],
                        },
                        {
                            label: t('menu.currencies'),
                            icon: "pi pi-money-bill",
                            to: route("currencies.index"),
                            permissions: [PERMISSIONS.COUNTRY.INDEX],
                        },
                    ],
                },
                {
                    label: t('menu.settings'),
                    icon: "pi pi-cog",
                    to: route("settings.general"),
                    permissions: [PERMISSIONS.SETTING.INDEX],
                },
            ],
        }
    ];
});

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
