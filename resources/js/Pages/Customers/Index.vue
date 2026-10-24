<template>
    <CrudComponent :form="form" :title="t('customers.customers')" :subtitle="t('customers_page.subtitle')" icon="pi pi-users"
        formWidth="56rem">
        <template #messages>
            <span class="text-xs text-slate-500"><i class="pi pi-info-circle me-1" />{{ t("customers_page.search_hint") }}</span>
        </template>

        <template #columns>
            <Column :header="t('customers_page.customer')" style="min-width: 14rem">
                <template #body="{ data }">
                    <div class="flex items-center gap-3 min-w-0">
                        <img v-if="data.photo" :src="$resolveImagePath(data.photo)" :alt="data.name"
                            class="w-10 h-10 rounded-full object-cover border border-slate-200 shrink-0" />
                        <span v-else
                            class="w-10 h-10 rounded-full bg-emerald-50 text-emerald-700 font-semibold flex items-center justify-center shrink-0">
                            {{ initials(data.name) }}
                        </span>
                        <div class="min-w-0">
                            <div class="font-medium text-slate-800 truncate">{{ data.name }}</div>
                            <div v-if="data.email" class="text-xs text-slate-500 truncate ltr-nums">{{ data.email }}</div>
                        </div>
                    </div>
                </template>
            </Column>
            <Column field="phone" :header="t('common.phone')" style="min-width: 9rem">
                <template #body="{ data }">
                    <a v-if="data.phone" :href="`tel:${data.phone}`" class="text-slate-700 hover:text-emerald-700 ltr-nums">{{ data.phone }}</a>
                    <div v-if="data.whatsapp_number && data.whatsapp_number !== data.phone" class="text-xs text-slate-500 ltr-nums">
                        <i class="pi pi-whatsapp !text-[10px]" /> {{ data.whatsapp_number }}
                    </div>
                </template>
            </Column>
            <Column field="customer_type" :header="t('customers_page.type')">
                <template #body="{ data }">
                    <Tag :value="t(`customers_page.types.${data.customer_type || 'retailer'}`)" severity="secondary" />
                </template>
            </Column>
            <Column field="status" :header="t('common.status')">
                <template #body="{ data }">
                    <Tag :value="Number(data.status) ? t('shared.status.active') : t('shared.status.inactive')"
                        :severity="Number(data.status) ? 'success' : 'secondary'" />
                </template>
            </Column>
            <Column field="created_at" :header="t('customers_page.since')" sortable>
                <template #body="{ data }">
                    <span class="text-sm text-slate-500">{{ date(data.created_at) }}</span>
                </template>
            </Column>
        </template>

        <template #form="{ submitted, handlePhotoUpload, photoPreview, resolveImagePath }">
            <Form :form="form" :currencies="currencies" v-bind="{ submitted, handlePhotoUpload, photoPreview, resolveImagePath }" />
        </template>
    </CrudComponent>
</template>

<script setup>
import CrudComponent from "@/Components/CrudComponent.vue";
import { useForm } from "@inertiajs/vue3";
import { useI18n } from "vue-i18n";
import Column from "primevue/column";
import Tag from "primevue/tag";
import { date } from "@/Helpers/format";
import Form from "./Form.vue";

const { t } = useI18n();

defineProps({
    currencies: { type: Array, default: () => [] },
});

const form = useForm({
    customer_type: "retailer",
    name: "",
    email: "",
    phone: "",
    whatsapp_number: "",
    tax_number: "",
    currency_id: "",
    status: 1,
    address: "",
    opening_balance: "0",
    opening_balance_date: "",
    opening_balance_type: "",
    credit_limit: "",
    has_credit_limit: 0,
    photo: "",
    file: "",
});

function initials(name) {
    return String(name || "?")
        .trim()
        .split(/\s+/)
        .slice(0, 2)
        .map((p) => p[0])
        .join("")
        .toUpperCase();
}
</script>
