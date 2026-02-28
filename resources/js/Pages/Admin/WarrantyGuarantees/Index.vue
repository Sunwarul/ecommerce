<template>
    <div>
        <CrudComponent :form :categories>
            <template #columns>
                <Column field="name" header="Name" sortable></Column>
                <Column field="type" header="Type" sortable></Column>
                <Column field="duration" header="Duration" sortable></Column>
                <Column field="category.name" header="Category" sortable>
                    <template #body="{ data }">
                        <Badge v-if="data.category" severity="info">
                            {{ data.category.name }}
                        </Badge>
                        <span v-else class="text-slate-400">General</span>
                    </template>
                </Column>
                <Column field="is_active" header="Status">
                    <template #body="{ data }">
                        <Badge :severity="data.is_active ? 'success' : 'danger'">
                            {{ data.is_active ? "Active" : "Inactive" }}
                        </Badge>
                    </template>
                </Column>
                <Column field="created_at" header="Created At" sortable></Column>
            </template>

            <template #form="{ submitted, statuses }">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="flex flex-col gap-2">
                        <label for="name" class="block font-bold">Name</label>
                        <InputText id="name" v-model.trim="form.name" required="true" autofocus
                            :invalid="submitted && !form.name" fluid />
                        <small v-if="submitted && !form.name" class="text-red-500">Name is required.</small>
                    </div>

                    <div class="flex flex-col gap-2">
                        <label for="category_id" class="block font-bold">Category (Optional)</label>
                        <Select v-model="form.category_id" :options="categories" optionLabel="name" optionValue="id"
                            placeholder="Select a Category" class="w-full" showClear />
                    </div>

                    <div class="flex flex-col gap-2">
                        <label for="type" class="block font-bold">Type</label>
                        <Dropdown id="type" v-model="form.type" :options="types" placeholder="Select Type" class="w-full"
                            :invalid="submitted && !form.type" />
                        <small v-if="submitted && !form.type" class="text-red-500">Type is required.</small>
                    </div>

                    <div class="flex flex-col gap-2">
                        <label for="duration" class="block font-bold">Duration</label>
                        <InputText id="duration" v-model.trim="form.duration" required="true"
                            :invalid="submitted && !form.duration" fluid placeholder="e.g. 1 Year, 6 Months" />
                        <small v-if="submitted && !form.duration" class="text-red-500">Duration is required.</small>
                    </div>

                    <div class="flex flex-col gap-2 md:col-span-2">
                        <label for="description" class="block font-bold">Description / Warranty Text</label>
                        <Textarea id="description" v-model.trim="form.description" rows="3" fluid
                            placeholder="Detailed warranty information..." />
                    </div>

                    <div class="flex flex-col gap-2">
                        <label for="is_active" class="block font-bold">Status</label>
                        <Select v-model="form.is_active" :options="statuses" optionLabel="label" optionValue="value"
                            placeholder="Select a status" class="w-full" :required="true" />
                        <small v-if="submitted && form.is_active == null" class="text-red-500">
                            Status is required.
                        </small>
                    </div>
                </div>
            </template>
        </CrudComponent>
    </div>
</template>

<script setup>
import CrudComponent from "@/Components/CrudComponent.vue";
import { useForm } from "@inertiajs/vue3";
import { ref } from "vue";

const props = defineProps({
    categories: { type: Array, default: () => [] },
});

const form = useForm({
    name: "",
    type: "Warranty",
    duration: "",
    description: "",
    category_id: null,
    is_active: 1,
});

const types = ref(["Warranty", "Guaranty", "None", "Service"]);
</script>
