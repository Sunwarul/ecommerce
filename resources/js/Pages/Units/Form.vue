<template>
    <div>
        <div class="mb-4">
            <label for="name" class="">Name</label>
            <InputText id="name" v-model="form.name" class="mt-1 block w-full" />
            <div v-if="form?.errors?.name" class="text-red-500 text-sm mt-1">{{ form?.errors?.name }}</div>
        </div>

        <div class="mb-4">
            <label for="short_code" class="">Short Code</label>
            <InputText id="short_code" v-model="form.short_code" class="mt-1 block w-full" />
            <div v-if="form?.errors?.short_code" class="text-red-500 text-sm mt-1">{{ form?.errors?.short_code }}</div>
        </div>

        <div class="mb-4">
            <div class="flex items-center">
                <Checkbox id="status" v-model="form.status" class="h-4 w-4" binary />
                <label for="status" class="ml-2 ">Status</label>
            </div>
            <div v-if="form?.errors?.status" class="text-red-500 text-sm mt-1">{{ form?.errors?.status }}</div>
        </div>

        <div class="mb-4">
            <label for="base_unit_id" class="">Base Unit</label>
            <Select v-model="form.base_unit_id" :options="baseUnits" filter optionLabel="name" option-value="id"
                placeholder="Select a Base Unit" class="w-full md:w-56">
            </Select>
            <div v-if="form?.errors?.base_unit_id" class="text-red-500 text-sm mt-1">
                Base Units is Required
            </div>
        </div>
    </div>
</template>

<script setup>
import { resolveImagePath } from '@/Helpers/imageHelper.js';

defineProps({
    form: {
        type: Object,
        required: true
    },
    baseUnits: Object,
});
</script>
