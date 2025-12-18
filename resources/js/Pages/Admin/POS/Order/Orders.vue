<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { router } from "@inertiajs/vue3";
import { computed, ref } from "vue";

const props = defineProps({
    orders: Object, // Laravel paginator
    filters: Object,
});

const search = ref(props.filters.search || "");
const status = ref(props.filters.status || "");
const paymentStatus = ref(props.filters.payment_status || "");

// shorthand for current page rows
const orderRows = computed(() => props.orders?.data || []);

const statusOptions = [
    { label: "All", value: "" },
    { label: "Completed", value: "completed" },
    { label: "Draft", value: "draft" },
    { label: "Void", value: "void" },
];

const paymentStatusOptions = [
    { label: "All", value: "" },
    { label: "Paid", value: "paid" },
    { label: "Partial", value: "partial" },
    { label: "Unpaid", value: "unpaid" },
];

// ---------- Small summary stats (current page only) ----------
const pageTotalAmount = computed(() =>
    orderRows.value.reduce((sum, o) => sum + Number(o.total_amount || 0), 0)
);

const paidCount = computed(
    () => orderRows.value.filter((o) => o.payment_status === "paid").length
);

const partialCount = computed(
    () => orderRows.value.filter((o) => o.payment_status === "partial").length
);

const unpaidCount = computed(
    () =>
        orderRows.value.filter(
            (o) => o.payment_status === "unpaid" || o.payment_status === "due"
        ).length
);

function applyFilter() {
    router.get(
        route("pos.orders.index"),
        {
            search: search.value,
            status: status.value,
            payment_status: paymentStatus.value,
        },
        {
            preserveState: true,
            replace: true,
        }
    );
}

function goToPage(url) {
    if (!url) return;
    router.get(url, {}, { preserveState: true });
}
</script>

<template>
    <AuthenticatedLayout>
        <div class="p-4 md:p-6 space-y-4">
            <!-- PAGE HEADER -->
            <div class="flex justify-between items-start gap-3 flex-wrap">
                <div>
                    <h2
                        class="text-xl font-semibold text-surface-900 flex items-center gap-2"
                    >
                        <i class="pi pi-receipt text-primary"></i>
                        POS Sales
                    </h2>
                    <p class="text-sm text-surface-500">
                        View and manage sales created through the Point of Sale.
                    </p>
                </div>

                <!-- Small summary chips -->
                <div class="flex flex-wrap gap-2 text-xs">
                    <div
                        class="px-3 py-2 rounded-full bg-primary-50 text-primary-700 flex items-center gap-2"
                    >
                        <i class="pi pi-wallet text-xs"></i>
                        <span class="font-medium">Page Total:</span>
                        <span class="font-semibold">
                            {{ pageTotalAmount.toFixed(2) }}
                        </span>
                    </div>
                    <div
                        class="px-3 py-2 rounded-full bg-green-50 text-green-700 flex items-center gap-2"
                    >
                        <i class="pi pi-check-circle text-xs"></i>
                        <span>Paid:</span>
                        <span class="font-semibold">{{ paidCount }}</span>
                    </div>
                    <div
                        class="px-3 py-2 rounded-full bg-amber-50 text-amber-700 flex items-center gap-2"
                    >
                        <i class="pi pi-exclamation-circle text-xs"></i>
                        <span>Partial:</span>
                        <span class="font-semibold">{{ partialCount }}</span>
                    </div>
                    <div
                        class="px-3 py-2 rounded-full bg-red-50 text-red-700 flex items-center gap-2"
                    >
                        <i class="pi pi-times-circle text-xs"></i>
                        <span>Unpaid:</span>
                        <span class="font-semibold">{{ unpaidCount }}</span>
                    </div>
                </div>
            </div>

            <!-- FILTER BAR -->
            <div class="card mb-2">
                <div
                    class="flex flex-col md:flex-row md:items-center md:justify-between gap-3"
                >
                    <div class="flex items-center gap-2">
                        <span class="p-input-icon-left w-full md:w-72">
                            <i class="pi pi-search" />
                            <InputText
                                v-model="search"
                                placeholder="Search invoice, customer, user"
                                class="w-full text-sm"
                                @keyup.enter="applyFilter"
                            />
                        </span>
                    </div>

                    <div class="flex flex-wrap gap-2">
                        <Dropdown
                            v-model="status"
                            :options="statusOptions"
                            option-label="label"
                            option-value="value"
                            placeholder="Order Status"
                            class="w-full sm:w-40"
                            @change="applyFilter"
                        />

                        <Dropdown
                            v-model="paymentStatus"
                            :options="paymentStatusOptions"
                            option-label="label"
                            option-value="value"
                            placeholder="Payment Status"
                            class="w-full sm:w-44"
                            @change="applyFilter"
                        />

                        <Button
                            label="Filter"
                            icon="pi pi-filter"
                            class="p-button-outlined w-full sm:w-auto"
                            @click="applyFilter"
                        />
                    </div>
                </div>
            </div>

            <!-- TABLE CARD -->
            <div class="card">
                <DataTable
                    :value="orderRows"
                    responsive-layout="scroll"
                    :showGridlines="true"
                    size="small"
                >
                    <Column field="invoice_no" header="Invoice">
                        <template #body="{ data }">
                            <div class="flex items-center gap-2">
                                <span class="font-medium text-surface-800">
                                    {{ data.invoice_no }}
                                </span>
                            </div>
                        </template>
                    </Column>

                    <Column header="Customer">
                        <template #body="{ data }">
                            <span class="text-sm text-surface-700">
                                {{ data.customer?.name || "Walk-in" }}
                            </span>
                        </template>
                    </Column>

                    <Column header="Cashier">
                        <template #body="{ data }">
                            <span class="text-sm text-surface-700">
                                {{ data.user?.name || "-" }}
                            </span>
                        </template>
                    </Column>

                    <Column field="total_amount" header="Total">
                        <template #body="{ data }">
                            <span class="font-semibold text-emerald-600">
                                {{ Number(data.total_amount).toFixed(2) }}
                            </span>
                        </template>
                    </Column>

                    <Column header="Payment">
                        <template #body="{ data }">
                            <Tag
                                :value="data.payment_status"
                                :severity="
                                    data.payment_status === 'paid'
                                        ? 'success'
                                        : data.payment_status === 'partial'
                                        ? 'warn'
                                        : 'danger'
                                "
                            />
                        </template>
                    </Column>

                    <Column header="Status">
                        <template #body="{ data }">
                            <Tag
                                :value="data.status"
                                :severity="
                                    data.status === 'completed'
                                        ? 'success'
                                        : data.status === 'draft'
                                        ? 'info'
                                        : 'danger'
                                "
                            />
                        </template>
                    </Column>

                    <Column header="Date">
                        <template #body="{ data }">
                            <span class="text-xs text-surface-600">
                                {{ new Date(data.created_at).toLocaleString() }}
                            </span>
                        </template>
                    </Column>

                    <!-- Actions (optional) -->
                    <!--
                    <Column header="Actions">
                        <template #body="{ data }">
                            <Button
                                icon="pi pi-eye"
                                class="p-button-text p-button-sm"
                                @click="$inertia.get(route('pos.orders.show', data.id))"
                            />
                        </template>
                    </Column>
                    -->
                </DataTable>

                <!-- Pagination -->
                <div
                    class="flex flex-column md:flex-row md:justify-content-between md:align-items-center gap-2 mt-3"
                >
                    <span class="text-sm text-surface-500">
                        Showing
                        <span class="font-semibold text-surface-700">
                            {{ orders.from }}–{{ orders.to }}
                        </span>
                        of
                        <span class="font-semibold text-surface-700">
                            {{ orders.total }}
                        </span>
                        sales
                    </span>

                    <div class="flex gap-1 justify-content-end">
                        <Button
                            icon="pi pi-angle-left"
                            class="p-button-text"
                            :disabled="!orders.prev_page_url"
                            @click="goToPage(orders.prev_page_url)"
                        />
                        <Button
                            icon="pi pi-angle-right"
                            class="p-button-text"
                            :disabled="!orders.next_page_url"
                            @click="goToPage(orders.next_page_url)"
                        />
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
