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
                        <div class="modal fade" id="invoiceModal{{ $invoice->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-lg modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Invoice Details</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <p><strong>Stripe Invoice ID:</strong>
                                                    {{ $invoice->stripe_invoice_id }}</p>
                                                <p><strong>Amount Due:</strong> {{ $invoice->amount_due }}</p>
                                                <p><strong>Currency:</strong> {{ $invoice->currency }}</p>
                                            </div>
                                            <div class="col-md-6">
                                                <p><strong>Invoice Date:</strong> {{ $invoice->invoice_date }}</p>
                                                <p><strong>Paid At:</strong> {{ $invoice->paid_at ?? 'N/A' }}</p>
                                                <p><strong>Subscription ID:</strong> {{ $invoice->subscription_id }}
                                                </p>
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
                        <div class="modal fade" id="txnModal{{ $txn->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-lg modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Transaction Details</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <p><strong>Stripe Txn ID:</strong> {{ $txn->stripe_transaction_id }}
                                                </p>
                                                <p><strong>Status:</strong> {{ $txn->status }}</p>
                                            </div>
                                            <div class="col-md-6">
                                                <p><strong>Paid At:</strong> {{ $txn->paid_at ?? 'N/A' }}</p>
                                                <p><strong>Invoice ID:</strong> {{ $txn->invoice_id }}</p>
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
