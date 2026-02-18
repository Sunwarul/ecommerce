<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>POS Orders Report</title>
    <style>
        body {
            font-family: 'Helvetica', sans-serif;
            font-size: 10px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 18px;
            color: #1a56db;
        }
        .header p {
            margin: 2px 0;
            color: #666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 6px;
            text-align: left;
        }
        th {
            background-color: #f3f4f6;
            font-weight: bold;
        }
        .text-right {
            text-align: right;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 8px;
            color: #999;
        }
        .badge {
            padding: 2px 5px;
            border-radius: 3px;
            font-size: 8px;
            text-transform: uppercase;
        }
        .badge-success { background-color: #def7ec; color: #03543f; }
        .badge-danger { background-color: #fde8e8; color: #9b1c1c; }
        .badge-warning { background-color: #fdf6b2; color: #723b13; }
        .badge-info { background-color: #e1effe; color: #1e429f; }
    </style>
</head>
<body>
    <div class="header">
        <h1>POS Orders Report</h1>
        <p>{{ $shop['name'] }}</p>
        <p>{{ $shop['address'] }} | {{ $shop['phone'] }}</p>
        <p>Generated on: {{ now()->format('Y-m-d H:i:s') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Invoice No</th>
                <th>Date</th>
                <th>Customer</th>
                <th>Subtotal</th>
                <th>Discount</th>
                <th>Tax</th>
                <th>Total</th>
                <th>Payment</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @php 
                $totalAmount = 0;
                $totalSubtotal = 0;
                $totalDiscount = 0;
                $totalTax = 0;
            @endphp
            @foreach($orders as $order)
                @php 
                    $totalAmount += $order->total_amount;
                    $totalSubtotal += $order->subtotal;
                    $totalDiscount += $order->discount_amount;
                    $totalTax += $order->tax_amount;
                @endphp
                <tr>
                    <td>{{ $order->invoice_no ?? ($order->status === 'draft' ? "DRAFT-{$order->id}" : "#{$order->id}") }}</td>
                    <td>{{ $order->created_at->format('Y-m-d') }}</td>
                    <td>{{ $order->customer?->name ?? 'Walk-in' }}</td>
                    <td class="text-right">{{ number_format($order->subtotal, 2) }}</td>
                    <td class="text-right">{{ number_format($order->discount_amount, 2) }}</td>
                    <td class="text-right">{{ number_format($order->tax_amount, 2) }}</td>
                    <td class="text-right"><strong>{{ number_format($order->total_amount, 2) }}</strong></td>
                    <td>
                        <span class="badge {{ $order->payment_status === 'paid' ? 'badge-success' : ($order->payment_status === 'partial' ? 'badge-warning' : 'badge-danger') }}">
                            {{ $order->payment_status }}
                        </span>
                    </td>
                    <td>
                        <span class="badge {{ $order->status === 'completed' ? 'badge-success' : ($order->status === 'draft' ? 'badge-info' : 'badge-danger') }}">
                            {{ $order->status }}
                        </span>
                    </td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr style="background-color: #f9fafb;">
                <th colspan="3" class="text-right">Totals:</th>
                <th class="text-right">{{ number_format($totalSubtotal, 2) }}</th>
                <th class="text-right">{{ number_format($totalDiscount, 2) }}</th>
                <th class="text-right">{{ number_format($totalTax, 2) }}</th>
                <th class="text-right">{{ number_format($totalAmount, 2) }}</th>
                <th colspan="2"></th>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        &copy; {{ date('Y') }} {{ $shop['name'] }}. All rights reserved.
    </div>
</body>
</html>
