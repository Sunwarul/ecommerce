<template>
    <div class="space-y-5">
        <!-- Basic info -->
        <section>
            <h3 class="text-sm font-semibold text-slate-700 mb-3">{{ t("customers_page.section_basic") }}</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="name" class="block text-sm font-medium text-slate-700 mb-1">{{ t("common.name") }} <span class="text-rose-600">*</span></label>
                    <InputText id="name" v-model="form.name" class="w-full" :invalid="!!form.errors?.name" autocomplete="name" />
                    <small v-if="form.errors?.name" class="text-rose-600 text-xs mt-1 block">{{ form.errors.name }}</small>
                </div>

                <div>
                    <label for="phone" class="block text-sm font-medium text-slate-700 mb-1">{{ t("common.phone") }} <span class="text-rose-600">*</span></label>
                    <InputText id="phone" v-model="form.phone" type="tel" inputmode="tel" class="w-full" :invalid="!!form.errors?.phone"
                        placeholder="01XXXXXXXXX" autocomplete="tel" />
                    <small v-if="form.errors?.phone" class="text-rose-600 text-xs mt-1 block">{{ form.errors.phone }}</small>
                </div>

                <div>
                    <label for="whatsapp_number" class="block text-sm font-medium text-slate-700 mb-1">{{ t("customers_page.whatsapp") }}</label>
                    <InputText id="whatsapp_number" v-model="form.whatsapp_number" type="tel" inputmode="tel" class="w-full"
                        :invalid="!!form.errors?.whatsapp_number" />
                    <small v-if="form.errors?.whatsapp_number" class="text-rose-600 text-xs mt-1 block">{{ form.errors.whatsapp_number }}</small>
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-slate-700 mb-1">{{ t("common.email") }}</label>
                    <InputText id="email" v-model="form.email" type="email" class="w-full" :invalid="!!form.errors?.email" autocomplete="email" />
                    <small v-if="form.errors?.email" class="text-rose-600 text-xs mt-1 block">{{ form.errors.email }}</small>
                </div>

                <div class="sm:col-span-2">
                    <label for="address" class="block text-sm font-medium text-slate-700 mb-1">{{ t("common.address") }}</label>
                    <Textarea id="address" v-model="form.address" class="w-full" rows="2" autoResize :invalid="!!form.errors?.address" />
                    <small v-if="form.errors?.address" class="text-rose-600 text-xs mt-1 block">{{ form.errors.address }}</small>
                </div>

                <div>
                    <label for="customer_type" class="block text-sm font-medium text-slate-700 mb-1">{{ t("customers_page.type") }}</label>
                    <Select inputId="customer_type" v-model="form.customer_type" :options="customerTypes" optionLabel="label" optionValue="value"
                        :placeholder="t('customers_page.select_type')" class="w-full" :invalid="!!form.errors?.customer_type" />
                    <small v-if="form.errors?.customer_type" class="text-rose-600 text-xs mt-1 block">{{ form.errors.customer_type }}</small>
                </div>

                <div>
                    <label for="tax_number" class="block text-sm font-medium text-slate-700 mb-1">{{ t("customers.tax_number") }}</label>
                    <InputText id="tax_number" v-model="form.tax_number" class="w-full" :invalid="!!form.errors?.tax_number" />
                    <small v-if="form.errors?.tax_number" class="text-rose-600 text-xs mt-1 block">{{ form.errors.tax_number }}</small>
                </div>

                <div class="flex items-center gap-3 sm:col-span-2">
                    <ToggleSwitch inputId="status" v-model="form.status" :trueValue="1" :falseValue="0" />
                    <label for="status" class="text-sm text-slate-700">{{ t("customers_page.active_customer") }}</label>
                    <small v-if="form.errors?.status" class="text-rose-600 text-xs">{{ form.errors.status }}</small>
                </div>
            </div>
        </section>

        <!-- Accounts -->
        <section class="pt-4 border-t border-slate-100">
            <h3 class="text-sm font-semibold text-slate-700 mb-3">{{ t("customers_page.section_account") }}</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="currency_id" class="block text-sm font-medium text-slate-700 mb-1">{{ t("customers_page.currency") }}</label>
                    <Select inputId="currency_id" v-model="form.currency_id" :options="currencies" optionLabel="title" optionValue="id"
                        :placeholder="t('customers_page.select_currency')" class="w-full" showClear />
                </div>

                <div>
                    <label for="opening_balance" class="block text-sm font-medium text-slate-700 mb-1">{{ t("customers_page.opening_balance") }}</label>
                    <InputText id="opening_balance" v-model="form.opening_balance" inputmode="decimal" class="w-full ltr-nums"
                        :invalid="!!form.errors?.opening_balance" />
                    <small v-if="form.errors?.opening_balance" class="text-rose-600 text-xs mt-1 block">{{ form.errors.opening_balance }}</small>
                </div>

                <div>
                    <label for="opening_balance_date" class="block text-sm font-medium text-slate-700 mb-1">{{ t("customers_page.opening_balance_date") }}</label>
                    <DatePicker inputId="opening_balance_date" v-model="openingDate" dateFormat="dd-mm-yy" showIcon class="w-full"
                        :invalid="!!form.errors?.opening_balance_date" />
                    <small v-if="form.errors?.opening_balance_date" class="text-rose-600 text-xs mt-1 block">{{ form.errors.opening_balance_date }}</small>
                </div>

                <div>
                    <span class="block text-sm font-medium text-slate-700 mb-1">{{ t("customers_page.opening_balance_is") }}</span>
                    <div class="flex flex-wrap items-center gap-4 mt-2">
                        <div class="flex items-center gap-2">
                            <RadioButton inputId="to_pay" v-model="form.opening_balance_type" name="opening_balance_type" value="to_pay" />
                            <label for="to_pay" class="text-sm">{{ t("customers_page.to_pay") }}</label>
                        </div>
                        <div class="flex items-center gap-2">
                            <RadioButton inputId="to_receive" v-model="form.opening_balance_type" name="opening_balance_type" value="to_receive" />
                            <label for="to_receive" class="text-sm">{{ t("customers_page.to_receive") }}</label>
                        </div>
                    </div>
                    <small v-if="form.errors?.opening_balance_type" class="text-rose-600 text-xs mt-1 block">{{ form.errors.opening_balance_type }}</small>
                </div>

                <div class="flex items-center gap-3">
                    <ToggleSwitch inputId="has_credit_limit" v-model="form.has_credit_limit" :trueValue="1" :falseValue="0" />
                    <label for="has_credit_limit" class="text-sm text-slate-700">{{ t("customers_page.has_credit_limit") }}</label>
                </div>

                <div v-if="Number(form.has_credit_limit)">
                    <label for="credit_limit" class="block text-sm font-medium text-slate-700 mb-1">{{ t("customers_page.credit_limit") }}</label>
                    <InputText id="credit_limit" v-model="form.credit_limit" inputmode="decimal" class="w-full ltr-nums"
                        :invalid="!!form.errors?.credit_limit" />
                    <small v-if="form.errors?.credit_limit" class="text-rose-600 text-xs mt-1 block">{{ form.errors.credit_limit }}</small>
                </div>
            </div>
        </section>

        <!-- Documents -->
        <section class="pt-4 border-t border-slate-100">
            <h3 class="text-sm font-semibold text-slate-700 mb-3">{{ t("customers_page.section_documents") }}</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <span class="block text-sm font-medium text-slate-700 mb-1">{{ t("customers_page.photo") }}</span>
                    <div class="flex items-center gap-3">
                        <img v-if="photoPreview || (form.photo && typeof form.photo === 'string')"
                            :src="photoPreview ?? resolveImagePath(form.photo)" alt=""
                            class="w-16 h-16 rounded-full object-cover border border-slate-200" />
                        <FileUpload mode="basic" name="photo" customUpload @select="handlePhotoUpload" :auto="true"
                            accept="image/jpeg,image/png" :maxFileSize="7 * 1024 * 1024" :chooseLabel="t('customers_page.choose_photo')" />
                    </div>
                    <small class="text-xs text-slate-500 mt-1 block">{{ t("customers_page.photo_hint") }}</small>
                    <small v-if="form.errors?.photo" class="text-rose-600 text-xs mt-1 block">{{ form.errors.photo }}</small>
                </div>

                <div>
                    <label for="file" class="block text-sm font-medium text-slate-700 mb-1">{{ t("customers_page.document") }}</label>
                    <input id="file" type="file" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                        class="block w-full text-sm text-slate-600 file:me-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200"
                        @change="onDocument" />
                    <small v-if="typeof form.file === 'string' && form.file" class="text-xs text-slate-500 mt-1 block">
                        <i class="pi pi-paperclip !text-[10px]" /> {{ t("customers_page.document_on_file") }}
                    </small>
                    <small class="text-xs text-slate-500 mt-1 block">{{ t("customers_page.document_hint") }}</small>
                    <small v-if="form.errors?.file" class="text-rose-600 text-xs mt-1 block">{{ form.errors.file }}</small>
                </div>
            </div>
        </section>
    </div>
</template>

<script setup>
import { computed } from "vue";
import { useI18n } from "vue-i18n";
import DatePicker from "primevue/datepicker";
import FileUpload from "primevue/fileupload";
import InputText from "primevue/inputtext";
import RadioButton from "primevue/radiobutton";
import Select from "primevue/select";
import Textarea from "primevue/textarea";
import ToggleSwitch from "primevue/toggleswitch";
import { ymd } from "@/Helpers/format";

const { t } = useI18n();

const props = defineProps({
    form: { type: Object, required: true },
    currencies: { type: Array, default: () => [] },
    submitted: Boolean,
    photoPreview: String,
    resolveImagePath: Function,
    handlePhotoUpload: Function,
});

const customerTypes = computed(() => [
    { label: t("customers_page.types.retailer"), value: "retailer" },
    { label: t("customers_page.types.wholesaler"), value: "wholesaler" },
]);

// DatePicker works with Date objects; the API stores Y-m-d strings
const openingDate = computed({
    get: () => {
        const v = props.form.opening_balance_date;
        if (!v) return null;
        const d = v instanceof Date ? v : new Date(String(v).slice(0, 10) + "T00:00:00");
        return isNaN(d) ? null : d;
    },
    set: (d) => {
        props.form.opening_balance_date = d ? ymd(d) : "";
    },
});

// a file input can't be bound with v-model; keep the selected File on the form
function onDocument(event) {
    props.form.file = event.target.files?.[0] || "";
}
</script>
