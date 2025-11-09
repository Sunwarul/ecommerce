<template>
    <div class="grid grid-cols-2 gap-4">
        <div class="mb-4">
            <label for="customer_type" class="mb-1">Customer Type</label>
            <Select
                v-model="form.customer_type"
                :options="customerTypes"
                optionLabel="label"
                optionValue="value"
                placeholder="Select a customer type"
                class="w-full"
            />
            <div
                v-if="form?.errors?.customer_type"
                class="text-red-500 text-sm mt-1"
            >
                {{ form?.errors?.customer_type }}
            </div>
        </div>

        <div class="mb-4">
            <label for="name" class="">Name</label>
            <InputText
                id="name"
                v-model="form.name"
                class="mt-1 block w-full"
            />
            <div v-if="form?.errors?.name" class="text-red-500 text-sm mt-1">
                {{ form?.errors?.name }}
            </div>
        </div>

        <div class="mb-4">
            <label for="email" class="">Email</label>
            <InputText
                id="email"
                v-model="form.email"
                type="email"
                class="mt-1 block w-full"
            />
            <div v-if="form?.errors?.email" class="text-red-500 text-sm mt-1">
                {{ form?.errors?.email }}
            </div>
        </div>

        <div class="mb-4">
            <label for="phone" class="">Phone</label>
            <InputText
                id="phone"
                v-model="form.phone"
                class="mt-1 block w-full"
            />
            <div v-if="form?.errors?.phone" class="text-red-500 text-sm mt-1">
                {{ form?.errors?.phone }}
            </div>
        </div>

        <div class="mb-4">
            <label for="mobile" class="">Mobile</label>
            <InputText
                id="mobile"
                v-model="form.mobile"
                class="mt-1 block w-full"
            />
            <div v-if="form?.errors?.mobile" class="text-red-500 text-sm mt-1">
                {{ form?.errors?.mobile }}
            </div>
        </div>

        <div class="mb-4">
            <label for="whatsapp_number" class="">Whatsapp Number</label>
            <InputText
                id="whatsapp_number"
                v-model="form.whatsapp_number"
                class="mt-1 block w-full"
            />
            <div
                v-if="form?.errors?.whatsapp_number"
                class="text-red-500 text-sm mt-1"
            >
                {{ form?.errors?.whatsapp_number }}
            </div>
        </div>

        <div class="mb-4">
            <label for="tax_number" class="">Tax Number</label>
            <InputText
                id="tax_number"
                v-model="form.tax_number"
                class="mt-1 block w-full"
            />
            <div
                v-if="form?.errors?.tax_number"
                class="text-red-500 text-sm mt-1"
            >
                {{ form?.errors?.tax_number }}
            </div>
        </div>

        <div class="flex flex-col gap-6 mb-4">
            <div>
                <label for="currency_id" class="block font-bold mb-1"
                    >Currency</label
                >
                <Select
                    v-model="form.currency_id"
                    :options="currencies"
                    optionLabel="title"
                    optionValue="id"
                    placeholder="Select a currency"
                    class="w-full"
                />
            </div>
        </div>

        <div class="mb-4">
            <label for="billing_address" class="">Billing Address</label>
            <Textarea
                id="billing_address"
                v-model="form.billing_address"
                class="mt-1 block w-full"
                rows="3"
            />
            <div
                v-if="form?.errors?.billing_address"
                class="text-red-500 text-sm mt-1"
            >
                {{ form?.errors?.billing_address }}
            </div>
        </div>

        <div class="mb-4">
            <label for="shipping_address" class="">Shipping Address</label>
            <Textarea
                id="shipping_address"
                v-model="form.shipping_address"
                class="mt-1 block w-full"
                rows="3"
            />
            <div
                v-if="form?.errors?.shipping_address"
                class="text-red-500 text-sm mt-1"
            >
                {{ form?.errors?.shipping_address }}
            </div>
        </div>

        <div class="mb-4">
            <label for="opening_balance" class="">Opening Balance</label>
            <InputText
                id="opening_balance"
                v-model="form.opening_balance"
                class="mt-1 block w-full"
            />
            <div
                v-if="form?.errors?.opening_balance"
                class="text-red-500 text-sm mt-1"
            >
                {{ form?.errors?.opening_balance }}
            </div>
        </div>

        <div class="mb-4">
            <label for="opening_balance_date" class=""
                >Opening Balance Date</label
            >
            <Calendar
                id="opening_balance_date"
                v-model="form.opening_balance_date"
                class="mt-1 block w-full"
            />
            <div
                v-if="form?.errors?.opening_balance_date"
                class="text-red-500 text-sm mt-1"
            >
                {{ form?.errors?.opening_balance_date }}
            </div>
        </div>

        <div class="mb-4">
            <label for="opening_balance_type">Opening Balance is</label>
            <div class="flex items-center gap-4 mt-2">
                <div class="flex items-center">
                    <RadioButton
                        inputId="to_pay"
                        v-model="form.opening_balance_type"
                        name="opening_balance_type"
                        value="to_pay"
                    />
                    <label for="to_pay" class="ml-2">To Pay</label>
                </div>

                <div class="flex items-center">
                    <RadioButton
                        inputId="to_receive"
                        v-model="form.opening_balance_type"
                        name="opening_balance_type"
                        value="to_receive"
                    />
                    <label for="to_receive" class="ml-2">To Receive</label>
                </div>
            </div>

            <div
                v-if="form?.errors?.opening_balance_type"
                class="text-red-500 text-sm mt-1"
            >
                {{ form?.errors?.opening_balance_type }}
            </div>
        </div>

        <div class="mb-4">
            <label for="credit_limit" class="">Credit Limit</label>
            <InputText
                id="credit_limit"
                v-model="form.credit_limit"
                class="mt-1 block w-full"
            />
            <div
                v-if="form?.errors?.credit_limit"
                class="text-red-500 text-sm mt-1"
            >
                {{ form?.errors?.credit_limit }}
            </div>
        </div>

        <div class="mb-4">
            <div class="flex items-center">
                <Checkbox
                    id="has_credit_limit"
                    v-model="form.has_credit_limit"
                    class="h-4 w-4"
                    binary
                />
                <label for="has_credit_limit" class="ml-2"
                    >Has Credit Limit</label
                >
            </div>
            <div
                v-if="form?.errors?.has_credit_limit"
                class="text-red-500 text-sm mt-1"
            >
                {{ form?.errors?.has_credit_limit }}
            </div>
        </div>

        <!-- <div class="flex flex-col gap-6 mt-3">
            <div>
                <label for="photo" class="block font-bold mb-2">Photo</label>
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
        </div> -->

        <!-- <div class="mb-4">
            <label for="file" class="">File</label>
            <InputText
                id="file"
                v-model="form.file"
                class="mt-1 block w-full"
            />
            <div v-if="form?.errors?.file" class="text-red-500 text-sm mt-1">
                {{ form?.errors?.file }}
            </div>
        </div> -->

        <div class="mb-4">
            <div class="flex items-center">
                <Checkbox
                    id="status"
                    v-model="form.status"
                    class="h-4 w-4"
                    binary
                />
                <label for="status" class="ml-2">Status</label>
            </div>
            <div v-if="form?.errors?.status" class="text-red-500 text-sm mt-1">
                {{ form?.errors?.status }}
            </div>
        </div>

        <div class="flex flex-col gap-6">
            <div>
                <label for="file" class="block font-bold mb-2">File (NID, Passport, Driving License etc)</label>
                <InputText
                    id="file"
                    v-model.trim="form.file"
                    required="true"
                    autofocus
                    :invalid="submitted && !form.file"
                    fluid
                    type="file"
                />
                <small v-if="submitted && !form.file" class="text-red-500"
                    >File is required.</small
                >
            </div>
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
                    class="shadow-md rounded-xl w-[100px]"
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
    currencies: {
        type: Object,
        required: true,
    },
    submitted: Boolean,
    photoPreview: String,
    resolveImagePath: Function,
    handlePhotoUpload: Function,
});
const customerTypes = [
    {
        label: "Retailer",
        value: "retailer",
    },
    {
        label: "Wholesaler",
        value: "wholesaler",
    },
];
</script>
