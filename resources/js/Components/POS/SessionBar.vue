<script setup>
import { router, usePage } from "@inertiajs/vue3";
import { useToast } from "primevue/usetoast";
import { computed, ref, watch } from "vue";
import { useI18n } from "vue-i18n";
import { firstError } from "@/Helpers/format";

import Button from "primevue/button";
import Dialog from "primevue/dialog";
import InputNumber from "primevue/inputnumber";
import InputText from "primevue/inputtext";
import Select from "primevue/select";

const props = defineProps({
    currentSession: { type: Object, default: null },
    branches: { type: Array, default: () => [] },
    warehouses: { type: Array, default: () => [] },
});

const { t } = useI18n();
const toast = useToast();
const page = usePage();

const posSession = computed(() => page.props.currentSession ?? props.currentSession);
const dialogBreakpoints = { "960px": "75vw", "640px": "95vw" };

const openDialog = ref(false);
const closeDialog = ref(false);
const loading = ref(false);

const openForm = ref({ branch_id: null, warehouse_id: null, opening_cash: 0, note: "" });
const closeForm = ref({ closing_cash: 0, note: "" });

watch(openDialog, (visible) => {
    if (!visible) return;
    if (props.branches.length && !openForm.value.branch_id) {
        openForm.value.branch_id = page.props.auth?.user?.branch_id || props.branches[0].id;
    }
    if (props.warehouses.length && !openForm.value.warehouse_id) {
        openForm.value.warehouse_id = props.warehouses[0].id;
    }
});

// The controller redirects back with a flash message (shown by the page), and fresh
// `currentSession` props — so no page reload is needed.
function submit(routeName, data, dialog, failKey) {
    router.post(route(routeName), data, {
        preserveScroll: true,
        preserveState: true,
        onStart: () => (loading.value = true),
        onFinish: () => (loading.value = false),
        onSuccess: () => (dialog.value = false),
        onError: (errors) => toast.add({ severity: "error", summary: t(failKey), detail: firstError(errors), life: 3500 }),
    });
}

const openSession = () => submit("pos.session.open", openForm.value, openDialog, "pos_screen.session_open_failed");
const closeSession = () => submit("pos.session.close", closeForm.value, closeDialog, "pos_screen.session_close_failed");
</script>

<template>
    <div class="flex items-center gap-2">
        <Button v-if="!posSession" :label="t('pos.open_session')" icon="pi pi-play" size="small" @click="openDialog = true" />
        <Button v-else :label="t('pos.close_session')" icon="pi pi-stop-circle" severity="danger" outlined size="small"
            @click="closeDialog = true" />

        <!-- OPEN -->
        <Dialog v-model:visible="openDialog" modal :header="t('pos.open_session')" :style="{ width: '440px' }" :breakpoints="dialogBreakpoints">
            <form class="space-y-3" @submit.prevent="openSession">
                <div>
                    <label class="text-sm font-medium text-slate-700 block mb-1">{{ t("shared.terms.branch") }} <span class="text-rose-600">*</span></label>
                    <Select v-model="openForm.branch_id" :options="branches" optionLabel="name" optionValue="id" class="w-full" />
                </div>
                <div>
                    <label class="text-sm font-medium text-slate-700 block mb-1">{{ t("pos_screen.warehouse") }} <span class="text-rose-600">*</span></label>
                    <Select v-model="openForm.warehouse_id" :options="warehouses" optionLabel="name" optionValue="id" class="w-full" />
                    <p class="text-[11px] text-slate-500 mt-1">{{ t("pos_screen.warehouse_hint") }}</p>
                </div>
                <div>
                    <label class="text-sm font-medium text-slate-700 block mb-1">{{ t("pos.opening_cash") }}</label>
                    <InputNumber v-model="openForm.opening_cash" class="w-full" inputClass="w-full" :min="0" :maxFractionDigits="2" />
                </div>
                <div>
                    <label class="text-sm font-medium text-slate-700 block mb-1">{{ t("shared.terms.notes") }}</label>
                    <InputText v-model="openForm.note" class="w-full" />
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <Button type="button" :label="t('shared.terms.cancel')" text severity="secondary" @click="openDialog = false" />
                    <Button type="submit" :label="t('pos_screen.open')" icon="pi pi-check" :loading="loading" />
                </div>
            </form>
        </Dialog>

        <!-- CLOSE -->
        <Dialog v-model:visible="closeDialog" modal :header="t('pos.close_session')" :style="{ width: '440px' }" :breakpoints="dialogBreakpoints">
            <form class="space-y-3" @submit.prevent="closeSession">
                <div class="text-sm text-slate-600 p-3 rounded-lg bg-amber-50 border border-amber-200 flex gap-2">
                    <i class="pi pi-info-circle text-amber-600 mt-0.5" />
                    <span>{{ t("pos_screen.close_session_hint") }}</span>
                </div>
                <div>
                    <label class="text-sm font-medium text-slate-700 block mb-1">{{ t("pos.closing_cash") }}</label>
                    <InputNumber v-model="closeForm.closing_cash" class="w-full" inputClass="w-full" :min="0" :maxFractionDigits="2" />
                </div>
                <div>
                    <label class="text-sm font-medium text-slate-700 block mb-1">{{ t("shared.terms.notes") }}</label>
                    <InputText v-model="closeForm.note" class="w-full" />
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <Button type="button" :label="t('shared.terms.cancel')" text severity="secondary" @click="closeDialog = false" />
                    <Button type="submit" :label="t('pos.close_session')" icon="pi pi-stop-circle" severity="danger" :loading="loading" />
                </div>
            </form>
        </Dialog>
    </div>
</template>
