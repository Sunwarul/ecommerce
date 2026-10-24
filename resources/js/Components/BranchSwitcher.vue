<script setup>
import { router, usePage } from "@inertiajs/vue3";
import Select from "primevue/select";
import { computed } from "vue";
import { useI18n } from "vue-i18n";

const { t } = useI18n();
const page = usePage();

const branches = computed(() => page.props.branches || []);
const currentBranchId = computed(() => page.props.auth?.user?.branch_id ?? null);

function changeBranch(id) {
    if (!id || id === currentBranchId.value) return;
    router.post(route("branch.switch"), { branch_id: id }, { preserveScroll: true });
}
</script>

<template>
    <div v-if="branches.length" class="flex items-center gap-2">
        <i class="pi pi-building text-slate-500" :title="t('layout.branch')" />
        <Select :modelValue="currentBranchId" @update:modelValue="changeBranch" :options="branches" optionLabel="name"
            optionValue="id" :placeholder="t('layout.select_branch')" size="small" class="min-w-[9rem] max-w-[14rem]"
            :aria-label="t('layout.branch')" />
    </div>
</template>
