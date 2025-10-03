<?php

use App\Models\Invoice;
use function Livewire\Volt\{state};

// Fetch all invoices dynamically
state([
    'invoices' => fn() => Invoice::all(),
]);

?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="datatable" class="table table-striped table-bordered align-middle">
                            <thead>
                                <tr>
                                    <th>Email</th>
                                    <th>Donation Type</th>
                                    <th>Amount Due</th>
                                    <th>Invoice Date</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($this->invoices as $invoice)
                                    <tr>
                                        <td>{{ $invoice->subscription->user->email ?? '-' }}</td>
                                        <td>
                                            {{ $invoice->subscription->type === 'day'
                                                ? 'Daily'
                                                : ($invoice->subscription->type === 'week'
                                                    ? 'Weekly'
                                                    : ($invoice->subscription->type === 'month'
                                                        ? 'Monthly'
                                                        : ($invoice->subscription->type
                                                            ? ucfirst($invoice->subscription->type)
                                                            : '-'))) }}
                                        </td>
                                        <td>
                                            {{ match (strtoupper($invoice->currency)) {
                                                'USD' => '$',
                                                'GBP' => '£',
                                                'EUR' => '€',
                                            } }}
                                            {{ number_format($invoice->amount_due, 2) }}
                                        </td>
                                        <td>{{ $invoice->invoice_date ? \Carbon\Carbon::parse($invoice->invoice_date)->format('Y-m-d') : '-' }}
                                        </td>
                                        <td class="text-center">
                                            <button class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                                data-bs-target="#invoiceModal{{ $invoice->id }}">
                                                View
                                            </button>
                                        </td>
                                    </tr>

                                    <!-- Bootstrap Modal -->
                                    <div class="modal fade" id="invoiceModal{{ $invoice->id }}" tabindex="-1"
                                        aria-labelledby="invoiceModalLabel{{ $invoice->id }}" aria-hidden="true">
                                        <div class="modal-dialog modal-lg modal-dialog-centered">
                                            <div class="modal-content border-0 rounded-4 shadow-lg">

                                                <!-- Minimal Header -->
                                                <div class="modal-header border-0 pb-0">
                                                    <h4 class="modal-title text-dark fw-semibold"
                                                        id="invoiceModalLabel{{ $invoice->id }}">
                                                        Invoice #{{ substr($invoice->stripe_invoice_id ?? 'N/A', -8) }}
                                                    </h4>
                                                    <button type="button" class="btn-close"
                                                        data-bs-dismiss="modal"></button>
                                                </div>

                                                <!-- Modal Body -->
                                                <div class="modal-body px-4 pb-4">

                                                    <!-- Customer & Status Row -->
                                                    <div class="d-flex justify-content-between align-items-center mb-4">
                                                        <div>
                                                            <h6 class="text-muted mb-1">Customer</h6>
                                                            <p class="mb-0 fw-medium">
                                                                {{ $invoice->subscription->user->name ?? 'Guest User' }}
                                                            </p>
                                                            <small
                                                                class="text-muted">{{ $invoice->subscription->user->email ?? '-' }}</small>
                                                        </div>
                                                        <div class="text-end">
                                                            <span
                                                                class="badge {{ $invoice->paid_at ? 'bg-success bg-opacity-10 text-success border border-success' : 'bg-warning bg-opacity-10 text-warning border border-warning' }} px-3 py-2 rounded-3">
                                                                {{ $invoice->paid_at ? 'Paid' : 'Pending' }}
                                                            </span>
                                                        </div>
                                                    </div>

                                                    <!-- Amount Section -->
                                                    <div class="row mb-4">
                                                        <div class="col-12">
                                                            <div class="text-center p-4 bg-light rounded-3">
                                                                <h6 class="text-muted mb-2">Amount</h6>
                                                                <h3 class="text-dark fw-bold mb-0">
                                                                    {{ match (strtoupper($invoice->currency)) {
                                                                        'USD' => '$',
                                                                        'GBP' => '£',
                                                                        'EUR' => '€',
                                                                    } }}
                                                                    {{ number_format($invoice->amount_due ?? 0, 2) }}
                                                                </h3>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Details Section -->
                                                    <div class="border-top pt-4">
                                                        <div class="row g-4">
                                                            <div class="col-sm-6">
                                                                <label
                                                                    class="form-label text-muted mb-2 fw-semibold">Stripe
                                                                    Invoice ID</label>
                                                                <input type="text"
                                                                    class="form-control bg-light border-0 fs-6 py-2"
                                                                    value="{{ $invoice->stripe_invoice_id ?? '-' }}"
                                                                    readonly>
                                                            </div>

                                                            @if ($invoice->paid_at)
                                                                <div class="col-sm-6">
                                                                    <label
                                                                        class="form-label text-muted mb-2 fw-semibold">Payment
                                                                        Date</label>
                                                                    <input type="text"
                                                                        class="form-control bg-light border-0 fs-6 py-2"
                                                                        value="{{ \Carbon\Carbon::parse($invoice->paid_at)->format('M d, Y') }}"
                                                                        readonly>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
