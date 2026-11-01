<template>
    <div class="space-y-4">
        <div class="flex flex-col gap-1.5">
            <label for="name" class="text-sm font-medium text-slate-700">{{ t('admin.roles.name') }} <span class="text-red-500">*</span></label>
            <InputText id="name" v-model.trim="form.name" :placeholder="t('admin.roles.name_placeholder')" fluid autofocus
                :invalid="!!form.errors.name" />
            <small v-if="form.errors.name" class="text-red-600">{{ form.errors.name }}</small>
        </div>

        <Message v-if="form.name === 'Super Admin'" severity="warn" :closable="false" size="small" icon="pi pi-star">
            {{ t('admin.roles.super_admin_note') }}
        </Message>

        <div class="flex flex-wrap items-center justify-between gap-2">
            <div>
                <div class="text-sm font-medium text-slate-700">{{ t('admin.roles.permissions') }} <span class="text-red-500">*</span></div>
                <div class="text-xs text-slate-500">{{ t('admin.roles.selected_count', { selected: selectedIds.length, total: permissions.length }) }}</div>
            </div>
            <div class="flex flex-wrap items-center gap-2 w-full sm:w-auto">
                <IconField class="flex-1 sm:w-60">
                    <InputIcon><i class="pi pi-search" /></InputIcon>
                    <InputText v-model="filter" type="search" :placeholder="t('admin.roles.search_permissions')" size="small" fluid />
                </IconField>
                <Button :label="t('admin.roles.select_all')" size="small" text @click="setMany(visibleIds, true)" />
                <Button :label="t('admin.roles.clear_all')" size="small" text severity="secondary" @click="setMany(visibleIds, false)" />
            </div>
        </div>
        <small v-if="form.errors.ids" class="text-red-600 block">{{ form.errors.ids }}</small>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 max-h-[55vh] overflow-y-auto pe-1">
            <div v-for="group in groups" :key="group.name" class="rounded-xl border border-slate-200 p-3">
                <label class="flex items-center gap-2 pb-2 mb-2 border-b border-slate-100 cursor-pointer">
                    <Checkbox :modelValue="groupState(group) === 'all'" :indeterminate="groupState(group) === 'some'" binary
                        @update:modelValue="(v) => setMany(group.items.map((p) => p.id), v)" />
                    <span class="text-sm font-semibold text-slate-800 flex-1">{{ group.name }}</span>
                    <span class="text-xs text-slate-500 tabular-nums">{{ group.items.filter((p) => selected.has(p.id)).length }}/{{ group.items.length }}</span>
                </label>
                <div class="flex flex-wrap gap-x-4 gap-y-2">
                    <label v-for="p in group.items" :key="p.id" class="flex items-center gap-2 text-sm text-slate-700 cursor-pointer">
                        <Checkbox :modelValue="selected.has(p.id)" binary @update:modelValue="(v) => setMany([p.id], v)" />
                        {{ p.action }}
                    </label>
                </div>
            </div>
            <p v-if="!groups.length" class="text-sm text-slate-500 md:col-span-2 py-6 text-center">{{ t('admin.roles.no_match') }}</p>
        </div>
    </div>
</template>

<script setup>
import { computed, ref } from 'vue';
import { useI18n } from 'vue-i18n';
import Button from 'primevue/button';
import Checkbox from 'primevue/checkbox';
import IconField from 'primevue/iconfield';
import InputIcon from 'primevue/inputicon';
import InputText from 'primevue/inputtext';
import Message from 'primevue/message';

const props = defineProps({
    form: { type: Object, required: true },
    submitted: Boolean,
    permissions: { type: Array, default: () => [] },
});

const { t } = useI18n();
const filter = ref('');

// "Payment Methods Bulk Delete" -> module "Payment Methods", action "Bulk Delete"
const ACTIONS = ['Bulk Delete', 'Bulk Restore', 'Plan Manage', 'Index', 'Store', 'Update', 'Edit', 'Delete', 'Search', 'Export',
    'Movement', 'View', 'Invoice', 'Complete', 'Void', 'Create', 'Collect', 'Cancel', 'Manage', 'Send', 'Settings'];

function split(name) {
    for (const a of ACTIONS) {
        if (name.endsWith(' ' + a)) return { module: name.slice(0, -a.length - 1), action: a };
    }
    return { module: name, action: name };
}

// ids may arrive as permission objects (older payloads) or plain ids
const selectedIds = computed(() => (props.form.ids || []).map((i) => (i && typeof i === 'object' ? i.id : i)));
const selected = computed(() => new Set(selectedIds.value));

const groups = computed(() => {
    const q = filter.value.trim().toLowerCase();
    const map = new Map();
    for (const p of props.permissions) {
        if (q && !p.name.toLowerCase().includes(q)) continue;
        const { module, action } = split(p.name);
        if (!map.has(module)) map.set(module, { name: module, items: [] });
        map.get(module).items.push({ id: p.id, action });
    }
    return [...map.values()];
});

const visibleIds = computed(() => groups.value.flatMap((g) => g.items.map((p) => p.id)));

function groupState(group) {
    const n = group.items.filter((p) => selected.value.has(p.id)).length;
    return n === 0 ? 'none' : n === group.items.length ? 'all' : 'some';
}

function setMany(ids, on) {
    const next = new Set(selectedIds.value);
    ids.forEach((id) => (on ? next.add(id) : next.delete(id)));
    props.form.ids = [...next];
}
</script>
