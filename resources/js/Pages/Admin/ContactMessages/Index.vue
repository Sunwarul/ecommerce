<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import useAuth from "@/Composables/useAuth";
import { useFlashToast } from "@/Composables/useFlashToast";
import { cleanParams, dateTime, num } from "@/Helpers/format";
import { Head, router } from "@inertiajs/vue3";
import Button from "primevue/button";
import Column from "primevue/column";
import DataTable from "primevue/datatable";
import Dialog from "primevue/dialog";
import IconField from "primevue/iconfield";
import InputIcon from "primevue/inputicon";
import InputText from "primevue/inputtext";
import Paginator from "primevue/paginator";
import Tag from "primevue/tag";
import Textarea from "primevue/textarea";
import ToggleSwitch from "primevue/toggleswitch";
import { useConfirm } from "primevue/useconfirm";
import { computed, ref } from "vue";
import { useI18n } from "vue-i18n";
import debounce from "lodash/debounce";

useFlashToast({ error: false });
const props = defineProps({ messages: Object, filters: Object, counts: Object });
const { t } = useI18n();
const confirm = useConfirm();
const { hasAnyPermission, hasRole } = useAuth();
const canDelete = computed(() => hasRole("Super Admin") || hasAnyPermission(["Contact Message Delete"]));

const search = ref(props.filters?.search || "");
const filter = computed(() => props.filters?.filter || "");
const isTrash = computed(() => filter.value === "trashed");
const selected = ref([]);

const tabs = computed(() => [
    { value: "", label: t("common.all"), count: props.counts.all, icon: "pi pi-inbox" },
    { value: "unread", label: t("website.inbox.unread"), count: props.counts.unread, icon: "pi pi-envelope" },
    { value: "replied", label: t("website.inbox.replied"), count: props.counts.replied, icon: "pi pi-reply" },
    ...(canDelete.value ? [{ value: "trashed", label: t("website.admin.trash"), count: props.counts.trashed, icon: "pi pi-trash" }] : []),
]);

function load(params = {}) {
    selected.value = [];
    router.get(route("contact-messages.index"), cleanParams({ search: search.value, filter: filter.value, ...params }), { preserveState: true, preserveScroll: true, replace: true });
}
const onSearch = debounce(() => load({ page: undefined }), 350);

// ---- reading a message ----
const open = ref(null);
const note = ref("");
const replied = ref(false);
function view(msg) {
    open.value = msg;
    note.value = msg.admin_note || "";
    replied.value = !!msg.replied_at;
    if (!msg.read_at && !msg.deleted_at) {
        router.post(route("contact-messages.read", msg.id), {}, { preserveScroll: true, preserveState: true, only: ["messages", "counts", "unread_contact_messages"] });
    }
}
function saveNote() {
    router.put(route("contact-messages.update", open.value.id), { admin_note: note.value, replied: replied.value }, {
        preserveScroll: true, preserveState: true, onSuccess: () => (open.value = null),
    });
}
function markUnread() {
    router.put(route("contact-messages.update", open.value.id), { unread: true }, { preserveScroll: true, preserveState: true, onSuccess: () => (open.value = null) });
}
const replyHref = (m) => `mailto:${m.email}?subject=${encodeURIComponent(`Re: ${m.subject || t("website.inbox.your_message")}`)}`;
const whatsappHref = (m) => `https://wa.me/${String(m.phone).replace(/[^\d]/g, "").replace(/^0/, "880")}`;

function bulk(action, ids = selected.value.map((m) => m.id)) {
    if (!ids.length) return;
    const run = () => router.post(route("contact-messages.bulk"), { ids, action }, { preserveScroll: true, onSuccess: () => { selected.value = []; open.value = null; } });
    if (action === "delete" || action === "force-delete") {
        confirm.require({
            message: action === "delete" ? t("website.inbox.confirm_delete", { n: ids.length }) : t("website.inbox.confirm_force_delete", { n: ids.length }),
            header: t("common.are_you_sure"), icon: "pi pi-exclamation-triangle",
            acceptProps: { label: t("common.delete"), severity: "danger" }, rejectProps: { label: t("common.cancel"), severity: "secondary", outlined: true }, accept: run,
        });
    } else run();
}
</script>

<template>
    <Head :title="t('menu.contact_messages')" />
    <AuthenticatedLayout>
        <div class="space-y-4">
            <div>
                <h1 class="text-xl font-semibold text-slate-800 flex items-center gap-2"><i class="pi pi-inbox text-primary" /> {{ t('menu.contact_messages') }}</h1>
                <p class="text-sm text-slate-500">{{ t('website.inbox.subtitle') }}</p>
            </div>

            <div class="card !p-2 sm:!p-4 space-y-3">
                <div class="flex flex-wrap items-center justify-between gap-2">
                    <div class="flex flex-wrap gap-1">
                        <Button v-for="tab in tabs" :key="tab.value" size="small" :icon="tab.icon" :severity="filter === tab.value ? 'primary' : 'secondary'" :text="filter !== tab.value"
                            :label="`${tab.label} (${num(tab.count)})`" @click="load({ filter: tab.value || undefined, page: undefined })" />
                    </div>
                    <IconField>
                        <InputIcon class="pi pi-search" />
                        <InputText v-model="search" :placeholder="t('website.inbox.search')" size="small" @input="onSearch" />
                    </IconField>
                </div>

                <div v-if="selected.length" class="flex flex-wrap items-center gap-2 rounded-lg bg-slate-50 border border-slate-200 px-3 py-2 text-sm">
                    <span class="font-medium">{{ t('website.inbox.selected', { n: selected.length }) }}</span>
                    <template v-if="!isTrash">
                        <Button size="small" text icon="pi pi-envelope-open" :label="t('website.inbox.mark_read')" @click="bulk('read')" />
                        <Button size="small" text icon="pi pi-envelope" :label="t('website.inbox.mark_unread')" @click="bulk('unread')" />
                        <Button v-if="canDelete" size="small" text severity="danger" icon="pi pi-trash" :label="t('common.delete')" @click="bulk('delete')" />
                    </template>
                    <template v-else>
                        <Button size="small" text icon="pi pi-replay" :label="t('website.admin.restore')" @click="bulk('restore')" />
                        <Button size="small" text severity="danger" icon="pi pi-times" :label="t('website.admin.delete_forever')" @click="bulk('force-delete')" />
                    </template>
                </div>

                <DataTable v-model:selection="selected" :value="messages.data" dataKey="id" size="small" scrollable :rowClass="(m) => (!m.read_at ? 'font-semibold' : '')"
                    @rowClick="(e) => view(e.data)" class="cursor-pointer">
                    <template #empty>
                        <div class="text-center text-slate-400 py-10"><i class="pi pi-inbox text-3xl block mb-2" />{{ t('website.inbox.empty') }}</div>
                    </template>
                    <Column selectionMode="multiple" headerStyle="width: 3rem" />
                    <Column :header="t('website.inbox.from')" style="min-width: 12rem">
                        <template #body="{ data }">
                            <div class="flex items-center gap-2">
                                <span v-if="!data.read_at" class="w-2 h-2 rounded-full bg-primary shrink-0" />
                                <div class="min-w-0">
                                    <div class="truncate">{{ data.name }}</div>
                                    <div class="text-xs text-slate-500 font-normal truncate">{{ data.email || data.phone }}</div>
                                </div>
                            </div>
                        </template>
                    </Column>
                    <Column :header="t('website.inbox.message')">
                        <template #body="{ data }">
                            <div class="truncate max-w-xl">
                                <span v-if="data.subject">{{ data.subject }} — </span><span class="font-normal text-slate-500">{{ data.message }}</span>
                            </div>
                        </template>
                    </Column>
                    <Column style="width: 7rem">
                        <template #body="{ data }"><Tag v-if="data.replied_at" :value="t('website.inbox.replied')" severity="success" /></template>
                    </Column>
                    <Column :header="t('common.date')" style="width: 11rem"><template #body="{ data }"><span class="text-xs font-normal whitespace-nowrap">{{ dateTime(data.created_at) }}</span></template></Column>
                </DataTable>
                <Paginator v-if="messages.total > messages.per_page" :rows="messages.per_page" :totalRecords="messages.total" :first="(messages.current_page - 1) * messages.per_page"
                    @page="(e) => load({ page: e.page + 1 })" />
            </div>
        </div>

        <Dialog :visible="!!open" @update:visible="(v) => !v && (open = null)" modal :header="open?.subject || t('website.inbox.your_message')"
            :style="{ width: '44rem' }" :breakpoints="{ '640px': '95vw' }">
            <div v-if="open" class="space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-sm">
                    <div><span class="text-slate-500">{{ t('common.name') }}:</span> <strong>{{ open.name }}</strong></div>
                    <div><span class="text-slate-500">{{ t('common.date') }}:</span> {{ dateTime(open.created_at) }}</div>
                    <div v-if="open.email"><span class="text-slate-500">{{ t('common.email') }}:</span> <a :href="`mailto:${open.email}`" class="text-primary">{{ open.email }}</a></div>
                    <div v-if="open.phone"><span class="text-slate-500">{{ t('common.phone') }}:</span> <a :href="`tel:${open.phone}`" class="text-primary">{{ open.phone }}</a></div>
                </div>
                <div class="rounded-lg bg-slate-50 border border-slate-200 p-4 whitespace-pre-wrap text-slate-800 break-words">{{ open.message }}</div>
                <div class="flex flex-wrap gap-2">
                    <a v-if="open.email" :href="replyHref(open)"><Button :label="t('website.inbox.reply_email')" icon="pi pi-reply" size="small" /></a>
                    <a v-if="open.phone" :href="`tel:${open.phone}`"><Button :label="t('website.inbox.call')" icon="pi pi-phone" size="small" severity="secondary" outlined /></a>
                    <a v-if="open.phone" :href="whatsappHref(open)" target="_blank" rel="noopener"><Button label="WhatsApp" icon="pi pi-whatsapp" size="small" severity="success" outlined /></a>
                </div>
                <div class="flex flex-col gap-1.5">
                    <label for="m_note" class="text-sm font-medium text-slate-700">{{ t('website.inbox.internal_note') }}</label>
                    <Textarea id="m_note" v-model="note" rows="2" autoResize maxlength="2000" fluid :placeholder="t('website.inbox.internal_note_hint')" />
                </div>
                <div class="flex items-center gap-3">
                    <ToggleSwitch inputId="m_replied" v-model="replied" />
                    <label for="m_replied" class="text-sm">{{ t('website.inbox.mark_replied') }}</label>
                </div>
                <p class="text-xs text-slate-400">IP {{ open.ip_address }} · {{ open.locale }}<span v-if="open.reader"> · {{ t('website.inbox.read_by', { name: open.reader.name }) }}</span></p>
            </div>
            <template #footer>
                <Button v-if="open && !open.deleted_at" :label="t('website.inbox.mark_unread')" icon="pi pi-envelope" severity="secondary" text @click="markUnread" />
                <Button v-if="open && canDelete && !open.deleted_at" :label="t('common.delete')" icon="pi pi-trash" severity="danger" text @click="bulk('delete', [open.id])" />
                <Button :label="t('common.save')" icon="pi pi-check" @click="saveNote" />
            </template>
        </Dialog>
    </AuthenticatedLayout>
</template>
