@extends('layouts.vertical', ['title' => 'Payment Receipt'])

@section('content')
<div class="row">
    <div class="col-lg-8 mx-auto">
        <div class="card">
            <div class="card-body">
                <!-- Receipt Header -->
                <div class="text-center mb-4">
                    <h2 class="fw-bold text-primary">PAYMENT RECEIPT</h2>
                    <p class="text-muted">Transaction ID: <code>{{ $payment->payment_id ?? 'TXN-' . $payment->id }}</code></p>
                </div>

                <hr>

                <!-- Company & Customer Info -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h5 class="fw-semibold mb-3">From:</h5>
                        <div class="mb-2">
                            <strong>{{ config('app.name', 'Luky') }}</strong>
                        </div>
                        <div class="text-muted">
                            <div>Platform Services</div>
                            <div>Saudi Arabia</div>
                        </div>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <h5 class="fw-semibold mb-3">To:</h5>
                        @if($payment->booking && $payment->booking->client)
                        <div class="mb-2">
                            <strong>{{ $payment->booking->client->name }}</strong>
                        </div>
                        <div class="text-muted">
                            <div>{{ $payment->booking->client->email ?? 'N/A' }}</div>
                            <div>{{ $payment->booking->client->phone ?? 'N/A' }}</div>
                        </div>
                        @else
                        <div class="text-muted">Customer information not available</div>
                        @endif
                    </div>
                </div>

                <!-- Payment Details -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="mb-2">
                            <span class="text-muted">Payment Date:</span>
                            <strong class="ms-2">{{ $payment->paid_at ? $payment->paid_at->format('d M Y, h:i A') : $payment->created_at->format('d M Y, h:i A') }}</strong>
                        </div>
                        <div class="mb-2">
                            <span class="text-muted">Payment Method:</span>
                            <strong class="ms-2">{{ ucfirst($payment->method ?? $payment->gateway ?? 'N/A') }}</strong>
                        </div>
                        <div class="mb-2">
                            <span class="text-muted">Status:</span>
                            @php
                                $statusColors = [
                                    'completed' => 'success',
                                    'pending' => 'warning',
                                    'failed' => 'danger',
                                    'refunded' => 'secondary',
                                ];
                                $statusColor = $statusColors[$payment->status] ?? 'secondary';
                            @endphp
                            <span class="badge bg-{{ $statusColor }} ms-2">{{ ucfirst($payment->status) }}</span>
                        </div>
                    </div>
                    <div class="col-md-6 text-md-end">
                        @if($payment->booking)
                        <div class="mb-2">
                            <span class="text-muted">Booking ID:</span>
                            <strong class="ms-2">#BK-{{ $payment->booking_id }}</strong>
                        </div>
                        @endif
                        <div class="mb-2">
                            <span class="text-muted">Gateway Transaction:</span>
                            <strong class="ms-2">{{ $payment->gateway_transaction_id ?? 'N/A' }}</strong>
                        </div>
                    </div>
                </div>

                <hr>

                <!-- Service Details -->
                @if($payment->booking && $payment->booking->items)
                <div class="mb-4">
                    <h5 class="fw-semibold mb-3">Service Details</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Service</th>
                                    <th>Provider</th>
                                    <th class="text-end">Price</th>
                                    <th class="text-center">Qty</th>
                                    <th class="text-end">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($payment->booking->items as $item)
                                <tr>
                                    <td>{{ $item->service->name_en ?? $item->service->name_ar ?? 'Service' }}</td>
                                    <td>{{ $payment->booking->provider->business_name ?? 'N/A' }}</td>
                                    <td class="text-end">{{ number_format($item->price, 2) }} {{ $payment->currency }}</td>
                                    <td class="text-center">{{ $item->quantity ?? 1 }}</td>
                                    <td class="text-end">{{ number_format($item->total_price, 2) }} {{ $payment->currency }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif

                <!-- Payment Summary -->
                <div class="row">
                    <div class="col-md-6 offset-md-6">
                        <table class="table table-sm">
                            <tr>
                                <td class="text-muted">Subtotal:</td>
                                <td class="text-end fw-semibold">
                                    {{ number_format(($payment->amount - ($payment->tax_amount ?? 0)), 2) }} {{ $payment->currency }}
                                </td>
                            </tr>
                            @if($payment->booking && $payment->booking->discount_amount > 0)
                            <tr>
                                <td class="text-muted">Discount:</td>
                                <td class="text-end text-success">
                                    -{{ number_format($payment->booking->discount_amount, 2) }} {{ $payment->currency }}
                                </td>
                            </tr>
                            @endif
                            @if($payment->tax_amount)
                            <tr>
                                <td class="text-muted">Tax (15%):</td>
                                <td class="text-end">
                                    {{ number_format($payment->tax_amount, 2) }} {{ $payment->currency }}
                                </td>
                            </tr>
                            @endif
                            <tr class="table-active">
                                <td class="fw-bold">Total Amount:</td>
                                <td class="text-end fw-bold fs-5 text-primary">
                                    {{ number_format($payment->amount, 2) }} {{ $payment->currency }}
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                <hr>

                <!-- Footer -->
                <div class="text-center text-muted small mt-4">
                    <p class="mb-1">Thank you for your payment!</p>
                    <p class="mb-0">This is a computer-generated receipt and does not require a signature.</p>
                </div>

                <!-- Action Buttons -->
                <div class="d-flex justify-content-center gap-2 mt-4">
                    <a href="{{ route('payments.transactions') }}" class="btn btn-light">
                        <i class="bx bx-arrow-back me-1"></i> Back to Transactions
                    </a>
                    <button onclick="window.print()" class="btn btn-primary">
                        <i class="bx bx-printer me-1"></i> Print Receipt
                    </button>
                    <a href="{{ route('payments.receipt.download', $payment->id) }}" target="_blank" class="btn btn-success">
                        <i class="bx bx-download me-1"></i> Download PDF
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')
<style>
    @media print {
        .btn, .card-header, nav, .sidebar, .topbar {
            display: none !important;
        }
        .card {
            border: none !important;
            box-shadow: none !important;
        }
    }
</style>
@endsection
