<template>
    <AuthenticatedLayout>
        <div class="card">
            <Toolbar class="mb-6">
                <template #start>
                    <!-- <Link :href="route('tags.index')">
                    <Button label="Back To List" icon="pi pi-plus" class="mr-2"></Button>
                    </Link> -->
                    <h1 class="">Create Tag</h1>
                </template>

                <template #end>
                    <Button label="Export" icon="pi pi-upload" severity="secondary" @click="exportCSV($event)" />
                </template>
            </Toolbar>

            <div class="card">
                <div class="flex flex-col gap-6">
                    <div>
                        <label for="name" class="block font-bold mb-2">Name</label>
                        <InputText id="name" v-model.trim="form.name" required="true" autofocus
                            :invalid="submitted && !form.name" fluid />
                        <small v-if="submitted && !form.name" class="text-red-500">Name is required.</small>
                    </div>
                </div>
                <div class="flex flex-col gap-6 mt-3">
                    <div>
                        <label for="is_active" class="block font-bold mb-2">Status</label>
                        <Select v-model="form.is_active" :options="statuses" optionLabel="label" optionValue="value"
                            placeholder="Select a status" class="w-full" :required="true" />
                        <small v-if="submitted && (form.is_active == null)" class="text-red-500">
                            Status is required.
                        </small>
                    </div>
                </div>

                <div>
                    <div class="mt-3">
                        <Button label="Cancel" icon="pi pi-times" text />
                        <Button label="Save & Continue" text icon="pi pi-check" @click="saveItem(true)"
                            v-if="!isEdit" />
                        <Button label="Save" icon="pi pi-check" @click="saveItem(false)" v-if="!isEdit" />
                        <Button label="Update" icon="pi pi-check" @click="updateItem" v-if="isEdit" />
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
<script setup>
import { router, useForm } from '@inertiajs/vue3';
import { statuses } from '@/Helpers/enums';
import { Link } from '@inertiajs/vue3'
import { ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { useToast } from 'primevue';

const toast = useToast();
const isEdit = ref(false);
const submitted = ref(false);

const form = useForm({
    name: '',
    is_active: 1,
});

const saveItem = (saveAndContinue) => {
    form.post(route('tags.store'), {
        onSuccess: () => {
            form.reset();
            toast.add({ severity: 'success', summary: 'Successful', detail: 'Successfully Created!', life: 3000 });
            if (!saveAndContinue) {
                router.visit(route('tags.index'));
            }
        }
    })
}
</script>
