<?php

use App\Models\Transaction;
use function Livewire\Volt\{state};

state([
    'transactions' => fn() => Transaction::latest()->get(),
]);

?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="" class="datatable table table-striped table-bordered align-middle">
                            <thead>
                                <tr>
                                    <th>Email</th>
                                    <th>Donation Type</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($transactions as $transaction)
                                    <tr>
                                        <td>{{ $transaction->invoice->subscription->user->email ?? '-' }}</td>
                                        <td>
                                            {{ $transaction->invoice->subscription->type === 'day'
                                                ? 'Daily'
                                                : ($transaction->invoice->subscription->type === 'week'
                                                    ? 'Weekly'
                                                    : ($transaction->invoice->subscription->type === 'month'
                                                        ? 'Monthly'
                                                        : ($transaction->invoice->subscription->type
                                                            ? ucfirst($transaction->invoice->subscription->type)
                                                            : '-'))) }}
                                        </td>
                                        <td>
                                            {{ match (strtoupper($transaction->invoice->currency)) {
                                                'USD' => '$',
                                                'GBP' => '£',
                                                'EUR' => '€',
                                            } }}
                                            {{ number_format($transaction->invoice->subscription->price, 2) }}
                                        </td>
                                        <td>
                                            @if ($transaction->status === 'paid' || $transaction->status === 'completed')
                                                <span class="badge bg-success">Paid</span>
                                            @elseif($transaction->status === 'failed')
                                                <span class="badge bg-danger">Failed</span>
                                            @elseif($transaction->status === 'pending')
                                                <span class="badge bg-warning text-dark">Pending</span>
                                            @else
                                                <span
                                                    class="badge bg-secondary">{{ ucfirst($transaction->status ?? 'N/A') }}</span>
                                            @endif
                                        </td>
                                        <td>{{ $transaction->paid_at ? \Carbon\Carbon::parse($transaction->paid_at)->format('Y-m-d') : '-' }}
                                        </td>
                                        <td class="text-center">
                                            <button class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                                data-bs-target="#transactionModal{{ $transaction->id }}">
                                                View
                                            </button>
                                        </td>
                                    </tr>

                                    <!-- Transaction Modal -->
                                    <div class="modal fade" id="transactionModal{{ $transaction->id }}" tabindex="-1"
                                        aria-labelledby="transactionModalLabel{{ $transaction->id }}"
                                        aria-hidden="true">
                                        <div class="modal-dialog modal-lg modal-dialog-centered">
                                            <div class="modal-content border-0 rounded-4 shadow-lg">

                                                <!-- Header -->
                                                <div class="modal-header border-0 pb-0">
                                                    <h4 class="modal-title text-dark fw-semibold"
                                                        id="transactionModalLabel{{ $transaction->id }}">
                                                        Transaction
                                                        #{{ substr($transaction->stripe_transaction_id ?? 'N/A', -8) }}
                                                    </h4>
                                                    <button type="button" class="btn-close"
                                                        data-bs-dismiss="modal"></button>
                                                </div>

                                                <!-- Body -->
                                                <div class="modal-body px-4 pb-4">

                                                    <!-- Customer & Status -->
                                                    <div class="d-flex justify-content-between align-items-center mb-4">
                                                        <div>
                                                            <h6 class="text-muted mb-1">Customer</h6>
                                                            <p class="mb-0 fw-medium">
                                                                {{ $transaction->invoice->subscription->user->name ?? 'Guest User' }}
                                                            </p>
                                                            <small class="text-muted">
                                                                {{ $transaction->invoice->subscription->user->email ?? '-' }}
                                                            </small>
                                                        </div>
                                                        <div class="text-end">
                                                            <span
                                                                class="badge 
                            @if ($transaction->status === 'paid' || $transaction->status === 'completed') bg-success bg-opacity-10 text-success border border-success
                            @elseif($transaction->statFus === 'failed') bg-danger bg-opacity-10 text-danger border border-danger
                            @elseif($transaction->status === 'pending') bg-warning bg-opacity-10 text-warning border border-warning
                            @else bg-secondary bg-opacity-10 text-secondary border border-secondary @endif
                            px-3 py-2 rounded-3">
                                                                {{ ucfirst($transaction->status ?? 'N/A') }}
                                                            </span>
                                                        </div>
                                                    </div>

                                                    <!-- Invoice & Transaction IDs -->
                                                    <div class="row mb-4">
                                                        <div class="col-sm-6">
                                                            <h6 class="text-muted mb-1">Invoice ID</h6>
                                                            <p class="fw-medium mb-0">
                                                                {{ $transaction->invoice->stripe_invoice_id ?? ($transaction->invoice_id ?? '-') }}
                                                            </p>
                                                        </div>
                                                        <div class="col-sm-6 text-sm-end">
                                                            <h6 class="text-muted mb-1">Transaction ID</h6>
                                                            <p class="fw-medium mb-0">
                                                                {{ $transaction->stripe_transaction_id ?? '-' }}
                                                            </p>
                                                        </div>
                                                    </div>

                                                    <!-- Amount Section -->
                                                    <div class="row mb-4">
                                                        <div class="col-12">
                                                            <div class="text-center p-4 bg-light rounded-3">
                                                                <h6 class="text-muted mb-2">Amount</h6>
                                                                <h3 class="text-dark fw-bold mb-0">
                                                                    {{ match (strtoupper($transaction->invoice->currency)) {
                                                                        'USD' => '$',
                                                                        'GBP' => '£',
                                                                        'EUR' => '€',
                                                                    } }}
                                                                    {{ number_format($transaction->invoice->subscription->price ?? 0, 2) }}
                                                                </h3>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Details Section -->
                                                    <div class="border-top pt-4">
                                                        <div class="row g-4">
                                                            <div class="col-sm-6">
                                                                <label
                                                                    class="form-label text-muted mb-2 fw-semibold">Paid
                                                                    At</label>
                                                                <input type="text"
                                                                    class="form-control bg-light border-0 fs-6 py-2"
                                                                    value="{{ $transaction->paid_at ? \Carbon\Carbon::parse($transaction->paid_at)->format('M d, Y') : 'Not set' }}"
                                                                    readonly>
                                                            </div>

                                                            <div class="col-sm-6">
                                                                <label
                                                                    class="form-label text-muted mb-2 fw-semibold">Invoice
                                                                    Date</label>
                                                                <input type="text"
                                                                    class="form-control bg-light border-0 fs-6 py-2"
                                                                    value="{{ $transaction->invoice->invoice_date ? \Carbon\Carbon::parse($transaction->invoice->invoice_date)->format('M d, Y') : '-' }}"
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
        </div>
    </div>
</div>