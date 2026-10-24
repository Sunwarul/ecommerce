<script setup>
import { useForm } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import Button from 'primevue/button';
import Password from 'primevue/password';

const { t } = useI18n();

const form = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

const focus = (id) => document.getElementById(id)?.focus();

const updatePassword = () => {
    form.put(route('password.update'), {
        preserveScroll: true,
        onSuccess: () => form.reset(),
        onError: () => {
            if (form.errors.password) {
                form.reset('password', 'password_confirmation');
                focus('password');
            }
            if (form.errors.current_password) {
                form.reset('current_password');
                focus('current_password');
            }
        },
    });
};
</script>

<template>
    <section class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <header>
            <h2 class="font-semibold text-slate-800">{{ t('profile.password_title') }}</h2>
            <p class="mt-1 text-sm text-slate-500">{{ t('profile.password_desc') }}</p>
        </header>

        <form @submit.prevent="updatePassword" class="md:col-span-2 space-y-4">
            <div class="flex flex-col gap-1.5">
                <label for="current_password" class="text-sm font-medium text-slate-700">{{ t('profile.current_password') }}</label>
                <Password inputId="current_password" v-model="form.current_password" :feedback="false" toggleMask
                    autocomplete="current-password" fluid :invalid="!!form.errors.current_password" />
                <small v-if="form.errors.current_password" class="text-red-600">{{ form.errors.current_password }}</small>
            </div>

            <div class="flex flex-col gap-1.5">
                <label for="password" class="text-sm font-medium text-slate-700">{{ t('profile.new_password') }}</label>
                <Password inputId="password" v-model="form.password" :feedback="false" toggleMask autocomplete="new-password"
                    fluid :invalid="!!form.errors.password" />
                <small v-if="form.errors.password" class="text-red-600">{{ form.errors.password }}</small>
            </div>

            <div class="flex flex-col gap-1.5">
                <label for="password_confirmation" class="text-sm font-medium text-slate-700">{{ t('profile.confirm_password') }}</label>
                <Password inputId="password_confirmation" v-model="form.password_confirmation" :feedback="false" toggleMask
                    autocomplete="new-password" fluid :invalid="!!form.errors.password_confirmation" />
                <small v-if="form.errors.password_confirmation" class="text-red-600">{{ form.errors.password_confirmation }}</small>
            </div>

            <div class="flex items-center gap-3">
                <Button type="submit" :label="t('profile.save')" icon="pi pi-check" :loading="form.processing" />
                <Transition enter-active-class="transition ease-in-out" enter-from-class="opacity-0"
                    leave-active-class="transition ease-in-out" leave-to-class="opacity-0">
                    <p v-if="form.recentlySuccessful" class="text-sm text-emerald-700 flex items-center gap-1">
                        <i class="pi pi-check-circle" />{{ t('profile.password_updated') }}
                    </p>
                </Transition>
            </div>
        </form>
    </section>
</template>
