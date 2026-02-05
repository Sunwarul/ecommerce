<template>

    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <div class="p-4">
            <!-- Header -->
            <div class="mb-6 flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">Dashboard</h1>
                    <p class="text-gray-500">Overview of your business performance.</p>
                </div>
                <div class="flex gap-2">
                    <a href="/admin/pos" target="_blank"
                        class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg shadow transition flex items-center gap-2">
                        <i class="pi pi-desktop"></i> Open POS
                    </a>
                </div>
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Daily Sales -->
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Today's Sales</p>
                            <h3 class="text-2xl font-bold text-gray-900 mt-1">{{ formatCurrency(stats.daily_sales) }}
                            </h3>
                        </div>
                        <div class="p-2 bg-green-100 rounded-lg">
                            <i class="pi pi-dollar text-green-600 text-xl"></i>
                        </div>
                    </div>
                    <div class="mt-4 text-sm text-gray-600">
                        <span class="font-medium text-gray-900">{{ stats.daily_orders }}</span> Orders today
                    </div>
                </div>

                <!-- Weekly Sales -->
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-sm font-medium text-gray-500">This Week</p>
                            <h3 class="text-2xl font-bold text-gray-900 mt-1">{{ formatCurrency(stats.weekly_sales) }}
                            </h3>
                        </div>
                        <div class="p-2 bg-blue-100 rounded-lg">
                            <i class="pi pi-chart-line text-blue-600 text-xl"></i>
                        </div>
                    </div>
                    <div class="mt-4 text-sm text-gray-600">
                        <span class="font-medium text-gray-900">{{ stats.weekly_orders }}</span> Orders this week
                    </div>
                </div>

                <!-- Monthly Sales -->
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-sm font-medium text-gray-500">This Month</p>
                            <h3 class="text-2xl font-bold text-gray-900 mt-1">{{ formatCurrency(stats.monthly_sales) }}
                            </h3>
                        </div>
                        <div class="p-2 bg-purple-100 rounded-lg">
                            <i class="pi pi-calendar text-purple-600 text-xl"></i>
                        </div>
                    </div>
                    <div class="mt-4 text-sm text-gray-600">
                        <span class="font-medium text-gray-900">{{ stats.monthly_orders }}</span> Orders this month
                    </div>
                </div>

                <!-- Top Product Stats -->
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Top Product</p>
                            <h3 class="text-lg font-bold text-gray-900 mt-1 truncate w-40"
                                :title="top_products[0]?.name || 'N/A'">
                                {{ top_products[0]?.name || 'N/A' }}
                            </h3>
                        </div>
                        <div class="p-2 bg-orange-100 rounded-lg">
                            <i class="pi pi-star text-orange-600 text-xl"></i>
                        </div>
                    </div>
                    <div class="mt-4 text-sm text-gray-600">
                        <span class="font-medium text-gray-900">{{ top_products[0]?.total_qty || 0 }}</span> Sold
                    </div>
                </div>
            </div>

            <!-- Charts & Quick Links Section -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                <!-- Sales Chart -->
                <div class="lg:col-span-2 bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Sales Overview (Last 7 Days)</h3>
                    <Chart type="bar" :data="chartData" :options="chartOptions" class="h-[300px]" />
                </div>

                <!-- Quick Links -->
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
                    <div class="grid grid-cols-1 gap-3">
                        <a href="/admin/products/create" target="_blank"
                            class="p-3 bg-gray-50 hover:bg-gray-100 rounded-lg transition flex items-center gap-3 group">
                            <div class="p-2 bg-blue-100 text-blue-600 rounded-md group-hover:bg-blue-200">
                                <i class="pi pi-plus"></i>
                            </div>
                            <span class="font-medium text-gray-700">Add New Product</span>
                        </a>
                        <a href="/admin/pos/orders" target="_blank"
                            class="p-3 bg-gray-50 hover:bg-gray-100 rounded-lg transition flex items-center gap-3 group">
                            <div class="p-2 bg-green-100 text-green-600 rounded-md group-hover:bg-green-200">
                                <i class="pi pi-list"></i>
                            </div>
                            <span class="font-medium text-gray-700">View Orders</span>
                        </a>
                        <a href="/admin/customers/create" target="_blank"
                            class="p-3 bg-gray-50 hover:bg-gray-100 rounded-lg transition flex items-center gap-3 group">
                            <div class="p-2 bg-purple-100 text-purple-600 rounded-md group-hover:bg-purple-200">
                                <i class="pi pi-user-plus"></i>
                            </div>
                            <span class="font-medium text-gray-700">Add Customer</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Bottom Section: Tables -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Top Products Table -->
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Top Selling Products</h3>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3">Product Name</th>
                                    <th class="px-4 py-3 text-right">Sold Qty</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="product in top_products" :key="product.product_id"
                                    class="border-b hover:bg-gray-50">
                                    <td class="px-4 py-3 font-medium text-gray-900">{{ product.name }}</td>
                                    <td class="px-4 py-3 text-right">{{ product.total_qty }}</td>
                                </tr>
                                <tr v-if="top_products.length === 0">
                                    <td colspan="2" class="px-4 py-3 text-center text-gray-500">No sales yet</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Low Stock Alert and Recent Orders -->
                <div class="flex flex-col gap-6">
                    <!-- Low Stock -->
                    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-red-600 flex items-center gap-2">
                                <i class="pi pi-exclamation-triangle"></i> Low Stock Alert
                            </h3>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm text-left">
                                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3">Product</th>
                                        <th class="px-4 py-3">Branch</th>
                                        <th class="px-4 py-3 text-right">Qty</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="stock in low_stock" :key="stock.id" class="border-b hover:bg-gray-50">
                                        <td class="px-4 py-3 font-medium text-gray-900">{{ stock.product?.name ||
                                            'Unknown' }}</td>
                                        <td class="px-4 py-3 text-gray-500">{{ stock.branch?.name || '-' }}</td>
                                        <td class="px-4 py-3 text-right font-bold text-red-600">{{ stock.quantity }}
                                        </td>
                                    </tr>
                                    <tr v-if="low_stock.length === 0">
                                        <td colspan="3" class="px-4 py-3 text-center text-green-600">All stock levels
                                            normal</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Recent Orders -->
                    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Orders</h3>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm text-left">
                                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3">Order #</th>
                                        <th class="px-4 py-3 text-right">Amount</th>
                                        <th class="px-4 py-3 text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="order in recent_orders" :key="order.id"
                                        class="border-b hover:bg-gray-50">
                                        <td class="px-4 py-3 font-medium text-gray-900">
                                            <Link :href="`/admin/pos-orders/${order.id}/invoice`"
                                                class="text-indigo-600 hover:text-indigo-800">{{ order.invoice_no ||
                                                    order.id }}</Link>
                                        </td>
                                        <td class="px-4 py-3 text-right">{{ formatCurrency(order.total_amount) }}</td>
                                        <td class="px-4 py-3 text-center">
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full" :class="{
                                                'bg-green-100 text-green-800': order.status === 'completed' || order.payment_status === 'paid',
                                                'bg-yellow-100 text-yellow-800': order.status === 'pending',
                                                'bg-red-100 text-red-800': order.status === 'cancelled'
                                            }">
                                                {{ order.status || order.payment_status }}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr v-if="recent_orders.length === 0">
                                        <td colspan="3" class="px-4 py-3 text-center text-gray-500">No recent orders
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { computed, ref, onMounted } from "vue";
import { Head, Link, usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
const page = usePage();

const { currency, currency_symbol } = page.props.configs;

const props = defineProps({
    stats: Object,
    chart: Object,
    top_products: Array,
    low_stock: Array,
    recent_orders: Array,
});

const formatCurrency = (value) => {
    return new Intl.NumberFormat('en-BD', {
        style: 'currency',
        currency: currency, // Adjust currency as needed
    }).format(value || 0);
};

const formatCurrencyUSD = (value) => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD', // Adjust currency as needed
    }).format(value || 0);
};

const chartData = computed(() => {
    const documentStyle = getComputedStyle(document.documentElement);
    return {
        labels: props.chart?.labels || [],
        datasets: [
            {
                label: 'Sales (Amount)',
                backgroundColor: documentStyle.getPropertyValue('--p-primary-500') || '#6366f1',
                borderColor: documentStyle.getPropertyValue('--p-primary-500') || '#6366f1',
                data: props.chart?.data || [],
                fill: false,
                tension: 0.4
            }
        ]
    };
});

const chartOptions = ref({});

onMounted(() => {
    const documentStyle = getComputedStyle(document.documentElement);
    const textColor = documentStyle.getPropertyValue('--text-color') || '#374151';
    const textColorSecondary = documentStyle.getPropertyValue('--text-color-secondary') || '#6b7280';
    const surfaceBorder = documentStyle.getPropertyValue('--surface-border') || '#e5e7eb';

    chartOptions.value = {
        maintainAspectRatio: false,
        aspectRatio: 0.6,
        plugins: {
            legend: {
                labels: {
                    color: textColor
                }
            }
        },
        scales: {
            x: {
                ticks: {
                    color: textColorSecondary
                },
                grid: {
                    color: surfaceBorder
                }
            },
            y: {
                ticks: {
                    color: textColorSecondary
                },
                grid: {
                    color: surfaceBorder
                }
            }
        }
    };
});
</script>
