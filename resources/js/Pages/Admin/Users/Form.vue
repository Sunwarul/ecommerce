<template>
    <div>
        <div class="mb-3">
            <label for="name" class="block font-bold mb-2">Name</label>
            <InputText
                id="name"
                v-model.trim="form.name"
                required="true"
                autofocus
                :invalid="submitted && !form.name"
                fluid
            />
            <small v-if="submitted && !form.name" class="text-red-500"
                >Name is required.</small
            >
        </div>
        <div class="mb-3">
            <label for="email" class="block font-bold mb-2">Email</label>
            <InputText
                id="email"
                v-model.trim="form.email"
                required="true"
                autofocus
                :invalid="submitted && !form.email"
                fluid
            />
            <small v-if="submitted && !form.email" class="text-red-500"
                >Email is required.</small
            >
        </div>
        <div class="mb-3">
            <label for="password" class="block font-bold mb-2">Password</label>
            <InputText
                type="password"
                id="password"
                v-model.trim="form.password"
                required="true"
                autofocus
                :invalid="submitted && !form.password"
                fluid
            />
            <small v-if="submitted && !form.password" class="text-red-500"
                >Password is required.</small
            >
        </div>
        <div class="mb-3">
            <label for="password_confirmation" class="block font-bold mb-2"
                >Password Confirmation</label
            >
            <InputText
                type="password"
                id="password_confirmation"
                v-model.trim="form.password_confirmation"
                required="true"
                autofocus
                :invalid="submitted && !form.password_confirmation"
                fluid
            />
            <small
                v-if="submitted && !form.password_confirmation"
                class="text-red-500"
                >Password confirmation is required.</small
            >
        </div>
        <div class="flex flex-col gap-6 mt-3 mb-4">
            <div>
                <label for="role" class="block font-bold mb-2">Roles</label>
                <MultiSelect
                    v-model="form.roles"
                    :options="roles"
                    optionLabel="name"
                    optionValue="id"
                    placeholder="Select Roles"
                    class="w-full"
                    display="chip"
                />
            </div>
        </div>
        <div class="mb-3">
            <label for="phone" class="block font-bold mb-2">Phone</label>
            <InputText
                id="phone"
                v-model.trim="form.phone"
                required="true"
                autofocus
                :invalid="submitted && !form.phone"
                fluid
            />
            <small v-if="submitted && !form.phone" class="text-red-500"
                >Phone is required.</small
            >
        </div>
        <div class="flex flex-col gap-6 mt-3">
            <div>
                <label for="photo" class="block font-bold mb-2">Avatar</label>
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
    </div>
</template>

<script setup>
defineProps({
    form: {
        type: Object,
        required: true,
    },
    submitted: Boolean,
    photoPreview: String,
    resolveImagePath: Function,
    handlePhotoUpload: Function,
    roles: {
        type: Array,
        required: true,
    },
});
</script>
