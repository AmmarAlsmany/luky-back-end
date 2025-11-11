<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Receipt - {{ $payment->payment_id ?? 'TXN-' . $payment->id }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            padding: 40px;
            color: #333;
        }
        .receipt-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #4CAF50;
        }
        .header h1 {
            color: #4CAF50;
            font-size: 32px;
            margin-bottom: 10px;
        }
        .header p {
            color: #666;
            font-size: 14px;
        }
        .info-section {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }
        .info-block h3 {
            font-size: 16px;
            margin-bottom: 10px;
            color: #333;
        }
        .info-block p {
            margin: 5px 0;
            font-size: 14px;
            color: #666;
        }
        .info-block strong {
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f5f5f5;
            font-weight: bold;
            color: #333;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .summary-table {
            margin-left: auto;
            width: 300px;
            margin-top: 20px;
        }
        .summary-table td {
            padding: 8px;
        }
        .total-row {
            background-color: #f5f5f5;
            font-weight: bold;
            font-size: 18px;
        }
        .total-row td {
            padding: 15px;
            color: #4CAF50;
        }
        .status-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }
        .status-completed {
            background-color: #4CAF50;
            color: white;
        }
        .status-pending {
            background-color: #FFC107;
            color: white;
        }
        .status-failed {
            background-color: #F44336;
            color: white;
        }
        .footer {
            text-align: center;
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            color: #666;
            font-size: 12px;
        }
        hr {
            border: none;
            border-top: 1px solid #ddd;
            margin: 20px 0;
        }
        @media print {
            body {
                padding: 0;
            }
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="receipt-container">
        <!-- Header -->
        <div class="header">
            <h1>PAYMENT RECEIPT</h1>
            <p>Transaction ID: <strong>{{ $payment->payment_id ?? 'TXN-' . $payment->id }}</strong></p>
        </div>

        <!-- Company & Customer Info -->
        <div class="info-section">
            <div class="info-block">
                <h3>From:</h3>
                <p><strong>{{ config('app.name', 'Luky') }}</strong></p>
                <p>Platform Services</p>
                <p>Saudi Arabia</p>
            </div>
            <div class="info-block" style="text-align: right;">
                <h3>To:</h3>
                @if($payment->booking && $payment->booking->client)
                <p><strong>{{ $payment->booking->client->name }}</strong></p>
                <p>{{ $payment->booking->client->email ?? 'N/A' }}</p>
                <p>{{ $payment->booking->client->phone ?? 'N/A' }}</p>
                @else
                <p>Customer information not available</p>
                @endif
            </div>
        </div>

        <!-- Payment Details -->
        <div class="info-section">
            <div class="info-block">
                <p><strong>Payment Date:</strong> {{ $payment->paid_at ? $payment->paid_at->format('d M Y, h:i A') : $payment->created_at->format('d M Y, h:i A') }}</p>
                <p><strong>Payment Method:</strong> {{ ucfirst($payment->method ?? $payment->gateway ?? 'N/A') }}</p>
                <p><strong>Status:</strong> 
                    @php
                        $statusClass = [
                            'completed' => 'status-completed',
                            'pending' => 'status-pending',
                            'failed' => 'status-failed',
                        ][$payment->status] ?? 'status-pending';
                    @endphp
                    <span class="status-badge {{ $statusClass }}">{{ strtoupper($payment->status) }}</span>
                </p>
            </div>
            <div class="info-block" style="text-align: right;">
                @if($payment->booking)
                <p><strong>Booking ID:</strong> #BK-{{ $payment->booking_id }}</p>
                @endif
                <p><strong>Gateway Transaction:</strong> {{ $payment->gateway_transaction_id ?? 'N/A' }}</p>
            </div>
        </div>

        <hr>

        <!-- Service Details -->
        @if($payment->booking && $payment->booking->items)
        <h3 style="margin-bottom: 15px;">Service Details</h3>
        <table>
            <thead>
                <tr>
                    <th>Service</th>
                    <th>Provider</th>
                    <th class="text-right">Price</th>
                    <th class="text-center">Qty</th>
                    <th class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($payment->booking->items as $item)
                <tr>
                    <td>{{ $item->service->name_en ?? $item->service->name_ar ?? 'Service' }}</td>
                    <td>{{ $payment->booking->provider->business_name ?? 'N/A' }}</td>
                    <td class="text-right">{{ number_format($item->price, 2) }} {{ $payment->currency }}</td>
                    <td class="text-center">{{ $item->quantity ?? 1 }}</td>
                    <td class="text-right">{{ number_format($item->total_price, 2) }} {{ $payment->currency }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif

        <!-- Payment Summary -->
        <table class="summary-table">
            <tr>
                <td>Subtotal:</td>
                <td class="text-right"><strong>{{ number_format(($payment->amount - ($payment->tax_amount ?? 0)), 2) }} {{ $payment->currency }}</strong></td>
            </tr>
            @if($payment->booking && $payment->booking->discount_amount > 0)
            <tr>
                <td>Discount:</td>
                <td class="text-right" style="color: #4CAF50;"><strong>-{{ number_format($payment->booking->discount_amount, 2) }} {{ $payment->currency }}</strong></td>
            </tr>
            @endif
            @if($payment->tax_amount)
            <tr>
                <td>Tax (15%):</td>
                <td class="text-right"><strong>{{ number_format($payment->tax_amount, 2) }} {{ $payment->currency }}</strong></td>
            </tr>
            @endif
            <tr class="total-row">
                <td>Total Amount:</td>
                <td class="text-right">{{ number_format($payment->amount, 2) }} {{ $payment->currency }}</td>
            </tr>
        </table>

        <!-- Footer -->
        <div class="footer">
            <p>Thank you for your payment!</p>
            <p>This is a computer-generated receipt and does not require a signature.</p>
            <p style="margin-top: 10px;">Generated on: {{ now()->format('d M Y, h:i A') }}</p>
        </div>
    </div>

    <script>
        // Auto print on load
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>
