<template>
    <div>
        <div class="mb-3">
            <label for="name" class="block font-bold mb-2">Name</label>
            <InputText id="name" v-model.trim="form.name" required="true" autofocus :invalid="submitted && !form.name"
                fluid />
            <small v-if="submitted && !form.name" class="text-red-500">Name is required.</small>
        </div>
        <div class="mb-3">
            <label for="email" class="block font-bold mb-2">Email</label>
            <InputText id="email" v-model.trim="form.email" required="true" autofocus
                :invalid="submitted && !form.email" fluid />
            <small v-if="submitted && !form.email" class="text-red-500">Email is required.</small>
        </div>
        <div class="mb-3">
            <label for="password" class="block font-bold mb-2">Password</label>
            <InputText type="password" id="password" v-model.trim="form.password" required="true" autofocus
                :invalid="submitted && !form.password" fluid />
            <small v-if="submitted && !form.password" class="text-red-500">Password is required.</small>
        </div>
        <div class="mb-3">
            <label for="password_confirmation" class="block font-bold mb-2">Password Confirmation</label>
            <InputText type="password" id="password_confirmation" v-model.trim="form.password_confirmation" required="true" autofocus
                :invalid="submitted && !form.password_confirmation" fluid />
            <small v-if="submitted && !form.password_confirmation" class="text-red-500">Password confirmation is
                required.</small>
        </div>
    </div>
</template>

<script setup>

defineProps({
    form: {
        type: Object,
        required: true
    },
    submitted: Boolean,
});
</script>
