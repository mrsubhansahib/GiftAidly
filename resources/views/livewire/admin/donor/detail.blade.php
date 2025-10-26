<?php

use App\Models\User;
use function Livewire\Volt\{state, mount};

state([
    'user' => null,
]);

mount(function ($id) {
    $this->user = User::with([
        'subscriptions' => fn($q) => $q->latest(), // subscriptions newest first
        'subscriptions.invoices' => fn($q) => $q->latest(), // invoices newest first
        'subscriptions.invoices.transactions' => fn($q) => $q->latest(), // transactions newest first
    ])->findOrFail($id);
});

?>
<div class="container">

    {{-- User Info --}}
    <div class="card mb-3 shadow-sm">
        <div class="card-header">
            <h4>Donor Information</h4>
        </div>
        <div class="card-body">
            <table class="table table-bordered">
                <tr>
                    <th>Name</th>
                    <td>{{ $user->name }}</td>
                </tr>
                <tr>
                    <th>Email</th>
                    <td>{{ $user->email }}</td>
                </tr>
                <tr>
                    <th>Country</th>
                    <td>{{ $user->country ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <th>City</th>
                    <td>{{ $user->city ?? 'N/A' }}</td>
                </tr>
            </table>
        </div>
    </div>

    {{-- Donations --}}
    <div class="card mb-3 shadow-sm">
        <div class="card-header">
            <h4>Donations</h4>
        </div>
        <div class="card-body table-responsive">
            <table id="" class="datatable table table-striped table-bordered align-middle">
                <thead>
                    <tr>
                        <th>Donation Type</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Cancel Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($user->subscriptions as $sub)
                        <tr>
                            <td>
                                {{ $sub->type === 'day'
                                    ? 'Daily'
                                    : ($sub->type === 'week'
                                        ? 'Weekly'
                                        : ($sub->type === 'month'
                                            ? 'Monthly'
                                            : ($sub->type
                                                ? ucfirst($sub->type)
                                                : '-'))) }}
                            </td>
                            <td>
                                {{ match (strtoupper($sub->currency)) {
                                    'USD' => '$',
                                    'GBP' => '£',
                                    'EUR' => '€',
                                } }}
                                {{ number_format($sub->price, 2) }}
                            </td>
                            <td>
                                <span class="badge {{ $sub->status === 'active' ? 'bg-success' : 'bg-danger' }}">
                                    {{ ucfirst($sub->status) }}
                                </span>
                            </td>
                            <td>{{ \Carbon\Carbon::parse($sub->start_date)->format('Y-m-d') }}</td>
                            <td>{{ \Carbon\Carbon::parse($sub->end_date)->format('Y-m-d') }}</td>
                            <td>{{ $sub->canceled_at ? \Carbon\Carbon::parse($sub->canceled_at)->format('Y-m-d') : 'N/A' }}
                            </td>
                            <td>
                                <a href="{{ route('admin.donations.detail', $sub->id) }}"
                                    class="btn btn-sm btn-primary">View</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Invoices --}}
    {{-- <div class="card mb-3 shadow-sm">
        <div class="card-header">
            <h4>Invoices</h4>
        </div>
        <div class="card-body table-responsive">
            <table id="" class="datatable table table-striped table-bordered align-middle">
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
                    @foreach ($user->subscriptions->flatMap->invoices->sortByDesc('invoice_date') as $invoice)
                        <tr>
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

                            <td>
                                <span class="badge {{ $invoice->paid_at ? 'bg-success' : 'bg-danger' }}">
                                    {{ $invoice->paid_at ? 'Paid' : 'Failed' }}
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
                                                <small class="text-muted">
                                                    {{ $invoice->subscription->user->email ?? '-' }}
                                                </small>
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
                                                    <label class="form-label text-muted mb-2 fw-semibold">Stripe Invoice
                                                        ID</label>
                                                    <input type="text"
                                                        class="form-control bg-light border-0 fs-6 py-2"
                                                        value="{{ $invoice->stripe_invoice_id ?? '-' }}" readonly>
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
    </div> --}}

    {{-- Transactions --}}
    <div class="card mb-3 shadow-sm">
        <div class="card-header">
            <h4>Transactions</h4>
        </div>
        <div class="card-body table-responsive">
            <table id="" class="datatable table table-striped table-bordered align-middle">
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
                    @foreach ($user->subscriptions->flatMap->invoices->flatMap->transactions->sortByDesc('paid_at') as $txn)
                        <tr>
                            <td>
                                {{ $txn->invoice->subscription->type === 'day'
                                    ? 'Daily'
                                    : ($txn->invoice->subscription->type === 'week'
                                        ? 'Weekly'
                                        : ($txn->invoice->subscription->type === 'month'
                                            ? 'Monthly'
                                            : ($txn->invoice->subscription->type
                                                ? ucfirst($txn->invoice->subscription->type)
                                                : '-'))) }}
                            </td>
                            <td>
                                {{ match (strtoupper($txn->invoice->currency)) {
                                    'USD' => '$',
                                    'GBP' => '£',
                                    'EUR' => '€',
                                } }}
                                {{ number_format($txn->invoice->subscription->price, 2) }}
                            </td>
                            <td>
                                <span
                                    class="badge 
                                    {{ $txn->status === 'paid' ? 'bg-success' : ($txn->status === 'pending' ? 'bg-warning text-light' : 'bg-danger') }}">
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

                                    <!-- Header -->
                                    <div class="modal-header border-0 pb-0">
                                        <h4 class="modal-title text-dark fw-semibold"
                                            id="txnModalLabel{{ $txn->id }}">
                                            Transaction #{{ substr($txn->stripe_transaction_id ?? 'N/A', -8) }}
                                        </h4>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>

                                    <!-- Body -->
                                    <div class="modal-body px-4 pb-4">

                                        <!-- Customer & Status -->
                                        <div class="d-flex justify-content-between align-items-center mb-4">
                                            <div>
                                                <h6 class="text-muted mb-1">Customer</h6>
                                                <p class="mb-0 fw-medium">
                                                    {{ $txn->invoice->subscription->user->name ?? 'Guest User' }}
                                                </p>
                                                <small class="text-muted">
                                                    {{ $txn->invoice->subscription->user->email ?? '-' }}
                                                </small>
                                            </div>
                                            <div class="text-end">
                                                <span
                                                    class="badge 
                            @if ($txn->status === 'paid' || $txn->status === 'completed') bg-success bg-opacity-10 text-success border border-success
                            @elseif($txn->status === 'failed') bg-danger bg-opacity-10 text-danger border border-danger
                            @elseif($txn->status === 'pending') bg-warning bg-opacity-10 text-warning border border-warning
                            @else bg-secondary bg-opacity-10 text-secondary border border-secondary @endif
                            px-3 py-2 rounded-3">
                                                    {{ ucfirst($txn->status ?? 'N/A') }}
                                                </span>
                                            </div>
                                        </div>

                                        <!-- Invoice & Transaction IDs -->
                                        <div class="row mb-4">
                                            <div class="col-sm-6">
                                                <h6 class="text-muted mb-1">Invoice ID</h6>
                                                <p class="fw-medium mb-0">
                                                    {{ $txn->invoice->stripe_invoice_id ?? ($txn->invoice_id ?? '-') }}
                                                </p>
                                            </div>
                                            <div class="col-sm-6 text-sm-end">
                                                <h6 class="text-muted mb-1">Transaction ID</h6>
                                                <p class="fw-medium mb-0">
                                                    {{ $txn->stripe_transaction_id ?? '-' }}
                                                </p>
                                            </div>
                                        </div>

                                        <!-- Amount Section -->
                                        <div class="row mb-4">
                                            <div class="col-12">
                                                <div class="text-center p-4 bg-light rounded-3">
                                                    <h6 class="text-muted mb-2">Amount</h6>
                                                    <h3 class="text-dark fw-bold mb-0">
                                                        {{ match (strtoupper($txn->invoice->currency)) {
                                                            'USD' => '$',
                                                            'GBP' => '£',
                                                            'EUR' => '€',
                                                        } }}
                                                        {{ number_format($txn->invoice->subscription->price ?? 0, 2) }}
                                                    </h3>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Details Section -->
                                        <div class="border-top pt-4">
                                            <div class="row g-4">
                                                <div class="col-sm-6">
                                                    <label class="form-label text-muted mb-2 fw-semibold">Paid
                                                        At</label>
                                                    <input type="text"
                                                        class="form-control bg-light border-0 fs-6 py-2"
                                                        value="{{ $txn->paid_at ? \Carbon\Carbon::parse($txn->paid_at)->format('M d, Y') : 'Not set' }}"
                                                        readonly>
                                                </div>

                                                <div class="col-sm-6">
                                                    <label class="form-label text-muted mb-2 fw-semibold">Invoice
                                                        Date</label>
                                                    <input type="text"
                                                        class="form-control bg-light border-0 fs-6 py-2"
                                                        value="{{ $txn->invoice->invoice_date ? \Carbon\Carbon::parse($txn->invoice->invoice_date)->format('M d, Y') : '-' }}"
                                                        readonly>
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
