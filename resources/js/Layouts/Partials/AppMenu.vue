<script setup>
import useAuth from "@/Composables/useAuth";
import { PERMISSIONS as P } from "@/constants/permissions";
import { computed } from "vue";
import { useI18n } from "vue-i18n";
import AppMenuItem from "./AppMenuItem.vue";

const { t } = useI18n();
const { hasAnyPermission, hasRole } = useAuth();

const leaf = (key, icon, routeName, permission) => ({
    label: t(`menu.${key}`),
    icon: `pi pi-fw ${icon}`,
    to: route().has(routeName) ? route(routeName) : null,
    permissions: [permission],
});

const group = (key, icon, items) => ({ label: t(`menu.${key}`), icon: `pi pi-fw ${icon}`, items });

// Re-computed when the locale changes because t() is reactive
const model = computed(() => [
    group("sales", "pi-shopping-cart", [
        leaf("pos", "pi-desktop", "pos.index", P.POS.INDEX),
        leaf("orders", "pi-receipt", "pos.orders.index", P.ORDER.INDEX),
        leaf("customers", "pi-users", "customers.index", P.CUSTOMER.INDEX),
        leaf("installments", "pi-calendar-clock", "installments.index", P.INSTALLMENT.INDEX),
        leaf("installment_plans", "pi-sliders-h", "installment-plans.index", P.INSTALLMENT.PLAN_MANAGE),
        leaf("bank_emi", "pi-credit-card", "emi.index", P.EMI.INDEX),
    ]),

    group("reports", "pi-chart-bar", [
        leaf("sales_report", "pi-chart-line", "reports.sales", P.REPORT.INDEX),
    ]),

    group("catalog", "pi-box", [
        leaf("products", "pi-shopping-bag", "products.index", P.PRODUCT.INDEX),
        group("product_setup", "pi-th-large", [
            leaf("categories", "pi-sitemap", "categories.index", P.CATEGORY.INDEX),
            leaf("sub_categories", "pi-list", "sub-categories.index", P.SUB_CATEGORY.INDEX),
            leaf("brands", "pi-star", "brands.index", P.BRAND.INDEX),
            leaf("tags", "pi-tags", "tags.index", P.TAG.INDEX),
            leaf("attributes", "pi-sliders-v", "product-attributes.index", P.PRODUCT_ATTRIBUTE.INDEX),
            leaf("attribute_values", "pi-list-check", "product-attribute-values.index", P.PRODUCT_ATTRIBUTE.INDEX),
            leaf("units", "pi-calculator", "units.index", P.UNIT.INDEX),
            leaf("base_units", "pi-objects-column", "base-units.index", P.BASE_UNIT.INDEX),
            leaf("taxes", "pi-percentage", "taxes.index", P.TAX.INDEX),
            leaf("warranty_guarantees", "pi-verified", "warranty-guarantees.index", P.WARRANTY.INDEX),
        ]),
        leaf("suppliers", "pi-truck", "suppliers.index", P.SUPPLIER.INDEX),
    ]),

    group("sms", "pi-comments", [
        leaf("sms_dashboard", "pi-chart-pie", "sms.dashboard", P.SMS.INDEX),
        leaf("sms_send", "pi-send", "sms.compose", P.SMS.SEND),
        leaf("sms_history", "pi-history", "sms.logs", P.SMS.INDEX),
        leaf("sms_settings", "pi-cog", "sms.settings", P.SMS.SETTINGS),
    ]),

    group("finance", "pi-wallet", [
        leaf("expenses", "pi-money-bill", "expenses.index", P.EXPENSE.INDEX),
        leaf("expense_categories", "pi-folder", "expense-categories.index", P.EXPENSE_CATEGORY.INDEX),
        leaf("payment_methods", "pi-credit-card", "payment-methods.index", P.PAYMENT_METHOD.INDEX),
        leaf("currencies", "pi-dollar", "currencies.index", P.COUNTRY.INDEX),
    ]),

    group("organization", "pi-building", [
        leaf("branches", "pi-building", "branches.index", P.BRANCH.INDEX),
        leaf("warehouses", "pi-warehouse", "warehouses.index", P.WAREHOUSE.INDEX),
    ]),

    group("user_management", "pi-shield", [
        leaf("users", "pi-user", "users.index", P.USER.INDEX),
        leaf("roles", "pi-id-card", "roles.index", P.ROLE.INDEX),
        leaf("permissions", "pi-lock", "permissions.index", P.PERMISSION.INDEX),
    ]),

    group("tools", "pi-briefcase", [
        leaf("todos", "pi-check-square", "todos.index", P.TODO.INDEX),
        leaf("tasks", "pi-list-check", "tasks.index", P.TASK.INDEX),
    ]),

    group("settings", "pi-cog", [
        leaf("general_settings", "pi-sliders-h", "settings.general", P.SETTING.INDEX),
    ]),
]);

// Super Admin passes every server-side check (Gate::before), so show everything
const isSuperAdmin = computed(() => hasRole("Super Admin"));

const allowed = (item) => isSuperAdmin.value || hasAnyPermission(item.permissions || []);

const filteredMenu = computed(() => {
    const filterItems = (items = []) =>
        items
            .map((item) => {
                if (item.items) {
                    // a group is shown only when at least one child is visible
                    const children = filterItems(item.items);
                    return children.length ? { ...item, items: children } : null;
                }
                return item.to && allowed(item) ? item : null;
            })
            .filter(Boolean);

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
