<?php

use App\Models\Subscription;
use function Livewire\Volt\{state, mount};

state([
    'subscription' => null,
]);

mount(function ($id) {
    $this->subscription = Subscription::with(['user', 'invoices.transactions'])->findOrFail($id);
});

?>

<div class="container">

    {{-- Subscription Info --}}
    <div class="card mb-3 shadow-sm">
        <div class="card-header">
            <h4>Donation Information</h4>
        </div>
        <div class="card-body">
            <table class="table table-bordered">
                <tr>
                    <th>User</th>
                    <td>{{ $subscription->user->name }} ({{ $subscription->user->email }})</td>
                </tr>
                <tr>
                    <th>Type</th>
                    <td>{{ ucfirst($subscription->type) }}</td>
                </tr>
                <tr>
                    <th>Price</th>
                    <td>{{ $subscription->price }} {{ $subscription->currency }}</td>
                </tr>
                <tr>
                    <th>Status</th>
                    <td>
                        <span class="badge {{ $subscription->status === 'active' ? 'bg-success' : 'bg-danger' }}">
                            {{ ucfirst($subscription->status) }}
                        </span>
                    </td>
                </tr>
                <tr>
                    <th>Start Date</th>
                    <td>{{ \Carbon\Carbon::parse($subscription->start_date)->format('Y-m-d') }}</td>
                </tr>
                <tr>
                    <th>End Date</th>
                    <td>{{ \Carbon\Carbon::parse($subscription->end_date)->format('Y-m-d') }}</td>
                </tr>
                <tr>
                    <th>Canceled At</th>
                    <td>{{ $subscription->canceled_at ? \Carbon\Carbon::parse($subscription->canceled_at)->format('Y-m-d') : 'N/A' }}
                    </td>
                </tr>
            </table>
        </div>
    </div>

    {{-- Invoices --}}
    <div class="card mb-3 shadow-sm">
        <div class="card-header">
            <h4>Invoices</h4>
        </div>
        <div class="card-body table-responsive">
            <table id="invoices-table" class="table table-striped table-bordered align-middle">
                <thead>
                    <tr>
                        <th>Donation type</th>
                        <th>Amount Due</th>
                        <th>Status</th>
                        <th>Invoice Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($subscription->invoices as $invoice)
                        <tr>
                            <td>{{ ucfirst($subscription->type) }}</td>
                            <td>{{ $invoice->amount_due }}</td>
                            <td>
                                <span
                                    class="badge {{ $subscription->status === 'active' ? 'bg-success' : 'bg-danger' }}">
                                    {{ ucfirst($subscription->status) }}
                                </span>
                            </td>
                            <td>{{ \Carbon\Carbon::parse($invoice->invoice_date)->format('Y-m-d') }}</td>
                            <td>
                                <button class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                    data-bs-target="#invoiceModal{{ $invoice->id }}">
                                    View
                                </button>
                            </td>
                        </tr>

                        <!-- Invoice Modal -->
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
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
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

                                        <!-- Amount & Date Section -->
                                        <div class="row mb-4">
                                            <div class="col-6">
                                                <div class="text-center p-4 bg-light rounded-3">
                                                    <h6 class="text-muted mb-2">Amount</h6>
                                                    <h3 class="text-dark fw-bold mb-0">
                                                        {{ number_format($invoice->amount_due ?? 0) }}
                                                    </h3>
                                                    <small
                                                        class="text-muted">{{ strtoupper($invoice->currency ?? 'PKR') }}</small>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="text-center p-4 bg-light rounded-3">
                                                    <h6 class="text-muted mb-2">Date</h6>
                                                    <p class="fw-semibold mb-1">
                                                        {{ $invoice->invoice_date ? \Carbon\Carbon::parse($invoice->invoice_date)->format('M d, Y') : 'Not set' }}
                                                    </p>
                                                    <small class="text-muted">Invoice Date</small>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Details Section -->
                                        <div class="border-top pt-4">
                                            <h5 class="text-muted mb-4 fw-semibold">Invoice Details</h5>

                                            <div class="row g-4">
                                                <div class="col-sm-6">
                                                    <label class="form-label text-muted mb-2 fw-semibold">Stripe Invoice
                                                        ID</label>
                                                    <input type="text"
                                                        class="form-control bg-light border-0 fs-6 py-2"
                                                        value="{{ $invoice->stripe_invoice_id ?? '-' }}" readonly>
                                                </div>

                                                <div class="col-sm-6">
                                                    <label class="form-label text-muted mb-2 fw-semibold">Subscription
                                                        ID</label>
                                                    <input type="text"
                                                        class="form-control bg-light border-0 fs-6 py-2"
                                                        value="{{ $invoice->subscription_id ?? '-' }}" readonly>
                                                </div>

                                                @if ($invoice->paid_at)
                                                    <div class="col-sm-6">
                                                        <label class="form-label text-muted mb-2 fw-semibold">Paid
                                                            At</label>
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

    {{-- Transactions --}}
    <div class="card mb-3 shadow-sm">
        <div class="card-header">
            <h4>Transactions</h4>
        </div>
        <div class="card-body table-responsive">
            <table id="transactions-table" class="table table-striped table-bordered align-middle">
                <thead>
                    <tr>
                        <th>Donation Type</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Paid at</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($subscription->invoices->flatMap->transactions as $txn)
                        <tr>
                            <td>{{ ucfirst($subscription->type) }}</td>
                            <td>{{ $subscription->price }}</td>
                            <td>
                                <span
                                    class="badge {{ $txn->status === 'completed' ? 'bg-success' : ($txn->status === 'pending' ? 'bg-warning text-dark' : 'bg-danger') }}">
                                    {{ ucfirst($txn->status) }}
                                </span>
                            </td>
                            <td>{{ \Carbon\Carbon::parse($txn->paid_at)->format('Y-m-d') }}</td>
                            <td>
                                <button class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                    data-bs-target="#txnModal{{ $txn->id }}">
                                    View
                                </button>
                            </td>
                        </tr>

                        <!-- Transaction Modal -->
                        <div class="modal fade" id="txnModal{{ $txn->id }}" tabindex="-1"
                            aria-labelledby="txnModalLabel{{ $txn->id }}" aria-hidden="true">
                            <div class="modal-dialog modal-lg modal-dialog-centered">
                                <div class="modal-content border-0 rounded-4 shadow-lg">

                                    <!-- Minimal Header -->
                                    <div class="modal-header border-0 pb-0">
                                        <h4 class="modal-title text-dark fw-semibold"
                                            id="txnModalLabel{{ $txn->id }}">
                                            Transaction #{{ substr($txn->stripe_transaction_id ?? 'N/A', -8) }}
                                        </h4>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>

                                    <!-- Modal Body -->
                                    <div class="modal-body px-4 pb-4">

                                        <!-- Status Row -->
                                        <div class="d-flex justify-content-between align-items-center mb-4">
                                            <div>
                                                <h6 class="text-muted mb-1">Invoice</h6>
                                                <p class="mb-0 fw-medium">{{ $txn->invoice_id ?? '-' }}</p>
                                            </div>
                                            <div class="text-end">
                                                <span
                                                    class="badge 
                            @if ($txn->status === 'paid' || $txn->status === 'completed') bg-success bg-opacity-10 text-success border border-success
                            @elseif($txn->status === 'failed') 
                                bg-danger bg-opacity-10 text-danger border border-danger
                            @elseif($txn->status === 'pending') 
                                bg-warning bg-opacity-10 text-warning border border-warning
                            @else 
                                bg-secondary bg-opacity-10 text-secondary border border-secondary @endif px-3 py-2 rounded-3">
                                                    {{ ucfirst($txn->status ?? 'N/A') }}
                                                </span>
                                            </div>
                                        </div>

                                        <!-- Date & Invoice Section -->
                                        <div class="row mb-4">
                                            <div class="col-6">
                                                <div class="text-center p-4 bg-light rounded-3">
                                                    <h6 class="text-muted mb-2">Paid At</h6>
                                                    <p class="fw-semibold mb-1">
                                                        {{ $txn->paid_at ? \Carbon\Carbon::parse($txn->paid_at)->format('M d, Y') : 'Not set' }}
                                                    </p>
                                                    <small class="text-muted">Payment Date</small>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="text-center p-4 bg-light rounded-3">
                                                    <h6 class="text-muted mb-2">Invoice ID</h6>
                                                    <p class="fw-semibold mb-1">{{ $txn->invoice_id ?? '-' }}</p>
                                                    <small class="text-muted">Linked Invoice</small>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Details Section -->
                                        <div class="border-top pt-4">
                                            <h5 class="text-muted mb-4 fw-semibold">Transaction Details</h5>

                                            <div class="row g-4">
                                                <div class="col-sm-6">
                                                    <label class="form-label text-muted mb-2 fw-semibold">Stripe
                                                        Transaction ID</label>
                                                    <input type="text"
                                                        class="form-control bg-light border-0 fs-6 py-2"
                                                        value="{{ $txn->stripe_transaction_id ?? '-' }}" readonly>
                                                </div>

                                                <div class="col-sm-6">
                                                    <label
                                                        class="form-label text-muted mb-2 fw-semibold">Status</label>
                                                    <input type="text"
                                                        class="form-control bg-light border-0 fs-6 py-2"
                                                        value="{{ ucfirst($txn->status ?? 'N/A') }}" readonly>
                                                </div>
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
