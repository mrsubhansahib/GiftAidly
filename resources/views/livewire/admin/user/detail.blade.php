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

    {{-- Subscriptions --}}
    <div class="card mb-3 shadow-sm">
        <div class="card-header">
            <h4>Subscriptions</h4>
        </div>
        <div class="card-body table-responsive">
            <table id="subscriptions-table" class="table table-striped table-bordered align-middle">
                <thead>
                    <tr>
                        <th>Stripe Sub ID</th>
                        <th>Price</th>
                        <th>Currency</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($user->subscriptions as $sub)
                        <tr>
                            <td>{{ $sub->stripe_subscription_id }}</td>
                            <td>{{ $sub->price }}</td>
                            <td>{{ $sub->currency }}</td>
                            <td>{{ $sub->status }}</td>
                            <td>
                                <button class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                    data-bs-target="#subscriptionModal{{ $sub->id }}">
                                    View
                                </button>
                            </td>
                        </tr>

                        <!-- Subscription Modal -->
                        <div class="modal fade" id="subscriptionModal{{ $sub->id }}" tabindex="-1"
                            aria-hidden="true">
                            <div class="modal-dialog modal-lg modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Subscription Details</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <p><strong>Stripe Sub ID:</strong> {{ $sub->stripe_subscription_id }}
                                                </p>
                                                <p><strong>Price:</strong> {{ $sub->price }}</p>
                                                <p><strong>Currency:</strong> {{ $sub->currency }}</p>
                                                <p><strong>Status:</strong> {{ $sub->status }}</p>
                                            </div>
                                            <div class="col-md-6">
                                                <p><strong>Start Date:</strong> {{ $sub->start_date }}</p>
                                                <p><strong>End Date:</strong> {{ $sub->end_date }}</p>
                                                <p><strong>Canceled At:</strong> {{ $sub->canceled_at }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center">No subscriptions found</td>
                        </tr>
                    @endforelse
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
                        <th>Currency</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($user->subscriptions->flatMap->invoices as $invoice)
                        <tr>
                            <td>Daily</td>
                            <td>{{ $invoice->amount_due }}</td>
                            <td>{{ $invoice->currency }}</td>
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
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">No invoices found</td>
                        </tr>
                    @endforelse
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
                        <th>Stripe Txn ID</th>
                        <th>Status</th>
                        <th>Paid At</th>
                        <th>Invoice ID</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($user->subscriptions->flatMap->invoices->flatMap->transactions as $txn)
                        <tr>
                            <td>{{ $txn->stripe_transaction_id }}</td>
                            <td>{{ $txn->status }}</td>
                            <td>{{ $txn->paid_at }}</td>
                            <td>{{ $txn->invoice_id }}</td>
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
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">No transactions found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
