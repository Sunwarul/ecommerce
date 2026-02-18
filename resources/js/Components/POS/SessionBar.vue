<script setup>
import { router, usePage } from "@inertiajs/vue3";
import { useToast } from "primevue/usetoast";
import { computed, ref, watch } from "vue";

import Button from "primevue/button";
import Dialog from "primevue/dialog";
import Dropdown from "primevue/dropdown";
import InputNumber from "primevue/inputnumber";
import InputText from "primevue/inputtext";

const toast = useToast();

const props = defineProps({
    currentSession: { type: Object, default: null },
    branches: { type: Array, default: () => [] }, // pass from controller
    warehouses: { type: Array, default: () => [] }, // pass from controller
});

const page = usePage();

const posSession = computed(() => page.props.currentSession);

const openDialog = ref(false);
const closeDialog = ref(false);

const openForm = ref({
    branch_id: null,
    warehouse_id: null,
    opening_cash: 0,
    note: "",
});

const closeForm = ref({
    closing_cash: 0,
    note: "",
});

watch(openDialog, (newVal) => {
    if (newVal) {
        if (props.branches && props.branches.length > 0 && !openForm.value.branch_id) {
            openForm.value.branch_id = props.branches[0].id;
        }
        if (props.warehouses && props.warehouses.length > 0 && !openForm.value.warehouse_id) {
            openForm.value.warehouse_id = props.warehouses[0].id;
        }
    }
});

function openSession() {
    router.post(route("pos.session.open"), openForm.value, {
        preserveScroll: true,
        onSuccess: () => {
            toast.add({
                severity: "success",
                summary: "Opened",
                detail: "POS session opened",
                life: 2000,
            });
            openDialog.value = false;
            window.location.reload();
        },
        onError: () => {
            toast.add({
                severity: "error",
                summary: "Error",
                detail: "Failed to open session",
                life: 2500,
            });
        },
    });
}

function closeSession() {
    router.post(route("pos.session.close"), closeForm.value, {
        preserveScroll: true,
        onSuccess: () => {
            toast.add({
                severity: "success",
                summary: "Closed",
                detail: "POS session closed",
                life: 2000,
            });
            closeDialog.value = false;
            window.location.reload();
        },
        onError: () => {
            toast.add({
                severity: "error",
                summary: "Error",
                detail: "Failed to close session",
                life: 2500,
            });
        },
    });
}
</script>

<template>
    <div class="flex items-center gap-2">
        <Button v-if="!posSession" label="Open POS Session" icon="pi pi-play" @click="openDialog = true" />

        <Button v-else label="Close POS Session" icon="pi pi-stop" class="p-button-danger"
            @click="closeDialog = true" />

        <!-- OPEN DIALOG -->
        <Dialog v-model:visible="openDialog" modal header="Open POS Session" :style="{ width: '420px' }">
            <div class="space-y-3">
                <div>
                    <label class="text-sm font-medium">Branch *</label>
                    <Dropdown v-model="openForm.branch_id" :options="branches" optionLabel="name" optionValue="id"
                        class="w-full" />
                </div>

                <div>
                    <label class="text-sm font-medium">Warehouse *</label>
                    <Dropdown v-model="openForm.warehouse_id" :options="warehouses" optionLabel="name" optionValue="id"
                        class="w-full" />
                </div>

                <div>
                    <label class="text-sm font-medium">Opening Cash</label>
                    <InputNumber v-model="openForm.opening_cash" class="w-full" :min="0" />
                </div>

                <div>
                    <label class="text-sm font-medium">Note</label>
                    <InputText v-model="openForm.note" class="w-full" />
                </div>

                <div class="flex justify-end gap-2 pt-2">
                    <Button label="Cancel" class="p-button-text" @click="openDialog = false" />
                    <Button label="Open" icon="pi pi-check" @click="openSession" />
                </div>
            </div>
        </Dialog>

        <!-- CLOSE DIALOG -->
        <Dialog v-model:visible="closeDialog" modal header="Close POS Session" :style="{ width: '420px' }">
            <div class="space-y-3">
                <div class="text-sm text-slate-600">
                    Closing session will stop sales until you open a new
                    session.
                </div>

                <div>
                    <label class="text-sm font-medium">Closing Cash</label>
                    <InputNumber v-model="closeForm.closing_cash" class="w-full" :min="0" />
                </div>

                <div>
                    <label class="text-sm font-medium">Note</label>
                    <InputText v-model="closeForm.note" class="w-full" />
                </div>

                <div class="flex justify-end gap-2 pt-2">
                    <Button label="Cancel" class="p-button-text" @click="closeDialog = false" />
                    <Button label="Close" icon="pi pi-check" class="p-button-danger" @click="closeSession" />
                </div>
            </div>
        </Dialog>
    </div>
</template>
