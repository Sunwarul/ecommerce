<template>
    <div class="grid grid-cols-2 gap-4">
        <div class="mb-4">
            <label for="title" class="">Title</label>
            <InputText id="title" v-model="form.title" class="mt-1 block w-full" />
            <div v-if="form?.errors?.title" class="text-red-500 text-sm mt-1">
                {{ form?.errors?.title }}
            </div>
        </div>

        <div class="mb-4">
            <label for="date" class="">Date</label>
            <Calendar id="date" v-model="form.date" class="mt-1 block w-full" dateFormat="yy-mm-dd" />
            <div v-if="form?.errors?.date" class="text-red-500 text-sm mt-1">
                {{ form?.errors?.date }}
            </div>
        </div>

        <div class="mb-4">
            <label for="amount" class="">Amount</label>
            <InputNumber id="amount" v-model="form.amount" class="mt-1 block w-full" />
            <div v-if="form?.errors?.amount" class="text-red-500 text-sm mt-1">
                {{ form?.errors?.amount }}
            </div>
        </div>

        <div class="flex flex-col gap-6 mt-3 mb-4">
            <div>
                <label for="expense_category_id" class="block font-bold mb-2">Expense Category</label>
                <Select v-model="form.expense_category_id" :options="expenseCategories" optionLabel="name"
                    optionValue="id" placeholder="Select a category" class="w-full" :required="true" />
                <small v-if="submitted && form.expense_category_id == null" class="text-red-500">
                    Expense category is required.
                </small>
            </div>
        </div>

        <div class="flex flex-col gap-6 mt-3 mb-4">
            <div>
                <label for="user_id" class="block font-bold mb-2">Users</label>
                <Select v-model="form.user_id" :options="users" optionLabel="name" optionValue="id"
                    placeholder="Select a user" class="w-full" :required="true" />
                <small v-if="submitted && form.user_id == null" class="text-red-500">
                    User is required.
                </small>
            </div>
        </div>

        <div class="flex flex-col gap-6 mt-3 mb-4">
            <div>
                <label for="warehouse_id" class="block font-bold mb-2">Warehouse</label>
                <Select v-model="form.warehouse_id" :options="warehouses" optionLabel="name" optionValue="id"
                    placeholder="Select a warehouse" class="w-full" :required="true" />
                <small v-if="submitted && form.warehouse_id == null" class="text-red-500">
                    Warehouse is required.
                </small>
            </div>
        </div>

        <div class="mb-4">
            <label for="details" class="">Details</label>
            <Textarea id="details" v-model="form.details" class="mt-1 block w-full" rows="3" />
            <div v-if="form?.errors?.details" class="text-red-500 text-sm mt-1">
                {{ form?.errors?.details }}
            </div>
        </div>
        <div class="mb-4 col-span-2">
            <div class="flex items-center">
                <Checkbox id="status" v-model="form.status" class="h-4 w-4" binary />
                <label for="status" class="ml-2">Status</label>
            </div>
            <div v-if="form?.errors?.status" class="text-red-500 text-sm mt-1">
                {{ form?.errors?.status }}
            </div>
        </div>

        <div class="flex flex-col gap-6 mt-3">
            <div>
                <label for="attachment" class="block font-bold mb-2">Attachment</label>
                <FileUpload mode="basic" name="attachment" customUpload @select="handlePhotoUpload" :auto="true" chooseLabel="Choose File" class="w-full" />
            </div>
            <div>
                <img v-if="form.photo || photoPreview" :src="photoPreview ?? resolveImagePath(form.photo)" alt="File"
                    class="shadow-md rounded-xl w-full" style="filter: grayscale(100%)" />
            </div>
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
const { expenseCategories, form } = defineProps({
    form: {
        type: Object,
        required: true,
    },
    users: {
        type: Object,
        required: true,
    },
    warehouses: {
        type: Object,
        required: true,
    },
    expenseCategories: {
        type: Object,
        required: true,
    },
});
</script>
