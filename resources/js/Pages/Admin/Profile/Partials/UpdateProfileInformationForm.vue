<script setup>
import { Link, useForm, usePage } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import Button from 'primevue/button';
import InputText from 'primevue/inputtext';
import Message from 'primevue/message';

defineProps({
    mustVerifyEmail: { type: Boolean },
    status: { type: String },
});

const { t } = useI18n();
const user = usePage().props.auth.user;

const form = useForm({
    name: user.name,
    email: user.email,
});
</script>

<template>
    <section class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <header>
            <h2 class="font-semibold text-slate-800">{{ t('profile.info_title') }}</h2>
            <p class="mt-1 text-sm text-slate-500">{{ t('profile.info_desc') }}</p>
        </header>

        <form @submit.prevent="form.patch(route('profile.update'), { preserveScroll: true })" class="md:col-span-2 space-y-4">
            <div class="flex flex-col gap-1.5">
                <label for="name" class="text-sm font-medium text-slate-700">{{ t('profile.name') }}</label>
                <InputText id="name" v-model="form.name" required autocomplete="name" fluid :invalid="!!form.errors.name" />
                <small v-if="form.errors.name" class="text-red-600">{{ form.errors.name }}</small>
            </div>

            <div class="flex flex-col gap-1.5">
                <label for="email" class="text-sm font-medium text-slate-700">{{ t('profile.email') }}</label>
                <InputText id="email" type="email" v-model="form.email" required autocomplete="username" fluid :invalid="!!form.errors.email" />
                <small v-if="form.errors.email" class="text-red-600">{{ form.errors.email }}</small>
            </div>

            <Message v-if="mustVerifyEmail && user.email_verified_at === null" severity="warn" :closable="false" size="small">
                {{ t('profile.unverified') }}
                <Link :href="route('verification.send')" method="post" as="button" class="underline font-medium ms-1">
                    {{ t('profile.resend') }}
                </Link>
                <div v-if="status === 'verification-link-sent'" class="mt-1 font-medium">{{ t('profile.link_sent') }}</div>
            </Message>

            <div class="flex items-center gap-3">
                <Button type="submit" :label="t('profile.save')" icon="pi pi-check" :loading="form.processing" />
                <Transition enter-active-class="transition ease-in-out" enter-from-class="opacity-0"
                    leave-active-class="transition ease-in-out" leave-to-class="opacity-0">
                    <p v-if="form.recentlySuccessful" class="text-sm text-emerald-700 flex items-center gap-1">
                        <i class="pi pi-check-circle" />{{ t('profile.saved') }}
                    </p>
                </Transition>
            </div>
        </form>
    </section>
</template>
