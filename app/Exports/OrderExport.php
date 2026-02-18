<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class OrderExport implements FromCollection, WithHeadings, WithMapping
{
    protected $orders;

    public function __construct($orders)
    {
        $this->orders = $orders;
    }

    public function collection()
    {
        return $this->orders;
    }

    public function headings(): array
    {
        return [
            'Invoice No',
            'Date',
            'Customer',
            'Cashier',
            'Subtotal',
            'Discount',
            'Tax',
            'Total',
            'Status',
            'Payment Status',
        ];
    }

    public function map($order): array
    {
        return [
            $order->invoice_no ?? ($order->status === 'draft' ? "DRAFT-{$order->id}" : "#{$order->id}"),
            $order->created_at->format('Y-m-d H:i:s'),
            $order->customer?->name ?? 'Walk-in',
            $order->user?->name ?? 'N/A',
            $order->subtotal,
            $order->discount_amount,
            $order->tax_amount,
            $order->total_amount,
            ucfirst($order->status),
            ucfirst($order->payment_status),
        ];
    }
}
