<template>
    <CrudComponent :form="form" icon="pi pi-wallet" formWidth="48rem">
        <template #columns>
            <Column field="date" :header="t('crud.fields.date')">
                <template #body="{ data }"><span class="whitespace-nowrap">{{ date(data.date) }}</span></template>
            </Column>
            <Column field="title" :header="t('crud.fields.title')" bodyClass="font-medium text-slate-800"></Column>
            <Column field="expense_category_id" :header="t('crud.fields.expense_category')">
                <template #body="{ data }">{{ nameOf(expenseCategories, data.expense_category_id) }}</template>
            </Column>
            <Column field="warehouse_id" :header="t('crud.fields.warehouse')">
                <template #body="{ data }">{{ nameOf(warehouses, data.warehouse_id) }}</template>
            </Column>
            <Column field="amount" :header="t('crud.fields.amount')" bodyClass="tabular-nums text-end font-semibold" headerClass="text-end">
                <template #body="{ data }">{{ money(data.amount) }}</template>
            </Column>
            <Column field="status" :header="t('crud.fields.status')">
                <template #body="{ data }">
                    <Tag :value="isOn(data.status) ? t('crud.fields.active') : t('crud.fields.inactive')"
                        :severity="isOn(data.status) ? 'success' : 'secondary'" />
                </template>
            </Column>
        </template>

        <template #form="{ handlePhotoUpload, photoPreview, resolveImagePath }">
            <Form :form="form" :users="users" :warehouses="warehouses" :expenseCategories="expenseCategories"
                :handlePhotoUpload="handlePhotoUpload" :photoPreview="photoPreview" :resolveImagePath="resolveImagePath" />
        </template>
    </CrudComponent>
</template>
<script setup>
import CrudComponent from "@/Components/CrudComponent.vue";
import { useForm, usePage } from "@inertiajs/vue3";
import { useI18n } from "vue-i18n";
import Column from "primevue/column";
import Tag from "primevue/tag";
import Form from "./Form.vue";
import { date, money, ymd } from "@/Helpers/format";

const { users, warehouses, expenseCategories } = defineProps({
    users: { type: Array, default: () => [] },
    warehouses: { type: Array, default: () => [] },
    expenseCategories: { type: Array, default: () => [] },
});

const { t } = useI18n();
const page = usePage();
const isOn = (v) => !!Number(v);
const nameOf = (list, id) => list.find((x) => x.id === id)?.name ?? "-";

const form = useForm({
    title: "",
    date: ymd(new Date()),
    amount: null,
    details: "",
    status: true,
    expense_category_id: null,
    user_id: page.props.auth?.user?.id ?? null,
    warehouse_id: warehouses.length === 1 ? warehouses[0].id : null,
    photo: null,
});
</script>
