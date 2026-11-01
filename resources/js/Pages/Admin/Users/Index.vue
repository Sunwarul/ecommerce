<template>
    <CrudComponent :form="form" icon="pi pi-user" formWidth="46rem" @editingItem="handleEditingItem">
        <template #columns>
            <Column field="name" :header="t('crud.fields.name')">
                <template #body="{ data }">
                    <div class="flex items-center gap-3 min-w-[12rem]">
                        <img v-if="data.photo" :src="$resolveImagePath(data.photo)" alt=""
                            class="w-9 h-9 rounded-full object-cover border border-slate-200 shrink-0" />
                        <span v-else class="w-9 h-9 rounded-full bg-emerald-50 text-emerald-700 font-semibold flex items-center justify-center shrink-0">
                            {{ initials(data.name) }}
                        </span>
                        <div class="min-w-0">
                            <div class="font-medium text-slate-800 truncate">{{ data.name }}</div>
                            <div class="text-xs text-slate-500 truncate ltr-nums">{{ data.email }}</div>
                        </div>
                    </div>
                </template>
            </Column>
            <Column field="phone" :header="t('crud.fields.phone')" bodyClass="ltr-nums whitespace-nowrap"></Column>
            <Column field="roles" :header="t('admin.users.roles')">
                <template #body="{ data }">
                    <div class="flex flex-wrap gap-1">
                        <Tag v-for="r in data.roles || []" :key="r.id" :value="r.name"
                            :severity="r.name === 'Super Admin' ? 'warn' : 'info'" />
                        <span v-if="!data.roles?.length" class="text-xs text-slate-400">{{ t('admin.users.no_role') }}</span>
                    </div>
                </template>
            </Column>
            <Column field="branch_id" :header="t('admin.users.branch')">
                <template #body="{ data }">{{ branchName(data.branch_id) }}</template>
            </Column>
        </template>
        <template #form="{ submitted, handlePhotoUpload, photoPreview, resolveImagePath, isEdit }">
            <Form :form="form" :roles="roles" :branches="branches"
                v-bind="{ submitted, handlePhotoUpload, photoPreview, resolveImagePath, isEdit }" />
        </template>
    </CrudComponent>
</template>
<script setup>
import CrudComponent from "@/Components/CrudComponent.vue";
import { useForm } from "@inertiajs/vue3";
import { nextTick } from "vue";
import { useI18n } from "vue-i18n";
import Column from "primevue/column";
import Tag from "primevue/tag";
import Form from "./Form.vue";

const { branches, roles } = defineProps({
    branches: { type: Array, default: () => [] },
    roles: { type: Array, default: () => [] },
});

const { t } = useI18n();
const branchName = (id) => branches.find((b) => b.id === id)?.name ?? "-";
const initials = (name) => String(name || "?").trim().split(/\s+/).slice(0, 2).map((p) => p[0]).join("").toUpperCase();

const form = useForm({
    name: "",
    email: "",
    branch_id: null,
    password: "",
    password_confirmation: "",
    roles: [],
    phone: "",
    photo: "",
});

const handleEditingItem = async (user) => {
    // CrudComponent copies the row into the form right after this event; fix up relations afterwards
    await nextTick();
    form.roles = user.roles?.map((role) => role.id) || [];
    form.branch_id = user.branch_id || null;
    form.password = "";
    form.password_confirmation = "";
};
</script>
