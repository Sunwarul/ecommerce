<template>
    <div class="min-h-screen bg-gray-100 py-3">
        <div class="max-w-4xl mx-auto bg-white p-2 text-sm text-gray-800">
            <!-- Header -->
            <div class="relative border-b pb-1 mb-1 min-h-[65px]">


                <div class="absolute top-4 left-6">
                    <img src="../../../assets/images/brand.jpeg" alt="Logo" class="h-[35px] object-contain" />
                </div>

                <div class="text-center">
                    <h1 class="font-semibold uppercase">Sales Receipt</h1>
                    <p class="text-xl font-bold uppercase">
                        {{ shop?.name ?? "" }}
                    </p>
                    <p>{{ shop?.address ?? "" }}</p>
                    <!-- <p class="text-[10px]">
                        Contact: {{ shop?.phone ?? "" }}
                    </p> -->
                </div>

                <div class="absolute top-0 right-6">
                    <img src="../../../assets/images/logo.jpeg" alt="Logo" class="h-[58px] object-contain" />
                </div>

            </div>

            <!-- Customer & Invoice Info -->
            <div class="grid grid-cols-2 gap-1 mb-2">
                <div>
                    <!-- <p class="text-[10px]">
                        <span class="font-semibold">Customer ID:</span>
                        {{ order?.customer?.id ?? "" }}
                    </p> -->
                    <p class="text-[10px]">
                        <span class="font-semibold">Customer Name:</span>
                        {{ order?.customer?.name ?? "" }}
                    </p>
                    <p class="text-[10px]">
                        <span class="font-semibold">Phone No:</span>
                        {{ order?.customer?.phone ?? "" }}
                    </p>
                    <p class="text-[10px]">
                        <span class="font-semibold">Address:</span>
                        {{ order?.customer?.address ?? "" }}
                    </p>
                </div>

                <div class="text-right">
                    <p class="text-[10px]">
                        <span class="font-semibold">Date:</span>
                        <!-- {{ formatDate(order?.created_at) }} -->
                        {{ order?.created_at }}
                    </p>
                    <p class="text-[10px]">
                        <span class="font-semibold">Invoice No.:</span>
                        {{ order?.invoice_no ?? "" }}
                    </p>
                    <p class="text-[10px]">
                        {{ order?.branch?.name ?? "" }}
                        #{{ order?.branch?.code ?? "" }}
                    </p>
                    <p class="text-[10px]">
                        {{ order?.branch?.address ?? "" }}
                    </p>
                </div>
            </div>

            <!-- Items Table -->
            <div class="overflow-x-auto">
                <table class="w-full border border-gray-800 text-xs">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="border px-2 py-1 text-left">Barcode/SKU/Serial</th>
                            <th class="border px-2 py-1 text-left">Product</th>
                            <th class="border px-2 py-1">Unit Price</th>
                            <th class="border px-2 py-1">Qty</th>
                            <th class="border px-2 py-1">Discount</th>
                            <th class="border px-2 py-1">Tax</th>
                            <th class="border px-2 py-1">Total</th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr v-for="item in order?.items ?? []" :key="item.id" class="text-center">
                            <td class="border px-2 py-1 text-left text-[10px]">
                                {{ item?.product?.barcode ?? item?.variation?.sku ?? item?.sku ?? item?.product?.serial
                                }}
                            </td>
                            <td class="border px-2 py-1 text-left text-[10px]">
                                {{ item?.name ?? "" }}
                            </td>
                            <td class="border px-2 py-1">
                                {{ item?.unit_price ?? "" }}
                            </td>
                            <td class="border px-2 py-1">
                                {{ item?.quantity ?? 0 }}
                            </td>
                            <td class="border px-2 py-1">
                                {{ item?.discount_amount ?? "" }}
                            </td>
                            <td class="border px-2 py-1">
                                {{ item?.tax_amount ?? "" }}
                            </td>
                            <td class="border px-2 py-1 font-semibold">
                                {{ item?.line_total ?? "" }}
                            </td>
                        </tr>

                        <tr v-if="!order?.items?.length">
                            <td colspan="7" class="text-center py-3">
                                No items found
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Warranty / Serial -->
            <!-- <div class="border border-t-0 p-1 text-xs">
                <p>
                    <span class="font-semibold">Invoice No:</span>
                    {{ order?.invoice_no ?? 'N/A' }}
                </p>
                <p>
                    <span class="font-semibold">Warranty Info:</span>
                    {{ order?.warranty_info ?? 'N/A' }}
                </p>
            </div> -->

            <!-- Summary -->
            <div class="grid grid-cols-3 gap-4 mt-3">
                <!-- Amount in Words -->
                <div class="text-xs mt-4 col-span-2">
                    <!-- <p>
                        <span class="font-semibold">Invoice No:</span>
                        {{ order?.invoice_no ?? "" }}
                    </p> -->
                    <p>
                        <span class="font-semibold">Warranty Info:</span>
                        {{ order?.warranty_info ?? "" }}
                    </p>
                    <p class="text-[10px] mt-1" v-if="order?.note">
                        <span class="font-semibold">Note:</span>
                        {{ order?.note }}
                    </p>
                    <p class="italic text-xs">
                        Amount in Words:
                        <strong>
                            {{ amountInWords(order?.total_amount) }}
                        </strong>
                    </p>
                </div>

                <!-- Totals -->
                <div class="border text-sm">
                    <div class="flex justify-between border-b px-2 text-[11px]">
                        <span>Sub Total</span>
                        <span>{{ order?.subtotal ?? "" }}</span>
                    </div>
                    <div class="flex justify-between border-b px-2 text-[11px]">
                        <span>Discount</span>
                        <span>{{ order?.discount_amount ?? "" }}</span>
                    </div>
                    <div class="flex justify-between border-b px-2 text-[11px]">
                        <span>Tax</span>
                        <span>{{ order?.tax_amount ?? "" }}</span>
                    </div>
                    <div class="flex justify-between border-b px-2 text-[11px] font-bold">
                        <span>Grand Total</span>
                        <span>{{ order?.total_amount ?? "" }}</span>
                    </div>
                    <div class="flex justify-between px-2 text-[11px] font-bold">
                        <span>Paid</span>
                        <span>{{ order?.paid_amount ?? "" }}</span>
                    </div>
                </div>
            </div>

            <div class="text-center mt-6 text-xs">
                Thank you for your purchase.
            </div>
        </div>
    </div>
</template>

<script setup>
const props = defineProps({
    order: Object,
    shop: Object,
});

/* Date: DD-MM-YYYY */
const formatDate = (date) => {
    if (!date) return "N/A";
    const d = new Date(date);
    const day = String(d.getDate()).padStart(2, "0");
    const month = String(d.getMonth() + 1).padStart(2, "0");
    const year = d.getFullYear();
    return `${day}-${month}-${year}`;
};

/* Amount to Words (BDT) */
const amountInWords = (amount) => {
    if (!amount) return "BDT Zero only";

    const numberToWords = (num) => {
        const ones = [
            "",
            "One",
            "Two",
            "Three",
            "Four",
            "Five",
            "Six",
            "Seven",
            "Eight",
            "Nine",
        ];
        const tens = [
            "",
            "",
            "Twenty",
            "Thirty",
            "Forty",
            "Fifty",
            "Sixty",
            "Seventy",
            "Eighty",
            "Ninety",
        ];
        const teens = [
            "Ten",
            "Eleven",
            "Twelve",
            "Thirteen",
            "Fourteen",
            "Fifteen",
            "Sixteen",
            "Seventeen",
            "Eighteen",
            "Nineteen",
        ];

        if (num < 10) return ones[num];
        if (num < 20) return teens[num - 10];
        if (num < 100) return tens[Math.floor(num / 10)] + " " + ones[num % 10];
        if (num < 1000)
            return (
                ones[Math.floor(num / 100)] +
                " Hundred " +
                numberToWords(num % 100)
            );
        if (num < 100000)
            return (
                numberToWords(Math.floor(num / 1000)) +
                " Thousand " +
                numberToWords(num % 1000)
            );
        if (num < 10000000)
            return (
                numberToWords(Math.floor(num / 100000)) +
                " Lakh " +
                numberToWords(num % 100000)
            );
        return "";
    };

    return `${numberToWords(parseInt(amount))} only`;
};
</script>
