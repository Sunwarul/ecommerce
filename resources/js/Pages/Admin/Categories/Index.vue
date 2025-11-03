<template>
    <div>
        <CrudComponent :form>
            <template #columns>
                <Column field="photo" header="Photo">
                    <template #body="{ data }">
                        <img
                            :src="$resolveImagePath(data.photo)"
                            alt="Category Photo"
                            class="shadow-md rounded-xl w-16 h-16"
                            style="filter: grayscale(100%)"
                            @click="() => console.log('data', data)"
                        />
                    </template>
                </Column>
                <Column field="name" header="Name"></Column>
                <Column field="parent" header="Parent">
                    <template #body="{ data }">
                        {{ data.parent ? data.parent.name : "N/A" }}
                    </template>
                </Column>
                <Column field="slug" header="Slug"></Column>
                <Column field="is_active" header="Status">
                    <template #body="{ data }">
                        <Badge
                            :severity="data.is_active ? 'success' : 'danger'"
                        >
                            {{ data.is_active ? "Active" : "Inactive" }}
                        </Badge>
                    </template>
                </Column>

                <Column
                    field="created_at"
                    header="Created At"
                    sortable
                ></Column>
                <!-- <Column
                    field="updated_at"
                    header="Updated At"
                    sortable
                ></Column> -->
            </template>
            <template
                #form="{
                    submitted,
                    statuses,
                    handlePhotoUpload,
                    photoPreview,
                    resolveImagePath,
                }"
            >
                <div class="flex flex-col gap-6">
                    <div>
                        <label for="name" class="block font-bold mb-2"
                            >Name</label
                        >
                        <InputText
                            id="name"
                            v-model.trim="form.name"
                            required="true"
                            autofocus
                            :invalid="submitted && !form.name"
                            fluid
                        />
                        <small
                            v-if="submitted && !form.name"
                            class="text-red-500"
                            >Name is required.</small
                        >
                    </div>
                </div>
                <div class="flex flex-col gap-6 mt-3 mb-4">
                    <div>
                        <label for="parent_id" class="block font-bold mb-2"
                            >Parent Category</label
                        >
                        <Select
                            v-model="form.parent_id"
                            :options="parentCategories"
                            optionLabel="name"
                            optionValue="id"
                            placeholder="Select a parent"
                            class="w-full"
                        />
                    </div>
                </div>
                <div class="flex flex-col gap-6 mt-3">
                    <div>
                        <label for="photo" class="block font-bold mb-2"
                            >Photo</label
                        >
                        <FileUpload
                            mode="basic"
                            name="photo"
                            customUpload
                            @select="handlePhotoUpload"
                            :auto="true"
                            accept="image/*"
                            chooseLabel="Choose Image"
                            class="w-full"
                        />
                    </div>
                    <div>
                        <img
                            v-if="form.photo || photoPreview"
                            :src="photoPreview ?? resolveImagePath(form.photo)"
                            alt="Image"
                            class="shadow-md rounded-xl w-[200px]"
                            style="filter: grayscale(100%)"
                        />
                    </div>
                </div>
                <div class="flex flex-col gap-6 mt-3">
                    <div>
                        <label for="is_active" class="block font-bold mb-2"
                            >Status</label
                        >
                        <Select
                            v-model="form.is_active"
                            :options="statuses"
                            optionLabel="label"
                            optionValue="value"
                            placeholder="Select a status"
                            class="w-full"
                            :required="true"
                        />
                        <small
                            v-if="submitted && form.is_active == null"
                            class="text-red-500"
                        >
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

const form = useForm({
    parent_id: null,
    name: "",
    photo: null,
    is_active: 1,
});
// define props
const { categories, parentCategories } = defineProps({
    categories: Object,
    parentCategories: Object,
});

// ✅ log full array/object
console.log("Full props.data:", parentCategories);
</script>
