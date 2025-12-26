<script setup>
import { router, usePage } from "@inertiajs/vue3";
import { computed } from "vue";

const page = usePage();

// branches passed from Laravel (shared)
const branches = computed(() => page.props.branches || []);
const currentBranchId = computed(() => page.props.auth.user.branch_id);

const changeBranch = (e) => {
    router.post(
        route("branch.switch"),
        { branch_id: e.target.value },
        {
            preserveScroll: true,
            preserveState: true,
        }
    );
};
</script>

<template>
    <div class="flex items-center gap-2">
        <label class="text-sm text-slate-600 font-medium hidden md:block">
            Branch
        </label>

        <select
            class="px-3 py-2 text-sm rounded-lg border border-slate-300 bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 hover:border-indigo-400 transition"
            :value="currentBranchId"
            @change="changeBranch"
        >
            <option disabled value="">Select Branch</option>

            <option
                v-for="branch in branches"
                :key="branch.id"
                :value="branch.id"
            >
                {{ branch.name }}
            </option>
        </select>
    </div>
</template>
