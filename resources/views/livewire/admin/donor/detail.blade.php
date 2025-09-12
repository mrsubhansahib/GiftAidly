<?php

use App\Models\User;
use function Livewire\Volt\{state, mount};

state([
    'user' => null,
]);

mount(function ($id) {
    $this->user = User::with(['subscriptions.invoices.transactions'])->findOrFail($id);
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
            <table id="subscriptions-table" class="table table-striped table-bordered align-middle">
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
                            <td>{{ ucfirst($sub->type) }}</td>
                            <td>{{ $sub->price }}</td>
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
                    @foreach ($user->subscriptions->flatMap->invoices as $invoice)
                        <tr>
                            <td>{{ ucfirst($invoice->subscription->type) }}</td>
                            <td>{{ $invoice->amount_due }}</td>
                            <td>
                                <span
                                    class="badge {{ $invoice->subscription->status === 'active' ? 'bg-success' : 'bg-danger' }}">
                                    {{ ucfirst($invoice->subscription->status) }}
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
                                                <p><strong>Paid At:</strong> {{ $invoice->paid_at }}</p>
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
                    @foreach ($user->subscriptions->flatMap->invoices->flatMap->transactions as $txn)
                        <tr>
                            <td>{{ ucfirst($txn->invoice->subscription->type) }}</td>
                            <td>{{ $txn->invoice->subscription->price }}</td>
                            <td>
                                <span
                                    class="badge 
                                    {{ $txn->status === 'completed' ? 'bg-success' : ($txn->status === 'pending' ? 'bg-warning text-dark' : 'bg-danger') }}">
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
                                                <p><strong>Paid At:</strong> {{ $txn->paid_at }}</p>
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
