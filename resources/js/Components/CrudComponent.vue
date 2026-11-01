<!--
    Generic list + create/edit dialog for HasCrud controllers.

    Contract (used by ~23 pages, keep stable):
      props : form (useForm), formWidth, canEdit, canDelete, title?, subtitle?, icon?
      emits : editingItem(row)
      slots : #columns, #form="{ submitted, statuses, handlePhotoUpload, photoPreview, resolveImagePath, isEdit }",
              #messages, #toolbar (extra buttons), #row-actions="{ data }" (extra buttons per row)
-->
<template>
    <AuthenticatedLayout>
        <Head :title="pageTitle" />

        <div class="space-y-4">
            <!-- Page header -->
            <div class="flex flex-wrap items-start justify-between gap-3">
                <div class="flex items-start gap-3 min-w-0">
                    <span class="hidden sm:flex w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 items-center justify-center shrink-0">
                        <i :class="icon || 'pi pi-list'" class="text-lg" />
                    </span>
                    <div class="min-w-0">
                        <h1 class="text-xl font-semibold text-slate-800 truncate">{{ pageTitle }}</h1>
                        <p class="text-sm text-slate-500">
                            <template v-if="pageSubtitle">{{ pageSubtitle }} · </template>
                            {{ t('crud.total_records', { count: totalRecords }) }}
                        </p>
                    </div>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <slot name="toolbar"></slot>
                    <Button v-if="vueProps?.config?.exportRoute" :label="t('crud.export')" icon="pi pi-download"
                        severity="secondary" outlined @click="exportExcel" />
                    <Button v-if="vueProps.config.storeRoute && !isTrashedPage" :label="t('crud.new')" icon="pi pi-plus"
                        @click="openNew" />
                </div>
            </div>

            <div class="card !p-0 overflow-hidden">
                <!-- Toolbar: active/trash switch, bulk actions, search -->
                <div class="flex flex-wrap items-center justify-between gap-3 p-3 sm:p-4 border-b border-slate-100">
                    <div class="flex flex-wrap items-center gap-2">
                        <div v-if="vueProps.config.bulkRestoreRoute" class="inline-flex rounded-lg border border-slate-200 p-0.5">
                            <Link :href="vueProps.config.indexRoute" preserve-scroll>
                                <Button :label="t('crud.active_records')" icon="pi pi-list" size="small"
                                    :text="isTrashedPage" :severity="isTrashedPage ? 'secondary' : 'primary'" />
                            </Link>
                            <Link :href="vueProps.config.indexRouteTrashed" preserve-scroll>
                                <Button :label="t('crud.trash')" icon="pi pi-trash" size="small"
                                    :text="!isTrashedPage" :severity="isTrashedPage ? 'danger' : 'secondary'" />
                            </Link>
                        </div>

                        <template v-if="selectedCount && vueProps.config.bulkRestoreRoute">
                            <Button v-if="!isTrashedPage" :label="t('crud.bulk_delete', { count: selectedCount })"
                                icon="pi pi-trash" severity="danger" outlined size="small" @click="confirmDeleteSelected" />
                            <template v-else>
                                <Button :label="t('crud.bulk_restore', { count: selectedCount })" icon="pi pi-undo"
                                    severity="warn" outlined size="small" @click="restoreSelected" />
                                <Button :label="t('crud.force_delete', { count: selectedCount })" icon="pi pi-times"
                                    severity="danger" outlined size="small" @click="forceDeleteSelected" />
                            </template>
                        </template>

                        <slot name="messages"></slot>
                    </div>

                    <IconField class="w-full sm:w-72">
                        <InputIcon><i :class="loading ? 'pi pi-spin pi-spinner' : 'pi pi-search'" /></InputIcon>
                        <InputText v-model="filters.global.value" type="search" class="w-full"
                            :placeholder="t('crud.search_placeholder')" :aria-label="t('crud.search_placeholder')"
                            @input="debouncedSearch" />
                    </IconField>
                </div>

                <Message v-if="isTrashedPage" severity="warn" :closable="false" class="!m-3 !mb-0" size="small" icon="pi pi-info-circle">
                    {{ t('crud.trash_mode') }}
                </Message>

                <DataTable ref="dt" v-model:selection="selectedItems" :value="vueProps.items.data" dataKey="id"
                    :paginator="true" :rows="perPage" :first="firstRow" :filters="filters"
                    :totalRecords="totalRecords" :lazy="true" :loading="loading"
                    @page="onPage"
                    :paginatorTemplate="paginatorTemplate"
                    :rowsPerPageOptions="[10, 15, 25, 50, 100]"
                    :currentPageReportTemplate="t('crud.page_report', { first: '{first}', last: '{last}', total: '{totalRecords}' })"
                    resizableColumns columnResizeMode="fit" scrollable stripedRows class="crud-table">
                    <template #empty>
                        <div class="py-10 text-center">
                            <i :class="isTrashedPage ? 'pi pi-trash' : 'pi pi-inbox'" class="text-4xl text-slate-300" />
                            <p class="mt-3 font-medium text-slate-700">
                                {{ isTrashedPage ? t('crud.empty_trash') : t('crud.empty_title') }}
                            </p>
                            <p v-if="!isTrashedPage" class="text-sm text-slate-500 mt-1">{{ t('crud.empty_hint') }}</p>
                            <Button v-if="!isTrashedPage && vueProps.config.storeRoute && !filters.global.value"
                                :label="t('crud.new')" icon="pi pi-plus" class="mt-4" size="small" @click="openNew" />
                        </div>
                    </template>

                    <Column selectionMode="multiple" style="width: 3rem" :exportable="false" header=""
                        v-if="vueProps.config.bulkRestoreRoute"></Column>

                    <slot name="columns"></slot>

                    <Column v-if="showActions" :header="t('crud.actions')" :exportable="false"
                        bodyClass="whitespace-nowrap" headerClass="whitespace-nowrap" style="width: 1%">
                        <template #body="slotProps">
                            <div class="flex items-center gap-1">
                                <slot name="row-actions" :data="slotProps.data"></slot>
                                <Button v-if="!hideEditAction && !isTrashedPage" icon="pi pi-pencil" text rounded
                                    severity="secondary" v-tooltip.top="t('crud.edit')" :aria-label="t('crud.edit')"
                                    @click="editItem(slotProps.data)" />
                                <Button v-if="!hideDeleteAction && !isTrashedPage" icon="pi pi-trash" text rounded
                                    severity="danger" v-tooltip.top="t('crud.delete')" :aria-label="t('crud.delete')"
                                    @click="confirmDeleteItem(slotProps.data)" />
                            </div>
                        </template>
                    </Column>
                </DataTable>
            </div>
        </div>

        <!-- Create & Edit Form Dialog -->
        <Dialog v-model:visible="itemDialog" modal maximizable :style="{ width: formWidth ?? '42rem' }"
            :breakpoints="{ '960px': '80vw', '640px': '96vw' }" :header="dialogTitle" :draggable="false"
            pt:mask:class="backdrop-blur-sm" @hide="onDialogHide">

            <Message v-if="form.hasErrors" severity="error" :closable="false" class="mb-4" size="small" icon="pi pi-exclamation-circle">
                {{ t('crud.validation_error') }}
            </Message>

            <slot name="form" v-bind="{ submitted, statuses, handlePhotoUpload, photoPreview, resolveImagePath, isEdit }">
            </slot>

            <template #footer>
                <div class="flex flex-wrap justify-end gap-2 w-full">
                    <Button :label="t('crud.cancel')" icon="pi pi-times" text severity="secondary" @click="hideDialog" />
                    <template v-if="!isEdit">
                        <Button :label="t('crud.save_continue')" icon="pi pi-plus" outlined :loading="form.processing"
                            @click="saveItem(true)" />
                        <Button :label="t('crud.save')" icon="pi pi-check" :loading="form.processing" @click="saveItem(false)" />
                    </template>
                    <Button v-else :label="t('crud.update')" icon="pi pi-check" :loading="updating" @click="updateItem" />
                </div>
            </template>
        </Dialog>

        <ConfirmDialog group="crud" :breakpoints="{ '640px': '92vw' }" :style="{ width: '28rem' }" />
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { FilterMatchMode } from '@primevue/core/api';
import { useToast } from 'primevue/usetoast';
import { useConfirm } from 'primevue/useconfirm';
import { useI18n } from 'vue-i18n';
import { router, Link, Head, usePage } from '@inertiajs/vue3';
import Button from 'primevue/button';
import Column from 'primevue/column';
import ConfirmDialog from 'primevue/confirmdialog';
import DataTable from 'primevue/datatable';
import Dialog from 'primevue/dialog';
import IconField from 'primevue/iconfield';
import InputIcon from 'primevue/inputicon';
import InputText from 'primevue/inputtext';
import Message from 'primevue/message';
import Tooltip from 'primevue/tooltip';
import { resolveImagePath } from '@/Helpers/imageHelper';
import { cleanParams, firstError } from '@/Helpers/format';
import debounce from 'lodash/debounce';
import { statuses as baseStatuses } from '@/Helpers/enums.js';

const vTooltip = Tooltip;

const { form, formWidth, canEdit, canDelete, title, subtitle, icon } = defineProps(['form', 'formWidth', 'canEdit', 'canDelete', 'title', 'subtitle', 'icon']);

const hideDeleteAction = computed(() => canDelete === false);
const hideEditAction = computed(() => canEdit === false);
const showActions = computed(() => !hideEditAction.value || !hideDeleteAction.value);

const { t, te } = useI18n();
const page = usePage();
const vueProps = computed(() => page.props);
const resource = computed(() => vueProps.value.config?.resource);

const pageTitle = computed(() => {
    if (title) return title;
    const key = `crud.resources.${resource.value}`;
    return te(key) ? t(key) : vueProps.value.config?.title;
});
const pageSubtitle = computed(() => {
    if (subtitle) return subtitle;
    const key = `crud.subtitles.${resource.value}`;
    return te(key) ? t(key) : '';
});
const singularName = computed(() => {
    const key = `crud.singular.${resource.value}`;
    return te(key) ? t(key) : (vueProps.value.config?.modelRaw || pageTitle.value);
});

// translated copy of the Active/Inactive options handed to form slots
const statuses = computed(() => baseStatuses.map((s) => ({ ...s, label: s.value ? t('crud.fields.active') : t('crud.fields.inactive') })));

const toast = useToast();
const confirm = useConfirm();
const dt = ref();
const itemDialog = ref(false);
const submitted = ref(false);
const loading = ref(false);
const updating = ref(false);
const selectedItems = ref();
const selectedCount = computed(() => selectedItems.value?.length || 0);

const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS },
});

// ---------- paging / trash state (derived from the URL so it survives partial reloads) ----------
const query = computed(() => new URLSearchParams((page.url.split('?')[1]) || ''));
const isTrashedPage = computed(() => ['1', 'true'].includes(query.value.get('trashed')));
const totalRecords = computed(() => vueProps.value.items?.total ?? 0);
const perPage = computed(() => Number(vueProps.value.items?.per_page) || 15);
const firstRow = computed(() => ((Number(vueProps.value.items?.current_page) || 1) - 1) * perPage.value);

const isNarrow = ref(typeof window !== 'undefined' && window.innerWidth < 640);
const onResize = () => (isNarrow.value = window.innerWidth < 640);
onMounted(() => window.addEventListener('resize', onResize));
onUnmounted(() => window.removeEventListener('resize', onResize));
const paginatorTemplate = computed(() => isNarrow.value
    ? 'PrevPageLink CurrentPageReport NextPageLink'
    : 'FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown');

function listParams(extra = {}) {
    return cleanParams({
        search: filters.value.global.value || undefined,
        trashed: isTrashedPage.value ? 1 : undefined,
        per_page: perPage.value !== 15 ? perPage.value : undefined,
        ...extra,
    });
}

function reload(params) {
    router.get(vueProps.value.config.indexRoute, params, {
        preserveState: true,
        preserveScroll: true,
        only: ['items', 'filters'],
        onStart: () => (loading.value = true),
        onFinish: () => (loading.value = false),
    });
}

const onPage = (event) => {
    reload(listParams({ page: event.page + 1, per_page: event.rows }));
};

const debouncedSearch = debounce((e) => {
    filters.value.global.value = e.target.value;
    selectedItems.value = null;
    reload(listParams({ search: e.target.value || undefined }));
}, 350);

// ---------- form helpers ----------
const photoPreview = ref(null);
const handlePhotoUpload = (event) => {
    const file = event.files[0];
    form.photo = file;
    const reader = new FileReader();
    reader.onload = async (e) => {
        photoPreview.value = e.target.result;
    };
    reader.readAsDataURL(file);
};

// useForm internals that must never be posted
const FORM_INTERNALS = ['errors', 'hasErrors', 'processing', 'progress', 'wasSuccessful', 'recentlySuccessful', 'isDirty', '__rememberable', '__remember'];

/** All data fields on the form (incl. ones assigned while editing), with relation pickers reduced to ids. */
function payload(source) {
    const out = {};
    for (const [k, v] of Object.entries(source)) {
        if (typeof v === 'function' || FORM_INTERNALS.includes(k)) continue;
        out[k] = v;
    }
    if (Array.isArray(out.ids)) {
        out.ids = out.ids.map((i) => (i && typeof i === 'object' ? i.id : i));
    }
    return out;
}

const isEdit = ref(false);
const editingId = ref(null);

const openNew = () => {
    isEdit.value = false;
    form.reset();
    form.clearErrors();
    submitted.value = false;
    itemDialog.value = true;
    photoPreview.value = null;
};

const hideDialog = () => {
    itemDialog.value = false;
    submitted.value = false;
};

const onDialogHide = () => {
    submitted.value = false;
    form.clearErrors();
    if (isEdit.value) form.reset();
};

const saveItem = (saveAndContinue = false) => {
    submitted.value = true;
    form.transform((data) => payload(data)).post(vueProps.value.config.storeRoute, {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            form.clearErrors();
            photoPreview.value = null;
            toast.add({ severity: 'success', summary: t('crud.success'), detail: t('crud.created'), life: 3000 });
            if (saveAndContinue) {
                submitted.value = false;
                return;
            }
            hideDialog();
        },
        onError: (errors) => {
            toast.add({ severity: 'error', summary: t('crud.failed'), detail: firstError(errors) || t('crud.validation_error'), life: 4000 });
        },
    });
};

const updateItem = () => {
    submitted.value = true;
    const url = vueProps.value.config.updateRoute.replace('__ID__', editingId.value);

    router.post(url, { _method: 'put', ...payload(form) }, {
        preserveScroll: true,
        onStart: () => (updating.value = true),
        onFinish: () => (updating.value = false),
        onSuccess: () => {
            hideDialog();
            photoPreview.value = null;
            form.reset();
            form.clearErrors();
            toast.add({ severity: 'success', summary: t('crud.success'), detail: t('crud.updated'), life: 3000 });
        },
        onError: (errors) => {
            form.clearErrors();
            form.setError(errors);
            toast.add({ severity: 'error', summary: t('crud.failed'), detail: firstError(errors) || t('crud.validation_error'), life: 4000 });
        },
    });
};

const emit = defineEmits(['editingItem']);

const editItem = (prod) => {
    form.clearErrors();
    emit('editingItem', prod);

    itemDialog.value = true;
    isEdit.value = true;
    submitted.value = false;
    photoPreview.value = null;

    Object.assign(form, prod);

    editingId.value = prod.id;
};

function onFail(errors) {
    toast.add({ severity: 'error', summary: t('crud.failed'), detail: firstError(errors) || t('crud.error'), life: 4000 });
}

const confirmDeleteItem = (prod) => {
    confirm.require({
        group: 'crud',
        header: t('crud.confirm_title'),
        message: vueProps.value.config.bulkRestoreRoute ? t('crud.confirm_delete') : t('crud.confirm_delete_hard'),
        icon: 'pi pi-exclamation-triangle',
        rejectProps: { label: t('crud.cancel'), severity: 'secondary', text: true },
        acceptProps: { label: t('crud.delete'), severity: 'danger', icon: 'pi pi-trash' },
        accept: () => {
            const url = vueProps.value.config.deleteRoute.replace('__ID__', prod.id);
            router.delete(url, {
                preserveScroll: true,
                onSuccess: () => toast.add({ severity: 'success', summary: t('crud.success'), detail: t('crud.deleted'), life: 3000 }),
                onError: onFail,
            });
        },
    });
};

function bulk(routeUrl, { message, acceptLabel, severity, done, back = false }) {
    const itemIds = selectedItems.value.map((c) => c.id);
    confirm.require({
        group: 'crud',
        header: t('crud.confirm_title'),
        message: message(itemIds.length),
        icon: 'pi pi-exclamation-triangle',
        rejectProps: { label: t('crud.cancel'), severity: 'secondary', text: true },
        acceptProps: { label: acceptLabel, severity },
        accept: () => {
            router.post(routeUrl, { ids: itemIds }, {
                preserveScroll: true,
                onSuccess: () => {
                    selectedItems.value = null;
                    toast.add({ severity: 'success', summary: t('crud.success'), detail: done(itemIds.length), life: 3000 });
                    if (back) router.get(vueProps.value.config.indexRoute);
                },
                onError: onFail,
            });
        },
    });
}

const confirmDeleteSelected = () => bulk(vueProps.value.config.bulkDeleteRoute, {
    message: (count) => t('crud.confirm_delete_many', { count }),
    acceptLabel: t('crud.delete'),
    severity: 'danger',
    done: (count) => t('crud.deleted_many', { count }),
});

const restoreSelected = () => bulk(vueProps.value.config.bulkRestoreRoute, {
    message: (count) => t('crud.confirm_restore', { count }),
    acceptLabel: t('crud.bulk_restore', { count: selectedCount.value }),
    severity: 'warn',
    done: (count) => t('crud.restored', { count }),
    back: true,
});

const forceDeleteSelected = () => bulk(vueProps.value.config.bulkForceDeleteRoute, {
    message: (count) => t('crud.confirm_force', { count }),
    acceptLabel: t('crud.delete'),
    severity: 'danger',
    done: (count) => t('crud.force_deleted', { count }),
});

const exportExcel = () => {
    const params = new URLSearchParams(cleanParams({
        search: filters.value.global.value || undefined,
        trashed: isTrashedPage.value ? 1 : undefined,
    })).toString();

    // Create a temporary link to trigger the download
    const link = document.createElement('a');
    link.href = params ? `${vueProps.value.config.exportRoute}?${params}` : vueProps.value.config.exportRoute;
    link.setAttribute('download', '');
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);

    toast.add({ severity: 'info', summary: t('crud.export'), detail: t('crud.export_started'), life: 3000 });
};

const dialogTitle = computed(() => (isEdit.value
    ? t('crud.edit_title', { name: singularName.value })
    : t('crud.create_title', { name: singularName.value })));

onMounted(() => {
    if (vueProps.value.filters?.search) {
        filters.value.global.value = vueProps.value.filters.search;
    }
});
</script>

<style scoped>
.crud-table :deep(.p-datatable-header) {
    display: none;
}

.crud-table :deep(.p-paginator) {
    border-top: 1px solid var(--p-content-border-color);
}
</style>
