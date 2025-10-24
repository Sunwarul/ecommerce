<template>
  <div>
    <div class="mb-4">
        <label for="code" class="">Code</label>
        <InputText id="code" v-model="form.code" class="mt-1 block w-full" />
        <div v-if="form?.errors?.code" class="text-red-500 text-sm mt-1">{{ form?.errors?.code }}</div>
    </div>

    <div class="mb-4">
        <label for="name" class="">Name</label>
        <InputText id="name" v-model="form.name" class="mt-1 block w-full" />
        <div v-if="form?.errors?.name" class="text-red-500 text-sm mt-1">{{ form?.errors?.name }}</div>
    </div>

    <div class="mb-4">
        <label for="address" class="">Address</label>
        <InputText id="address" v-model="form.address" class="mt-1 block w-full" />
        <div v-if="form?.errors?.address" class="text-red-500 text-sm mt-1">{{ form?.errors?.address }}</div>
    </div>

    <div class="mb-4">
        <label for="phone" class="">Phone</label>
        <InputText id="phone" v-model="form.phone" class="mt-1 block w-full" />
        <div v-if="form?.errors?.phone" class="text-red-500 text-sm mt-1">{{ form?.errors?.phone }}</div>
    </div>

    <div class="mb-4">
        <div class="flex items-center">
            <Checkbox id="status" v-model="form.status" class="h-4 w-4" binary />
            <label for="status" class="ml-2 ">Status</label>
        </div>
        <div v-if="form?.errors?.status" class="text-red-500 text-sm mt-1">{{ form?.errors?.status }}</div>
    </div>

    <!--
    <div class="flex flex-col gap-6">
        <div>
            <label for="name" class="block font-bold mb-2">Name</label>
            <InputText id="name" v-model.trim="form.name" required="true" autofocus
                :invalid="submitted && !form.name" fluid />
            <small v-if="submitted && !form.name" class="text-red-500">Name is required.</small>
        </div>
    </div>
    -->
    <!--
    <div class="flex flex-col gap-6 mt-3">
        <div>
            <label for="photo" class="block font-bold mb-2">Photo</label>
            <FileUpload mode="basic" name="photo" customUpload @select="handlePhotoUpload" :auto="true"
                accept="image/*" chooseLabel="Choose Image" class="w-full" />
        </div>
        <div>
            <img v-if="form.photo || photoPreview" :src="photoPreview ?? resolveImagePath(form.photo)"
                alt="Image" class="shadow-md rounded-xl w-full" style="filter: grayscale(100%)" />
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
    -->

  </div>
</template>

<script setup>
import { resolveImagePath } from '@/Helpers/imageHelper.js';

defineProps({
  form: {
    type: Object,
    required: true
  }
});
</script>
