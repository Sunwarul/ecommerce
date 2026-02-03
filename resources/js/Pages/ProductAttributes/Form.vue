<template>
    <div class="flex flex-col gap-6">

        <!-- Name -->
        <div>
            <label for="name" class="block font-bold mb-2">Name</label>
            <InputText
                id="name"
                v-model.trim="form.name"
                autofocus
                :invalid="submitted && !form.name"
                class="w-full"
            />
            <small v-if="submitted && !form.name" class="text-red-500">
                Name is required.
            </small>
        </div>

        <!-- Display Name -->
        <div>
            <label for="display_name" class="block font-bold mb-2">Display Name</label>
            <InputText
                id="display_name"
                v-model.trim="form.display_name"
                :invalid="submitted && !form.display_name"
                class="w-full"
            />
            <small v-if="submitted && !form.display_name" class="text-red-500">
                Display name is required.
            </small>
        </div>

        <!-- Type -->
        <div>
            <label for="type" class="block font-bold mb-2">Type</label>
            <Dropdown
                id="type"
                v-model="form.type"
                :options="types"
                placeholder="Select attribute type"
                class="w-full"
            />
            <small v-if="submitted && !form.type" class="text-red-500">
                Type is required.
            </small>
        </div>

        <!-- Status -->
        <div>
            <label for="is_active" class="block font-bold mb-2">Status</label>
            <div class="flex items-center gap-3">
                <InputSwitch v-model="form.is_active" />
                <span class="text-sm">
                    {{ form.is_active ? 'Active' : 'Inactive' }}
                </span>
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
    submitted: {
        type: Boolean,
        default: false,
    },
});

const types = [
    'select',
    'radio',
    'color',
    'button',
];
</script>
