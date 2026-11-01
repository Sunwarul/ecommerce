<script setup>
import { useForm, usePage } from "@inertiajs/vue3";
import Button from "primevue/button";
import InputText from "primevue/inputtext";
import Message from "primevue/message";
import Textarea from "primevue/textarea";
import { ref } from "vue";
import { useI18n } from "vue-i18n";

const props = defineProps({ token: { type: String, required: true } });
const { t } = useI18n();
const page = usePage();
const sent = ref(false);

// "website" is a honeypot: hidden from people, filled in by bots. form_token proves the page was open a few seconds.
const form = useForm({ name: "", phone: "", email: "", subject: "", message: "", website: "", form_token: props.token });

function submit() {
    form.post(route("website.contact.send"), {
        preserveScroll: true,
        onSuccess: () => {
            if (page.props.flash?.success) {
                sent.value = true;
                form.reset("name", "phone", "email", "subject", "message");
            }
        },
    });
}
const label = "text-sm font-medium text-slate-700";
</script>

<template>
    <div v-if="sent" class="flex flex-col items-center text-center gap-3 py-10">
        <span class="w-14 h-14 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center"><i class="pi pi-check text-2xl" /></span>
        <h3 class="text-lg font-semibold text-slate-900">{{ t('website.contact.sent_title') }}</h3>
        <p class="text-slate-600 max-w-sm">{{ page.props.flash?.success || t('website.contact.sent_body') }}</p>
        <Button :label="t('website.contact.send_another')" severity="secondary" outlined size="small" @click="sent = false" />
    </div>
    <form v-else class="grid grid-cols-1 sm:grid-cols-2 gap-4" novalidate @submit.prevent="submit">
        <div class="flex flex-col gap-1.5 sm:col-span-2">
            <label for="cf_name" :class="label">{{ t('website.contact.name') }} <span class="text-red-500">*</span></label>
            <InputText id="cf_name" v-model="form.name" autocomplete="name" maxlength="120" fluid :invalid="!!form.errors.name" />
            <small v-if="form.errors.name" class="text-red-600">{{ form.errors.name }}</small>
        </div>
        <div class="flex flex-col gap-1.5">
            <label for="cf_phone" :class="label">{{ t('website.contact.phone') }}</label>
            <InputText id="cf_phone" v-model="form.phone" type="tel" autocomplete="tel" maxlength="40" placeholder="01XXXXXXXXX" fluid :invalid="!!form.errors.phone" />
            <small v-if="form.errors.phone" class="text-red-600">{{ form.errors.phone }}</small>
        </div>
        <div class="flex flex-col gap-1.5">
            <label for="cf_email" :class="label">{{ t('website.contact.email') }}</label>
            <InputText id="cf_email" v-model="form.email" type="email" autocomplete="email" maxlength="191" fluid :invalid="!!form.errors.email" />
            <small v-if="form.errors.email" class="text-red-600">{{ form.errors.email }}</small>
        </div>
        <small class="sm:col-span-2 -mt-2 text-slate-500">{{ t('website.contact.phone_or_email') }}</small>
        <div class="flex flex-col gap-1.5 sm:col-span-2">
            <label for="cf_subject" :class="label">{{ t('website.contact.subject') }}</label>
            <InputText id="cf_subject" v-model="form.subject" maxlength="191" fluid />
        </div>
        <div class="flex flex-col gap-1.5 sm:col-span-2">
            <label for="cf_message" :class="label">{{ t('website.contact.message') }} <span class="text-red-500">*</span></label>
            <Textarea id="cf_message" v-model="form.message" rows="5" autoResize maxlength="5000" fluid :invalid="!!form.errors.message" />
            <small v-if="form.errors.message" class="text-red-600">{{ form.errors.message }}</small>
        </div>
        <!-- honeypot -->
        <div class="absolute -left-[9999px] w-px h-px overflow-hidden" aria-hidden="true">
            <label for="cf_website">Website</label>
            <input id="cf_website" v-model="form.website" type="text" tabindex="-1" autocomplete="off" />
        </div>
        <Message v-if="form.errors.form_token" severity="error" size="small" class="sm:col-span-2">{{ form.errors.form_token }}</Message>
        <div class="sm:col-span-2">
            <Button type="submit" :label="t('website.contact.send')" icon="pi pi-send" :loading="form.processing" class="w-full sm:w-auto" />
        </div>
    </form>
</template>
