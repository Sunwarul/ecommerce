<script setup>
import { useForm } from '@inertiajs/vue3';
import { nextTick, ref } from 'vue';
import { useI18n } from 'vue-i18n';
import Button from 'primevue/button';
import Dialog from 'primevue/dialog';
import Password from 'primevue/password';

const { t } = useI18n();
const confirmingUserDeletion = ref(false);

const form = useForm({
    password: '',
});

const focusPassword = () => nextTick(() => document.getElementById('delete_password')?.focus());

const confirmUserDeletion = () => {
    confirmingUserDeletion.value = true;
    focusPassword();
};

const deleteUser = () => {
    form.delete(route('profile.destroy'), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
        onError: () => focusPassword(),
        onFinish: () => form.reset(),
    });
};

const closeModal = () => {
    confirmingUserDeletion.value = false;
    form.clearErrors();
    form.reset();
};
</script>

<template>
    <section class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <header>
            <h2 class="font-semibold text-red-700">{{ t('profile.delete_title') }}</h2>
        </header>

        <div class="md:col-span-2 space-y-4">
            <p class="text-sm text-slate-600">{{ t('profile.delete_desc') }}</p>
            <Button :label="t('profile.delete_button')" icon="pi pi-trash" severity="danger" outlined @click="confirmUserDeletion" />
        </div>

        <Dialog v-model:visible="confirmingUserDeletion" modal :header="t('profile.delete_confirm_title')" :style="{ width: '28rem' }"
            :breakpoints="{ '640px': '92vw' }" @hide="closeModal">
            <p class="text-sm text-slate-600 mb-4">{{ t('profile.delete_confirm_desc') }}</p>
            <Password inputId="delete_password" v-model="form.password" :feedback="false" toggleMask fluid
                :invalid="!!form.errors.password" @keyup.enter="deleteUser" />
            <small v-if="form.errors.password" class="text-red-600 block mt-1">{{ form.errors.password }}</small>

            <template #footer>
                <Button :label="t('profile.cancel')" text severity="secondary" @click="closeModal" />
                <Button :label="t('profile.delete_button')" icon="pi pi-trash" severity="danger" :loading="form.processing" @click="deleteUser" />
            </template>
        </Dialog>
    </section>
</template>
