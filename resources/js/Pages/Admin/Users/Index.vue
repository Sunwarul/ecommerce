<template>
    <div>
        <CrudComponent :form="form" @editingItem="handleEditingItem">
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
                <Column field="email" header="Email"></Column>
                <Column field="phone" header="Phone"></Column>
                <!-- roles column -->
                <Column field="roles" header="Roles">
                    <template #body="slotProps">
                        <div>
                            <span
                                v-for="role in slotProps.data.roles"
                                :key="role.id"
                                class="inline-block bg-blue-100 text-blue-800 text-xs px-2 rounded-full mr-1"
                            >
                                {{ role.name }}
                            </span>
                        </div>
                    </template>
                </Column>
                <!-- <Column
                    field="updated_at"
                    header="Created At"
                    sortable
                ></Column> -->
            </template>
            <template
                #form="{
                    submitted,
                    handlePhotoUpload,
                    photoPreview,
                    resolveImagePath,
                }"
            >
                <Form
                    :form="form"
                    :roles="roles"
                    :branches="branches"
                    v-bind="{
                        submitted,
                        handlePhotoUpload,
                        photoPreview,
                        resolveImagePath,
                    }"
                />
            </template>
        </CrudComponent>
    </div>
</template>
<script setup>
import CrudComponent from "@/Components/CrudComponent.vue";
import { useForm } from "@inertiajs/vue3";
import { nextTick } from "vue";
import Form from "./Form.vue";
const { branches, roles } = defineProps(["branches", "roles"]);

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
    form.name = user.name;
    form.email = user.email;
    // form.phone = user.phone;
    await nextTick();
    form.roles = user.roles?.map((role) => role.id) || [];
    form.branch_id = user.branch_id || null;
};
</script>
